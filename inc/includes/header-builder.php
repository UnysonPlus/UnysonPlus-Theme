<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

if ( ! function_exists( 'unysonplus_element_visibility_classes' ) ) :
/**
 * Map an element's per-device "Hide On" checkboxes to hide-xs/sm/md classes
 * (shared by header + footer element renderers). Handles both Unyson checkbox
 * shapes: associative (key => bool) and a plain list of selected keys.
 */
function unysonplus_element_visibility_classes( $element ) {
	$vis = ! empty( $element['visibility'] ) ? $element['visibility'] : array();
	if ( ! is_array( $vis ) ) { return ''; }
	$out = '';
	foreach ( array( 'hide-xs', 'hide-sm', 'hide-md' ) as $hc ) {
		$on = isset( $vis[ $hc ] ) ? ! empty( $vis[ $hc ] ) : in_array( $hc, $vis, true );
		if ( $on ) { $out .= ' ' . $hc; }
	}
	return $out;
}
endif;

if ( ! function_exists( 'unysonplus_render_primary_toggler' ) ) :
function unysonplus_render_primary_toggler() {
        static $rendered = false;
        if ( $rendered ) return;
        $rendered = true;
        echo '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primary-navigation" aria-controls="primary-navigation" aria-expanded="false" aria-label="' . esc_attr__( 'Toggle navigation', 'unysonplus' ) . '">';
        echo '<span class="navbar-toggler-icon"></span>';
        echo '</button>';
}
endif;


if ( ! function_exists( 'unysonplus_render_drawer_content' ) ) :
/**
 * Contents of the off-canvas / mobile drawer panel.
 *
 * The panel is SHARED: it is the entire menu in Off-Canvas mode and the mobile drawer
 * in every other header mode, so one setting (Theme Settings → Header → Layout →
 * Off-Canvas Content) drives them all. Users compose it from the same element list a
 * header column uses (Menu, Snippet, CTA Button, Social Icons, Custom HTML, …).
 *
 * Back-compat: when nothing is configured we render exactly what THIS mode's drawer
 * always rendered — hence $fallback_location, since the default menu differs per mode
 * (off-canvas → Off-Canvas menu, overlay → Overlay menu, top/vertical → Primary). So
 * existing sites are untouched and there is no migration.
 *
 * @param string $fallback_location Menu location to show when no content is configured.
 */
function unysonplus_render_drawer_content( $fallback_location = 'off-canvas' ) {
	// offcanvas_content consolidated to a top-level key (Header → Mobile & Tablet).
	$oc       = function_exists( 'fw_get_db_settings_option' ) ? fw_get_db_settings_option( 'offcanvas_content', array() ) : array();
	$elements = ( ! empty( $oc ) && is_array( $oc ) ) ? $oc : array();

	// Drawer SEARCH (Phase 2). Optional search form at the top of the drawer.
	if ( function_exists( 'fw_get_db_settings_option' ) && fw_get_db_settings_option( 'drawer_search', 'no' ) === 'yes' ) {
		echo '<div class="drawer-search">';
		get_search_form();
		echo '</div>';
	}

	if ( empty( $elements ) ) {
		// unysonplus_drawer_nav_menu() already falls back to Primary when the location
		// has no menu assigned, so this reproduces each mode's historical default.
		unysonplus_drawer_nav_menu( $fallback_location );
		// The inline header CTA is HIDDEN on mobile (it overflows the bar and pushes the toggle off-screen);
		// re-render it here so the primary action (e.g. "Book Now") MOVES INTO the drawer rather than vanishing.
		if ( function_exists( 'unysonplus_header_cta_elements' ) ) {
			$drawer_ctas = unysonplus_header_cta_elements();
			if ( $drawer_ctas ) {
				echo '<div class="drawer-cta">';
				foreach ( $drawer_ctas as $cta_el ) {
					echo '<div class="header-element header-element--cta_button">';
					unysonplus_render_header_element( $cta_el );
					echo '</div>';
				}
				echo '</div>';
			}
		}
		return;
	}

	echo '<div class="primary-navigation-drawer__content">';
	unysonplus_render_header_column( $elements, 'start' );
	echo '</div>';
}
endif;

if ( ! function_exists( 'unysonplus_header_cta_elements' ) ) :
/**
 * Collect the CTA Button elements placed in the Main Header (any column) — used to re-surface the header
 * CTA inside the mobile drawer, since the inline CTA is hidden on mobile so the hamburger stays reachable.
 *
 * @return array List of cta_button element nodes.
 */
function unysonplus_header_cta_elements() {
	$hm  = function_exists( 'fw_get_db_settings_option' ) ? fw_get_db_settings_option( 'header_main', array() ) : array();
	$out = array();
	if ( ! is_array( $hm ) ) { return $out; }
	foreach ( array( 'main_left', 'main_center', 'main_right' ) as $zone ) {
		if ( empty( $hm[ $zone ] ) || ! is_array( $hm[ $zone ] ) ) { continue; }
		foreach ( $hm[ $zone ] as $el ) {
			if ( isset( $el['element_type']['element'] ) && 'cta_button' === $el['element_type']['element'] ) { $out[] = $el; }
		}
	}
	return $out;
}
endif;

if ( ! function_exists( 'unysonplus_offcanvas_icons' ) ) :
/**
 * Read the off-canvas open/close icons from Theme Settings.
 *
 * Returns [ 'open' => <icon-v2|null>, 'close' => <icon-v2|null> ] from the
 * `offcanvas_trigger_icon` multi-inline. Tolerates the legacy single-icon
 * shape (a scalar icon-v2 value) by treating it as the Open icon, so sites
 * saved before this became multi-inline keep their trigger glyph.
 *
 * @return array{open: mixed, close: mixed}
 */
function unysonplus_offcanvas_icons() {
	// offcanvas_trigger_icon consolidated to a top-level key (Header → Mobile & Tablet).
	$val = function_exists( 'fw_get_db_settings_option' ) ? fw_get_db_settings_option( 'offcanvas_trigger_icon', null ) : null;

	if ( is_array( $val ) && ( array_key_exists( 'open', $val ) || array_key_exists( 'close', $val ) ) ) {
		// Current shape: [ 'open' => …, 'close' => … ].
		return array(
			'open'  => ! empty( $val['open'] ) ? $val['open'] : null,
			'close' => ! empty( $val['close'] ) ? $val['close'] : null,
		);
	}
	// Legacy: a bare icon-v2 value is the Open icon; no Close was configurable then.
	return array( 'open' => ! empty( $val ) ? $val : null, 'close' => null );
}
endif;

if ( ! function_exists( 'unysonplus_hf_toggle_icon_svg' ) ) :
/**
 * Render an icon-v2 value to an SVG string for a drawer toggle/close button.
 *
 * Enqueues the picked icon's pack (non-FA glyphs), mirroring the logo /
 * icon_text elements. Returns '' when no icon is set or the framework is
 * unavailable, so callers can fall back to their default markup.
 *
 * @param mixed  $icon  An icon-v2 value.
 * @param string $class CSS class for the emitted <svg>/<i>.
 * @return string
 */
function unysonplus_hf_toggle_icon_svg( $icon, $class ) {
	if ( empty( $icon ) || ! function_exists( 'sc_icon_render' ) ) {
		return '';
	}
	if ( function_exists( 'fw' ) && isset( fw()->backend ) && method_exists( fw()->backend, 'option_type' )
		&& isset( fw()->backend->option_type( 'icon' )->packs_loader ) ) {
		fw()->backend->option_type( 'icon' )->packs_loader->enqueue_pack_for_icon( $icon );
	}
	$svg = sc_icon_render( $icon, array( 'class' => $class, 'aria_hidden' => true ) );
	return is_string( $svg ) ? $svg : '';
}
endif;

if ( ! function_exists( 'unysonplus_render_mobile_bottom_nav' ) ) :
/**
 * Mobile bottom tab bar (Phase 3, Header → Mobile & Tablet → Bottom Navigation).
 *
 * A fixed bottom bar on phones built from the first N top-level Primary-menu items —
 * an app-like alternative/companion to the drawer. Output at wp_footer; shown/hidden
 * purely in CSS (below the collapse width). No-op unless enabled and a Primary menu
 * with items exists.
 */
function unysonplus_render_mobile_bottom_nav() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return; }
	if ( fw_get_db_settings_option( 'mobile_bottom_nav', 'no' ) !== 'yes' ) { return; }

	$locations = get_nav_menu_locations();
	$menu_id   = isset( $locations['primary'] ) ? (int) $locations['primary'] : 0;
	if ( ! $menu_id ) { return; }
	$items = wp_get_nav_menu_items( $menu_id );
	if ( empty( $items ) ) { return; }

	$top = array_values( array_filter( (array) $items, function ( $it ) {
		return isset( $it->menu_item_parent ) && (int) $it->menu_item_parent === 0;
	} ) );
	if ( empty( $top ) ) { return; }

	$max    = (int) fw_get_db_settings_option( 'mobile_bottom_nav_max', '5' );
	if ( $max < 1 ) { $max = 5; }
	$labels = fw_get_db_settings_option( 'mobile_bottom_nav_labels', 'yes' ) !== 'no';
	$top    = array_slice( $top, 0, $max );

	$cls = 'mobile-bottom-nav' . ( $labels ? '' : ' mobile-bottom-nav--no-labels' );
	echo '<nav class="' . esc_attr( $cls ) . '" aria-label="' . esc_attr__( 'Mobile navigation', 'unysonplus' ) . '"><ul>';
	foreach ( $top as $it ) {
		$current = ( ! empty( $it->classes ) && is_array( $it->classes )
			&& ( in_array( 'current-menu-item', $it->classes, true ) || in_array( 'current_page_item', $it->classes, true ) ) );
		// A menu item can carry an icon via the icon meta the theme's nav walker reads;
		// fall back to a small dot so an icon-only bar still has a target.
		$icon = '';
		if ( function_exists( 'sc_icon_render' ) ) {
			$mi = get_post_meta( $it->ID, '_menu_item_unysonplus_icon', true );
			if ( $mi ) { $icon = sc_icon_render( $mi, array( 'class' => 'mbn-icon', 'aria_hidden' => true ) ); }
		}
		if ( $icon === '' ) { $icon = '<span class="mbn-icon mbn-icon--dot" aria-hidden="true"></span>'; }
		printf(
			'<li class="%s"><a href="%s"%s>%s<span class="mbn-label">%s</span></a></li>',
			$current ? 'is-current' : '',
			esc_url( $it->url ),
			$current ? ' aria-current="page"' : '',
			$icon, // phpcs:ignore — icon markup
			esc_html( $it->title )
		);
	}
	echo '</ul></nav>';
}
add_action( 'wp_footer', 'unysonplus_render_mobile_bottom_nav' );
endif;

if ( ! function_exists( 'unysonplus_mobile_body_classes' ) ) :
/**
 * Body classes for the mobile bottom bar + per-device toggles (Phase 3). Kept in
 * body_class so the page can pad its bottom (behind the fixed bar) and CSS can gate
 * the safe-area / no-sticky treatments.
 */
function unysonplus_mobile_body_classes( $classes ) {
	if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return $classes; }
	if ( fw_get_db_settings_option( 'mobile_bottom_nav', 'no' ) === 'yes' && has_nav_menu( 'primary' ) ) { $classes[] = 'has-bottom-nav'; }
	if ( fw_get_db_settings_option( 'mobile_safe_area', 'no' ) === 'yes' )      { $classes[] = 'mobile-safe-area'; }
	if ( fw_get_db_settings_option( 'mobile_disable_sticky', 'no' ) === 'yes' ) { $classes[] = 'mobile-no-sticky'; }
	return $classes;
}
add_filter( 'body_class', 'unysonplus_mobile_body_classes' );
endif;

if ( ! function_exists( 'unysonplus_custom_breakpoint_css' ) ) :
/**
 * Emit the custom collapse-width CSS (Header → Mobile & Tablet → Custom Collapse Width).
 *
 * When a pixel width is set, the header carries `header-collapse-custom` instead of the
 * md/lg preset class. The static CSS only knows the two presets, so this prints a small
 * per-request <style> reproducing the collapse (hide inline nav / show hamburger), the
 * mobile bar background and the mobile header layouts at that exact width — so every
 * mobile control engages at the same custom breakpoint. No-op unless a width is set.
 */
function unysonplus_custom_breakpoint_css() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return; }
	$bp  = fw_get_db_settings_option( 'mobile_breakpoint_px', array() );
	$val = ( is_array( $bp ) && isset( $bp['value'] ) && is_numeric( $bp['value'] ) ) ? (float) $bp['value'] : 0;
	if ( $val <= 0 ) { return; }

	$below = rtrim( rtrim( number_format( $val - 0.02, 2, '.', '' ), '0' ), '.' );
	$at    = rtrim( rtrim( number_format( $val, 2, '.', '' ), '0' ), '.' );
	$S     = '.site-header.header-collapse-custom';
	$L     = '.header-collapse-custom';

	$css  = '@media (max-width:' . $below . 'px){';
	$css .= $S . ' .header-element--menu,' . $S . ' .header-element--menu_area,' . $S . ' .header-element--cta_button{display:none}';
	$css .= $S . ' .menu-toggle{display:inline-flex}';
	$css .= $S . ' .header-main{background-color:var(--mobile-bar-bg)}';
	// Mobile layouts (mirror of header-footer-builder.css, scoped to the custom class).
	$css .= $L . '.header-mlayout--toggle-left .header-main .header-row{position:relative;justify-content:flex-start}';
	$css .= $L . '.header-mlayout--toggle-left .header-main .header-col--end{order:-1}';
	$css .= $L . '.header-mlayout--toggle-left .header-main .header-col--start{position:absolute;left:50%;transform:translateX(-50%)}';
	$css .= $L . '.header-mlayout--logo-right .header-main .header-row{justify-content:space-between}';
	$css .= $L . '.header-mlayout--logo-right .header-main .header-col--end{order:-1}';
	$css .= $L . '.header-mlayout--logo-right .header-main .header-col--start{order:2}';
	$css .= $L . '.header-mlayout--center-below .header-main .header-row{flex-wrap:wrap;row-gap:.5rem;justify-content:center}';
	$css .= $L . '.header-mlayout--center-below .header-main .header-col--start{flex:0 0 100%;display:flex;justify-content:center;order:1}';
	$css .= $L . '.header-mlayout--center-below .header-main .header-col--center{flex:0 0 100%;display:flex;justify-content:center;order:2}';
	$css .= $L . '.header-mlayout--center-below .header-main .header-col--end{flex:0 0 100%;display:flex;justify-content:center;order:3}';
	$css .= '}';
	$css .= '@media (min-width:' . $at . 'px){' . $S . ' .menu-toggle{display:none}}';

	echo '<style id="unysonplus-header-breakpoint">' . $css . '</style>'; // phpcs:ignore — static CSS, values sanitized numeric
}
add_action( 'wp_head', 'unysonplus_custom_breakpoint_css', 20 );
endif;

if ( ! function_exists( 'unysonplus_render_menu_toggle' ) ) :
function unysonplus_render_menu_toggle( $extra_class = '' ) {
	$icons = unysonplus_offcanvas_icons();
	$get   = 'fw_get_db_settings_option';

	// Hamburger DESIGN (Header → Mobile & Tablet). Only applies to the built-in bars —
	// a custom Open icon set under Header → Layout takes precedence (icon mode).
	$style   = function_exists( $get ) ? (string) $get( 'mobile_toggle_style', 'bars' ) : 'bars';
	$animate = function_exists( $get ) ? ( $get( 'mobile_toggle_animate', 'yes' ) !== 'no' ) : true;
	$label   = function_exists( $get ) ? trim( (string) $get( 'mobile_toggle_label', '' ) ) : '';
	$size    = function_exists( $get ) ? $get( 'mobile_toggle_size', array() ) : array();
	$color   = function_exists( $get ) ? $get( 'mobile_toggle_color', '' ) : '';

	$classes = 'menu-toggle' . ( $extra_class !== '' ? ' ' . $extra_class : '' );

	$inner = unysonplus_hf_toggle_icon_svg( $icons['open'], 'menu-toggle__icon' );
	if ( $inner === '' ) {
		// Built-in bars. A label wraps them in .menu-toggle__bars so the icon+text sit in a row.
		$bars    = '<span class="menu-toggle__bar"></span><span class="menu-toggle__bar"></span><span class="menu-toggle__bar"></span>';
		$inner   = ( $label !== '' ) ? '<span class="menu-toggle__bars">' . $bars . '</span>' : $bars;
		$classes .= ' menu-toggle--bars menu-toggle--style-' . sanitize_html_class( $style !== '' ? $style : 'bars' );
		if ( ! $animate ) { $classes .= ' menu-toggle--no-animate'; }
	} else {
		$classes .= ' menu-toggle--icon';
	}
	if ( $label !== '' ) {
		$inner   .= '<span class="menu-toggle__label">' . esc_html( $label ) . '</span>';
		$classes .= ' menu-toggle--has-label';
	}

	// Per-toggle CSS vars (fresh per request, no dependency on the cached generated CSS).
	$style_attr = '';
	$len        = function_exists( 'unysonplus_css_length' ) ? unysonplus_css_length( $size ) : '';
	if ( $len !== '' ) { $style_attr .= '--toggle-size:' . $len . ';'; }
	$color_css = function_exists( 'unysonplus_preset_color_to_css' ) ? unysonplus_preset_color_to_css( $color ) : ( is_string( $color ) ? $color : '' );
	if ( $color_css !== '' ) { $style_attr .= '--toggle-color:' . $color_css . ';'; }

	printf(
		'<button type="button" class="%s"%s aria-controls="primary-navigation-drawer" aria-expanded="false" aria-label="%s">%s</button>',
		esc_attr( $classes ),
		$style_attr !== '' ? ' style="' . esc_attr( $style_attr ) . '"' : '',
		esc_attr__( 'Toggle navigation', 'unysonplus' ),
		$inner // phpcs:ignore — icon markup / static spans
	);
}
endif;

if ( ! function_exists( 'unysonplus_render_drawer_close' ) ) :
/**
 * Render the drawer's Close button (shared across all header templates).
 *
 * Uses the Close icon from Theme Settings when set, otherwise the classic
 * &times;. Extracted from ten identical hardcoded buttons so the Close icon
 * option lands everywhere at once.
 */
function unysonplus_render_drawer_close() {
	$icons = unysonplus_offcanvas_icons();
	$get   = 'fw_get_db_settings_option';

	// Close-button DESIGN (Phase 2, Header → Mobile & Tablet). A custom Close icon
	// (Trigger & Close Icons) still wins; otherwise pick a built-in glyph/style.
	$style = function_exists( $get ) ? (string) $get( 'drawer_close_style', 'default' ) : 'default';
	$pos   = function_exists( $get ) ? (string) $get( 'drawer_close_position', 'in-panel' ) : 'in-panel';
	$csize = function_exists( $get ) ? $get( 'drawer_close_size', array() ) : array();
	$ccol  = function_exists( $get ) ? $get( 'drawer_close_color', '' ) : '';

	$svg     = unysonplus_hf_toggle_icon_svg( $icons['close'], 'primary-navigation-drawer__close-icon' );
	$classes = 'primary-navigation-drawer__close';
	if ( $svg !== '' ) {
		$classes .= ' primary-navigation-drawer__close--icon';
		$inner    = $svg;
	} elseif ( 'arrow' === $style ) {
		$inner = '&lsaquo;';
	} elseif ( 'text' === $style ) {
		$classes .= ' primary-navigation-drawer__close--text';
		$inner    = '<span class="primary-navigation-drawer__close-text">' . esc_html__( 'Close', 'unysonplus' ) . '</span>';
	} else {
		$inner = '&times;';
	}
	if ( 'floating' === $pos ) { $classes .= ' primary-navigation-drawer__close--floating'; }

	$style_attr = '';
	$len = function_exists( 'unysonplus_css_length' ) ? unysonplus_css_length( $csize ) : '';
	if ( $len !== '' ) { $style_attr .= '--close-size:' . $len . ';'; }
	$col = function_exists( 'unysonplus_preset_color_to_css' ) ? unysonplus_preset_color_to_css( $ccol ) : ( is_string( $ccol ) ? $ccol : '' );
	if ( $col !== '' ) { $style_attr .= '--close-color:' . $col . ';'; }

	printf(
		'<button type="button" class="%s"%s data-drawer-close aria-label="%s">%s</button>',
		esc_attr( $classes ),
		$style_attr !== '' ? ' style="' . esc_attr( $style_attr ) . '"' : '',
		esc_attr__( 'Close menu', 'unysonplus' ),
		$inner // phpcs:ignore — icon markup / static entity
	);
}
endif;

if ( ! function_exists( 'unysonplus_render_hf_snippet' ) ) :
/**
 * Render the Snippet element (shared by the header, footer and off-canvas drawer).
 *
 * A Snippet is page-builder content (post type `snippet`), so this is how arbitrary
 * shortcodes / bespoke markup get into the chrome without a new element type for each.
 * fw_ext_snippets_render() returns fully shortcode-processed HTML and guards against
 * recursion + unpublished posts, so we only need the availability check.
 *
 * @param array $settings Element settings ( snippet_id ).
 */
function unysonplus_render_hf_snippet( $settings ) {
	$id = ! empty( $settings['snippet_id'] ) ? (int) $settings['snippet_id'] : 0;
	if ( ! $id || ! function_exists( 'fw_ext_snippets_render' ) ) {
		return;
	}
	$html = fw_ext_snippets_render( $id );
	if ( $html === '' ) {
		return;
	}
	echo '<div class="up-hf-snippet">' . $html . '</div>'; // phpcs:ignore — builder output, already processed
}
endif;

if ( ! function_exists( 'unysonplus_render_header_element' ) ) :
function unysonplus_render_header_element( $element ) {
        if ( empty( $element['element_type']['element'] ) ) return;

        $type     = $element['element_type']['element'];
        $settings = ! empty( $element['element_type'][ $type ] ) ? $element['element_type'][ $type ] : array();

        switch ( $type ) {
                case 'logo':
                        unysonplus_logo();
                        break;

                case 'primary_menu':
                        unysonplus_render_primary_menu_inline();
                        break;

                case 'secondary_menu':
                        unysonplus_nav_menu( 'secondary' );
                        break;

                case 'cta_button':
                        unysonplus_render_cta_button( $settings );
                        break;

                case 'phone':
                        unysonplus_render_phone( $settings );
                        break;

                case 'icon_text':
                        unysonplus_render_icon_text( $settings );
                        break;

                case 'search':
                        unysonplus_render_search();
                        break;

                case 'social_icons':
                        unysonplus_render_social_icons();
                        break;

                case 'heading':
                        unysonplus_render_heading_element( $settings );
                        break;

                case 'list_item':
                        unysonplus_render_list_item_element( $settings );
                        break;

                case 'link': // legacy — superseded by list_item; still rendered for older saved headers.
                        unysonplus_render_link_element( $settings );
                        break;

                case 'menu':
                        unysonplus_render_menu( $settings );
                        break;

                case 'menu_area':
			$upw_menu_loc = ! empty( $settings['menu_location'] ) ? $settings['menu_location'] : 'primary';
			// Primary AND secondary (the split-nav left/right clusters) render inline via the same treatment;
			// any other location falls back to the generic menu-area render.
			if ( 'primary' === $upw_menu_loc || 'secondary' === $upw_menu_loc ) {
				unysonplus_render_primary_menu_inline( $upw_menu_loc );
			} else {
				unysonplus_render_menu_area( $settings );
			}
			break;

                case 'text':
                        unysonplus_render_text_element( $settings );
                        break;

                case 'custom_html':
                        unysonplus_render_custom_html( $settings );
                        break;

                case 'widget_area':
                        if ( function_exists( 'unysonplus_render_widget_area' ) ) {
                                unysonplus_render_widget_area( $settings );
                        }
                        break;

                case 'builder_section':
                        if ( function_exists( 'unysonplus_render_builder_section' ) ) {
                                unysonplus_render_builder_section( $settings );
                        }
                        break;

                case 'snippet':
                        unysonplus_render_hf_snippet( $settings );
                        break;

                case 'spacer':
                        echo '<span class="header-spacer" aria-hidden="true"></span>';
                        break;

                case 'divider':
                        echo '<span class="header-divider" role="separator" aria-orientation="vertical"></span>';
                        break;

                default:
                        // Addon-registered element types (see the unysonplus_hf_elements
                        // filter). Render via a per-type action, plus a generic catch-all.
                        do_action( 'unysonplus_render_hf_element_' . $type, $settings, $element, 'header' );
                        do_action( 'unysonplus_render_hf_element', $type, $settings, $element, 'header' );
                        break;
        }
}
endif;


if ( ! function_exists( 'unysonplus_render_header_column' ) ) :
/**
 * Render the inner elements for a header slot. The caller is responsible
 * for wrapping with the outer .header-col container (see
 * template-parts/header-builder.php), so this helper only emits the
 * per-element .header-element wrappers.
 *
 * @param array  $elements Element configs from header_layout.
 * @param string $align    Retained for backward compatibility; unused now
 *                         that alignment lives on the outer wrapper.
 */
function unysonplus_render_header_column( $elements, $align = 'start' ) {
        // Let addons inject / reorder elements at render time (optional). $align is
        // the zone: 'start' | 'center' | 'end' (= left / center / right).
        $elements = apply_filters( 'unysonplus_hf_column_elements', $elements, 'header', $align );

        if ( empty( $elements ) || ! is_array( $elements ) ) return;

        foreach ( $elements as $element ) {
                if ( empty( $element['element_type']['element'] ) ) continue;
                $type = $element['element_type']['element'];

                // Per-element CSS Class (addable-popup field): sanitize each token so a
                // user can safely target this element wrapper with custom CSS.
                $extra_class = '';
                if ( ! empty( $element['element_css_class'] ) ) {
                        $safe = array();
                        foreach ( preg_split( '/\s+/', trim( (string) $element['element_css_class'] ) ) as $cls ) {
                                $cls = sanitize_html_class( $cls );
                                if ( $cls !== '' ) { $safe[] = $cls; }
                        }
                        if ( $safe ) { $extra_class = ' ' . implode( ' ', $safe ); }
                }

                echo '<div class="header-element header-element--' . esc_attr( $type ) . esc_attr( unysonplus_element_visibility_classes( $element ) ) . esc_attr( $extra_class ) . '">';
                unysonplus_render_header_element( $element );
                echo '</div>';
        }
}
endif;


if ( ! function_exists( 'unysonplus_render_primary_menu_inline' ) ) :
/**
 * Render the primary navigation inline inside a header slot. Shows the
 * registered primary menu when assigned; shows an admin-only setup
 * notice otherwise so site editors know to assign one.
 */
function unysonplus_render_primary_menu_inline( $location = 'primary' ) {
        if ( has_nav_menu( $location ) ) {
                if ( 'primary' === $location && function_exists( 'unysonplus_nav_menu' ) ) {
                        unysonplus_nav_menu( 'primary' );
                } else {
                        // A non-primary header nav zone (e.g. the split-nav SECONDARY / right cluster): render it
                        // inline with the SAME `.primary-menu` treatment as the primary, so both zones read as one
                        // horizontal nav — instead of the global Secondary config's Bootstrap navbar (bulleted block).
                        wp_nav_menu( array(
                                'theme_location'  => $location,
                                'depth'           => 4,
                                'container'       => 'nav',
                                'container_class' => sanitize_html_class( $location ) . '-navigation header-inline-menu',
                                'menu_class'      => 'primary-menu',
                                'item_spacing'    => 'discard',
                                'fallback_cb'     => false,
                        ) );
                }
                return;
        }

        // The "no menu assigned" admin notice only makes sense for the Primary location.
        if ( 'primary' !== $location || ! current_user_can( 'edit_theme_options' ) ) {
                return;
        }

        echo '<div class="primary-menu-notice" role="note">';
        printf(
                '<p>%s</p>',
                sprintf(
                        wp_kses(
                                /* translators: %s: URL to the WordPress nav-menus screen */
                                __( 'No menu assigned to the <strong>Primary</strong> location. <a href="%s">Set one up &rarr;</a> <span class="primary-menu-notice__hint">(admins only)</span>', 'unysonplus' ),
                                array(
                                        'a'      => array( 'href' => array() ),
                                        'strong' => array(),
                                        'span'   => array( 'class' => array() ),
                                )
                        ),
                        esc_url( admin_url( 'nav-menus.php?action=locations' ) )
                )
        );
        echo '</div>';
}
endif;


if ( ! function_exists( 'unysonplus_header_has_primary_nav' ) ) :
function unysonplus_header_has_primary_nav( $columns ) {
        foreach ( $columns as $col ) {
                if ( ! is_array( $col ) ) continue;
                foreach ( $col as $element ) {
                        if ( empty( $element['element_type']['element'] ) ) continue;
                        $type = $element['element_type']['element'];
                        if ( $type === 'primary_menu' ) return true;
                        if ( $type === 'menu_area' && ! empty( $element['element_type']['menu_area']['menu_location'] )
                             && $element['element_type']['menu_area']['menu_location'] === 'primary' ) {
                                return true;
                        }
                }
        }
        return false;
}
endif;


if ( ! function_exists( 'unysonplus_render_cta_button' ) ) :
function unysonplus_render_cta_button( $settings ) {
        $text = ! empty( $settings['cta_text'] ) ? $settings['cta_text'] : 'Get Started';
        $link = ! empty( $settings['cta_link'] ) ? $settings['cta_link'] : '#';

        // Style + size ride the theme's button preset classes (btn btn-{preset}
        // btn-{size}) from Theme Settings > General > Buttons — no inline styles here.
        $classes = function_exists( 'unysonplus_cta_button_classes' )
                ? unysonplus_cta_button_classes( $settings )
                : 'header-cta-btn';

        echo '<a href="' . esc_url( $link ) . '" class="' . esc_attr( $classes ) . '">' . esc_html( $text ) . '</a>';
}
endif;


if ( ! function_exists( 'unysonplus_render_phone' ) ) :
function unysonplus_render_phone( $settings ) {
        $phone = ! empty( $settings['phone_number'] ) ? $settings['phone_number'] : '';
        if ( empty( $phone ) ) return;

        $phone_link = preg_replace( '/[^0-9+]/', '', $phone );
        echo '<a href="tel:' . esc_attr( $phone_link ) . '" class="header-phone">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/></svg>';
        echo ' <span>' . esc_html( $phone ) . '</span>';
        echo '</a>';
}
endif;


if ( ! function_exists( 'unysonplus_render_icon_text' ) ) :
/**
 * Icon Text element — an icon + a line of text, optionally a smart link
 * (Website / Email → mailto: / Phone → tel:). Generalises the old Phone element,
 * so it also covers email, address, hours, website, etc.
 *
 * @param array $settings icon_text element settings.
 */
function unysonplus_render_icon_text( $settings ) {
        // Render the icon from the FULL icon-v2 value (inline SVG / library glyph / font icon) via
        // sc_icon_render — not just a legacy `icon-class` string. The old code only handled font classes,
        // so an SVG icon (e.g. a converted contact row's inline map-pin/phone/mail) rendered nothing.
        $icon_val = isset( $settings['icontext_icon'] ) ? $settings['icontext_icon'] : '';
        $icon = function_exists( 'unysonplus_hf_toggle_icon_svg' ) ? unysonplus_hf_toggle_icon_svg( $icon_val, 'header-icon-text__icon' ) : '';
        // Back-compat: a bare legacy icon-class string still renders as a Font Awesome <i>.
        if ( $icon === '' && ! empty( $settings['icontext_icon']['icon-class'] ) ) {
                $icon = '<i class="' . esc_attr( $settings['icontext_icon']['icon-class'] ) . '" aria-hidden="true"></i>';
        }
        $text = isset( $settings['icontext_text'] ) ? trim( (string) $settings['icontext_text'] ) : '';
        if ( $text === '' && $icon === '' ) { return; }

        $type = ! empty( $settings['icontext_link_type'] ) ? $settings['icontext_link_type'] : 'none';
        $val  = isset( $settings['icontext_link'] ) ? trim( (string) $settings['icontext_link'] ) : '';
        // Email / Phone fall back to the visible text when no explicit target is set.
        if ( $val === '' && ( $type === 'email' || $type === 'phone' ) ) { $val = $text; }

        $href = ''; $rel = '';
        switch ( $type ) {
                case 'url':
                        $href = esc_url( $val );
                        // External URL → new tab (mirrors the tag_list convention).
                        $host = wp_parse_url( $val, PHP_URL_HOST );
                        if ( $host && $host !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
                                $rel = ' target="_blank" rel="noopener noreferrer"';
                        }
                        break;
                case 'email':
                        $href = 'mailto:' . antispambot( $val );
                        break;
                case 'phone':
                        $href = 'tel:' . preg_replace( '/[^0-9+]/', '', $val );
                        break;
        }

        $inner  = $icon !== '' ? $icon . ' ' : ''; // $icon is already rendered markup (SVG or <i>)
        $inner .= '<span>' . esc_html( $text ) . '</span>';

        if ( $href !== '' ) {
                echo '<a class="header-icon-text" href="' . $href . '"' . $rel . '>' . $inner . '</a>'; // phpcs:ignore -- href pre-escaped per type
        } else {
                echo '<span class="header-icon-text">' . $inner . '</span>';
        }
}
endif;

if ( ! function_exists( 'unysonplus_render_list_item_element' ) ) :
/**
 * List Item element — the UNIFIED header/footer row that supersedes the old `link` + `icon_text` elements:
 * a line of TEXT, an optional ICON, and an optional smart LINK (Website / Email → mailto: / Phone → tel:).
 * A run of 2+ consecutive List Item elements auto-groups into a semantic <ul><li> list at render (see the
 * footer column renderer); a lone one stays a standalone link / span. One element expresses a menu link, a
 * plain text line, an icon+text contact row, and a clickable email/phone.
 *
 * @param array $settings list_item element settings (li_text, li_icon, li_link_type, li_link, li_target).
 */
function unysonplus_render_list_item_element( $settings ) {
        $icon_val = isset( $settings['li_icon'] ) ? $settings['li_icon'] : '';
        $icon = function_exists( 'unysonplus_hf_toggle_icon_svg' ) ? unysonplus_hf_toggle_icon_svg( $icon_val, 'list-item__icon' ) : '';
        if ( $icon === '' && ! empty( $settings['li_icon']['icon-class'] ) ) {
                $icon = '<i class="' . esc_attr( $settings['li_icon']['icon-class'] ) . '" aria-hidden="true"></i>';
        }
        $text = isset( $settings['li_text'] ) ? trim( (string) $settings['li_text'] ) : '';
        if ( $text === '' && $icon === '' ) { return; }

        $type = ! empty( $settings['li_link_type'] ) ? $settings['li_link_type'] : 'none';
        $val  = isset( $settings['li_link'] ) ? trim( (string) $settings['li_link'] ) : '';
        // Email / Phone fall back to the visible text when no explicit target is set.
        if ( $val === '' && ( $type === 'email' || $type === 'phone' ) ) { $val = $text; }

        $href = ''; $rel = '';
        switch ( $type ) {
                case 'url':
                        $href = esc_url( $val );
                        if ( ! empty( $settings['li_target'] ) && '_blank' === $settings['li_target'] ) {
                                $rel = ' target="_blank" rel="noopener noreferrer"';
                        } else {
                                // External URL → new tab (mirrors the icon_text / tag_list convention).
                                $host = wp_parse_url( $val, PHP_URL_HOST );
                                if ( $host && $host !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
                                        $rel = ' target="_blank" rel="noopener noreferrer"';
                                }
                        }
                        break;
                case 'email':
                        $href = 'mailto:' . antispambot( $val );
                        break;
                case 'phone':
                        $href = 'tel:' . preg_replace( '/[^0-9+]/', '', $val );
                        break;
        }

        $inner  = $icon !== '' ? $icon . ' ' : ''; // $icon is already rendered markup (SVG or <i>)
        $inner .= '<span class="list-item__text">' . esc_html( $text ) . '</span>';

        if ( $href !== '' ) {
                echo '<a class="footer-link hf-link list-item" href="' . $href . '"' . $rel . '>' . $inner . '</a>'; // phpcs:ignore -- href pre-escaped per type
        } else {
                echo '<span class="list-item">' . $inner . '</span>';
        }
}
endif;


if ( ! function_exists( 'unysonplus_render_search' ) ) :
function unysonplus_render_search() {
        // A compact SEARCH ICON that reveals the field on click (a popover), instead of an always-expanded
        // box in the bar — the standard, less-heavy header pattern (and what most sources use). navigation.js
        // toggles `.is-open` on the wrapper; CSS reveals the form. Without JS the <details>-free fallback still
        // shows the field on focus-within, so search stays usable.
        $lbl  = esc_attr__( 'Search', 'unysonplus' );
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>';
        echo '<div class="header-search" data-header-search>';
        echo '<button type="button" class="header-search-toggle" aria-label="' . $lbl . '" aria-expanded="false">' . $icon . '</button>';
        echo '<form role="search" method="get" class="header-search-form" action="' . esc_url( home_url( '/' ) ) . '">';
        echo '<input type="search" class="header-search-input" placeholder="' . esc_attr__( 'Search…', 'unysonplus' ) . '" value="' . get_search_query() . '" name="s" />';
        echo '<button type="submit" class="header-search-btn" aria-label="' . $lbl . '">' . $icon . '</button>';
        echo '</form>';
        echo '</div>';
}
endif;


if ( ! function_exists( 'unysonplus_social_brand_color' ) ) :
/**
 * Brand hex for a social network, matched by the profile's name (lowercased). Used
 * by the "Use Brand Colors" style option. Unknown networks return '' (fall back to
 * the configured Icon/Background colors).
 *
 * @param string $name
 * @return string hex or ''
 */
function unysonplus_social_brand_color( $name ) {
        $key = preg_replace( '/[^a-z0-9]/', '', strtolower( (string) $name ) );
        $map = array(
                'facebook' => '#1877f2', 'fb' => '#1877f2',
                'x' => '#000000', 'twitter' => '#1da1f2',
                'instagram' => '#e4405f', 'ig' => '#e4405f',
                'youtube' => '#ff0000', 'linkedin' => '#0a66c2',
                'tiktok' => '#000000', 'pinterest' => '#bd081c',
                'github' => '#181717', 'whatsapp' => '#25d366',
                'telegram' => '#0088cc', 'dribbble' => '#ea4c89',
                'behance' => '#1769ff', 'discord' => '#5865f2',
                'reddit' => '#ff4500', 'vimeo' => '#1ab7ea',
                'snapchat' => '#fffc00', 'twitch' => '#9146ff',
                'spotify' => '#1db954', 'medium' => '#000000',
                'threads' => '#000000', 'mastodon' => '#6364ff',
                'tumblr' => '#36465d', 'soundcloud' => '#ff5500',
                'email' => '#0d6efd', 'mail' => '#0d6efd',
        );
        return isset( $map[ $key ] ) ? $map[ $key ] : '';
}
endif;

if ( ! function_exists( 'unysonplus_social_fa_class' ) ) :
/**
 * Fallback Font Awesome (v6 brands) class for a social network, matched by the
 * profile name — used when a profile has no explicit icon set, so a profile named
 * "Facebook" / "X" / "Instagram" shows the right glyph out of the box (the theme
 * loads a FA6 kit on the front end). Unknown networks return ''.
 *
 * @param string $name
 * @return string e.g. 'fab fa-facebook-f', or ''
 */
function unysonplus_social_fa_class( $name ) {
        $key = preg_replace( '/[^a-z0-9]/', '', strtolower( (string) $name ) );
        $map = array(
                'facebook' => 'fab fa-facebook-f', 'fb' => 'fab fa-facebook-f',
                'x' => 'fab fa-x-twitter', 'twitter' => 'fab fa-twitter',
                'instagram' => 'fab fa-instagram', 'ig' => 'fab fa-instagram',
                'youtube' => 'fab fa-youtube', 'linkedin' => 'fab fa-linkedin-in',
                'tiktok' => 'fab fa-tiktok', 'pinterest' => 'fab fa-pinterest-p',
                'github' => 'fab fa-github', 'whatsapp' => 'fab fa-whatsapp',
                'telegram' => 'fab fa-telegram', 'dribbble' => 'fab fa-dribbble',
                'behance' => 'fab fa-behance', 'discord' => 'fab fa-discord',
                'reddit' => 'fab fa-reddit-alien', 'vimeo' => 'fab fa-vimeo-v',
                'snapchat' => 'fab fa-snapchat', 'twitch' => 'fab fa-twitch',
                'spotify' => 'fab fa-spotify', 'medium' => 'fab fa-medium',
                'threads' => 'fab fa-threads', 'mastodon' => 'fab fa-mastodon',
                'tumblr' => 'fab fa-tumblr', 'soundcloud' => 'fab fa-soundcloud',
                'email' => 'fas fa-envelope', 'mail' => 'fas fa-envelope',
                // Generic (non-brand) links commonly used in footers — a website/globe, a
                // plain link, or an RSS feed. Solid icons (fas), so no brand dependency.
                'globe' => 'fas fa-globe', 'website' => 'fas fa-globe', 'web' => 'fas fa-globe',
                'link' => 'fas fa-link', 'rss' => 'fas fa-rss',
        );
        return isset( $map[ $key ] ) ? $map[ $key ] : '';
}
endif;

if ( ! function_exists( 'unysonplus_render_social_icons' ) ) :
function unysonplus_render_social_icons() {
        if ( ! function_exists( 'fw_get_db_settings_option' ) ) return;
        $social_profiles = fw_get_db_settings_option( 'social_profiles' );
        if ( empty( $social_profiles ) || ! is_array( $social_profiles ) ) return;

        // Global icon style (Social tab → social_style). Stored flat (groups aren't stored).
        $style = fw_get_db_settings_option( 'social_style', array() );
        $style = is_array( $style ) ? $style : array();
        $shape = ! empty( $style['social_icon_style'] ) ? preg_replace( '/[^a-z-]/', '', $style['social_icon_style'] ) : 'bare';
        $fx    = ! empty( $style['social_icon_hover_fx'] ) ? preg_replace( '/[^a-z-]/', '', $style['social_icon_hover_fx'] ) : 'none';
        $brand = ! empty( $style['social_icon_brand'] ) && $style['social_icon_brand'] === 'yes';

        $wrap = array( 'header-social-icons', 'social-icons', 'social-style-' . $shape );
        if ( $fx !== 'none' ) { $wrap[] = 'social-fx-' . $fx; }
        if ( $brand )         { $wrap[] = 'social-brand'; }

        // icon-v2 packs loader (enqueues the pack CSS for a picked icon so it renders).
        $packs = ( function_exists( 'fw' ) && isset( fw()->backend ) && method_exists( fw()->backend, 'option_type' ) )
                ? ( isset( fw()->backend->option_type( 'icon' )->packs_loader ) ? fw()->backend->option_type( 'icon' )->packs_loader : null )
                : null;

        echo '<div class="' . esc_attr( implode( ' ', $wrap ) ) . '">';
        foreach ( $social_profiles as $profile ) {
                if ( empty( $profile['link'] ) ) continue;
                $name    = ! empty( $profile['name'] ) ? $profile['name'] : '';
                $new_tab = ! isset( $profile['new_tab'] ) || $profile['new_tab'] !== 'no';
                $target  = $new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';

                if ( $packs && ! empty( $profile['icon'] ) ) {
                        $packs->enqueue_pack_for_icon( $profile['icon'] );
                }

                $brand_attr = '';
                if ( $brand ) {
                        $hex = unysonplus_social_brand_color( $name );
                        if ( $hex !== '' ) { $brand_attr = ' style="--social-brand:' . esc_attr( $hex ) . '"'; }
                }

                /**
                 * Icon: render the PICKED value through the central renderer.
                 *
                 * This used to read only $profile['icon']['icon-class'], which
                 * exists solely for icon-font picks — so an SVG / emoji /
                 * uploaded icon was silently discarded and replaced by a
                 * name-matched Font Awesome class whose CSS often isn't on the
                 * page, leaving the front end with no visible icon at all.
                 * sc_icon_render() handles every icon type and enqueues the
                 * pack it needs; the FA guess is now only a last resort for
                 * profiles that have no icon picked.
                 */
                $icon_html = '';

                if ( ! empty( $profile['icon'] ) && function_exists( 'sc_icon_render' ) ) {
                        $icon_html = sc_icon_render( $profile['icon'] );
                }

                if ( $icon_html === '' ) {
                        $icon_class = ! empty( $profile['icon']['icon-class'] )
                                ? $profile['icon']['icon-class']
                                : unysonplus_social_fa_class( $name );

                        if ( $icon_class !== '' ) {
                                $icon_html = '<i class="' . esc_attr( $icon_class ) . '" aria-hidden="true"></i>';
                        }
                }

                echo '<a href="' . esc_url( $profile['link'] ) . '" class="header-social-icon"' . $target . $brand_attr . ' aria-label="' . esc_attr( $name ) . '" title="' . esc_attr( $name ) . '">';
                if ( $icon_html !== '' ) {
                        echo $icon_html; // phpcs:ignore WordPress.Security.EscapingOutput -- sc_icon_render() sanitises SVG; the <i> branch escapes its class.
                } else {
                        echo '<span class="header-social-icon__label">' . esc_html( $name ) . '</span>';
                }
                echo '</a>';
        }
        echo '</div>';
}
endif;


if ( ! function_exists( 'unysonplus_render_custom_html' ) ) :
function unysonplus_render_custom_html( $settings ) {
        if ( ! empty( $settings['custom_html_content'] ) ) {
                echo '<div class="header-custom-html">' . unysonplus_img_add_dimensions( do_shortcode( $settings['custom_html_content'] ) ) . '</div>';
        }
}
endif;

if ( ! function_exists( 'unysonplus_url_to_path' ) ) :
/**
 * Map a same-site media URL to its filesystem path (uploads → content → site root),
 * or '' if it isn't a local file we can resolve. Query/hash stripped.
 *
 * @param string $url
 * @return string
 */
function unysonplus_url_to_path( $url ) {
        $url = preg_replace( '/[?#].*$/', '', (string) $url );
        if ( $url === '' ) { return ''; }
        $up = wp_get_upload_dir();
        if ( ! empty( $up['baseurl'] ) && strpos( $url, $up['baseurl'] ) === 0 ) {
                return $up['basedir'] . substr( $url, strlen( $up['baseurl'] ) );
        }
        $cu = content_url();
        if ( strpos( $url, $cu ) === 0 ) { return WP_CONTENT_DIR . substr( $url, strlen( $cu ) ); }
        $su = site_url();
        if ( strpos( $url, $su ) === 0 ) { return ABSPATH . ltrim( substr( $url, strlen( $su ) ), '/' ); }
        if ( isset( $url[0] ) && $url[0] === '/' ) { return ABSPATH . ltrim( $url, '/' ); }
        return '';
}
endif;

if ( ! function_exists( 'unysonplus_local_img_dimensions' ) ) :
/**
 * Intrinsic [width, height] for a local image URL, or false. SVGs read the root
 * width/height (else the viewBox); rasters use getimagesize(). Cached per request.
 *
 * @param string $url
 * @return array|false
 */
function unysonplus_local_img_dimensions( $url ) {
        static $cache = array();
        if ( array_key_exists( $url, $cache ) ) { return $cache[ $url ]; }
        $dim  = false;
        $path = unysonplus_url_to_path( $url );
        if ( $path !== '' && @is_file( $path ) ) {
                if ( preg_match( '/\.svg$/i', $path ) ) {
                        $svg = (string) @file_get_contents( $path );
                        if ( preg_match( '/<svg\b[^>]*\bwidth\s*=\s*["\']?([\d.]+)/i', $svg, $w )
                                && preg_match( '/<svg\b[^>]*\bheight\s*=\s*["\']?([\d.]+)/i', $svg, $h ) ) {
                                $dim = array( (int) round( (float) $w[1] ), (int) round( (float) $h[1] ) );
                        } elseif ( preg_match( '/viewBox\s*=\s*["\']([-\d.\s,]+)["\']/i', $svg, $vb ) ) {
                                $p = preg_split( '/[\s,]+/', trim( $vb[1] ) );
                                if ( is_array( $p ) && count( $p ) === 4 ) {
                                        $dim = array( (int) round( (float) $p[2] ), (int) round( (float) $p[3] ) );
                                }
                        }
                } else {
                        $sz = @getimagesize( $path );
                        if ( $sz && ! empty( $sz[0] ) && ! empty( $sz[1] ) ) { $dim = array( (int) $sz[0], (int) $sz[1] ); }
                }
        }
        if ( is_array( $dim ) && ( $dim[0] < 1 || $dim[1] < 1 ) ) { $dim = false; }
        $cache[ $url ] = $dim;
        return $dim;
}
endif;

if ( ! function_exists( 'unysonplus_img_add_dimensions' ) ) :
/**
 * Add explicit width/height to any <img> in a hand-authored HTML blob that lacks them
 * (custom-HTML header/footer elements, badge strips) — resolving the intrinsic size from
 * the local file — so they don't cause CLS. Images that already declare BOTH width and
 * height, or whose file can't be resolved, are left untouched. CSS still controls the
 * display size; these attributes only let the browser reserve the box.
 *
 * @param string $html
 * @return string
 */
function unysonplus_img_add_dimensions( $html ) {
        if ( strpos( $html, '<img' ) === false ) { return $html; }
        return preg_replace_callback(
                '#<img\b([^>]*)>#i',
                function ( $m ) {
                        $attrs = $m[1];
                        if ( preg_match( '/\bwidth\s*=/i', $attrs ) && preg_match( '/\bheight\s*=/i', $attrs ) ) { return $m[0]; }
                        if ( ! preg_match( '/\bsrc\s*=\s*["\']([^"\']+)["\']/i', $attrs, $sm ) ) { return $m[0]; }
                        $dim = unysonplus_local_img_dimensions( $sm[1] );
                        if ( ! $dim ) { return $m[0]; }
                        return '<img' . rtrim( $attrs ) . ' width="' . $dim[0] . '" height="' . $dim[1] . '">';
                },
                $html
        );
}
endif;
