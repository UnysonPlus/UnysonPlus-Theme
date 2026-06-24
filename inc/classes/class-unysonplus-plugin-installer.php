<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * Self-contained installer for the required UnysonPlus plugin.
 *
 * Replaces TGM-Plugin-Activation (abandoned since 2017; it repeatedly breaks on
 * modern WordPress / PHP 8). This fetches the plugin straight from its GitHub
 * master archive — the SAME source the plugin's own update checker uses —
 * installs it through WordPress core's well-maintained Plugin_Upgrader, renames
 * the archive's `UnysonPlus-master/` folder to the `unysonplus` slug, and
 * activates it.
 *
 * NOTE: this file must run WITHOUT the framework (the plugin isn't present yet),
 * so it is guarded by ABSPATH only — never by `FW`. Its hooks are registered
 * solely from Theme_Includes::bootstrap_without_framework() (i.e. only while the
 * plugin is missing); merely including the file does nothing on its own.
 */
class UnysonPlus_Plugin_Installer {

	/** Plugin entry file, relative to wp-content/plugins. */
	const PLUGIN_FILE = 'unysonplus/unysonplus.php';

	/** Destination folder slug the GitHub archive is renamed to. */
	const PLUGIN_SLUG = 'unysonplus';

	/**
	 * Complete plugin build, published as a GitHub *release asset* — the same
	 * source the plugin's own auto-updater consumes (it calls
	 * enableReleaseAssets()). NOTE: the core UnysonPlus repo's source archive is
	 * NOT a complete plugin (the shortcodes / page-builder / … extensions live in
	 * separate repos), so we must use the release-asset zip, which bundles
	 * everything and unzips straight to an `unysonplus/` folder.
	 */
	const SOURCE_ZIP = 'https://github.com/UnysonPlus/UnysonPlus/releases/latest/download/UnysonPlus.zip';

	/** admin-post action slug. */
	const ACTION = 'unysonplus_install_plugin';

	/** Transient that carries the result across the post-install redirect. */
	const RESULT_KEY = 'unysonplus_install_result';

	/**
	 * Wire up the admin notice + install handlers. Called only when FW is absent.
	 *
	 * Install runs over AJAX with a live progress UI (see print_assets). The plain
	 * admin-post handler stays as a no-JS fallback (progressive enhancement).
	 */
	public static function init(): void {
		add_action( 'admin_notices', array( __CLASS__, 'prompt_notice' ) );
		add_action( 'admin_notices', array( __CLASS__, 'result_notice' ) );
		add_action( 'admin_footer', array( __CLASS__, 'print_assets' ) );
		add_action( 'admin_post_' . self::ACTION, array( __CLASS__, 'handle_install' ) );      // no-JS fallback
		add_action( 'wp_ajax_' . self::ACTION, array( __CLASS__, 'handle_ajax_install' ) );    // progress UI
	}

	/** Whether the plugin files already exist on disk (installed but inactive). */
	private static function is_installed(): bool {
		return file_exists( WP_PLUGIN_DIR . '/' . self::PLUGIN_FILE );
	}

	/** Nonce-protected URL that triggers the install/activate. */
	private static function action_url(): string {
		return wp_nonce_url(
			admin_url( 'admin-post.php?action=' . self::ACTION ),
			self::ACTION
		);
	}

	/**
	 * Admin notice prompting the user to install (or just activate) UnysonPlus.
	 *
	 * The button is a real nonce'd link (works without JS); print_assets() upgrades
	 * the click into an AJAX install with the live progress bar below it.
	 */
	public static function prompt_notice(): void {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$installed = self::is_installed();
		$label     = $installed ? __( 'Activate UnysonPlus', 'unysonplus' ) : __( 'Install UnysonPlus', 'unysonplus' );
		$busy      = $installed
			? __( 'Activating…', 'unysonplus' )
			: __( 'Downloading &amp; installing from GitHub… this can take up to a minute.', 'unysonplus' );

		echo '<div class="notice notice-error" id="unysonplus-install-notice"><p><strong>'
			. esc_html__( 'Unyson+ Theme', 'unysonplus' ) . '</strong> &mdash; '
			. esc_html__( 'this theme requires the UnysonPlus plugin (the Unyson+ framework) to be installed and active. The theme’s features stay disabled until then.', 'unysonplus' )
			. ' <a class="button button-primary" id="unysonplus-install-btn" style="vertical-align:baseline;margin-left:.25rem"'
			. ' href="' . esc_url( self::action_url() ) . '"'
			. ' data-action="' . esc_attr( self::ACTION ) . '"'
			. ' data-nonce="' . esc_attr( wp_create_nonce( self::ACTION ) ) . '"'
			. ' data-busy="' . esc_attr( $busy ) . '">'
			. esc_html( $label ) . '</a></p>'
			// Progress UI (hidden until the button is clicked).
			. '<div id="unysonplus-install-progress" style="display:none;margin:.25rem 0 .5rem">'
			. '<div class="upi-track"><div class="upi-fill"></div></div>'
			. '<p class="upi-status" style="margin:.5rem 0 0;color:#50575e"></p>'
			. '</div></div>';
	}

	/**
	 * admin-post handler: download (if needed) + install + activate, then redirect
	 * back with a one-time result notice.
	 */
	public static function handle_install(): void {
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_die( esc_html__( 'You do not have permission to install plugins on this site.', 'unysonplus' ) );
		}
		check_admin_referer( self::ACTION );

		$result = self::install_and_activate();

		set_transient(
			self::RESULT_KEY,
			array(
				'status' => is_wp_error( $result ) ? 'error' : 'success',
				'msg'    => is_wp_error( $result ) ? $result->get_error_message() : '',
			),
			MINUTE_IN_SECONDS
		);

		// On success the plugin is now active, so the next request loads the
		// framework and the theme comes fully online. Land on the dashboard.
		wp_safe_redirect( self_admin_url( 'index.php' ) );
		exit;
	}

	/**
	 * AJAX handler powering the live progress UI. Same work as handle_install(),
	 * but returns JSON so the front-end can show progress + a redirect target.
	 */
	public static function handle_ajax_install(): void {
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to install plugins on this site.', 'unysonplus' ) ) );
		}
		check_ajax_referer( self::ACTION );

		$result = self::install_and_activate();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'redirect' => self_admin_url( 'index.php' ) ) );
	}

	/**
	 * Print the progress-bar styles + the JS that upgrades the install button
	 * into an AJAX install with a live (indeterminate) progress bar and staged
	 * status text. Without JS the button's nonce'd href falls back to the
	 * synchronous admin-post handler.
	 */
	public static function print_assets(): void {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		?>
		<style>
			#unysonplus-install-progress .upi-track{position:relative;height:8px;border-radius:6px;background:#dcdcde;overflow:hidden;max-width:420px}
			#unysonplus-install-progress .upi-fill{position:absolute;top:0;left:0;height:100%;width:40%;border-radius:6px;background:#2271b1;animation:upiSlide 1.4s ease-in-out infinite}
			#unysonplus-install-progress.upi-done .upi-fill{width:100%;animation:none;background:#46b450;transition:width .4s ease}
			#unysonplus-install-progress.upi-error .upi-fill{width:100%;animation:none;background:#d63638}
			@keyframes upiSlide{0%{left:-40%}50%{left:30%}100%{left:100%}}
		</style>
		<script>
		(function(){
			document.addEventListener('click', function(e){
				var btn = e.target.closest && e.target.closest('#unysonplus-install-btn');
				if(!btn || typeof ajaxurl === 'undefined') return;
				e.preventDefault();
				if(btn.getAttribute('aria-busy') === 'true') return;
				btn.setAttribute('aria-busy','true');

				var box    = document.getElementById('unysonplus-install-progress');
				var status = box.querySelector('.upi-status');
				btn.style.pointerEvents='none'; btn.style.opacity='.6';
				box.style.display='block'; box.className=''; // reset done/error states

				var stages = [
					<?php echo "'" . esc_js( __( 'Connecting to GitHub…', 'unysonplus' ) ) . "',"; ?>
					<?php echo "'" . esc_js( __( 'Downloading UnysonPlus…', 'unysonplus' ) ) . "',"; ?>
					<?php echo "'" . esc_js( __( 'Installing the plugin…', 'unysonplus' ) ) . "',"; ?>
					<?php echo "'" . esc_js( __( 'Activating…', 'unysonplus' ) ) . "'"; ?>
				];
				var i=0; status.textContent = stages[0];
				var timer = setInterval(function(){ if(i < stages.length-1){ status.textContent = stages[++i]; } }, 6000);

				var body = new FormData();
				body.append('action', btn.dataset.action);
				body.append('_ajax_nonce', btn.dataset.nonce);

				fetch(ajaxurl, {method:'POST', credentials:'same-origin', body:body})
					.then(function(r){ return r.json(); })
					.then(function(res){
						clearInterval(timer);
						if(res && res.success){
							box.className='upi-done';
							status.textContent = <?php echo "'" . esc_js( __( 'Installed! Reloading…', 'unysonplus' ) ) . "'"; ?>;
							setTimeout(function(){ window.location = (res.data && res.data.redirect) || window.location.href; }, 800);
						} else {
							box.className='upi-error';
							status.textContent = (res && res.data && res.data.message) ? res.data.message : <?php echo "'" . esc_js( __( 'Installation failed.', 'unysonplus' ) ) . "'"; ?>;
							btn.style.pointerEvents=''; btn.style.opacity=''; btn.removeAttribute('aria-busy');
							btn.textContent = <?php echo "'" . esc_js( __( 'Retry', 'unysonplus' ) ) . "'"; ?>;
						}
					})
					.catch(function(){
						clearInterval(timer);
						box.className='upi-error';
						status.textContent = <?php echo "'" . esc_js( __( 'Installation failed (network error). Please try again.', 'unysonplus' ) ) . "'"; ?>;
						btn.style.pointerEvents=''; btn.style.opacity=''; btn.removeAttribute('aria-busy');
					});
			});
		})();
		</script>
		<?php
	}

	/**
	 * Download + install (if not already on disk) + activate the plugin.
	 *
	 * @return true|WP_Error
	 */
	private static function install_and_activate() {
		@set_time_limit( 300 ); // the GitHub download + install can take ~a minute

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		if ( ! self::is_installed() ) {
			// The GitHub archive unzips to `UnysonPlus-master/`; rename it to the
			// plugin slug so it installs as `unysonplus/` and activates cleanly.
			$rename = array( __CLASS__, 'rename_source_to_slug' );
			add_filter( 'upgrader_source_selection', $rename, 10, 3 );

			$skin      = new WP_Ajax_Upgrader_Skin(); // quiet; accumulates errors.
			$upgrader  = new Plugin_Upgrader( $skin );
			$installed = $upgrader->install( self::SOURCE_ZIP );

			remove_filter( 'upgrader_source_selection', $rename, 10 );

			if ( is_wp_error( $installed ) ) {
				return $installed;
			}
			if ( is_wp_error( $skin->result ) ) {
				return $skin->result;
			}
			if ( method_exists( $skin, 'get_errors' ) && $skin->get_errors()->has_errors() ) {
				return $skin->get_errors();
			}
			if ( true !== $installed ) {
				return new WP_Error( 'unysonplus_install_failed', __( 'UnysonPlus could not be installed from GitHub.', 'unysonplus' ) );
			}
		}

		$activated = activate_plugin( self::PLUGIN_FILE );
		if ( is_wp_error( $activated ) ) {
			return $activated;
		}

		return true;
	}

	/**
	 * `upgrader_source_selection` filter: rename the extracted `UnysonPlus-*`
	 * directory to the `unysonplus` slug so the plugin installs under the folder
	 * its entry file expects.
	 *
	 * @param string $source        Unzipped source directory.
	 * @param string $remote_source Parent working directory.
	 * @param object $upgrader      Upgrader instance (unused).
	 * @return string|WP_Error
	 */
	public static function rename_source_to_slug( $source, $remote_source, $upgrader ) {
		$basename = basename( untrailingslashit( $source ) );

		// Only touch our own GitHub archive folder.
		if ( stripos( $basename, 'UnysonPlus-' ) !== 0 ) {
			return $source;
		}

		global $wp_filesystem;
		$target = trailingslashit( $remote_source ) . self::PLUGIN_SLUG . '/';

		if ( trailingslashit( $source ) === $target ) {
			return $source;
		}

		if ( $wp_filesystem && $wp_filesystem->move( $source, $target, true ) ) {
			return $target;
		}

		return new WP_Error( 'unysonplus_rename_failed', __( 'Could not prepare the UnysonPlus plugin folder.', 'unysonplus' ) );
	}

	/**
	 * One-time success/failure notice shown after the install redirect.
	 */
	public static function result_notice(): void {
		$res = get_transient( self::RESULT_KEY );
		if ( ! $res ) {
			return;
		}
		delete_transient( self::RESULT_KEY );

		if ( 'success' === $res['status'] ) {
			echo '<div class="notice notice-success is-dismissible"><p>'
				. esc_html__( 'UnysonPlus installed and activated — the Unyson+ Theme is now fully enabled.', 'unysonplus' )
				. '</p></div>';
		} else {
			echo '<div class="notice notice-error is-dismissible"><p><strong>'
				. esc_html__( 'UnysonPlus installation failed:', 'unysonplus' ) . '</strong> '
				. esc_html( $res['msg'] ) . '</p></div>';
		}
	}
}
