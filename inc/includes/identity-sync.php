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
/** Current attachment id stored for header_logo field (image|favicon), read flattened. */
function unysonplus_identity_get_id( $field ) {
	if ( ! function_exists( 'unysonplus_header_logo_cfg' ) ) { return 0; }
	$logo = unysonplus_header_logo_cfg();
	return isset( $logo[ $field ] ) ? unysonplus_identity_attachment_id( $logo[ $field ] ) : 0;
}
endif;

if ( ! function_exists( 'unysonplus_identity_path' ) ) :
/**
 * Settings path to WRITE a header_logo field to. Logo Type is a multi-picker, so image
 * fields live under logo_type/simple/… and text/icon fields under logo_type/custom/…;
 * favicon stays a flat, shared field.
 */
function unysonplus_identity_path( $field ) {
	if ( 'favicon' === $field ) { return 'header_logo/favicon'; }
	$simple = array( 'image', 'image_2x', 'sticky_image', 'mobile_image', 'transparent_image', 'alt', 'width' );
	$group  = in_array( $field, $simple, true ) ? 'simple' : 'custom';
	return 'header_logo/logo_type/' . $group . '/' . $field;
}
endif;

if ( ! function_exists( 'unysonplus_identity_get_text' ) ) :
/** Current text stored in header_logo[$field] (site_title | tagline_text). */
function unysonplus_identity_get_text( $field ) {
	if ( ! function_exists( 'unysonplus_header_logo_cfg' ) ) { return ''; }
	$logo = unysonplus_header_logo_cfg();
	return isset( $logo[ $field ] ) ? trim( (string) $logo[ $field ] ) : '';
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
			fw_set_db_settings_option( unysonplus_identity_path( 'image' ), unysonplus_identity_upload_value( $core ) );
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
			fw_set_db_settings_option( unysonplus_identity_path( 'favicon' ), unysonplus_identity_upload_value( $core ) );
		}
	}
}
endif;
add_action( 'fw_settings_form_saved', 'unysonplus_identity_sync_on_save', 30, 2 );

/* Site Title ⇄ blogname · Tagline ⇄ blogdescription — the header text identity holds the
 * SAME value as Settings → General. Push the header field when set; pull the core value in
 * when empty. Equality-guarded (no churn); fw_set_db_settings_option() doesn't re-fire this
 * action, so no loop. */
if ( ! function_exists( 'unysonplus_identity_sync_text_on_save' ) ) :
function unysonplus_identity_sync_text_on_save() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}
	foreach ( array( 'site_title' => 'blogname', 'tagline_text' => 'blogdescription' ) as $field => $core_opt ) {
		$val = unysonplus_identity_get_text( $field );
		if ( $val !== '' ) {
			if ( (string) get_option( $core_opt ) !== $val ) { update_option( $core_opt, $val ); }
		} else {
			$core = (string) get_option( $core_opt );
			if ( $core !== '' ) { fw_set_db_settings_option( unysonplus_identity_path( $field ), $core ); }
		}
	}
}
endif;
add_action( 'fw_settings_form_saved', 'unysonplus_identity_sync_text_on_save', 31 );

/* ---------------------------------------------------------------------------
 * Native edits (Customizer / Settings → General) → pull into Theme Settings
 * ------------------------------------------------------------------------- */
if ( ! function_exists( 'unysonplus_identity_pull_from_core' ) ) :
function unysonplus_identity_pull_from_core() {
	if ( ! function_exists( 'fw_set_db_settings_option' ) ) { return; }

	$core_logo = (int) get_theme_mod( 'custom_logo' );
	if ( $core_logo && unysonplus_identity_get_id( 'image' ) !== $core_logo ) {
		fw_set_db_settings_option( unysonplus_identity_path( 'image' ), unysonplus_identity_upload_value( $core_logo ) );
	}

	$core_icon = (int) get_option( 'site_icon' );
	if ( $core_icon && unysonplus_identity_get_id( 'favicon' ) !== $core_icon ) {
		fw_set_db_settings_option( unysonplus_identity_path( 'favicon' ), unysonplus_identity_upload_value( $core_icon ) );
	}
}
endif;
add_action( 'customize_save_after', 'unysonplus_identity_pull_from_core', 30 );
add_action( 'update_option_site_icon', 'unysonplus_identity_pull_from_core', 30 );
add_action( 'add_option_site_icon', 'unysonplus_identity_pull_from_core', 30 );

/* Settings → General (or Customizer) edits Site Title / Tagline → pull into the header field
 * so the two stay identical. Only writes when the header field already holds a DIFFERENT value
 * (or is set) — a header field left empty keeps falling back to core, so we don't force-fill it. */
if ( ! function_exists( 'unysonplus_identity_pull_text_from_core' ) ) :
function unysonplus_identity_pull_text_from_core( $field, $new ) {
	if ( ! function_exists( 'fw_set_db_settings_option' ) ) { return; }
	$new = trim( (string) $new );
	// Only mirror when the header field is non-empty and now diverges (empty = intentional fallback).
	if ( unysonplus_identity_get_text( $field ) !== '' && unysonplus_identity_get_text( $field ) !== $new ) {
		fw_set_db_settings_option( unysonplus_identity_path( $field ), $new );
	}
}
endif;
add_action( 'update_option_blogname', function ( $old, $new ) { unysonplus_identity_pull_text_from_core( 'site_title', $new ); }, 30, 2 );
add_action( 'update_option_blogdescription', function ( $old, $new ) { unysonplus_identity_pull_text_from_core( 'tagline_text', $new ); }, 30, 2 );

/* ---------------------------------------------------------------------------
 * One-time migration: legacy FLAT header_logo → the new multi-picker NESTED shape.
 * Logo Type became a multi-picker, so the image fields nest under logo_type/simple/… and
 * the icon + wordmark fields under logo_type/custom/…. Old saves stored everything flat;
 * lift them into the nested shape (idempotent — returns early once nested) so the settings
 * UI renders correctly and no data is lost. The front end never needed this (it reads via
 * unysonplus_header_logo_cfg(), which tolerates both shapes). Also folds the old 3-way
 * Logo Layout + Logo Icon Position into the new 6-way "<arrangement>-<side>" value.
 * ------------------------------------------------------------------------- */
if ( ! function_exists( 'unysonplus_migrate_header_logo_shape' ) ) :
function unysonplus_migrate_header_logo_shape() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) { return; }
	$hl = fw_get_db_settings_option( 'header_logo' );
	if ( ! is_array( $hl ) ) { return; }
	if ( isset( $hl['logo_type'] ) && is_array( $hl['logo_type'] ) ) { return; } // already nested

	$simple_keys = array( 'image', 'image_2x', 'sticky_image', 'mobile_image', 'transparent_image', 'alt', 'width' );
	$custom_keys = array( 'site_title', 'title_size', 'title_weight', 'color', 'logo_icon', 'logo_layout', 'logo_icon_frame', 'logo_icon_color', 'logo_icon_size', 'logo_custom_css', 'tagline', 'tagline_text', 'tagline_color' );

	// Nothing legacy worth lifting? (fresh install) — leave it for the option defaults.
	$has_flat = false;
	foreach ( array_merge( $simple_keys, $custom_keys, array( 'logo_type', 'logo_icon_position' ) ) as $k ) {
		if ( isset( $hl[ $k ] ) ) { $has_flat = true; break; }
	}
	if ( ! $has_flat ) { return; }

	$type   = ( ! empty( $hl['logo_type'] ) && is_string( $hl['logo_type'] ) ) ? $hl['logo_type'] : ( ! empty( $hl['image']['url'] ) ? 'simple' : 'custom' );
	$simple = array();
	$custom = array();
	foreach ( $simple_keys as $k ) { if ( isset( $hl[ $k ] ) ) { $simple[ $k ] = $hl[ $k ]; unset( $hl[ $k ] ); } }
	foreach ( $custom_keys as $k ) { if ( isset( $hl[ $k ] ) ) { $custom[ $k ] = $hl[ $k ]; unset( $hl[ $k ] ); } }
	// Legacy 3-way layout ('inline'|'stacked'|'eyebrow') + old logo_icon_position → 6-way.
	if ( ! empty( $custom['logo_layout'] ) && false === strpos( (string) $custom['logo_layout'], '-' ) ) {
		$side = ( ! empty( $hl['logo_icon_position'] ) && 'after' === $hl['logo_icon_position'] ) ? 'right' : 'left';
		$custom['logo_layout'] .= '-' . $side;
	}
	unset( $hl['logo_icon_position'] );

	$hl['logo_type'] = array( 'logo_type' => $type, 'simple' => $simple, 'custom' => $custom );
	fw_set_db_settings_option( 'header_logo', $hl );
}
endif;
add_action( 'admin_init', 'unysonplus_migrate_header_logo_shape' );
