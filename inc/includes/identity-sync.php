<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * Logo & Favicon sync — bridges the Theme Settings logo/favicon with WordPress
 * core's Custom Logo (theme_mod `custom_logo`, Customize → Site Identity) and
 * Site Icon (option `site_icon`, Settings → General / Customizer), so they stay
 * identical no matter where they're edited.
 *
 *   Theme Settings save → push theme image → core; if a theme field is empty,
 *                         pull the existing core value into it.
 *   Customizer save     → pull custom_logo / site_icon → theme settings.
 *   Site Icon change    → pull site_icon → theme settings.
 *
 * Loop-safe: set_theme_mod() / update_option() don't re-fire the Unyson save
 * action, and every write is guarded by an equality check so nothing churns.
 *
 * @since unysonplus-theme 2.1.50
 */

if ( ! is_admin() ) {
	return; // identity is only ever edited in admin contexts
}

if ( ! function_exists( 'unysonplus_identity_attachment_id' ) ) :
/** Extract an attachment id from a Theme Settings `upload` field value. */
function unysonplus_identity_attachment_id( $value ) {
	if ( ! is_array( $value ) ) { return 0; }
	if ( ! empty( $value['attachment_id'] ) ) { return (int) $value['attachment_id']; }
	if ( ! empty( $value['id'] ) )            { return (int) $value['id']; }
	return 0;
}
endif;

if ( ! function_exists( 'unysonplus_identity_upload_value' ) ) :
/** Build a Theme Settings `upload` field value from an attachment id. */
function unysonplus_identity_upload_value( $id ) {
	$id  = (int) $id;
	$url = $id ? wp_get_attachment_url( $id ) : '';
	if ( ! $id || ! $url ) { return []; }
	return [ 'attachment_id' => $id, 'id' => $id, 'url' => $url ];
}
endif;

if ( ! function_exists( 'unysonplus_identity_get_id' ) ) :
/** Current attachment id stored in header_logo[$field] (image|favicon). */
function unysonplus_identity_get_id( $field ) {
	if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return 0; }
	$logo = fw_get_db_settings_option( 'header_logo' );
	return ( is_array( $logo ) && isset( $logo[ $field ] ) )
		? unysonplus_identity_attachment_id( $logo[ $field ] )
		: 0;
}
endif;

/* ---------------------------------------------------------------------------
 * Theme Settings save → reconcile both ways (push when set, pull when empty)
 * ------------------------------------------------------------------------- */
if ( ! function_exists( 'unysonplus_identity_sync_on_save' ) ) :
function unysonplus_identity_sync_on_save( $old = null, $new = null ) {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}

	// LOGO  ⇄  theme_mod('custom_logo')
	$logo_id = unysonplus_identity_get_id( 'image' );
	if ( $logo_id ) {
		if ( (int) get_theme_mod( 'custom_logo' ) !== $logo_id ) {
			set_theme_mod( 'custom_logo', $logo_id );
		}
	} else {
		$core = (int) get_theme_mod( 'custom_logo' );
		if ( $core ) {
			fw_set_db_settings_option( 'header_logo/image', unysonplus_identity_upload_value( $core ) );
		}
	}

	// FAVICON  ⇄  option('site_icon')
	$fav_id = unysonplus_identity_get_id( 'favicon' );
	if ( $fav_id ) {
		if ( (int) get_option( 'site_icon' ) !== $fav_id ) {
			update_option( 'site_icon', $fav_id );
		}
	} else {
		$core = (int) get_option( 'site_icon' );
		if ( $core ) {
			fw_set_db_settings_option( 'header_logo/favicon', unysonplus_identity_upload_value( $core ) );
		}
	}
}
endif;
add_action( 'fw_settings_form_saved', 'unysonplus_identity_sync_on_save', 30, 2 );

/* ---------------------------------------------------------------------------
 * Native edits (Customizer / Settings → General) → pull into Theme Settings
 * ------------------------------------------------------------------------- */
if ( ! function_exists( 'unysonplus_identity_pull_from_core' ) ) :
function unysonplus_identity_pull_from_core() {
	if ( ! function_exists( 'fw_set_db_settings_option' ) ) { return; }

	$core_logo = (int) get_theme_mod( 'custom_logo' );
	if ( $core_logo && unysonplus_identity_get_id( 'image' ) !== $core_logo ) {
		fw_set_db_settings_option( 'header_logo/image', unysonplus_identity_upload_value( $core_logo ) );
	}

	$core_icon = (int) get_option( 'site_icon' );
	if ( $core_icon && unysonplus_identity_get_id( 'favicon' ) !== $core_icon ) {
		fw_set_db_settings_option( 'header_logo/favicon', unysonplus_identity_upload_value( $core_icon ) );
	}
}
endif;
add_action( 'customize_save_after', 'unysonplus_identity_pull_from_core', 30 );
add_action( 'update_option_site_icon', 'unysonplus_identity_pull_from_core', 30 );
add_action( 'add_option_site_icon', 'unysonplus_identity_pull_from_core', 30 );
