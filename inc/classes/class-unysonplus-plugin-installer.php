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

	/** GitHub branch archive (stable plugin lives on master). */
	const SOURCE_ZIP = 'https://github.com/UnysonPlus/UnysonPlus/archive/refs/heads/master.zip';

	/** admin-post action slug. */
	const ACTION = 'unysonplus_install_plugin';

	/** Transient that carries the result across the post-install redirect. */
	const RESULT_KEY = 'unysonplus_install_result';

	/**
	 * Wire up the admin notice + install handler. Called only when FW is absent.
	 */
	public static function init(): void {
		add_action( 'admin_notices', array( __CLASS__, 'prompt_notice' ) );
		add_action( 'admin_notices', array( __CLASS__, 'result_notice' ) );
		add_action( 'admin_post_' . self::ACTION, array( __CLASS__, 'handle_install' ) );
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
	 */
	public static function prompt_notice(): void {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$label = self::is_installed()
			? __( 'Activate UnysonPlus', 'unysonplus' )
			: __( 'Install UnysonPlus', 'unysonplus' );

		echo '<div class="notice notice-error"><p><strong>'
			. esc_html__( 'Unyson+ Theme', 'unysonplus' ) . '</strong> &mdash; '
			. esc_html__( 'this theme requires the UnysonPlus plugin (the Unyson+ framework) to be installed and active. The theme’s features stay disabled until then.', 'unysonplus' )
			. ' <a class="button button-primary" style="vertical-align:baseline;margin-left:.25rem" href="' . esc_url( self::action_url() ) . '">'
			. esc_html( $label ) . '</a></p></div>';
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
	 * Download + install (if not already on disk) + activate the plugin.
	 *
	 * @return true|WP_Error
	 */
	private static function install_and_activate() {
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
