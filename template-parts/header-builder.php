<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

/**
 * Header ROUTER.
 *
 * Resolves the active header configuration + layout mode ONCE, then dispatches to
 * a self-contained per-mode template in template-parts/header/. Each mode template
 * owns its full markup and receives the resolved data through get_template_part()'s
 * $args. The shared "primitives" stay out of the mode files on purpose:
 *   - element rendering (logo / menu / button …): unysonplus_render_header_column()
 *   - config + class resolution: this router
 * so a mode template only ever contains that mode's layout — easy to trace/maintain.
 *
 * Mode → template (template-parts/header/):
 *   top (+ design)      → top-default | top-floating-pill | top-elevated-card | top-centered
 *   vertical-left/right  → vertical-left-default | vertical-right-default
 *   off-canvas-only      → off-canvas-default
 *   overlay (+ style)    → overlay-default | overlay-radial
 *   builder-authored     → builder
 *
 * NOTE: several slot modes render identical markup today (the mode is expressed via
 * CSS classes computed here). They are still separate files so each mode can diverge
 * independently — that is the point of the split.
 */

$unyson = function_exists( 'fw_get_db_settings_option' );

/* ============================================================
 * Branch A — builder-authored header (Header & Footer Builder)
 * ============================================================ */
$hf_render = function_exists( 'unysonplus_get_active_header_render' )
	? unysonplus_get_active_header_render()
	: array( 'mode' => 'slots' );

if ( $hf_render['mode'] === 'builder' && function_exists( 'fw_ext_hfbuilder_render' ) ) {
	$hf_type     = sanitize_html_class( $hf_render['type'] );
	$hf_behavior = sanitize_html_class( $hf_render['behavior'] );

	$h_classes = array( 'site-header', 'site-header--' . $hf_type, 'site-header--' . $hf_behavior );
	if ( in_array( $hf_render['behavior'], array( 'sticky', 'sticky-shrink', 'hide-on-scroll', 'transparent-overlay' ), true ) ) {
		$h_classes[] = 'header-sticky';
	}
	if ( $hf_render['behavior'] === 'transparent-overlay' ) { $h_classes[] = 'site-header--transparent'; }
	if ( function_exists( 'fw_get_db_post_option' ) && fw_get_db_post_option( get_the_ID(), 'page_header' ) === 'd-none' ) {
		$h_classes[] = 'd-none';
	}

	$hf_ov_style = function_exists( 'unysonplus_header_layout_get' ) ? unysonplus_header_layout_get( 'overlay_style', 'panel' ) : 'panel';
	$hf_corner = 'tr';
	$hf_cmode  = 'shade';
	if ( ( $hf_type === 'fullscreen-overlay' ) && function_exists( 'fw_akg' ) && function_exists( 'fw_get_db_settings_option' ) ) {
		$hf_chrome = fw_get_db_settings_option( 'header_layout', array() );
		$hf_chrome = is_array( $hf_chrome ) ? $hf_chrome : array();
		$m = fw_akg( 'header_mode/overlay/overlay_color_mode', $hf_chrome, 'shade' );
		if ( in_array( $m, array( 'shade', 'tint', 'aurora', 'rainbow', 'mono', 'duotone', 'alternating', 'glass' ), true ) ) { $hf_cmode = $m; }
		if ( $hf_ov_style === 'concentric' ) {
			$c = fw_akg( 'header_mode/overlay/overlay_style/concentric/overlay_corner', $hf_chrome, 'tr' );
			if ( in_array( $c, array( 'tr', 'tl', 'br', 'bl' ), true ) ) { $hf_corner = $c; }
		}
	}

	get_template_part( 'template-parts/header/builder', null, array(
		'h_classes'      => implode( ' ', $h_classes ),
		'hf_type'        => $hf_type,
		'hf_behavior'    => $hf_behavior,
		'post_id'        => $hf_render['post_id'],
		'needs_drawer'   => in_array( $hf_type, array( 'off-canvas', 'fullscreen-overlay' ), true ),
		'is_radial'      => ( $hf_type === 'fullscreen-overlay' && $hf_ov_style === 'radial' ),
		'is_concentric'  => ( $hf_type === 'fullscreen-overlay' && $hf_ov_style === 'concentric' ),
		'overlay_corner' => $hf_corner,
		'overlay_color_mode' => $hf_cmode,
	) );
	return;
}

/* ============================================================
 * Branch B — slot header: resolve config, chrome, classes
 * ============================================================ */
$header_cfg  = isset( $hf_render['config'] ) && is_array( $hf_render['config'] ) ? $hf_render['config'] : array();
$get_section = function ( $id ) use ( $header_cfg, $unyson ) {
	if ( isset( $header_cfg[ $id ] ) && is_array( $header_cfg[ $id ] ) ) { return $header_cfg[ $id ]; }
	if ( $unyson ) { $v = fw_get_db_settings_option( $id, array() ); return is_array( $v ) ? $v : array(); }
	return array();
};

$chrome    = $get_section( 'header_layout' );
$topbar    = $get_section( 'header_topbar' );
$main      = $get_section( 'header_main' );
$bottombar = $get_section( 'header_bottombar' );

$container = ! empty( $chrome['container'] ) ? $chrome['container'] : 'container';

// Behavior — the TWO-STATE model: POSITION (static/sticky/overlay) is orthogonal to the At-top /
// On-scroll appearance, so any combination works. A read-side LEGACY SHIM maps an older saved
// header_behavior onto the new position (+ the matching on-scroll toggle) so a not-yet-re-picked
// header still renders sensibly — no DB migration.
$hlg      = function_exists( 'unysonplus_header_layout_get' );
$legacy   = ! empty( $chrome['header_behavior'] ) ? (string) $chrome['header_behavior'] : '';
$position = ! empty( $chrome['header_position'] ) ? (string) $chrome['header_position'] : '';
if ( $position === '' && $legacy !== '' ) {
	$position = ( $legacy === 'transparent-overlay' ) ? 'overlay'
		: ( in_array( $legacy, array( 'sticky', 'sticky-shrink', 'hide-on-scroll' ), true ) ? 'sticky' : 'static' );
}
if ( $position === '' && ! empty( $chrome['sticky_header'] ) && $chrome['sticky_header'] === 'yes' ) { $position = 'sticky'; }
if ( function_exists( 'fw_get_db_post_option' ) && fw_get_db_post_option( get_the_ID(), 'page_header' ) === 'transparent' ) { $position = 'overlay'; }
if ( $position === '' ) { $position = 'static'; }

// On-scroll state (takes effect only when the master switch is on). Legacy shim: old sticky-shrink
// enables shrink; old hide-on-scroll enables hide.
$scroll_change  = ( $hlg && unysonplus_header_layout_get( 'header_scroll_change', 'no' ) === 'yes' );
$scroll_shrink  = ( $scroll_change && $hlg && unysonplus_header_layout_get( 'scroll_shrink', 'no' ) === 'yes' );
if ( $legacy === 'sticky-shrink' ) { $scroll_change = true; $scroll_shrink = true; }
$hide_on_scroll = ( $hlg && unysonplus_header_layout_get( 'header_hide_on_scroll', 'no' ) === 'yes' ) || ( $legacy === 'hide-on-scroll' );

// Internal composite kept for data-hf-behavior + the existing sticky/overlay positioning CSS.
$behavior = ( $position === 'overlay' ) ? 'transparent-overlay' : $position; // static | sticky | transparent-overlay

// Section column arrays (a bar renders only when a column has content).
$topbar_left   = ! empty( $topbar['topbar_left'] )   ? $topbar['topbar_left']   : array();
$topbar_center = ! empty( $topbar['topbar_center'] ) ? $topbar['topbar_center'] : array();
$topbar_right  = ! empty( $topbar['topbar_right'] )  ? $topbar['topbar_right']  : array();
$bottombar_left   = ! empty( $bottombar['bottombar_left'] )   ? $bottombar['bottombar_left']   : array();
$bottombar_center = ! empty( $bottombar['bottombar_center'] ) ? $bottombar['bottombar_center'] : array();
$bottombar_right  = ! empty( $bottombar['bottombar_right'] )  ? $bottombar['bottombar_right']  : array();
$main_left   = ! empty( $main['main_left'] )   ? $main['main_left']   : array();
$main_center = ! empty( $main['main_center'] ) ? $main['main_center'] : array();
$main_right  = ! empty( $main['main_right'] )  ? $main['main_right']  : array();

// Per-section wrapper container + classes from each row's Custom Styling block.
if ( function_exists( 'unysonplus_hf_section_render_attrs' ) ) {
	$topbar_attr    = unysonplus_hf_section_render_attrs( isset( $topbar['topbar_custom_styling'] ) ? $topbar['topbar_custom_styling'] : array(), 'topbar', $container );
	$main_attr      = unysonplus_hf_section_render_attrs( isset( $main['main_custom_styling'] ) ? $main['main_custom_styling'] : array(), 'main', $container );
	$bottombar_attr = unysonplus_hf_section_render_attrs( isset( $bottombar['bottombar_custom_styling'] ) ? $bottombar['bottombar_custom_styling'] : array(), 'bottombar', $container );
} else {
	$topbar_attr = $main_attr = $bottombar_attr = array( 'container' => $container, 'class' => '' );
}

// Fresh-install fallback: synthesize a logo + primary menu so the header is usable.
if ( ! $unyson || ( empty( $main_left ) && empty( $main_center ) && empty( $main_right ) ) ) {
	$main_left   = array( array( 'element_type' => array( 'element' => 'logo', 'logo' => array() ) ) );
	$main_center = array( array( 'element_type' => array( 'element' => 'menu_area', 'menu_area' => array( 'menu_location' => 'primary' ) ) ) );
}

// Header classes.
$header_classes = array( 'site-header', 'site-header--' . sanitize_html_class( $behavior ) );
if ( in_array( $position, array( 'sticky', 'overlay' ), true ) ) { $header_classes[] = 'header-sticky'; }
if ( $position === 'overlay' ) { $header_classes[] = 'site-header--transparent'; }
if ( $hide_on_scroll ) { $header_classes[] = 'site-header--hide'; }
if ( function_exists( 'fw_get_db_post_option' ) && fw_get_db_post_option( get_the_ID(), 'page_header' ) === 'd-none' ) { $header_classes[] = 'd-none'; }

// Collapse breakpoint: a custom pixel width (Header → Mobile & Tablet) overrides the md/lg
// preset. The custom class is targeted by the per-request <style> in wp_head
// (unysonplus_custom_breakpoint_css), which reproduces the collapse + mobile bar + layout
// rules at that width so everything engages together.
$mobile_bp    = fw_get_db_settings_option( 'mobile_breakpoint', 'lg' );
if ( empty( $mobile_bp ) ) { $mobile_bp = 'lg'; }
$mbp_px       = function_exists( 'fw_get_db_settings_option' ) ? fw_get_db_settings_option( 'mobile_breakpoint_px', array() ) : array();
$mbp_px_val   = ( is_array( $mbp_px ) && isset( $mbp_px['value'] ) && is_numeric( $mbp_px['value'] ) && (float) $mbp_px['value'] > 0 ) ? (float) $mbp_px['value'] : 0;
$header_classes[] = $mbp_px_val > 0 ? 'header-collapse-custom' : ( 'header-collapse-' . sanitize_html_class( $mobile_bp ) );

// Mobile controls: hide the top / bottom bar on small screens.
if ( fw_get_db_settings_option( 'mobile_hide_topbar', 'no' ) === 'yes' )    { $header_classes[] = 'header-hide-topbar-m'; }
if ( fw_get_db_settings_option( 'mobile_hide_bottombar', 'no' ) === 'yes' ) { $header_classes[] = 'header-hide-bottombar-m'; }

// Mobile Header Layout (Header → Mobile & Tablet). A top-level key (not in the
// header_layout group); the class engages the CSS in header-footer-builder.css that
// re-arranges logo + hamburger below the collapse width. 'default' = no class.
$mobile_layout = function_exists( 'fw_get_db_settings_option' ) ? fw_get_db_settings_option( 'mobile_header_layout', 'default' ) : 'default';
if ( is_string( $mobile_layout ) && $mobile_layout !== '' && $mobile_layout !== 'default' ) {
	$header_classes[] = 'header-mlayout--' . sanitize_html_class( $mobile_layout );
}

$header_design = function_exists( 'unysonplus_header_layout_get' ) ? unysonplus_header_layout_get( 'header_design', 'classic' ) : 'classic';
if ( $header_design && $header_design !== 'classic' ) { $header_classes[] = 'site-header--design-' . sanitize_html_class( $header_design ); }

if ( $hlg ) {
	// Appearance — AT TOP (the resting look; also the scrolled look when Change-on-scroll is off).
	if ( unysonplus_header_layout_get( 'header_border', 'no' ) === 'yes' )        { $header_classes[] = 'site-header--border'; }
	if ( unysonplus_header_layout_get( 'header_shadow', 'no' ) === 'yes' )        { $header_classes[] = 'site-header--shadow'; }
	if ( unysonplus_header_layout_get( 'header_uppercase_nav', 'no' ) === 'yes' ) { $header_classes[] = 'site-header--uppercase-nav'; }
	if ( unysonplus_header_layout_get( 'header_glass', 'no' ) === 'yes' )         { $header_classes[] = 'site-header--glass'; }
	// Appearance — ON SCROLL (applied only while stuck, and only when the master switch is on). The
	// --scroll-change class gates the CSS so the At-top rules stop at .is-stuck and these take over.
	if ( $scroll_change ) {
		$header_classes[] = 'site-header--scroll-change';
		if ( unysonplus_header_layout_get( 'scroll_glass', 'no' ) === 'yes' )  { $header_classes[] = 'site-header--scroll-glass'; }
		if ( unysonplus_header_layout_get( 'scroll_border', 'no' ) === 'yes' ) { $header_classes[] = 'site-header--scroll-border'; }
		if ( unysonplus_header_layout_get( 'scroll_shadow', 'no' ) === 'yes' ) { $header_classes[] = 'site-header--scroll-shadow'; }
		if ( $scroll_shrink )                                                  { $header_classes[] = 'site-header--scroll-shrink'; }
	}
}

// Bootstrap color-mode from the header background luma. bg_color is now a compact
// preset-colour value ({predefined, custom}); resolve it to a real hex/rgba first
// (presets via the palette map), tolerating the legacy plain-string shape.
$header_theme = '';
$bg_color     = isset( $chrome['bg_color'] ) && function_exists( 'unysonplus_preset_color_to_hex' )
	? unysonplus_preset_color_to_hex( $chrome['bg_color'] )
	: ( is_string( $chrome['bg_color'] ?? null ) ? $chrome['bg_color'] : '' );
if ( $bg_color ) {
	$rgb = null;
	if ( preg_match( '/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/', $bg_color, $m ) ) {
		$rgb = array( (int) $m[1], (int) $m[2], (int) $m[3] );
	} elseif ( preg_match( '/^#?([0-9a-f]{6})$/i', trim( $bg_color ), $m ) ) {
		$rgb = array( hexdec( substr( $m[1], 0, 2 ) ), hexdec( substr( $m[1], 2, 2 ) ), hexdec( substr( $m[1], 4, 2 ) ) );
	} elseif ( preg_match( '/^#?([0-9a-f]{3})$/i', trim( $bg_color ), $m ) ) {
		$rgb = array( hexdec( str_repeat( $m[1][0], 2 ) ), hexdec( str_repeat( $m[1][1], 2 ) ), hexdec( str_repeat( $m[1][2], 2 ) ) );
	}
	if ( $rgb ) {
		$luma = ( 0.299 * $rgb[0] + 0.587 * $rgb[1] + 0.114 * $rgb[2] ) / 255;
		if ( $luma < 0.5 ) { $header_theme = 'dark'; }
	}
}

// Layout mode + overlay style → drawer classes + which template runs.
$layout_mode   = function_exists( 'unysonplus_header_layout_get' )
	? unysonplus_header_layout_get( 'header_mode', function_exists( 'unysonplus_layout_get' ) ? unysonplus_layout_get( 'layout_header_mode', 'top' ) : 'top' )
	: 'top';
$overlay_style = function_exists( 'unysonplus_header_layout_get' ) ? unysonplus_header_layout_get( 'overlay_style', 'panel' ) : 'panel';
$is_radial     = ( $layout_mode === 'overlay' && $overlay_style === 'radial' );
$is_concentric = ( $layout_mode === 'overlay' && $overlay_style === 'concentric' );

// Color Mode applies to ALL overlay styles — housed at the overlay reveal level:
// header_layout['header_mode']['overlay']['overlay_color_mode']. Concentric ALSO has
// a Grow-From corner nested under its own picker reveal.
$overlay_corner     = 'tr';
$overlay_color_mode = 'shade';
if ( $layout_mode === 'overlay' && function_exists( 'fw_akg' ) ) {
	$cmode = fw_akg( 'header_mode/overlay/overlay_color_mode', $chrome, 'shade' );
	if ( in_array( $cmode, array( 'shade', 'tint', 'aurora', 'rainbow', 'mono', 'duotone', 'alternating', 'glass' ), true ) ) { $overlay_color_mode = $cmode; }
	if ( $is_concentric ) {
		$corner = fw_akg( 'header_mode/overlay/overlay_style/concentric/overlay_corner', $chrome, 'tr' );
		if ( in_array( $corner, array( 'tr', 'tl', 'br', 'bl' ), true ) ) { $overlay_corner = $corner; }
	}
}

$drawer_classes = array( 'primary-navigation-drawer' );
if ( $layout_mode === 'overlay' ) {
	$drawer_classes[] = 'primary-navigation-drawer--overlay';
	$drawer_classes[] = 'primary-navigation-drawer--cc-' . $overlay_color_mode; // all overlay styles
	if ( $is_radial )     { $drawer_classes[] = 'primary-navigation-drawer--radial'; }
	if ( $is_concentric ) {
		$drawer_classes[] = 'primary-navigation-drawer--concentric';
		$drawer_classes[] = 'primary-navigation-drawer--corner-' . $overlay_corner;
	}
}
// Mobile drawer side (standard side drawer only — overlay styles are fullscreen).
if ( $layout_mode !== 'overlay' && fw_get_db_settings_option( 'mobile_drawer_side', 'right' ) === 'left' ) {
	$drawer_classes[] = 'primary-navigation-drawer--left';
}
// Drawer Item Dividers (Header → Layout → Mobile). Off by default; the class adds a
// hairline rule between drawer menu items (see style.css).
if ( fw_get_db_settings_option( 'drawer_dividers', 'no' ) === 'yes' ) {
	$drawer_classes[] = 'primary-navigation-drawer--dividers';
}
// Drawer submenu behaviour (Header → Mobile & Tablet). Top-level keys; the mode class
// drives the navigation.js branch (accordion / flyout / expand-all) + style.css.
if ( function_exists( 'fw_get_db_settings_option' ) ) {
	$submenu_mode = (string) fw_get_db_settings_option( 'mobile_submenu_mode', 'accordion' );
	if ( $submenu_mode !== '' ) { $drawer_classes[] = 'primary-navigation-drawer--submenu-' . sanitize_html_class( $submenu_mode ); }
	if ( fw_get_db_settings_option( 'mobile_submenu_parent_link', 'yes' ) === 'no' ) { $drawer_classes[] = 'primary-navigation-drawer--parent-toggle'; }
	// Drawer open animation (Phase 2). 'slide' is the default (no class).
	$drawer_anim = (string) fw_get_db_settings_option( 'drawer_animation', 'slide' );
	if ( $drawer_anim !== '' && $drawer_anim !== 'slide' ) { $drawer_classes[] = 'primary-navigation-drawer--anim-' . sanitize_html_class( $drawer_anim ); }
	// Dismissal (Phase 2) — both ON by default; a class opts out (navigation.js checks it).
	if ( fw_get_db_settings_option( 'drawer_close_on_click', 'yes' ) === 'no' ) { $drawer_classes[] = 'primary-navigation-drawer--no-close-on-click'; }
	if ( fw_get_db_settings_option( 'drawer_swipe_close', 'yes' ) === 'no' )    { $drawer_classes[] = 'primary-navigation-drawer--no-swipe'; }
}

// Header Design sub-option CSS vars → inline style on <header>.
$header_style = '';
if ( $header_design && 'classic' !== $header_design && function_exists( 'unysonplus_header_design_css_vars' ) ) {
	foreach ( unysonplus_header_design_css_vars() as $hv_k => $hv_v ) { $header_style .= $hv_k . ':' . $hv_v . ';'; }
}

// Resolve the per-mode template file. Vertical Left/Right are one merged mode
// (the side is a CSS concern via the body class), so both use vertical-default.
if ( function_exists( 'unysonplus_header_is_vertical' ) && unysonplus_header_is_vertical() ) {
	$template = 'vertical-default';
} else {
	switch ( $layout_mode ) {
		case 'off-canvas-only': $template = 'off-canvas-default'; break;
		case 'overlay':
			$template = $is_radial ? 'overlay-radial' : ( $is_concentric ? 'overlay-concentric' : 'overlay-default' );
			break;
		case 'top':
		default:
			switch ( $header_design ) {
				case 'pill':     $template = 'top-floating-pill';  break;
				case 'card':     $template = 'top-elevated-card';  break;
				case 'centered': $template = 'top-centered';       break;
				default:         $template = 'top-default';
			}
	}
}

get_template_part( 'template-parts/header/' . $template, null, array(
	'header_classes'    => implode( ' ', $header_classes ),
	'behavior'          => $behavior,
	'header_theme'      => $header_theme,
	'header_style'      => $header_style,
	'topbar_enabled'    => ! empty( $topbar_left ) || ! empty( $topbar_center ) || ! empty( $topbar_right ),
	'bottombar_enabled' => ! empty( $bottombar_left ) || ! empty( $bottombar_center ) || ! empty( $bottombar_right ),
	'topbar_attr'       => $topbar_attr,
	'main_attr'         => $main_attr,
	'bottombar_attr'    => $bottombar_attr,
	'topbar_left'       => $topbar_left,    'topbar_center'    => $topbar_center,    'topbar_right'    => $topbar_right,
	'main_left'         => $main_left,      'main_center'      => $main_center,      'main_right'      => $main_right,
	'bottombar_left'    => $bottombar_left, 'bottombar_center' => $bottombar_center, 'bottombar_right' => $bottombar_right,
	'drawer_classes'    => implode( ' ', $drawer_classes ),
	'is_radial'         => $is_radial,
) );
