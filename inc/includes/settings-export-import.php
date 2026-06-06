<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * Theme Settings — Export / Import
 * =================================
 * Adds a self-contained "Export / Import" panel to the top of the Theme
 * Settings screen (Appearance → Theme Settings) so a site's *design* can travel
 * as a single portable `.json` file. Companion to the page-builder template
 * export/import: theme settings carry the global look (colors, typography,
 * header/footer, spacing, custom CSS); builder templates carry section content.
 *
 * Storage uses the framework as-is: `fw_get_db_settings_option()` /
 * `fw_set_db_settings_option()` (one wp_option, `fw_theme_settings_options:{id}`).
 * No framework files are modified. The panel is rendered via `admin_notices`
 * (outside the settings <form>, so the multipart import form isn't nested), and
 * both actions run through `admin-post.php` with nonce + capability checks.
 *
 * Scope: DESIGN-ONLY for now (operational/site-specific settings are excluded —
 * see unysonplus_settings_io_exclude_keys()). A per-tab checkbox UI can be added
 * later; the exclude list is filterable so that change is non-breaking.
 *
 * Media (logo/background images/favicon) is stripped on export: those store
 * source-site attachment IDs/URLs that don't exist on the target. Colors, fonts,
 * layout and custom CSS transfer cleanly; the user re-adds their own images.
 *
 * @since unysonplus-theme 2.1.40
 */

if ( ! is_admin() ) {
	return; // admin-only feature
}

/**
 * Operational / site-specific settings that must NOT travel with a design.
 * Excluded from export AND ignored on import (defense in depth: a tampered file
 * can't inject tracking pixels or custom <script> via the design importer).
 * Filterable so a future "choose what to export" UI can narrow/widen it.
 *
 * @return string[] top-level settings option ids
 */
function unysonplus_settings_io_exclude_keys() {
	return (array) apply_filters( 'unysonplus_settings_io_exclude_keys', array(
		'misc_analytics',       // GA4 / GTM / Meta Pixel / Clarity ids
		'misc_performance',     // per-site performance toggles
		'misc_maintenance',     // maintenance-mode content
		'misc_404',             // 404 page selection
		'misc_custom_scripts',  // custom head/body/footer scripts (tracking, exec)
	) );
}

/**
 * Recursively blank media fields. Unyson upload / background-image / favicon
 * values are arrays carrying an `attachment_id` (+ `url`) keyed to the source
 * site. Replace any such array with an empty array so the field reads as unset.
 *
 * @param mixed $value
 * @return mixed
 */
function unysonplus_settings_io_strip_media( $value ) {
	if ( is_array( $value ) ) {
		if ( array_key_exists( 'attachment_id', $value ) ) {
			return array();
		}
		foreach ( $value as $k => $v ) {
			$value[ $k ] = unysonplus_settings_io_strip_media( $v );
		}
	}
	return $value;
}

/** @return bool current user may manage theme settings */
function unysonplus_settings_io_can() {
	return current_user_can( 'manage_options' );
}

/** @return string the Theme Settings page slug (defaults to fw-settings) */
function unysonplus_settings_io_page_slug() {
	if ( function_exists( 'fw' ) && fw()->backend && method_exists( fw()->backend, '_get_settings_page_slug' ) ) {
		return (string) fw()->backend->_get_settings_page_slug();
	}
	return 'fw-settings';
}

/** @return string admin URL of the Theme Settings page (registered under Appearance) */
function unysonplus_settings_io_page_url() {
	return admin_url( 'themes.php?page=' . unysonplus_settings_io_page_slug() );
}

/** @return array{0:string,1:string} [theme_id, theme_version] */
function unysonplus_settings_io_theme_meta() {
	if ( function_exists( 'fw' ) && fw()->theme && fw()->theme->manifest ) {
		return array( (string) fw()->theme->manifest->get_id(), (string) fw()->theme->manifest->get_version() );
	}
	return array( '', '' );
}

/** Redirect back to the settings page with a result code, then exit. */
function unysonplus_settings_io_redirect( $code ) {
	wp_safe_redirect( add_query_arg( 'unysonplus_io', rawurlencode( $code ), unysonplus_settings_io_page_url() ) );
	exit;
}

/* -------------------------------------------------------------------------
 * Export  (admin-post.php?action=unysonplus_export_theme_settings)
 * ---------------------------------------------------------------------- */
add_action( 'admin_post_unysonplus_export_theme_settings', 'unysonplus_settings_io_export' );
function unysonplus_settings_io_export() {
	if ( ! unysonplus_settings_io_can() ) {
		wp_die( esc_html__( 'You are not allowed to export theme settings.', 'unysonplus' ) );
	}
	check_admin_referer( 'unysonplus_export_theme_settings' );

	if ( ! function_exists( 'fw_get_db_settings_option' ) ) {
		wp_die( esc_html__( 'The Unyson framework is not active.', 'unysonplus' ) );
	}

	$values = fw_get_db_settings_option();
	$values = is_array( $values ) ? $values : array();

	// Design-only: drop operational keys, then strip site-specific media refs.
	foreach ( unysonplus_settings_io_exclude_keys() as $k ) {
		unset( $values[ $k ] );
	}
	$values = unysonplus_settings_io_strip_media( $values );

	list( $theme_id, $theme_version ) = unysonplus_settings_io_theme_meta();

	$envelope = array(
		'_fw_settings_export' => array(
			'format_version' => 1,
			'scope'          => 'design',
			'theme_id'       => $theme_id,
			'theme_version'  => $theme_version,
			'exported_at'    => time(),
			'excluded'       => array_values( unysonplus_settings_io_exclude_keys() ),
			'media_stripped' => true,
		),
		'values' => $values,
	);

	$slug     = sanitize_title( $theme_id ? $theme_id : 'theme' );
	$filename = $slug . '-settings-design-' . gmdate( 'Ymd-His' ) . '.json';

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	echo wp_json_encode( $envelope, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
}

/* -------------------------------------------------------------------------
 * Import  (admin-post.php?action=unysonplus_import_theme_settings)
 * ---------------------------------------------------------------------- */
add_action( 'admin_post_unysonplus_import_theme_settings', 'unysonplus_settings_io_import' );
function unysonplus_settings_io_import() {
	if ( ! unysonplus_settings_io_can() ) {
		wp_die( esc_html__( 'You are not allowed to import theme settings.', 'unysonplus' ) );
	}
	check_admin_referer( 'unysonplus_import_theme_settings' );

	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		wp_die( esc_html__( 'The Unyson framework is not active.', 'unysonplus' ) );
	}

	if (
		empty( $_FILES['settings_file'] ) ||
		! isset( $_FILES['settings_file']['error'] ) ||
		$_FILES['settings_file']['error'] !== UPLOAD_ERR_OK
	) {
		unysonplus_settings_io_redirect( 'err_no_file' );
	}

	if ( (int) $_FILES['settings_file']['size'] > 5 * 1024 * 1024 ) {
		unysonplus_settings_io_redirect( 'err_too_large' );
	}

	$tmp = isset( $_FILES['settings_file']['tmp_name'] ) ? $_FILES['settings_file']['tmp_name'] : '';
	if ( empty( $tmp ) || ! is_uploaded_file( $tmp ) ) {
		unysonplus_settings_io_redirect( 'err_no_file' );
	}

	$raw  = file_get_contents( $tmp );
	$data = ( is_string( $raw ) && '' !== $raw ) ? json_decode( $raw, true ) : null;

	if (
		! is_array( $data ) ||
		empty( $data['_fw_settings_export'] ) || ! is_array( $data['_fw_settings_export'] ) ||
		! isset( $data['values'] ) || ! is_array( $data['values'] )
	) {
		unysonplus_settings_io_redirect( 'err_invalid' );
	}

	$env      = $data['_fw_settings_export'];
	$incoming = $data['values'];

	// Never import operational/script keys (matches design scope; blocks injection).
	foreach ( unysonplus_settings_io_exclude_keys() as $k ) {
		unset( $incoming[ $k ] );
	}

	if ( empty( $incoming ) ) {
		unysonplus_settings_io_redirect( 'err_empty' );
	}

	// Overlay imported design keys onto current values (top-level option ids are
	// whole multi-containers), preserving every setting the file doesn't carry.
	$current = fw_get_db_settings_option();
	$current = is_array( $current ) ? $current : array();

	$merged = $current;
	foreach ( $incoming as $k => $v ) {
		$merged[ $k ] = $v;
	}

	fw_set_db_settings_option( null, $merged );

	// Let the framework / theme react (cache flush, regenerated assets, etc.).
	do_action( 'fw_settings_form_saved', $current, $merged );

	list( $theme_id ) = unysonplus_settings_io_theme_meta();
	$cross_theme = ! empty( $env['theme_id'] ) && $theme_id && $env['theme_id'] !== $theme_id;

	unysonplus_settings_io_redirect( $cross_theme ? 'imported_warning' : 'imported' );
}

/* -------------------------------------------------------------------------
 * Result notice after an import redirect. Slim — the control itself now lives
 * in the Misc tab (see unysonplus_settings_io_misc_field_html()); this only
 * surfaces the success/error message on return.
 * ---------------------------------------------------------------------- */
add_action( 'admin_notices', 'unysonplus_settings_io_result_notice' );
function unysonplus_settings_io_result_notice() {
	if ( ! unysonplus_settings_io_can() ) {
		return;
	}
	if ( ! isset( $_GET['page'] ) || $_GET['page'] !== unysonplus_settings_io_page_slug() ) {
		return;
	}
	if ( ! isset( $_GET['unysonplus_io'] ) ) {
		return;
	}

	$map = array(
		'imported'         => array( 'success', __( 'Theme settings imported — the design has been applied.', 'unysonplus' ) ),
		'imported_warning' => array( 'warning', __( 'Imported, but the file came from a different theme; only recognized design settings were applied.', 'unysonplus' ) ),
		'err_no_file'      => array( 'error',   __( 'No file was uploaded, or the upload failed.', 'unysonplus' ) ),
		'err_too_large'    => array( 'error',   __( 'That file is too large (maximum 5 MB).', 'unysonplus' ) ),
		'err_invalid'      => array( 'error',   __( 'That file is not a valid Unyson+ theme-settings export.', 'unysonplus' ) ),
		'err_empty'        => array( 'error',   __( 'No importable design settings were found in that file.', 'unysonplus' ) ),
	);

	$code = sanitize_key( wp_unslash( $_GET['unysonplus_io'] ) );
	if ( isset( $map[ $code ] ) ) {
		printf(
			'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
			esc_attr( $map[ $code ][0] ),
			esc_html( $map[ $code ][1] )
		);
	}
}

/* -------------------------------------------------------------------------
 * The Export / Import control, rendered as an `html-full` option inside the
 * Misc tab (Theme Settings → Miscellaneous → Export / Import, before Reset
 * Settings). Wired up from framework-customizations/theme/options/misc.php.
 *
 * Why the JS dance: this markup lives INSIDE the settings <form>, which is not
 * multipart and posts to the settings save handler. So the file control carries
 * NO `name` (it isn't submitted on Save), the button is type="button", and on
 * "Import" we build a throwaway multipart form, move the chosen file into it,
 * and submit that to the existing admin-post import handler (which redirects
 * back with a result code). Nothing here interferes with Save / Reset.
 * ---------------------------------------------------------------------- */
function unysonplus_settings_io_misc_field_html() {
	$export_url   = wp_nonce_url(
		admin_url( 'admin-post.php?action=unysonplus_export_theme_settings' ),
		'unysonplus_export_theme_settings'
	);
	$import_nonce = wp_create_nonce( 'unysonplus_import_theme_settings' );
	$post_url     = admin_url( 'admin-post.php' );

	ob_start();
	?>
	<div class="unysonplus-io">
		<p style="max-width:70ch;color:#50575e;margin-top:0;">
			<?php esc_html_e( 'Save this site\'s design (colors, typography, header, footer, spacing, custom CSS) to a portable .json file, or apply a design file to this site. Operational settings (analytics, performance, maintenance, 404, custom scripts) and uploaded images are not included.', 'unysonplus' ); ?>
		</p>
		<p>
			<a href="<?php echo esc_url( $export_url ); ?>" class="button button-secondary" style="display:inline-flex;align-items:center;gap:6px;">
				<span class="dashicons dashicons-download"></span>
				<?php esc_html_e( 'Export design', 'unysonplus' ); ?>
			</a>
		</p>
		<p style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
			<input type="file" id="unysonplus-io-file" accept="application/json,.json" />
			<button type="button" id="unysonplus-io-import" class="button button-primary" style="display:inline-flex;align-items:center;gap:6px;">
				<span class="dashicons dashicons-upload"></span>
				<?php esc_html_e( 'Import design', 'unysonplus' ); ?>
			</button>
		</p>
	</div>
	<script>
	(function () {
		var btn  = document.getElementById('unysonplus-io-import');
		var file = document.getElementById('unysonplus-io-file');
		if (!btn || !file) { return; }
		btn.addEventListener('click', function () {
			if (!file.files || !file.files.length) {
				window.alert(<?php echo wp_json_encode( __( 'Please choose a .json design file to import first.', 'unysonplus' ) ); ?>);
				return;
			}
			var form = document.createElement('form');
			form.method        = 'post';
			form.enctype       = 'multipart/form-data';
			form.action        = <?php echo wp_json_encode( $post_url ); ?>;
			form.style.display = 'none';

			var a = document.createElement('input');
			a.type = 'hidden'; a.name = 'action'; a.value = 'unysonplus_import_theme_settings';
			form.appendChild(a);

			var n = document.createElement('input');
			n.type = 'hidden'; n.name = '_wpnonce'; n.value = <?php echo wp_json_encode( $import_nonce ); ?>;
			form.appendChild(n);

			// Move the chosen file input into the throwaway form (preserves the file).
			file.name = 'settings_file';
			form.appendChild(file);

			document.body.appendChild(form);
			form.submit();
		});
	})();
	</script>
	<?php
	return ob_get_clean();
}
