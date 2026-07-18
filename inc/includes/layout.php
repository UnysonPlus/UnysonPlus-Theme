<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }

/**
 * General → Layout / Sidebar / Preloader / Scrolling sub-tab runtime.
 *
 * Reads options stored under the `general_layout`, `general_sidebar`,
 * `general_preloader` and `general_scroll` multi keys (defined in
 * framework-customizations/theme/options/general-{layout,sidebar,preloader,scroll}.php)
 * and wires them into WordPress via body classes, theme-var emissions, and
 * preloader / scroll-progress hooks.
 *
 * The General → Layout tab was split into separate sub-tabs (Layout / Sidebar /
 * Preloader / Scrolling), each with its own storage key, and the header
 * layout-mode / vertical-rail-width controls moved to Header → Layout
 * (`header_layout`). `unysonplus_layout_get()` merges the general_* keys so every
 * read site keeps using the same key names;
 * `unysonplus_migrate_layout_settings()` moves existing saved values into their
 * new homes.
 */

/* ============================================================
 * Helper
 * ============================================================ */

if ( ! function_exists( 'unysonplus_layout_get' ) ) :
/**
 * Read one key from the merged general layout option groups.
 *
 * Merges `general_layout`, `general_sidebar`, `general_preloader` and
 * `general_scroll` (later keys win) so callers can keep reading
 * `layout_sidebar_*` / `layout_preloader_*` / `layout_*scroll*` regardless of
 * which sub-tab now owns them.
 *
 * @param string $key     Inner-option key (e.g. 'site_width_mode').
 * @param mixed  $default Returned when Unyson is inactive or the key is empty.
 * @return mixed
 */
function unysonplus_layout_get( $key, $default = '' ) {
	static $cache = null;

	if ( $cache === null ) {
		$cache = array();
		if ( function_exists( 'fw_get_db_settings_option' ) ) {
			foreach ( array( 'general_layout', 'general_sidebar', 'general_preloader', 'general_scroll' ) as $opt ) {
				$raw = fw_get_db_settings_option( $opt, array() );
				if ( is_array( $raw ) ) { $cache = array_merge( $cache, $raw ); }
			}
		}
	}

	if ( ! array_key_exists( $key, $cache ) ) { return $default; }
	$val = $cache[ $key ];
	if ( $val === '' || $val === null ) { return $default; }
	return $val;
}
endif;

if ( ! function_exists( 'unysonplus_header_layout_get' ) ) :
/**
 * Read one key from the `header_layout` option group (Header → Layout).
 *
 * Used for the relocated `header_mode` / `vertical_width` controls. Falls back to
 * the caller-supplied default, which the call sites set to the legacy
 * general_layout value so pre-migration installs still render correctly.
 *
 * @param string $key
 * @param mixed  $default
 * @return mixed
 */
function unysonplus_header_layout_get( $key, $default = '' ) {
	static $cache = null;

	if ( $cache === null ) {
		$raw   = function_exists( 'fw_get_db_settings_option' ) ? fw_get_db_settings_option( 'header_layout', array() ) : array();
		$cache = is_array( $raw ) ? $raw : array();
	}

	// `header_mode` is now an inline multi-picker: [ 'mode' => 'top', '<mode>' => [ … ] ].
	// Tolerate the legacy scalar shape (a bare 'top'/'vertical-left'/… string) so
	// pre-migration installs still resolve.
	$hm = isset( $cache['header_mode'] ) ? $cache['header_mode'] : null;

	if ( $key === 'header_mode' ) {
		if ( is_array( $hm ) ) {
			return ( isset( $hm['mode'] ) && $hm['mode'] !== '' ) ? $hm['mode'] : $default;
		}
		return ( is_string( $hm ) && $hm !== '' ) ? $hm : $default;
	}

	// Reveal-housed keys (`vertical_width`; `header_design` for the Top mode) live under
	// the active mode's sub-array; fall back to the legacy flat key, then the default.
	// (The flat toggle keys — header_border / header_shadow / header_uppercase_nav /
	// header_glass — are served by the generic $cache[$key] tail below.)
	if ( in_array( $key, array( 'vertical_width', 'header_design', 'overlay_style', 'vertical_side' ), true ) && is_array( $hm ) ) {
		$mode = ( isset( $hm['mode'] ) && $hm['mode'] !== '' ) ? $hm['mode'] : 'top';
		if ( isset( $hm[ $mode ][ $key ] ) && $hm[ $mode ][ $key ] !== '' && $hm[ $mode ][ $key ] !== null ) {
			$rv = $hm[ $mode ][ $key ];
			// header_design is now a multi-picker: [ 'design' => 'pill', 'pill' => [ … ] ].
			// Return the chosen design SLUG; tolerate the legacy scalar shape.
			if ( 'header_design' === $key && is_array( $rv ) ) {
				return ( isset( $rv['design'] ) && $rv['design'] !== '' ) ? $rv['design'] : $default;
			}
			// overlay_style is a popover multi-picker: [ 'style' => 'panel'|'radial' ].
			// Return the chosen style slug; tolerate the legacy scalar shape.
			if ( 'overlay_style' === $key && is_array( $rv ) ) {
				return ( isset( $rv['style'] ) && $rv['style'] !== '' ) ? $rv['style'] : $default;
			}
			// vertical_side is a popover multi-picker: [ 'side' => 'left'|'right' ].
			if ( 'vertical_side' === $key && is_array( $rv ) ) {
				return ( isset( $rv['side'] ) && $rv['side'] !== '' ) ? $rv['side'] : $default;
			}
			return $rv;
		}
	}

	if ( ! array_key_exists( $key, $cache ) ) { return $default; }
	$val = $cache[ $key ];
	if ( $val === '' || $val === null ) { return $default; }
	return $val;
}
endif;

if ( ! function_exists( 'unysonplus_header_is_vertical' ) ) :
/**
 * Whether the header is in a Vertical (side-rail) mode. Vertical-Left and
 * Vertical-Right were merged into one 'vertical' mode with a Left/Right side
 * picker; the legacy split modes are still recognised (pre-migration installs).
 */
function unysonplus_header_is_vertical() {
	$mode = unysonplus_header_layout_get( 'header_mode', 'top' );
	return in_array( $mode, array( 'vertical', 'vertical-left', 'vertical-right' ), true );
}
endif;

if ( ! function_exists( 'unysonplus_header_vertical_side' ) ) :
/**
 * Resolve the side the vertical rail sits on: 'left' | 'right'. Reads the new
 * vertical_side picker; falls back to the legacy vertical-left / vertical-right
 * mode value, then 'left'.
 */
function unysonplus_header_vertical_side() {
	$mode = unysonplus_header_layout_get( 'header_mode', 'top' );
	if ( 'vertical-right' === $mode ) { return 'right'; }
	if ( 'vertical-left'  === $mode ) { return 'left'; }
	$side = unysonplus_header_layout_get( 'vertical_side', 'left' );
	return in_array( $side, array( 'left', 'right' ), true ) ? $side : 'left';
}
endif;

if ( ! function_exists( 'unysonplus_header_design_options' ) ) :
/**
 * The active Header Design's revealed sub-options (roundness / shadow / inset / gap).
 * Reads the nested multi-picker at header_layout → header_mode → <mode> → header_design.
 *
 * @return array e.g. [ 'pill_radius' => 'full', 'pill_inset' => 'none', 'pill_shadow' => 'medium' ]; empty when none.
 */
function unysonplus_header_design_options() {
	$raw = function_exists( 'fw_get_db_settings_option' ) ? fw_get_db_settings_option( 'header_layout', array() ) : array();
	$hm  = ( is_array( $raw ) && isset( $raw['header_mode'] ) ) ? $raw['header_mode'] : null;
	if ( ! is_array( $hm ) ) {
		return array();
	}
	$mode = ( isset( $hm['mode'] ) && $hm['mode'] !== '' ) ? $hm['mode'] : 'top';
	$hd   = isset( $hm[ $mode ]['header_design'] ) ? $hm[ $mode ]['header_design'] : null;
	if ( ! is_array( $hd ) || empty( $hd['design'] ) ) {
		return array();
	}
	$slug = $hd['design'];
	return ( isset( $hd[ $slug ] ) && is_array( $hd[ $slug ] ) ) ? $hd[ $slug ] : array();
}
endif;

if ( ! function_exists( 'unysonplus_header_design_css_vars' ) ) :
/**
 * Map the active Header Design's sub-option choices to the CSS custom properties its
 * partial consumes (--header-design-radius / -inset / -shadow / -gap). Only the active
 * design's vars are returned; the partial defaults cover the rest.
 *
 * @return array<string,string>
 */
function unysonplus_header_design_css_vars() {
	$design = function_exists( 'unysonplus_header_layout_get' ) ? unysonplus_header_layout_get( 'header_design', 'classic' ) : 'classic';
	$opts   = function_exists( 'unysonplus_header_design_options' ) ? unysonplus_header_design_options() : array();
	$vars   = array();

	$pick = function ( $map, $key, $fallback ) use ( $opts ) {
		$v = isset( $opts[ $key ] ) ? $opts[ $key ] : '';
		return isset( $map[ $v ] ) ? $map[ $v ] : $fallback;
	};
	$shadow = array(
		'soft'   => '0 4px 16px -10px rgba(15, 23, 42, .22)',
		'medium' => '0 8px 24px -12px rgba(15, 23, 42, .30)',
		'strong' => '0 14px 34px -12px rgba(15, 23, 42, .42)',
	);

	if ( 'pill' === $design ) {
		$vars['--header-design-radius'] = $pick( array( 'full' => '999px', 'large' => '1.5rem', 'medium' => '.75rem' ), 'pill_radius', '999px' );
		$vars['--header-design-inset']  = $pick( array( 'none' => '0px', 'small' => '1.5rem', 'large' => '4rem' ), 'pill_inset', '0px' );
		$vars['--header-design-shadow'] = $pick( $shadow, 'pill_shadow', $shadow['medium'] );
	} elseif ( 'card' === $design ) {
		$vars['--header-design-radius'] = $pick( array( 'small' => '8px', 'medium' => '14px', 'large' => '22px' ), 'card_radius', '14px' );
		$vars['--header-design-shadow'] = $pick( $shadow, 'card_shadow', $shadow['medium'] );
	} elseif ( 'centered' === $design ) {
		$vars['--header-design-gap'] = $pick( array( 'tight' => '.25rem', 'normal' => '.5rem', 'roomy' => '1rem' ), 'centered_gap', '.5rem' );
	}
	return $vars;
}
endif;

if ( ! function_exists( 'unysonplus_migrate_layout_settings' ) ) :
/**
 * One-time migration: distribute the old single `general_layout` blob into the
 * new per-tab storage keys after the General → Layout tab was split.
 *
 *  - sidebar keys   → `general_sidebar`
 *  - preloader keys → `general_preloader`
 *  - scroll keys    → `general_scroll`
 *  - header_mode / vertical_width → `header_layout` (renamed to header_mode /
 *    vertical_width)
 *  - drops the deprecated `layout_header_position` (superseded by Header →
 *    Layout → Header Behavior) and the unused `layout_mobile_breakpoint`.
 *
 * Idempotent + self-terminating: once the moved keys are gone from
 * `general_layout` there is nothing left to do, so it stops rewriting.
 */
function unysonplus_migrate_layout_settings() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}

	$general = fw_get_db_settings_option( 'general_layout', array() );
	if ( ! is_array( $general ) ) { return; }

	$sidebar_keys   = array( 'layout_sidebar_position', 'layout_sidebar_width', 'layout_sidebar_gap', 'layout_sidebar_sticky' );
	$preloader_keys = array( 'layout_preloader_style', 'layout_preloader_bg_color' );
	$scroll_keys    = array( 'layout_smooth_scroll', 'layout_scroll_progress', 'layout_scroll_progress_color' );
	$header_map     = array( 'layout_header_mode' => 'header_mode', 'layout_vertical_width' => 'vertical_width' );
	$drop_keys      = array( 'layout_header_position', 'layout_mobile_breakpoint' );

	// Nothing to migrate? bail (this is the steady state).
	$present = false;
	foreach ( array_merge( $sidebar_keys, $preloader_keys, $scroll_keys, array_keys( $header_map ), $drop_keys ) as $k ) {
		if ( array_key_exists( $k, $general ) ) { $present = true; break; }
	}
	if ( ! $present ) { return; }

	$sidebar = fw_get_db_settings_option( 'general_sidebar', array() );
	if ( ! is_array( $sidebar ) ) { $sidebar = array(); }
	foreach ( $sidebar_keys as $k ) {
		if ( array_key_exists( $k, $general ) ) {
			if ( ! array_key_exists( $k, $sidebar ) ) { $sidebar[ $k ] = $general[ $k ]; }
			unset( $general[ $k ] );
		}
	}

	$preloader = fw_get_db_settings_option( 'general_preloader', array() );
	if ( ! is_array( $preloader ) ) { $preloader = array(); }
	foreach ( $preloader_keys as $k ) {
		if ( array_key_exists( $k, $general ) ) {
			if ( ! array_key_exists( $k, $preloader ) ) { $preloader[ $k ] = $general[ $k ]; }
			unset( $general[ $k ] );
		}
	}

	$scroll = fw_get_db_settings_option( 'general_scroll', array() );
	if ( ! is_array( $scroll ) ) { $scroll = array(); }
	foreach ( $scroll_keys as $k ) {
		if ( array_key_exists( $k, $general ) ) {
			if ( ! array_key_exists( $k, $scroll ) ) { $scroll[ $k ] = $general[ $k ]; }
			unset( $general[ $k ] );
		}
	}

	$header = fw_get_db_settings_option( 'header_layout', array() );
	if ( ! is_array( $header ) ) { $header = array(); }
	foreach ( $header_map as $old => $new ) {
		if ( array_key_exists( $old, $general ) ) {
			if ( ! array_key_exists( $new, $header ) ) { $header[ $new ] = $general[ $old ]; }
			unset( $general[ $old ] );
		}
	}

	foreach ( $drop_keys as $k ) { unset( $general[ $k ] ); }

	fw_set_db_settings_option( 'general_layout', $general );
	fw_set_db_settings_option( 'general_sidebar', $sidebar );
	fw_set_db_settings_option( 'general_preloader', $preloader );
	fw_set_db_settings_option( 'general_scroll', $scroll );
	fw_set_db_settings_option( 'header_layout', $header );
}
endif;
// Invoked by the central schema-migration runner (inc/includes/migrations.php),
// not hooked directly. Kept idempotent so a re-run is a safe no-op.


/* ============================================================
 * Layout overrides (template + per-page meta + global Pages)
 *
 * Centralizes resolution of per-render layout intent. Templates call
 * unysonplus_set_layout_override() at the top of the file to declare
 * their intent ('sidebar=right', 'width=full', etc.); wrapper helpers
 * read the resolved value via unysonplus_resolve_layout().
 *
 * Priority cascade (highest wins):
 *   1. Per-page meta override (fw_get_db_post_option) — Phase 2
 *   2. Template-set override (this module's static store)
 *   3. Global Pages default (general_pages multi) — Phase 3
 *   4. General Layout default (general_layout multi)
 *   5. Hardcoded fallback (passed as $default)
 *
 * Supported keys:
 *   sidebar      — 'none' | 'left' | 'right'
 *   width        — 'default' | 'narrow' | 'wide' | 'full'
 *   hide_header  — bool
 *   hide_footer  — bool
 *   hide_title   — bool
 * ============================================================ */

if ( ! function_exists( 'unysonplus_layout_override_store' ) ) :
function &unysonplus_layout_override_store() {
	static $store = array();
	return $store;
}
endif;

if ( ! function_exists( 'unysonplus_set_layout_override' ) ) :
function unysonplus_set_layout_override( array $overrides ) {
	$store =& unysonplus_layout_override_store();
	foreach ( $overrides as $k => $v ) {
		$store[ $k ] = $v;
	}
}
endif;

if ( ! function_exists( 'unysonplus_get_layout_override' ) ) :
function unysonplus_get_layout_override( $key, $default = null ) {
	$store =& unysonplus_layout_override_store();
	return array_key_exists( $key, $store ) ? $store[ $key ] : $default;
}
endif;

if ( ! function_exists( 'unysonplus_pages_get' ) ) :
/**
 * Read one key from the general_pages option group (Phase 3).
 */
function unysonplus_pages_get( $key, $default = '' ) {
	static $cache = null;
	if ( $cache === null ) {
		// Read-transparent merge of the Pages sub-tab groups (like unysonplus_layout_get):
		// Defaults (general_pages) + Layout (pages_layout) + Hero (pages_hero) share one
		// flat key space, so reads stay key-name stable across the split.
		$cache = array();
		if ( function_exists( 'fw_get_db_settings_option' ) ) {
			foreach ( array( 'general_pages', 'pages_layout', 'pages_hero' ) as $grp ) {
				$raw = fw_get_db_settings_option( $grp, array() );
				if ( is_array( $raw ) ) { $cache = array_merge( $cache, $raw ); }
			}
		}
	}
	if ( ! array_key_exists( $key, $cache ) ) { return $default; }
	$val = $cache[ $key ];
	if ( $val === '' || $val === null ) { return $default; }
	return $val;
}
endif;

if ( ! function_exists( 'unysonplus_get_page_meta' ) ) :
/**
 * Read a per-page meta value, returning null when unset / empty / 'default'.
 * Only fires inside the loop on a page post type (Phase 2).
 */
function unysonplus_get_page_meta( $key ) {
	if ( ! function_exists( 'fw_get_db_post_option' ) ) { return null; }
	$pid = get_the_ID();
	if ( ! $pid || get_post_type( $pid ) !== 'page' ) { return null; }
	$val = fw_get_db_post_option( $pid, $key );
	if ( $val === null || $val === '' || $val === 'default' ) { return null; }
	return $val;
}
endif;

if ( ! function_exists( 'unysonplus_get_singular_meta' ) ) :
/**
 * Read a per-content meta value on ANY singular view (page, post, or CPT),
 * returning null when unset / empty / 'default'. Used for the header/footer
 * preset keys, which — unlike the page-only layout keys above — apply to all
 * singular content. Reads the queried object so it is correct in the header /
 * footer templates regardless of the inner loop state.
 */
function unysonplus_get_singular_meta( $key ) {
	if ( ! function_exists( 'fw_get_db_post_option' ) ) { return null; }
	if ( ! is_singular() ) { return null; }
	$pid = get_queried_object_id();
	if ( ! $pid ) { $pid = get_the_ID(); }
	if ( ! $pid ) { return null; }
	$val = fw_get_db_post_option( $pid, $key );
	if ( $val === null || $val === '' || $val === 'default' ) { return null; }
	return $val;
}
endif;

if ( ! function_exists( 'unysonplus_resolve_layout' ) ) :
/**
 * Resolve a layout key through the 5-level priority cascade.
 *
 * @param string $key     One of: sidebar, width, hide_header, hide_footer, hide_title.
 * @param mixed  $default Hardcoded fallback (level 5).
 * @return mixed
 */
function unysonplus_resolve_layout( $key, $default = null ) {
	// Header / footer preset keys resolve independently of the page-only cascade
	// below: a preset can be set on any singular content, plus a site-wide
	// default that applies everywhere (incl. archives / posts).
	//   per-content meta  →  site-wide default (General → Pages)  →  $default ('')
	if ( $key === 'header_preset' || $key === 'footer_preset' ) {
		$meta = unysonplus_get_singular_meta( $key );
		if ( $meta !== null ) { return $meta; }
		$gd = unysonplus_pages_get( 'default_' . $key, '' ); // default_header_preset | default_footer_preset
		if ( $gd !== '' && $gd !== null ) { return $gd; }
		return $default;
	}

	// 1. Per-page meta override (Phase 2).
	// Header visibility is a single source of truth: the per-page `page_header`
	// select (Global / Transparent / Hidden). 'd-none' = hidden; anything else
	// (incl. 'transparent') = shown. This replaces the old redundant
	// `hide_site_header` switch.
	if ( $key === 'hide_header' ) {
		$ph = unysonplus_get_page_meta( 'page_header' );
		if ( $ph !== null ) { return ( $ph === 'd-none' ); }
	}
	$meta_map = array(
		'sidebar'     => 'sidebar_override',
		'width'       => 'content_width',
		'hide_footer' => 'hide_site_footer',
		'hide_title'  => 'hide_page_title',
	);
	if ( isset( $meta_map[ $key ] ) ) {
		$meta = unysonplus_get_page_meta( $meta_map[ $key ] );
		if ( $meta !== null ) {
			// Switches store boolean true/false in Unyson multi containers.
			if ( in_array( $key, array( 'hide_footer', 'hide_title' ), true ) ) {
				if ( is_bool( $meta ) )   { return $meta; }
				if ( $meta === 'yes' )    { return true; }
				if ( $meta === 'no' )     { return false; }
			}
			return $meta;
		}
	}

	// 2. Template-set override.
	$tpl = unysonplus_get_layout_override( $key, null );
	if ( $tpl !== null ) { return $tpl; }

	// 3. Global Pages default (Phase 3) — only meaningful on pages.
	if ( is_page() ) {
		if ( $key === 'sidebar' ) {
			// Explicit Pages → Layout → Default Sidebar wins; else the Default Page
			// Layout mapping (the legacy quick-picker) still applies.
			$explicit = unysonplus_pages_get( 'default_sidebar', 'inherit' );
			if ( in_array( $explicit, array( 'none', 'left', 'right' ), true ) ) { return $explicit; }
			$layout_pref = unysonplus_pages_get( 'default_page_layout', '' );
			$layout_to_sidebar = array(
				'sidebar-right' => 'right',
				'sidebar-left'  => 'left',
				'full-width'    => 'none',
				'boxed-narrow'  => 'none',
			);
			if ( isset( $layout_to_sidebar[ $layout_pref ] ) ) {
				return $layout_to_sidebar[ $layout_pref ];
			}
		}
		if ( $key === 'width' ) {
			$explicit = unysonplus_pages_get( 'default_content_width', 'default' );
			if ( in_array( $explicit, array( 'narrow', 'wide', 'full' ), true ) ) { return $explicit; }
			$layout_pref = unysonplus_pages_get( 'default_page_layout', '' );
			$layout_to_width = array(
				'full-width'   => 'full',
				'boxed-narrow' => 'narrow',
			);
			if ( isset( $layout_to_width[ $layout_pref ] ) ) {
				return $layout_to_width[ $layout_pref ];
			}
		}
	}

	// 3.5. Per-context sidebar default (General → Sidebar): more specific than the
	// global default below, so it wins for its context. Each context Inherits (falls
	// through) unless set to none/left/right. Pages are covered by step 3 above.
	if ( $key === 'sidebar' ) {
		$ctx = null;
		if ( is_singular( 'post' ) )            { $ctx = 'post'; }
		elseif ( is_search() )                  { $ctx = 'search'; }
		elseif ( is_404() )                     { $ctx = '404'; }
		elseif ( is_home() || is_archive() )    { $ctx = 'archive'; }
		if ( $ctx !== null ) {
			$cv = unysonplus_layout_get( 'layout_sidebar_context_' . $ctx, 'inherit' );
			if ( in_array( $cv, array( 'none', 'left', 'right' ), true ) ) { return $cv; }
		}
	}

	// 4. General Layout default.
	if ( $key === 'sidebar' ) {
		$pos = unysonplus_layout_get( 'layout_sidebar_position', '' );
		if ( in_array( $pos, array( 'none', 'left', 'right' ), true ) ) { return $pos; }
	}

	// 5. Hardcoded fallback.
	return $default;
}
endif;


/* ============================================================
 * Body classes
 * ============================================================ */

if ( ! function_exists( 'unysonplus_width_get' ) ) :
/**
 * Read a value from the `site_width_mode` multi-picker.
 *
 * Stored shape: [ 'mode' => 'full|boxed|framed', 'boxed' => [ … ], 'framed' => [ … ] ].
 * Tolerates the legacy flat shape (mode as a bare string + sub-options stored at
 * the top level of general_layout) so reads work before the migration runs.
 *
 * @param string $key     'mode' or a sub-option id (site_boxed_* / site_frame_*).
 * @param mixed  $default
 * @return mixed
 */
function unysonplus_width_get( $key, $default = '' ) {
	$wm = unysonplus_layout_get( 'site_width_mode', null );

	if ( ! is_array( $wm ) ) { // legacy flat
		if ( $key === 'mode' ) {
			return ( $wm !== null && $wm !== '' ) ? $wm : $default;
		}
		return unysonplus_layout_get( $key, $default );
	}

	if ( $key === 'mode' ) {
		return ! empty( $wm['mode'] ) ? $wm['mode'] : $default;
	}

	$group = ( strpos( $key, 'site_boxed' ) === 0 ) ? 'boxed' : 'framed';
	if ( isset( $wm[ $group ][ $key ] ) && $wm[ $group ][ $key ] !== '' && $wm[ $group ][ $key ] !== null ) {
		return $wm[ $group ][ $key ];
	}
	return unysonplus_layout_get( $key, $default ); // legacy flat fallback
}
endif;

if ( ! function_exists( 'unysonplus_migrate_width_mode' ) ) :
/**
 * Schema migration (v3): convert the legacy flat Site Width Mode (a string +
 * top-level boxed/frame sub-options) into the `site_width_mode` multi-picker
 * shape, and drop the removed (broken) `layout_container_max_width` option.
 * Idempotent. Invoked by the central runner (inc/includes/migrations.php).
 */
function unysonplus_migrate_width_mode() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}
	$gl = fw_get_db_settings_option( 'general_layout', array() );
	if ( ! is_array( $gl ) ) { return; }

	$changed = false;
	$wm      = isset( $gl['site_width_mode'] ) ? $gl['site_width_mode'] : null;

	if ( ! is_array( $wm ) ) {
		$mode   = ( $wm !== null && $wm !== '' ) ? $wm : 'full';
		$nested = array( 'mode' => $mode );

		$boxed = array();
		foreach ( array( 'site_boxed_width', 'site_boxed_alignment', 'site_boxed_margin' ) as $k ) {
			if ( array_key_exists( $k, $gl ) ) { $boxed[ $k ] = $gl[ $k ]; unset( $gl[ $k ] ); }
		}
		if ( $boxed ) { $nested['boxed'] = $boxed; }

		$framed = array();
		foreach ( array( 'site_frame_width', 'site_frame_color' ) as $k ) {
			if ( array_key_exists( $k, $gl ) ) { $framed[ $k ] = $gl[ $k ]; unset( $gl[ $k ] ); }
		}
		if ( $framed ) { $nested['framed'] = $framed; }

		$gl['site_width_mode'] = $nested;
		$changed = true;
	}

	if ( array_key_exists( 'layout_container_max_width', $gl ) ) {
		unset( $gl['layout_container_max_width'] );
		$changed = true;
	}

	if ( $changed ) {
		fw_set_db_settings_option( 'general_layout', $gl );
	}
}
endif;

if ( ! function_exists( 'unysonplus_migrate_overlay_style' ) ) :
/**
 * Schema migration (v6): the Overlay Style option became a popover multi-picker,
 * so its saved value changed from a scalar ('panel'|'radial') to the picker shape
 * [ 'style' => … ]. Wrap any legacy scalar in place so the settings UI shows the
 * right tile. The front-end getter tolerates both shapes regardless. Idempotent.
 */
function unysonplus_migrate_overlay_style() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}
	$hl = fw_get_db_settings_option( 'header_layout', array() );
	if ( ! is_array( $hl ) || ! isset( $hl['header_mode']['overlay']['overlay_style'] ) ) { return; }
	$v = $hl['header_mode']['overlay']['overlay_style'];
	if ( is_string( $v ) && $v !== '' ) {
		$hl['header_mode']['overlay']['overlay_style'] = array( 'style' => $v );
		fw_set_db_settings_option( 'header_layout', $hl );
	}
}
endif;

if ( ! function_exists( 'unysonplus_migrate_vertical_merge' ) ) :
/**
 * Schema migration (v7): Vertical-Left and Vertical-Right header modes were
 * merged into a single 'vertical' mode with a Left/Right side picker. Convert a
 * stored 'vertical-left'/'vertical-right' mode into 'vertical' + vertical_side,
 * carrying the saved rail width across. Reads/writes the RAW option store
 * (FW_WP_Option) so the legacy mode isn't lost to picker normalisation first,
 * and so the other modes' saved sub-options are preserved untouched. Idempotent.
 */
function unysonplus_migrate_vertical_merge() {
	if ( ! class_exists( 'FW_WP_Option' ) ) { return; }
	$theme_id = ( function_exists( 'fw' ) && fw()->theme ) ? fw()->theme->manifest->get_id() : 'unysonplus';
	$opt      = 'fw_theme_settings_options:' . $theme_id;
	$hl       = FW_WP_Option::get( $opt, 'header_layout' );
	if ( ! is_array( $hl ) || ! isset( $hl['header_mode']['mode'] ) ) { return; }

	$mode = $hl['header_mode']['mode'];
	if ( $mode !== 'vertical-left' && $mode !== 'vertical-right' ) { return; }

	$side  = ( $mode === 'vertical-right' ) ? 'right' : 'left';
	$width = isset( $hl['header_mode'][ $mode ]['vertical_width'] ) ? $hl['header_mode'][ $mode ]['vertical_width'] : null;

	$hl['header_mode']['mode'] = 'vertical';
	if ( ! isset( $hl['header_mode']['vertical'] ) || ! is_array( $hl['header_mode']['vertical'] ) ) {
		$hl['header_mode']['vertical'] = array();
	}
	$hl['header_mode']['vertical']['vertical_side'] = array( 'side' => $side );
	if ( $width !== null ) { $hl['header_mode']['vertical']['vertical_width'] = $width; }

	FW_WP_Option::set( $opt, 'header_layout', $hl );
}
endif;

if ( ! function_exists( 'unysonplus_layout_body_classes' ) ) :
function unysonplus_layout_body_classes( $classes ) {
	// Site width mode (read from the multi-picker; legacy flat tolerated).
	$width_mode = unysonplus_width_get( 'mode', 'full' );
	if ( ! in_array( $width_mode, array( 'full', 'boxed', 'framed' ), true ) ) {
		$width_mode = 'full';
	}
	$classes[] = 'site-' . $width_mode;

	if ( $width_mode === 'boxed' ) {
		$align = unysonplus_width_get( 'site_boxed_alignment', 'center' );
		if ( ! in_array( $align, array( 'left', 'center', 'right' ), true ) ) { $align = 'center'; }
		$classes[] = 'site-boxed--' . $align;
	}

	// Background pattern overlay is now applied via the `--site-bg-pattern` CSS
	// variable (emitted in theme-vars.php from the background-image option), so it
	// supports uploaded images and no longer needs a per-preset body class.

	// Spacing scale
	$spacing = unysonplus_layout_get( 'layout_section_spacing', 'cozy' );
	if ( ! in_array( $spacing, array( 'compact', 'cozy', 'spacious' ), true ) ) { $spacing = 'cozy'; }
	$classes[] = 'spacing-' . $spacing;

	// Header layout mode (now owned by Header → Layout; legacy general_layout
	// value is the fallback for pre-migration installs).
	$header_mode = unysonplus_header_layout_get( 'header_mode', unysonplus_layout_get( 'layout_header_mode', 'top' ) );
	if ( unysonplus_header_is_vertical() ) {
		// Merged Vertical mode: layout-vertical + a side hook (layout-vertical-left
		// / -right) so the shared vertical.css positions the rail on the chosen side.
		$classes[] = 'layout-vertical';
		$classes[] = 'layout-vertical-' . unysonplus_header_vertical_side();
	} else {
		$valid_modes = array( 'top', 'off-canvas-only', 'overlay' );
		if ( ! in_array( $header_mode, $valid_modes, true ) ) { $header_mode = 'top'; }
		$classes[] = 'layout-' . $header_mode;
	}

	// Header → Menu: item style (hover/active treatment on top-level nav items).
	// 'none' is the bare default and needs no class; style.css keys the rest off
	// body.menu-style-<slug>.
	$menu_opts  = fw_get_db_settings_option( 'header_menu', array() );
	$menu_style = ( is_array( $menu_opts ) && ! empty( $menu_opts['menu_item_style'] ) ) ? (string) $menu_opts['menu_item_style'] : 'none';
	$menu_style = preg_replace( '/[^a-z0-9\-]/', '', strtolower( $menu_style ) );
	if ( in_array( $menu_style, array( 'underline', 'underline-grow', 'pill', 'box', 'outline', 'bottom-bar', 'top-bar', 'highlight' ), true ) ) {
		$classes[] = 'menu-style-' . $menu_style;
	}

		// Dropdown panel design (overall submenu-box treatment). 'classic' is the
		// bare default and needs no class; style.css keys the rest off
		// body.dropdown-style-<slug>.
		$dropdown_style = ( is_array( $menu_opts ) && ! empty( $menu_opts['menu_dropdown_style'] ) ) ? (string) $menu_opts['menu_dropdown_style'] : 'classic';
		$dropdown_style = preg_replace( '/[^a-z0-9\-]/', '', strtolower( $dropdown_style ) );
		if ( in_array( $dropdown_style, array( 'elevated', 'bordered', 'minimal', 'top-accent' ), true ) ) {
			$classes[] = 'dropdown-style-' . $dropdown_style;
		}

	// Header → Layout: Scroll Spy (active-section menu highlighting + smooth anchor
	// scroll). Gated behind a body class so normal multi-page sites are unaffected;
	// navigation.js + style.css key off body.nav-scrollspy.
	if ( function_exists( 'unysonplus_header_layout_get' )
		&& unysonplus_header_layout_get( 'nav_scrollspy', 'no' ) === 'yes' ) {
		$classes[] = 'nav-scrollspy';
	}

	// Resolved sidebar position (cascade-aware: per-page meta > template > global > default).
	$sidebar = unysonplus_resolve_layout( 'sidebar', 'right' );
	if ( ! in_array( $sidebar, array( 'none', 'left', 'right' ), true ) ) { $sidebar = 'right'; }
	$classes[] = 'sidebar-' . $sidebar;

	// Sticky sidebar (desktop only; CSS gates the breakpoint).
	if ( unysonplus_layout_get( 'layout_sidebar_sticky', 'no' ) === 'yes' ) {
		$classes[] = 'sidebar-sticky';
	}

	// Sidebar responsive behavior (General → Sidebar → Responsive & Sticky).
	// Collapse breakpoint (lg = 992px default, md = 768px).
	$collapse = unysonplus_layout_get( 'layout_sidebar_collapse_bp', 'lg' );
	$classes[] = 'sidebar-collapse-' . ( $collapse === 'md' ? 'md' : 'lg' );
	// Sidebar above content when stacked.
	if ( unysonplus_layout_get( 'layout_sidebar_mobile_order', 'below' ) === 'above' ) {
		$classes[] = 'sidebar-mobile-above';
	}
	// Hide sidebar on stacked (mobile) layouts.
	if ( unysonplus_layout_get( 'layout_sidebar_mobile_hide', 'no' ) === 'yes' ) {
		$classes[] = 'sidebar-hide-mobile';
	}

	// General → Base: custom scrollbar is opt-in (only when a color is set), so the
	// bare ::-webkit-scrollbar rule doesn't restyle every default scrollbar.
	$base_opts = fw_get_db_settings_option( 'general_base', array() );
	if ( is_array( $base_opts ) ) {
		$sb = isset( $base_opts['base_scrollbar_color'] ) ? unysonplus_preset_color_to_css( $base_opts['base_scrollbar_color'] ) : '';
		if ( $sb !== '' ) { $classes[] = 'custom-scrollbar'; }

		// General → Base: content-protection deterrents (opt-in). Each switch adds a
		// body class that style.css (selection) / theme.js (context menu, copy) key off.
		if ( ( isset( $base_opts['base_disable_text_selection'] ) ? $base_opts['base_disable_text_selection'] : 'no' ) === 'yes' ) { $classes[] = 'up-noselect'; }
		if ( ( isset( $base_opts['base_disable_right_click'] )    ? $base_opts['base_disable_right_click']    : 'no' ) === 'yes' ) { $classes[] = 'up-nocontext'; }
		if ( ( isset( $base_opts['base_disable_copy'] )           ? $base_opts['base_disable_copy']           : 'no' ) === 'yes' ) { $classes[] = 'up-nocopy'; }
	}

	// Resolved content width (only emits when not 'default').
	$width = unysonplus_resolve_layout( 'width', 'default' );
	if ( in_array( $width, array( 'narrow', 'wide', 'full' ), true ) ) {
		$classes[] = 'layout-width-' . $width;
	}

	// Page-template slug class for CSS targeting (e.g. page-layout-landing).
	if ( is_page() ) {
		$tpl = get_page_template_slug( get_the_ID() );
		if ( $tpl ) {
			$slug = preg_replace( '/^page-|\.php$/', '', $tpl );
			if ( $slug ) {
				$classes[] = 'page-layout-' . sanitize_html_class( $slug );
			}
		}
	}

	// Preloader (active while page loads; JS removes after window.load).
	// Skip when the Animation Engine owns the preloader (see unysonplus_render_preloader).
	if ( unysonplus_layout_get( 'layout_preloader_style', 'none' ) !== 'none'
		&& ! ( function_exists( 'unysonplus_engine_preloader_active' ) && unysonplus_engine_preloader_active() ) ) {
		$classes[] = 'preloader-active';
	}

	return $classes;
}
endif;
add_filter( 'body_class', 'unysonplus_layout_body_classes', 20 );


/* ============================================================
 * Preloader
 * ============================================================ */

if ( ! function_exists( 'unysonplus_engine_preloader_active' ) ) :
/**
 * The Animation Engine plugin extension ships a richer preloader (Theme Settings →
 * Animations → Preloader). When that extension is active it OWNS the preloader, and
 * the theme's own (spinner / logo) preloader stands down so the two never stack.
 */
function unysonplus_engine_preloader_active() {
	return function_exists( 'fw_ext' ) && fw_ext( 'animation-engine' );
}
endif;

if ( ! function_exists( 'unysonplus_render_preloader' ) ) :
function unysonplus_render_preloader() {
	if ( unysonplus_engine_preloader_active() ) { return; }
	$style = unysonplus_layout_get( 'layout_preloader_style', 'none' );
	if ( $style === 'none' ) { return; }

	$logo_url = '';
	if ( $style === 'logo' ) {
		$custom = get_theme_mod( 'custom_logo' );
		if ( $custom ) {
			$src = wp_get_attachment_image_src( $custom, 'full' );
			if ( $src ) { $logo_url = $src[0]; }
		}
	}
	?>
	<div class="preloader preloader--<?php echo esc_attr( $style ); ?>" aria-hidden="true">
		<?php if ( $style === 'spinner' ) : ?>
			<div class="preloader__spinner"></div>
		<?php elseif ( $style === 'logo' && $logo_url ) : ?>
			<img class="preloader__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="">
		<?php elseif ( $style === 'logo' ) : ?>
			<div class="preloader__logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></div>
		<?php endif; ?>
	</div>
	<?php
}
endif;
add_action( 'wp_body_open', 'unysonplus_render_preloader', 1 );


/* ============================================================
 * Scroll Progress Bar
 * ============================================================ */

if ( ! function_exists( 'unysonplus_render_scroll_progress' ) ) :
function unysonplus_render_scroll_progress() {
	if ( unysonplus_layout_get( 'layout_scroll_progress', 'no' ) !== 'yes' ) { return; }
	// Defer to the Animation Engine's Scroll Progress (16 styles) when IT is enabled,
	// so the two bars never stack. (The theme's basic bar still shows if the engine's is off.)
	if ( function_exists( 'upw_scrollprog_enabled' ) && upw_scrollprog_enabled() ) { return; }
	?>
	<div class="scroll-progress" aria-hidden="true"><div class="scroll-progress__bar"></div></div>
	<?php
}
endif;
add_action( 'wp_body_open', 'unysonplus_render_scroll_progress', 2 );


/* ============================================================
 * Default Sidebar (Phase 5)
 *
 * Theme-wide default for which sidebar widget area renders alongside
 * main content. Templates (page.php, single.php, archive.php, etc.)
 * call unysonplus_render_default_sidebar() after their <main> and
 * read unysonplus_has_default_sidebar() to decide whether to apply
 * the .has-sidebar class on their .with-sidebar wrapper.
 *
 * Returning null / false here means "no sidebar" — either because
 * the option is set to 'none' or because the chosen widget area has
 * no active widgets.
 * ============================================================ */

if ( ! function_exists( 'unysonplus_get_default_sidebar_id' ) ) :
function unysonplus_get_default_sidebar_id() {
	$pos = unysonplus_resolve_layout( 'sidebar', 'right' );
	if ( $pos === 'none' ) { return null; }
	if ( $pos === 'left' )  { return 'sidebar-left'; }
	return 'sidebar-right';
}
endif;

if ( ! function_exists( 'unysonplus_has_default_sidebar' ) ) :
function unysonplus_has_default_sidebar() {
	$id = unysonplus_get_default_sidebar_id();
	return $id && is_active_sidebar( $id );
}
endif;

if ( ! function_exists( 'unysonplus_render_default_sidebar' ) ) :
function unysonplus_render_default_sidebar() {
	$pos = unysonplus_resolve_layout( 'sidebar', 'right' );
	if ( $pos === 'none' ) { return; }
	if ( $pos === 'left' ) {
		get_sidebar( 'left' );
	} else {
		get_sidebar();
	}
}
endif;

if ( ! function_exists( 'unysonplus_should_hide_site_header' ) ) :
function unysonplus_should_hide_site_header() {
	return (bool) unysonplus_resolve_layout( 'hide_header', false );
}
endif;

if ( ! function_exists( 'unysonplus_should_hide_site_footer' ) ) :
function unysonplus_should_hide_site_footer() {
	return (bool) unysonplus_resolve_layout( 'hide_footer', false );
}
endif;

if ( ! function_exists( 'unysonplus_should_hide_page_title' ) ) :
function unysonplus_should_hide_page_title() {
	return (bool) unysonplus_resolve_layout( 'hide_title', false );
}
endif;


/* ============================================================
 * Hero Header (Phase 4)
 *
 * Renders a full-width banner at the top of a page when the per-page
 * "Header Image" meta (or the global Pages fallback) is set. Otherwise
 * silently no-ops — content-page.php falls back to its simple title
 * header.
 *
 * Reads per-page meta first (header_image, header_height, overlay_color,
 * overlay_opacity, content_position), falling back to the global Pages
 * default_page_header_image / default_page_header_height.
 * ============================================================ */

if ( ! function_exists( 'unysonplus_get_page_hero_data' ) ) :
function unysonplus_get_page_hero_data() {
	if ( ! function_exists( 'fw_get_db_post_option' ) ) { return null; }
	$pid = get_the_ID();
	if ( ! $pid || get_post_type( $pid ) !== 'page' ) { return null; }

	$image = fw_get_db_post_option( $pid, 'header_image' );
	$url   = is_array( $image ) && ! empty( $image['url'] ) ? $image['url'] : '';

	// Global fallback when per-page image is empty.
	if ( ! $url ) {
		$default_img = unysonplus_pages_get( 'default_page_header_image', array() );
		if ( is_array( $default_img ) && ! empty( $default_img['url'] ) ) {
			$url = $default_img['url'];
		}
	}
	if ( ! $url ) { return null; }

	$height = fw_get_db_post_option( $pid, 'header_height' );
	if ( ! $height || $height === 'auto' ) {
		$height = unysonplus_pages_get( 'default_page_header_height', 'auto' );
	}
	$valid_heights = array( 'auto', 'small', 'medium', 'large', 'fullscreen' );
	if ( ! in_array( $height, $valid_heights, true ) ) { $height = 'auto'; }

	// Overlay colour: per-page meta, else the global Hero default (Pages → Page
	// Title / Hero → Default Overlay Color, a compact preset resolved to CSS).
	$overlay_color = fw_get_db_post_option( $pid, 'header_overlay_color' );
	// The per-page field is now a compact preset ({predefined,custom}); resolve
	// it. An empty/unset value falls back to the global Hero overlay preset.
	if ( function_exists( 'unysonplus_preset_color_to_css' ) ) {
		$resolved = unysonplus_preset_color_to_css( $overlay_color );
		if ( $resolved === '' ) {
			$resolved = unysonplus_preset_color_to_css( unysonplus_pages_get( 'default_hero_overlay_color', '' ) );
		}
		$overlay_color = $resolved;
	} elseif ( $overlay_color === null || $overlay_color === '' ) {
		$gc = unysonplus_pages_get( 'default_hero_overlay_color', '' );
		$overlay_color = is_string( $gc ) ? $gc : '';
	}
	$overlay_opacity = (int) fw_get_db_post_option( $pid, 'header_overlay_opacity' );
	if ( $overlay_opacity <= 0 ) { $overlay_opacity = (int) unysonplus_pages_get( 'default_hero_overlay_opacity', 0 ); }
	$position = fw_get_db_post_option( $pid, 'header_content_position' );
	if ( ! in_array( $position, array( 'top', 'center', 'bottom' ), true ) ) {
		$position = unysonplus_pages_get( 'default_hero_align', 'center' );
		if ( ! in_array( $position, array( 'top', 'center', 'bottom' ), true ) ) { $position = 'center'; }
	}

	return array(
		'url'             => $url,
		'height'          => $height,
		'overlay_color'   => $overlay_color,
		'overlay_opacity' => max( 0, min( 100, $overlay_opacity ) ),
		'position'        => $position,
	);
}
endif;

if ( ! function_exists( 'unysonplus_render_page_hero' ) ) :
/**
 * Emit the hero header markup, or return false when no hero is configured
 * so the caller can fall back to its default header.
 *
 * @return bool True when a hero rendered, false when no-op.
 */
function unysonplus_render_page_hero() {
	$hero = unysonplus_get_page_hero_data();
	if ( ! $hero ) { return false; }

	$classes = array( 'page-hero', 'page-hero--' . $hero['height'], 'page-hero--align-' . $hero['position'] );
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="background-image: url('<?php echo esc_url( $hero['url'] ); ?>');">
		<?php if ( $hero['overlay_color'] && $hero['overlay_opacity'] > 0 ) : ?>
			<div class="page-hero__overlay" style="background-color: <?php echo esc_attr( $hero['overlay_color'] ); ?>; opacity: <?php echo esc_attr( $hero['overlay_opacity'] / 100 ); ?>;"></div>
		<?php endif; ?>
		<div class="page-hero__inner fw-container">
			<?php if ( ! unysonplus_should_hide_page_title() ) : ?>
				<h1 class="entry-title page-hero__title"><?php the_title(); ?></h1>
			<?php endif; ?>
			<?php do_action( 'unysonplus_page_hero_inner' ); ?>
		</div>
	</div>
	<?php
	return true;
}
endif;


/* ============================================================
 * Per-page custom CSS / JS / background color (Phase 5)
 *
 * Editors can paste page-specific CSS or JS via the Custom Code meta
 * box. We emit it inline on that page only — wp_head priority 999 for
 * CSS (after every enqueued stylesheet so it wins specificity ties)
 * and wp_footer priority 999 for JS (after every enqueued script).
 *
 * JS injection requires unfiltered_html on the post author at save
 * time — defense in depth against an editor with restricted role
 * uploading <script>.
 *
 * Also: emits a --page-bg-color CSS variable on body when per-page
 * page_bg_color is set, so style.css can `background-color: var(--page-bg-color, …)`.
 * ============================================================ */

if ( ! function_exists( 'unysonplus_build_page_custom_css' ) ) :
/**
 * Build this page's CSS body: background colour/image variables + the page's
 * own "Custom CSS (this page only)" meta. Returns '' when nothing applies.
 */
function unysonplus_build_page_custom_css( $pid ) {
	$pid = (int) $pid;
	if ( ! $pid || ! function_exists( 'fw_get_db_post_option' ) || get_post_type( $pid ) !== 'page' ) {
		return '';
	}

	$parts = array();

	$bg = fw_get_db_post_option( $pid, 'page_bg_color' );
	// Compact preset ({predefined,custom}) → CSS color; tolerates a legacy string.
	if ( function_exists( 'unysonplus_preset_color_to_css' ) ) {
		$bg = unysonplus_preset_color_to_css( $bg );
	}
	if ( is_string( $bg ) && $bg !== '' ) {
		$parts[] = 'body{--page-bg-color:' . $bg . ';background-color:' . $bg . ';}';
	}

	$bg_image = fw_get_db_post_option( $pid, 'page_bg_image' );
	if ( is_array( $bg_image ) && ! empty( $bg_image['url'] ) ) {
		$parts[] = 'body{background-image:url(' . esc_url_raw( $bg_image['url'] ) . ');background-size:cover;background-attachment:fixed;}';
	}

	$custom = fw_get_db_post_option( $pid, 'page_custom_css' );
	if ( is_string( $custom ) && trim( $custom ) !== '' ) {
		// Strip </style> as a minimal injection guard; the editor still has
		// post-edit capability, so this is belt-and-suspenders only.
		$parts[] = str_replace( '</style', '<\/style', $custom );
	}

	return implode( "\n", $parts );
}
endif;

// Contribute this page's CSS to the plugin's per-page stylesheet
// (page-{id}-{hash}.css), so it's combiner-absorbed alongside the element CSS
// instead of being its own inline <style> block.
if ( ! function_exists( 'unysonplus_filter_page_css' ) ) :
function unysonplus_filter_page_css( $css, $post_id ) {
	$own = unysonplus_build_page_custom_css( $post_id );
	if ( $own === '' ) { return $css; }
	return $css === '' ? $own : $css . "\n" . $own;
}
endif;
add_filter( 'unysonplus_page_css', 'unysonplus_filter_page_css', 10, 2 );

// Legacy inline fallback: only when the plugin's per-page pipeline is NOT
// loaded (plugin inactive) — otherwise the CSS lands in page-{id}-{hash}.css
// and emitting here too would duplicate it.
if ( ! function_exists( 'unysonplus_emit_page_custom_css' ) ) :
function unysonplus_emit_page_custom_css() {
	if ( function_exists( 'unysonplus_build_page_css_string' ) ) { return; }
	if ( ! is_singular( 'page' ) ) { return; }
	$pid = get_queried_object_id();
	$css = unysonplus_build_page_custom_css( $pid );
	if ( $css === '' ) { return; }
	echo "\n" . '<style id="unysonplus-page-css-' . absint( $pid ) . '">' . $css . '</style>' . "\n";
}
endif;
add_action( 'wp_head', 'unysonplus_emit_page_custom_css', 999 );

if ( ! function_exists( 'unysonplus_emit_page_custom_js' ) ) :
function unysonplus_emit_page_custom_js() {
	if ( ! is_singular( 'page' ) || ! function_exists( 'fw_get_db_post_option' ) ) { return; }
	$pid = get_queried_object_id();
	if ( ! $pid ) { return; }

	$js = fw_get_db_post_option( $pid, 'page_custom_js' );
	if ( ! is_string( $js ) || trim( $js ) === '' ) { return; }

	// Require the page author to have unfiltered_html — prevents privilege
	// escalation if an editor without the cap was given access to the field
	// (e.g. through a custom role).
	$post = get_post( $pid );
	if ( ! $post || ! user_can( $post->post_author, 'unfiltered_html' ) ) { return; }

	$js = str_replace( '</script', '<\/script', $js );
	echo "\n" . '<script id="unysonplus-page-js-' . absint( $pid ) . '">' . $js . '</script>' . "\n";
}
endif;
add_action( 'wp_footer', 'unysonplus_emit_page_custom_js', 999 );


/* ============================================================
 * Migration notice for the renamed Left Sidebar template
 *
 * page-boxed.php (Template Name: "Left Sidebar") was renamed to
 * page-sidebar-left.php in v2.1.16. Any pages still pointing at the
 * old slug lose their template assignment; surface a one-time admin
 * notice so the editor can re-pick the new template.
 * ============================================================ */

if ( ! function_exists( 'unysonplus_check_boxed_template_migration' ) ) :
function unysonplus_check_boxed_template_migration() {
	if ( ! current_user_can( 'edit_pages' ) ) { return; }
	if ( get_transient( 'unysonplus_boxed_migration_dismissed' ) ) { return; }

	$pages = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'posts_per_page' => 25,
		'fields'         => 'ids',
		'meta_query'     => array( array(
			'key'   => '_wp_page_template',
			'value' => 'page-boxed.php',
		) ),
	) );

	if ( empty( $pages ) ) { return; }

	echo '<div class="notice notice-warning is-dismissible"><p><strong>Unyson+:</strong> The "Left Sidebar" template was renamed from <code>page-boxed.php</code> to <code>page-sidebar-left.php</code>. The following pages still reference the old slug and need their template re-assigned: ';
	$links = array();
	foreach ( $pages as $pid ) {
		$links[] = '<a href="' . esc_url( get_edit_post_link( $pid ) ) . '">' . esc_html( get_the_title( $pid ) ) . '</a>';
	}
	echo implode( ', ', $links );
	echo '.</p></div>';
}
endif;
add_action( 'admin_notices', 'unysonplus_check_boxed_template_migration' );

if ( ! function_exists( 'unysonplus_is_page_builder_post' ) ) :
function unysonplus_is_page_builder_post() {
	return function_exists( 'fw_ext_page_builder_is_builder_post' )
		&& fw_ext_page_builder_is_builder_post( get_the_ID() );
}
endif;

if ( ! function_exists( 'unysonplus_fw_container_class' ) ) :
/**
 * Normalize a container keyword to the UnysonPlus plugin's frontend-grid class
 * (frontend-grid.min.css ships .fw-container*). The theme uses the plugin grid
 * for layout, so legacy/saved values ('container' / 'container-fluid') from the
 * header/footer settings are mapped to their fw- equivalents at output time —
 * no option/DB migration needed. Anything already fw-prefixed or custom passes
 * through untouched.
 *
 * @param string $value Container keyword (e.g. 'container', 'container-fluid').
 * @return string fw- grid container class.
 */
function unysonplus_fw_container_class( $value = 'container' ) {
	switch ( $value ) {
		case 'container':       return 'fw-container';
		case 'container-fluid': return 'fw-container-fluid';
		default:                return $value;
	}
}
endif;

if ( ! function_exists( 'unysonplus_main_wrapper_open' ) ) :
/**
 * Opens the standard content wrapper:
 *   <div class="fw-container"><div class="with-sidebar [has-sidebar]">
 *     <main id="main" class="site-main {extra}" role="main">
 *
 * Page-builder posts get NO wrapper at all (the builder controls
 * its own container/grid). Sidebar is included only when the chosen
 * widget area is active.
 *
 * @param string $extra_main_classes Optional extra classes for <main>.
 */
function unysonplus_main_wrapper_open( $extra_main_classes = '' ) {
	$is_builder = unysonplus_is_page_builder_post();

	if ( ! $is_builder ) {
		$has_sidebar = unysonplus_has_default_sidebar();
		$width       = unysonplus_resolve_layout( 'width', 'default' );

		$wrapper_classes = array( 'with-sidebar' );
		if ( $has_sidebar ) { $wrapper_classes[] = 'has-sidebar'; }
		if ( in_array( $width, array( 'narrow', 'wide', 'full' ), true ) ) {
			$wrapper_classes[] = 'layout-width-' . $width;
		}

		// Width=full skips the container constraint so content can go edge-to-edge.
		// Layout uses the UnysonPlus plugin's frontend-grid (.fw-container*).
		$container_class = ( $width === 'full' ) ? 'fw-container-fluid' : 'fw-container';
		echo '<div class="' . esc_attr( $container_class ) . '"><div class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '">';
	}

	/**
	 * Fires just before <main> opens. Use to inject banners, breadcrumbs,
	 * or sticky bars inside the content container.
	 */
	do_action( 'unysonplus_before_main' );

	$main_classes = 'site-main';
	if ( $extra_main_classes ) { $main_classes .= ' ' . $extra_main_classes; }
	echo '<main id="main" class="' . esc_attr( $main_classes ) . '" role="main">';
}
endif;

if ( ! function_exists( 'unysonplus_main_wrapper_close' ) ) :
function unysonplus_main_wrapper_close() {
	$is_builder = unysonplus_is_page_builder_post();

	echo '</main>';

	/**
	 * Fires just after </main> closes, before the sidebar renders.
	 * Use to inject post-navigation, related posts, social share, etc.
	 */
	do_action( 'unysonplus_after_main' );

	if ( ! $is_builder ) {
		if ( unysonplus_has_default_sidebar() ) {
			unysonplus_render_default_sidebar();
		}
		echo '</div></div>';
	}
}
endif;


/* ============================================================
 * Smooth scroll (CSS-only, emitted in <head>)
 * ============================================================ */

if ( ! function_exists( 'unysonplus_emit_smooth_scroll_css' ) ) :
function unysonplus_emit_smooth_scroll_css() {
	if ( unysonplus_layout_get( 'layout_smooth_scroll', 'no' ) !== 'yes' ) { return; }
	echo '<style id="unysonplus-smooth-scroll">html{scroll-behavior:smooth;}</style>' . "\n";
}
endif;
add_action( 'wp_head', 'unysonplus_emit_smooth_scroll_css', 25 );
