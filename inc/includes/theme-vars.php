<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }

if ( ! function_exists( 'unysonplus_background_pro_css_vars' ) ) :
/**
 * Turn a Background Pro option value into a set of CSS custom properties
 * ({$prefix}-color / -image / -position / -repeat / -attachment / -size). The
 * image layer stacks on top of the gradient (image, gradient) so both show.
 * Mirrors the Site Background parsing (see below) but with a configurable prefix
 * so any surface can consume a Background Pro. Video is not applied.
 *
 * @param array  $bg     Background Pro option value.
 * @param string $prefix e.g. '--overlay-bg'.
 * @return array<string,string> var => value
 */
function unysonplus_background_pro_css_vars( $bg, $prefix ) {
	$out = array();
	if ( ! is_array( $bg ) || ! $bg || ! function_exists( 'fw_akg' ) ) { return $out; }

	$color_val = fw_akg( 'color/value', $bg );
	if ( is_array( $color_val ) && function_exists( 'unysonplus_get_option_color_picker' ) ) {
		$color = unysonplus_get_option_color_picker( $color_val );
		if ( is_string( $color ) && '' !== $color ) { $out[ $prefix . '-color' ] = $color; }
	}

	$images  = array();
	$img_url = fw_akg( 'image/src/url', $bg );
	if ( $img_url ) {
		$images[] = 'url(' . esc_url_raw( $img_url ) . ')';
		$pos      = fw_akg( 'image/position', $bg, 'center center' );
		$rep      = fw_akg( 'image/repeat', $bg, 'no-repeat' );
		$att      = fw_akg( 'image/attachment', $bg, 'scroll' );
		$size_sel = fw_akg( 'image/size/selected', $bg, 'cover' );
		$size     = ( 'custom' === $size_sel ) ? fw_akg( 'image/size/custom', $bg, 'auto' ) : $size_sel;
		if ( $pos )  { $out[ $prefix . '-position' ]   = $pos; }
		if ( $rep )  { $out[ $prefix . '-repeat' ]     = $rep; }
		if ( $att )  { $out[ $prefix . '-attachment' ] = $att; }
		if ( $size ) { $out[ $prefix . '-size' ]       = $size; }
	}

	$stops = fw_akg( 'gradient/data/stops', $bg );
	if ( is_array( $stops ) && count( $stops ) >= 2
		&& class_exists( 'FW_Option_Type_Gradient_V2' )
		&& method_exists( 'FW_Option_Type_Gradient_V2', 'to_css' ) ) {
		$grad = FW_Option_Type_Gradient_V2::to_css( fw_akg( 'gradient/data', $bg ) );
		if ( $grad ) {
			$images[] = $grad;
			// A canvas GRADIENT spans the document, so it scrolls with the page: that is what makes it read
			// as one long wash. The body rule's fallback is `fixed`, which maps the gradient to the VIEWPORT
			// instead — every screenful then re-renders the whole ramp, so a mid-gradient stop (a teal, say)
			// showed up as a tint on every screen that the source only reaches near the bottom. Only the image
			// branch above ever set this var, so a gradient-only background always took that fallback.
			if ( ! isset( $out[ $prefix . '-attachment' ] ) ) {
				$g_att = fw_akg( 'gradient/attachment', $bg, 'scroll' );
				$out[ $prefix . '-attachment' ] = ( 'fixed' === $g_att ) ? 'fixed' : 'scroll';
			}
		}
	}

	if ( $images ) { $out[ $prefix . '-image' ] = implode( ', ', $images ); }
	return $out;
}
endif;

if ( ! function_exists( 'unysonplus_preset_color_to_css' ) ) :
/**
 * Resolve a compact preset-colour value to a CSS colour string.
 * Accepts the { predefined:'bg-red'|'text-red', custom:'#hex' } shape from
 * sc_color_field_compact (predefined → live-linked var(--color-<slug>)), or a
 * legacy plain hex / rgba string (passed through). Returns '' when empty.
 *
 * @param mixed $value
 * @return string
 */
function unysonplus_preset_color_to_css( $value ) {
	if ( is_array( $value ) ) {
		if ( ! empty( $value['predefined'] ) ) {
			$slug = preg_replace( '/^(?:text|bg)-/', '', (string) $value['predefined'] );
			$slug = preg_replace( '/[^a-z0-9\-]/', '', strtolower( $slug ) );
			return $slug !== '' ? 'var(--color-' . $slug . ')' : '';
		}
		return ! empty( $value['custom'] ) ? (string) $value['custom'] : '';
	}
	return is_string( $value ) ? $value : '';
}
endif;

if ( ! function_exists( 'unysonplus_preset_color_to_hex' ) ) :
/**
 * Resolve a compact preset-colour value to an actual hex / rgba (presets via the
 * live palette map) — for luma / contrast checks that need real channel values.
 *
 * @param mixed $value
 * @return string
 */
function unysonplus_preset_color_to_hex( $value ) {
	if ( is_array( $value ) ) {
		if ( ! empty( $value['predefined'] ) ) {
			$slug = preg_replace( '/^(?:text|bg)-/', '', (string) $value['predefined'] );
			$slug = preg_replace( '/[^a-z0-9\-]/', '', strtolower( $slug ) );
			if ( $slug !== '' && function_exists( 'unysonplus_color_preset_slug_map' ) ) {
				$map = unysonplus_color_preset_slug_map();
				if ( ! empty( $map[ $slug ] ) ) { return (string) $map[ $slug ]; }
			}
			return '';
		}
		return ! empty( $value['custom'] ) ? (string) $value['custom'] : '';
	}
	return is_string( $value ) ? $value : '';
}
endif;

/**
 * Design-token CSS custom properties that style.css consumes (colors, header,
 * footer, layout). All values are GLOBAL (same on every page).
 *
 * On the FRONT END these are compiled into the generated CSS file by
 * inc/includes/hf-custom-css.php (no inline <style>); the file loads after
 * parent-style so the tokens win the cascade against any defaults in style.css.
 * In the ADMIN they are still emitted inline on admin_head so the page-builder
 * editor preview stays live.
 *
 * Reads Unyson options when the framework is active; falls back to neutral
 * defaults so a fresh install with the plugin inactive still renders cleanly.
 */

if ( ! function_exists( 'unysonplus_theme_vars_css' ) ) :
	/**
	 * Build the theme-vars CSS (minified `:root{}`), no <style> wrapper.
	 *
	 * @return string  '' when there are no vars.
	 */
	function unysonplus_theme_vars_css() {
		$vars = unysonplus_collect_theme_vars();
		if ( empty( $vars ) ) { return ''; }
		$css = ':root{';
		foreach ( $vars as $name => $value ) {
			$css .= $name . ':' . $value . ';';
		}
		$css .= '}';
		return $css;
	}
endif;

if ( ! function_exists( 'unysonplus_emit_theme_vars' ) ) :
	/** Inline emitter — admin only (front end uses the generated file). */
	function unysonplus_emit_theme_vars() {
		$css = unysonplus_theme_vars_css();
		if ( $css === '' ) { return; }
		echo '<style id="unysonplus-theme-vars">' . $css . '</style>'; // phpcs:ignore — CSS, values sanitized upstream
	}
endif;

if ( ! function_exists( 'unysonplus_theme_vars_defaults' ) ) :
	/** Baseline defaults — used when Unyson is inactive or an option is empty. */
	function unysonplus_theme_vars_defaults( array &$out ) {
		$out += array(
			'--color-primary'     => '#0d6efd',
			'--color-accent'      => '#6610f2',
			'--color-text'        => '#212529',
			'--color-muted'       => '#6c757d',
			'--color-bg'          => '#ffffff',
			'--color-border'      => 'rgba(0, 0, 0, 0.1)', // global border/divider/card/input colour (General → Layout → Border Color)

			// NOTE: --font-body / --font-heading are owned by css-tokens.php (Typography
			// presets / pairing); style.css :root supplies the plugin-inactive fallback.

			'--header-bg'         => 'transparent', // unset Main Header Background = no fill (shows the page behind); a set colour overrides this below
			'--header-min-height' => '80px',
			'--header-sticky-bg'  => 'rgba(255,255,255,0.95)',
			'--header-z'          => '1030',

			'--topbar-bg'         => '#212529',
			'--topbar-color'      => '#ffffff',

			'--footer-bg'         => '#1a1a1a',
			'--footer-color'      => '#cccccc',
			'--footer-link-color' => '#ffffff',
			'--footer-pad-top'    => '2rem',
			'--footer-pad-bottom' => '1.5rem',

			'--menu-link-color'            => 'var(--color-text)',
			'--menu-link-hover'            => 'var(--color-primary)',
			'--menu-item-bg'               => 'transparent',
			'--menu-item-hover-bg'         => 'rgba(0, 0, 0, 0.05)',
			'--menu-dropdown-bg'           => '#ffffff',
			'--menu-dropdown-link'         => 'var(--color-text)',
			'--menu-dropdown-link-hover'   => 'var(--menu-dropdown-link)',
			'--menu-dropdown-item-hover-bg'=> 'rgba(0, 0, 0, 0.05)',
			'--menu-dropdown-width'        => '220px',
			'--menu-dropdown-radius'       => 'var(--radius)',

			/* Mega Menu panel (Header → Mega Menu). Consumed by the extension's
			   baseline stylesheet; these mirror its hard-coded fallbacks so the
			   look is identical whether or not the theme emits them. */
			'--mm-panel-bg'        => '#ffffff',
			'--mm-panel-border'    => '1px solid rgba(0, 0, 0, 0.08)',
			'--mm-panel-shadow'    => '0 8px 24px rgba(0, 0, 0, 0.12)',
			'--mm-panel-radius'    => '0',
			'--mm-panel-pad'       => '16px',
			'--mm-panel-min-width' => '240px',
			'--mm-col-gap'         => '24px',
			'--mm-col-min'         => '170px',
			'--mm-col-divider'     => '0 solid transparent',
			'--mm-col-divider-pad' => '0',

			'--site-max-width'    => '1320px',

			/* General → Layout defaults (Phase 2-4) */
			'--site-bg-color'         => '#ffffff',
			'--site-margin'           => '40px',
			'--site-frame-width'      => '0px',
			'--site-frame-color'      => '#222222',
			'--site-bg-image'         => 'none',
			'--container-gutter'      => '1.5rem',
			'--section-spacing-scale' => '1',

			/* Glass Surface design token — the frosted-glass "card on glass" look shared by
			   the .glass-surface utility and any shortcode that opts in (Section / Flexbox
			   Backdrop Blur). One place to tune the site's glass so every glass panel matches. */
			'--glass-blur'   => '10px',                       // backdrop-filter blur radius
			'--glass-bg'     => 'rgba(255, 255, 255, 0.6)',   // translucent fill the blur shows through
			'--glass-border' => '1px solid rgba(255, 255, 255, 0.35)',
			'--glass-radius' => 'var(--radius-lg, 16px)',
			'--glass-shadow' => '0 8px 32px rgba(0, 0, 0, 0.12)',

			'--preloader-bg'          => '#ffffff',
			'--scroll-progress-color' => 'var(--color-primary)',
			'--sidebar-width'         => '300px',
			'--vertical-header-width' => '260px',
		);
	}
endif;

if ( ! function_exists( 'unysonplus_theme_vars_color_presets' ) ) :
	/* Color design-tokens come from the user's Color Presets (Theme Settings →
		 * General → Color Presets) — the SAME source the plugin uses for its
		 * `.bg-{slug}` / `.text-{slug}` utilities and `:root --color-{slug}` vars.
		 * Without this the theme's hardcoded defaults (e.g. --color-accent: #6610f2)
		 * print later in <head> and override the user's preset (#fd7e14) on every
		 * `.bg-accent` element. Only the tokens that mirror a preset are pulled in;
		 * --color-text stays driven by Typography, --color-bg by the layout. */
	function unysonplus_theme_vars_color_presets( array &$out ) {
		if ( function_exists( 'unysonplus_color_preset_slug_map' ) ) {
			$color_presets = unysonplus_color_preset_slug_map();
			foreach ( array( 'primary', 'accent', 'muted' ) as $cslug ) {
				if ( ! empty( $color_presets[ $cslug ] ) ) {
					$out[ '--color-' . $cslug ] = $color_presets[ $cslug ];
				}
			}
		}
	}
endif;

if ( ! function_exists( 'unysonplus_theme_vars_build_layout' ) ) :
	/* General → Layout / Sidebar / Preloader (the tab was split into three
	 * storage keys; merge them so the reads below are key-name stable). */
	function unysonplus_theme_vars_build_layout() : array {
		$layout = array();
		foreach ( array( 'general_layout', 'general_sidebar', 'general_preloader', 'general_scroll', 'general_base' ) as $layout_opt ) {
			$layout_raw = fw_get_db_settings_option( $layout_opt, array() );
			if ( is_array( $layout_raw ) ) { $layout = array_merge( $layout, $layout_raw ); }
		}
		return $layout;
	}
endif;

if ( ! function_exists( 'unysonplus_theme_vars_layout' ) ) :
	/** General → Layout / Sidebar / Preloader / Scroll / Base tokens. */
	function unysonplus_theme_vars_layout( array &$out, array $layout ) {
		if ( ! empty( $layout ) ) {
			$lget = function ( $k, $d = '' ) use ( $layout ) {
				return ( isset( $layout[ $k ] ) && $layout[ $k ] !== '' && $layout[ $k ] !== null ) ? $layout[ $k ] : $d;
			};
			// A choice key (roundness, spacing scale …) read as a map index: a malformed saved value
			// (an array where a slug belongs) falls back to the default instead of fataling the CSS
			// rebuild with "Illegal offset type".
			$lkey = function ( $k, $d ) use ( $lget ) {
				$v = $lget( $k, $d );
				return is_scalar( $v ) ? (string) $v : $d;
			};

			// Site Width Mode sub-options now live in the site_width_mode multi-picker
			// (boxed / framed groups); read them via the width helper. site_boxed_width
			// is the sole writer of --site-max-width (the old layout_container_max_width
			// collision was removed — it clobbered Boxed Width and never styled .container).
			$wget = function_exists( 'unysonplus_width_get' ) ? 'unysonplus_width_get' : null;
			if ( $wget ) {
				if ( ( $v = $wget( 'site_boxed_margin' ) ) !== '' ) { $out['--site-margin']      = unysonplus_css_length( $v ); }
				if ( ( $v = $wget( 'site_frame_width' ) ) !== '' )  { $out['--site-frame-width'] = unysonplus_css_length( $v ); }
				if ( ( $v = unysonplus_preset_color_to_css( $wget( 'site_frame_color' ) ) ) !== '' ) { $out['--site-frame-color'] = $v; }
				if ( ( $v = $wget( 'site_boxed_width' ) ) !== '' )  { $out['--site-max-width']   = unysonplus_css_length( $v ); }
			}
			// Guard the length result too: a blank unit-input resolves to '' here, and
			// emitting `--container-gutter:` empty makes every `var(--container-gutter,
			// fallback)` substitute the empty token (NOT the fallback) → padding turns
			// invalid and collapses to 0 (this zeroed the full-width header rows). Keep
			// the 1.5rem default instead when the option yields no usable length.
			if ( ( $v = $lget( 'layout_container_gutter' ) ) !== '' ) {
				$gutter_len = unysonplus_css_length( $v );
				if ( $gutter_len !== '' ) { $out['--container-gutter'] = $gutter_len; }
			}
			if ( ( $v = $lget( 'layout_sidebar_width' ) ) !== '' )       { $out['--sidebar-width']    = unysonplus_css_length( $v ); }
			if ( ( $v = unysonplus_preset_color_to_css( $lget( 'layout_preloader_bg_color' ) ) ) !== '' )  { $out['--preloader-bg']     = $v; }
			if ( ( $v = unysonplus_preset_color_to_css( $lget( 'layout_scroll_progress_color' ) ) ) !== '' ) { $out['--scroll-progress-color'] = $v; }
			if ( ( $v = unysonplus_preset_color_to_css( $lget( 'layout_border_color' ) ) ) !== '' ) { $out['--color-border'] = $v; }

			// Section spacing scale
			$scale_map = array( 'compact' => '0.75', 'cozy' => '1', 'spacious' => '1.5' );
			$scale_key = $lkey( 'layout_section_spacing', 'cozy' );
			if ( isset( $scale_map[ $scale_key ] ) ) {
				$out['--section-spacing-scale'] = $scale_map[ $scale_key ];
			}

			// Site background (Background Pro: color + gradient + image layers).
			// Video is intentionally NOT applied to the site-wide body background.
			// Reuses the shared Background Pro parser with the --site-bg prefix; it
			// emits the SAME keys (--site-bg-color / -image / -position / -repeat /
			// -attachment / -size) as the old inline copy, overwriting the two default
			// keys in place and appending the rest (identical order).
			$bg = ( isset( $layout['site_background'] ) && is_array( $layout['site_background'] ) ) ? $layout['site_background'] : array();
			if ( $bg && function_exists( 'unysonplus_background_pro_css_vars' ) ) {
				$out = array_merge( $out, unysonplus_background_pro_css_vars( $bg, '--site-bg' ) );
			}

			// Site background pattern overlay (background-image option type: a
			// preset tiling PNG or a user-uploaded image). data/css/background-image
			// is already a ready-to-use `url("…")` (or 'none').
			$pattern_val = ( isset( $layout['site_bg_pattern'] ) && is_array( $layout['site_bg_pattern'] ) ) ? $layout['site_bg_pattern'] : array();
			$pattern_img = (string) fw_akg( 'data/css/background-image', $pattern_val, '' );
			if ( '' !== $pattern_img && 'none' !== $pattern_img ) {
				$out['--site-bg-pattern'] = $pattern_img;
			}

			// Border roundness → --radius tokens (drives cards / buttons / inputs / images).
			$round_map = array(
				'sharp'   => array( '0', '0', '0' ),
				'subtle'  => array( '0.25rem', '0.375rem', '0.5rem' ),
				'rounded' => array( '0.375rem', '0.75rem', '1rem' ),
				'soft'    => array( '0.5rem', '1rem', '1.5rem' ),
			);
			$round = $lkey( 'layout_roundness', 'subtle' );
			if ( isset( $round_map[ $round ] ) ) {
				$out['--radius']    = $round_map[ $round ][0];
				$out['--radius-sm'] = $round_map[ $round ][0];
				$out['--radius-md'] = $round_map[ $round ][1];
				$out['--radius-lg'] = $round_map[ $round ][2];
			}

			// Content/sidebar gap + reading (prose) width.
			if ( ( $v = $lget( 'layout_sidebar_gap' ) ) !== '' )  { $out['--content-sidebar-gap'] = unysonplus_css_length( $v ); }
			if ( ( $v = $lget( 'layout_prose_width' ) ) !== '' )  { $out['--prose-max-width']      = unysonplus_css_length( $v ); }

			// Sidebar styling + sticky offset (General → Sidebar). Colors run through
			// the preset resolver; lengths through unysonplus_css_length.
			if ( ( $v = $lget( 'layout_sidebar_sticky_offset' ) ) !== '' ) { $out['--sticky-sidebar-top'] = unysonplus_css_length( $v ); }
			$sbg = unysonplus_preset_color_to_css( $lget( 'layout_sidebar_bg' ) );
			if ( $sbg !== '' ) { $out['--sidebar-bg'] = $sbg; }
			if ( ( $v = $lget( 'layout_sidebar_padding' ) ) !== '' )      { $out['--sidebar-padding']      = unysonplus_css_length( $v ); }
			// Sidebar border — now the combined multi-inline row layout_sidebar_border =>
			// { width:{value,unit}, style, color:{predefined,custom} }. Falls back to the
			// legacy flat leaves layout_sidebar_border_{width,color}.
			$sb = $lget( 'layout_sidebar_border' );
			if ( is_array( $sb ) && ( isset( $sb['width'] ) || isset( $sb['color'] ) ) ) {
				if ( isset( $sb['width'] ) && ( $sbw = unysonplus_css_length( $sb['width'] ) ) !== '' ) { $out['--sidebar-border-width'] = $sbw; }
				if ( isset( $sb['style'] ) && $sb['style'] !== '' ) { $out['--sidebar-border-style'] = $sb['style']; }
				$sbc = isset( $sb['color'] ) ? unysonplus_preset_color_to_css( $sb['color'] ) : '';
				if ( $sbc !== '' ) { $out['--sidebar-border-color'] = $sbc; }
			} else {
				if ( ( $v = $lget( 'layout_sidebar_border_width' ) ) !== '' ) { $out['--sidebar-border-width'] = unysonplus_css_length( $v ); }
				$sbc = unysonplus_preset_color_to_css( $lget( 'layout_sidebar_border_color' ) );
				if ( $sbc !== '' ) { $out['--sidebar-border-color'] = $sbc; }
			}
			if ( ( $v = $lget( 'layout_sidebar_radius' ) ) !== '' )       { $out['--sidebar-radius']       = unysonplus_css_length( $v ); }
			if ( ( $v = $lget( 'layout_sidebar_widget_spacing' ) ) !== '' ) { $out['--widget-spacing']     = unysonplus_css_length( $v ); }
			if ( ( $v = $lget( 'layout_sidebar_widget_title_size' ) ) !== '' ) { $out['--widget-title-size'] = unysonplus_css_length( $v ); }
			if ( ( $v = $lget( 'layout_sidebar_widget_title_weight' ) ) !== '' ) { $out['--widget-title-weight'] = preg_replace( '/[^0-9]/', '', (string) $v ); }
			if ( $lget( 'layout_sidebar_widget_title_uppercase' ) === 'yes' ) { $out['--widget-title-transform'] = 'uppercase'; }
			$wtc = unysonplus_preset_color_to_css( $lget( 'layout_sidebar_widget_title_color' ) );
			if ( $wtc !== '' ) { $out['--widget-title-color'] = $wtc; }

			// General → Base: selection / scrollbar / focus outline (all opt-in).
			$sel_bg = unysonplus_preset_color_to_css( $lget( 'base_selection_bg' ) );
			if ( $sel_bg !== '' ) { $out['--selection-bg'] = $sel_bg; }
			$sel_fg = unysonplus_preset_color_to_css( $lget( 'base_selection_color' ) );
			if ( $sel_fg !== '' ) { $out['--selection-color'] = $sel_fg; }
			$sb_color = unysonplus_preset_color_to_css( $lget( 'base_scrollbar_color' ) );
			if ( $sb_color !== '' ) { $out['--scrollbar-color'] = $sb_color; }
			if ( ( $v = $lget( 'base_scrollbar_width' ) ) !== '' ) { $out['--scrollbar-width'] = unysonplus_css_length( $v ); }
			$focus_c = unysonplus_preset_color_to_css( $lget( 'base_focus_color' ) );
			if ( $focus_c !== '' ) { $out['--focus-color'] = $focus_c; }
			if ( ( $v = $lget( 'base_focus_width' ) ) !== '' ) { $out['--focus-width'] = unysonplus_css_length( $v ); }

			// General → Layout: responsive container max-widths (per device).
			// Container Width — one responsive control ({base:Phone, md:Tablet, lg:Desktop},
			// each a CSS length string; mobile-first, a blank device inherits the smaller).
			// Emits the --container-max-* vars consumed by .fw-container / .container.
			$cw = $lget( 'layout_container_width' );
			if ( is_array( $cw ) ) {
				$cw_base = unysonplus_css_length( isset( $cw['base'] ) ? $cw['base'] : '' );
				$cw_md   = unysonplus_css_length( isset( $cw['md'] ) ? $cw['md'] : '' );
				$cw_lg   = unysonplus_css_length( isset( $cw['lg'] ) ? $cw['lg'] : '' );
				$cw_tablet  = ( $cw_md !== '' ) ? $cw_md : $cw_base;
				$cw_desktop = ( $cw_lg !== '' ) ? $cw_lg : $cw_tablet;
				if ( $cw_base    !== '' ) { $out['--container-max-mobile']  = $cw_base; }
				if ( $cw_tablet  !== '' ) { $out['--container-max-tablet']  = $cw_tablet; }
				if ( $cw_desktop !== '' ) { $out['--container-max-desktop'] = $cw_desktop; }
			} else {
				// Legacy fallback: pre-merge per-device keys (guard on resolved length).
				if ( ( $v = unysonplus_css_length( $lget( 'layout_container_width_desktop' ) ) ) !== '' ) { $out['--container-max-desktop'] = $v; }
				if ( ( $v = unysonplus_css_length( $lget( 'layout_container_width_tablet' ) ) ) !== '' )  { $out['--container-max-tablet']  = $v; }
				if ( ( $v = unysonplus_css_length( $lget( 'layout_container_width_mobile' ) ) ) !== '' )  { $out['--container-max-mobile']  = $v; }
			}
		}
	}
endif;

if ( ! function_exists( 'unysonplus_theme_vars_header_layout' ) ) :
	/** Header → Layout tokens (needs $layout for the legacy vertical-width fallback). */
	function unysonplus_theme_vars_header_layout( array &$out, array $layout ) {
		// Header layout — container, min_height, bg_color, topbar.
		$header_layout = fw_get_db_settings_option( 'header_layout', array() );
		if ( is_array( $header_layout ) ) {
			$hbg = isset( $header_layout['bg_color'] ) ? unysonplus_preset_color_to_css( $header_layout['bg_color'] ) : '';
			if ( $hbg !== '' ) {
				$out['--header-bg']        = $hbg;
				// color-mix keeps the ~95% sticky tint working for a preset var() too.
				$out['--header-sticky-bg'] = 'color-mix(in srgb, ' . $hbg . ' 95%, transparent)';
			}
			// Two-state model: the SCROLLED header fill (Header -> Layout -> Appearance on scroll ->
			// Scrolled Background). Consumed by .site-header--scroll-change.is-stuck; the CSS falls back
			// to --header-bg when this is unset, so a scroll-change header with no scrolled colour just
			// keeps its at-top fill.
			$hsbg = isset( $header_layout['scroll_bg_color'] ) ? unysonplus_preset_color_to_css( $header_layout['scroll_bg_color'] ) : '';
			if ( $hsbg !== '' ) { $out['--header-scroll-bg'] = $hsbg; }

			// Glass TINT: the frost rule mixes the fill 72% with transparent so an OPAQUE colour reads
			// as translucent. A colour that already has alpha must NOT be mixed again — doing so
			// double-applies transparency (a captured rgba(...,.9) rendered at .65). Detect an
			// already-translucent fill and pin the mix to 100% for that state.
			$is_translucent = function ( $c ) {
				$c = strtolower( trim( (string) $c ) );
				if ( $c === '' ) { return false; }
				if ( strpos( $c, 'transparent' ) !== false ) { return true; }
				// rgba()/hsla() with an alpha below 1, or an 8-digit hex whose alpha byte is not ff.
				if ( preg_match( '/(?:rgba|hsla)\([^)]*[,\/]\s*(0|0?\.[0-9]+)\s*\)/', $c ) ) { return true; }
				if ( preg_match( '/^#[0-9a-f]{6}([0-9a-f]{2})$/', $c, $m ) && strtolower( $m[1] ) !== 'ff' ) { return true; }
				return false;
			};
			if ( isset( $out['--header-bg'] ) && $is_translucent( $out['--header-bg'] ) ) {
				$out['--header-glass-tint'] = '100%';
			}
			if ( $hsbg !== '' && $is_translucent( $hsbg ) ) {
				$out['--header-scroll-glass-tint'] = '100%';
			}
			$hmh = unysonplus_css_length( isset( $header_layout['min_height'] ) ? $header_layout['min_height'] : '' );
			if ( $hmh !== '' ) { $out['--header-min-height'] = $hmh; }

			// Header Container Width (Fixed-Width mode) — CONTENT width for the header's own
			// .fw-container, independent of the site-wide Container Width the footer/body use.
			// Consumed by the scoped `.site-header .fw-container` max-width rule in style.css,
			// which falls back to --container-max-desktop when this is unset.
			$hcw = unysonplus_css_length( isset( $header_layout['container_width'] ) ? $header_layout['container_width'] : '' );
			if ( $hcw !== '' ) { $out['--header-container-max'] = $hcw; }

			// mobile_min_height consolidated to a top-level key (Header → Mobile & Tablet).
			$hmh_mobile = unysonplus_css_length( fw_get_db_settings_option( 'mobile_min_height', '' ) );
			if ( $hmh_mobile !== '' ) { $out['--header-min-height-mobile'] = $hmh_mobile; }

			// Sticky-shrink logo height (Behavior = Sticky + Shrink); CSS falls back to 40px.
			$shrink = unysonplus_css_length( isset( $header_layout['sticky_shrink_height'] ) ? $header_layout['sticky_shrink_height'] : '' );
			if ( $shrink !== '' ) { $out['--header-shrink-logo'] = $shrink; }

			// Scrolled Header Height — the numeric counterpart to scroll_shrink. Unset, the CSS
			// falls back to --header-min-height, so the stuck row keeps the at-rest height.
			$hsh = unysonplus_css_length( isset( $header_layout['scroll_height'] ) ? $header_layout['scroll_height'] : '' );
			if ( $hsh !== '' ) { $out['--header-height-stuck'] = $hsh; }

			// Scrolled link colour — only meaningful with Change-appearance-on-scroll; the CSS
			// scopes it to .site-header--scroll-change.is-stuck so it can't leak into the at-top look.
			$hslc = isset( $header_layout['scroll_link_color'] ) ? unysonplus_preset_color_to_css( $header_layout['scroll_link_color'] ) : '';
			if ( $hslc !== '' ) { $out['--header-scroll-link'] = $hslc; }

			// Glass blur radius (both glass states). CSS falls back to the historic 10px.
			$hgb = unysonplus_css_length( isset( $header_layout['header_glass_blur'] ) ? $header_layout['header_glass_blur'] : '' );
			if ( $hgb !== '' ) { $out['--glass-blur'] = $hgb; }
			$hgs = isset( $header_layout['header_glass_saturate'] ) ? (int) $header_layout['header_glass_saturate'] : 0;
			if ( $hgs > 0 && $hgs !== 140 ) { $out['--glass-saturate'] = round( $hgs / 100, 2 ); }

			// Shadow depth — the toggles stay boolean; this scales what they emit. `medium` is the
			// historic value, so an unset/default install renders exactly as before.
			$hsd = isset( $header_layout['header_shadow_depth'] ) ? (string) $header_layout['header_shadow_depth'] : '';
			$shadow_map = array(
				'soft'   => '0 3px 12px -8px rgba(15, 23, 42, .22)',
				'strong' => '0 12px 38px -12px rgba(15, 23, 42, .42)',
			);
			if ( isset( $shadow_map[ $hsd ] ) ) { $out['--header-shadow'] = $shadow_map[ $hsd ]; }

			// NOTE: the floating-design geometry overrides (offset / radius / inset) are NOT emitted
			// here. unysonplus_header_design_css_vars() writes those vars onto the header ELEMENT,
			// so a :root value would always lose the cascade to the design preset. They live beside
			// the presets in inc/includes/layout.php instead.

			// Header row vertical alignment + element gap (all header rows/columns).
			$valign_map = array( 'top' => 'flex-start', 'center' => 'center', 'bottom' => 'flex-end' );
			$valign = isset( $header_layout['header_valign'] ) ? (string) $header_layout['header_valign'] : '';
			if ( isset( $valign_map[ $valign ] ) && $valign !== 'center' ) { $out['--header-valign'] = $valign_map[ $valign ]; }
			$egap = unysonplus_css_length( isset( $header_layout['header_element_gap'] ) ? $header_layout['header_element_gap'] : '' );
			if ( $egap !== '' ) { $out['--header-element-gap'] = $egap; }

			// Mobile drawer PANEL appearance — consolidated to TOP-LEVEL keys (Header → Mobile
			// & Tablet). The drawer used to inherit the desktop menu palette (tuned for a header
			// over a hero), so its links rendered washed-out on a solid panel. These scope the
			// panel's own look; each falls back in CSS to a legible default.
			$dbg = unysonplus_preset_color_to_css( fw_get_db_settings_option( 'drawer_bg', '' ) );
			if ( $dbg !== '' ) { $out['--drawer-bg'] = $dbg; }
			$dlc = unysonplus_preset_color_to_css( fw_get_db_settings_option( 'drawer_link_color', '' ) );
			if ( $dlc !== '' ) { $out['--drawer-color'] = $dlc; }
			$dla = unysonplus_preset_color_to_css( fw_get_db_settings_option( 'drawer_link_active_color', '' ) );
			if ( $dla !== '' ) { $out['--drawer-active'] = $dla; }
			$dls = unysonplus_css_length( fw_get_db_settings_option( 'drawer_link_size', '' ) );
			if ( $dls !== '' ) { $out['--drawer-link-size'] = $dls; }
			$dis = unysonplus_css_length( fw_get_db_settings_option( 'drawer_item_spacing', '' ) );
			if ( $dis !== '' ) { $out['--drawer-item-gap'] = $dis; }
			$dal_map = array( 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' );
			$dal_txt = array( 'left' => 'left', 'center' => 'center', 'right' => 'right' );
			$dal = (string) fw_get_db_settings_option( 'drawer_align', 'left' );
			if ( isset( $dal_map[ $dal ] ) && $dal !== 'left' ) {
				$out['--drawer-align']      = $dal_map[ $dal ];
				$out['--drawer-text-align'] = $dal_txt[ $dal ];
			}

			// Mobile BAR background (Header → Mobile & Tablet). A top-level key, applied by
			// header-footer-builder.css only below the collapse width, so the desktop header
			// keeps its own background.
			$mbar = fw_get_db_settings_option( 'mobile_bar_bg', '' );
			$mbar_css = is_string( $mbar ) ? unysonplus_preset_color_to_css( $mbar ) : '';
			if ( $mbar_css !== '' ) { $out['--mobile-bar-bg'] = $mbar_css; }

			// Drawer MOTION & SCRIM (Phase 2, Header → Mobile & Tablet). Panel size/shape + the
			// dim overlay. Consumed by .primary-navigation-drawer in style.css; each falls back to
			// a sensible default when unset.
			$dw = unysonplus_css_length( fw_get_db_settings_option( 'drawer_width', '' ) );
			if ( $dw !== '' ) { $out['--drawer-width'] = $dw; }
			$drad = unysonplus_css_length( fw_get_db_settings_option( 'drawer_radius', '' ) );
			if ( $drad !== '' ) { $out['--drawer-radius'] = $drad; }
			$dsc = unysonplus_preset_color_to_css( fw_get_db_settings_option( 'drawer_scrim_color', '' ) );
			if ( $dsc !== '' ) { $out['--drawer-scrim-color'] = $dsc; }
			$dso = fw_get_db_settings_option( 'drawer_scrim_opacity', 50 );
			if ( is_numeric( $dso ) && (int) $dso !== 50 ) { $out['--drawer-scrim-opacity'] = round( max( 0, min( 100, (int) $dso ) ) / 100, 2 ); }
			$dsb = unysonplus_css_length( fw_get_db_settings_option( 'drawer_scrim_blur', '' ) );
			if ( $dsb !== '' ) { $out['--drawer-scrim-blur'] = $dsb; }
			$dimh = unysonplus_css_length( fw_get_db_settings_option( 'drawer_item_min_height', '' ) );
			if ( $dimh !== '' ) { $out['--drawer-item-minh'] = $dimh; }

			// Vertical header rail width. Now housed inside the header_mode multi-picker's
			// Vertical reveals — the accessor resolves the active mode's value (and the
			// legacy flat key); fall back to the older general_layout key too.
			$vw_raw = function_exists( 'unysonplus_header_layout_get' )
				? unysonplus_header_layout_get( 'vertical_width', isset( $layout['layout_vertical_width'] ) ? $layout['layout_vertical_width'] : '' )
				: ( ! empty( $header_layout['vertical_width'] ) ? $header_layout['vertical_width'] : ( isset( $layout['layout_vertical_width'] ) ? $layout['layout_vertical_width'] : '' ) );
			$vw = unysonplus_css_length( $vw_raw );
			if ( $vw !== '' ) { $out['--vertical-header-width'] = $vw; }

			// NOTE: Header Design sub-option CSS vars (--header-design-*) are emitted as
			// an inline style on the <header> in template-parts/header-builder.php instead
			// of here — that is rendered per-request (always fresh) and doesn't depend on
			// the cached generated CSS file this :root block is written into.

			// Overlay Fullscreen background (Background Pro: color + gradient + image).
			// Housed in the header_mode → overlay reveal; applies to both Panel and Radial.
			$ov_bg = ( isset( $header_layout['header_mode']['overlay']['overlay_background'] ) && is_array( $header_layout['header_mode']['overlay']['overlay_background'] ) )
				? $header_layout['header_mode']['overlay']['overlay_background'] : array();
			if ( $ov_bg && function_exists( 'unysonplus_background_pro_css_vars' ) ) {
				$out = array_merge( $out, unysonplus_background_pro_css_vars( $ov_bg, '--overlay-bg' ) );
			}

			// Radial disc fill (Background Pro, Video disabled). Housed in the
			// overlay_style → radial reveal; consumed as --radial-disc-color / -image.
			// (Concentric has no fill of its own — its rings use --overlay-bg-* above.)
			$rd_bg = ( isset( $header_layout['header_mode']['overlay']['overlay_style']['radial']['radial_disc_bg'] ) && is_array( $header_layout['header_mode']['overlay']['overlay_style']['radial']['radial_disc_bg'] ) )
				? $header_layout['header_mode']['overlay']['overlay_style']['radial']['radial_disc_bg'] : array();
			if ( $rd_bg && function_exists( 'unysonplus_background_pro_css_vars' ) ) {
				$out = array_merge( $out, unysonplus_background_pro_css_vars( $rd_bg, '--radial-disc' ) );
			}

			// Concentric ring opacity (slider 0-100). Emitted as --cc-bg-opacity (0-1);
			// only when < 100, since 100 = the solid default the CSS already assumes.
			$cc_op = fw_akg( 'header_mode/overlay/overlay_bg_opacity', $header_layout, 100 );
			$cc_op = is_numeric( $cc_op ) ? (int) $cc_op : 100;
			if ( $cc_op >= 0 && $cc_op < 100 ) {
				$out['--cc-bg-opacity'] = round( $cc_op / 100, 3 );
			}

			// Duotone second colour → --cc-duotone-color (compact preset or legacy hex).
			$cc_dc_color = unysonplus_preset_color_to_css( fw_akg( 'header_mode/overlay/overlay_duotone_color', $header_layout ) );
			if ( $cc_dc_color !== '' ) { $out['--cc-duotone-color'] = $cc_dc_color; }
		}

		// Top Bar / Bottom Bar styling (bg, typography, link, borders) is compiled
		// into the generated header/footer CSS file (inc/includes/hf-custom-css.php),
		// not emitted as :root tokens here.
	}
endif;

if ( ! function_exists( 'unysonplus_theme_vars_header_logo' ) ) :
	/** Header → Identity logo width + site-title typography tokens. */
	function unysonplus_theme_vars_header_logo( array &$out ) {
			// Logo width (Header → Identity) — an explicit display width overrides
			// the header-height cap (see .site-title img in style.css).
			// Use the FLATTENED logo config (leaf values hoisted out of
			// header_logo[logo_type][simple|custom][...]) — the raw nested option keeps
			// title_size / title_weight / width under logo_type/custom, so reading them off
			// the top level always came back empty and --site-title-size never emitted (the
			// Site Converter's captured 30px logo size silently fell back to the default).
			$header_logo = function_exists( 'unysonplus_header_logo_cfg' )
				? unysonplus_header_logo_cfg()
				: fw_get_db_settings_option( 'header_logo', array() );
			if ( is_array( $header_logo ) ) {
				$logo_w = unysonplus_css_length( isset( $header_logo['width'] ) ? $header_logo['width'] : '' );
				if ( $logo_w !== '' ) {
					$out['--logo-width']      = $logo_w;
					$out['--logo-max-height'] = 'none';
				}
				// Text site-title typography (Header → Identity).
				$title_size = unysonplus_css_length( isset( $header_logo['title_size'] ) ? $header_logo['title_size'] : '' );
				if ( $title_size !== '' ) {
					$out['--site-title-size'] = $title_size;
				}
				if ( ! empty( $header_logo['title_weight'] ) ) {
					$out['--site-title-weight'] = $header_logo['title_weight'];
				}
				// Wordmark FACE (Header → Identity → Site Title Font Family). A brand wordmark is very often
				// set in a face of its own, so it gets its own control rather than inheriting the heading font.
				$title_family = '';
				if ( isset( $header_logo['title_font'] ) ) {
					$tf = $header_logo['title_font'];
					if ( is_array( $tf ) && ! empty( $tf['family'] ) ) { $title_family = (string) $tf['family']; }
					elseif ( is_string( $tf ) ) { $title_family = $tf; }
				}
				if ( '' !== trim( $title_family ) ) {
					// (the family value is already a usable CSS stack, as --font-heading / --font-body are)
					$title_family = trim( preg_replace( '/[{};<>]/', '', $title_family ) );
					if ( '' !== $title_family ) { $out['--site-title-font'] = $title_family; }
				}
			}
	}
endif;

if ( ! function_exists( 'unysonplus_theme_vars_footer' ) ) :
	/** Footer padding / colors / borders / background tokens. */
	function unysonplus_theme_vars_footer( array &$out ) {
		// Footer — direct settings keys (used by footer.php). Length values
		// get unit normalization; color values pass through.
		$footer_lengths = array(
			'footer_padding_top'    => '--footer-pad-top',
			'footer_padding_bottom' => '--footer-pad-bottom',
		);
		$footer_colors = array(
			'footer_text_color'       => '--footer-color',
			'footer_link_color'       => '--footer-link-color',
			'footer_link_hover_color' => '--footer-link-hover',
		);
		foreach ( $footer_lengths as $opt => $var ) {
			$css = unysonplus_css_length( fw_get_db_settings_option( $opt ) );
			if ( $css !== '' ) { $out[ $var ] = $css; }
		}

		// Exact padding overrides. The two selects above are constrained to the site spacing scale,
		// which tops out at 8rem; these win when filled so a 160-240px source footer is reachable.
		foreach ( array( 'footer_padding_top_custom' => '--footer-pad-top', 'footer_padding_bottom_custom' => '--footer-pad-bottom' ) as $opt => $var ) {
			$css = unysonplus_css_length( fw_get_db_settings_option( $opt ) );
			if ( $css !== '' ) { $out[ $var ] = $css; }
		}

		// Column gap between footer columns (grid / equal / auto rows all read it).
		$fcg = unysonplus_css_length( fw_get_db_settings_option( 'footer_col_gap' ) );
		if ( $fcg !== '' ) { $out['--footer-col-gap'] = $fcg; }

		// BOXED BODY (Footer → Layout → Boxed Body): the .footer__body panel's width cap, gutter, padding,
		// fill, border, radius and shadow → --footer-box-* (consumed by .footer--boxed .footer__body in
		// style.css; the class itself is added in footer.php). Nothing is emitted while it is off.
		$fbox = fw_get_db_settings_option( 'footer_body_box' );
		if ( is_array( $fbox ) && ! empty( $fbox['enabled'] ) && 'yes' === $fbox['enabled'] ) {
			$fb = isset( $fbox['yes'] ) && is_array( $fbox['yes'] ) ? $fbox['yes'] : array();
			foreach ( array( 'footer_box_max_width' => '--footer-box-max', 'footer_box_gutter' => '--footer-box-gutter', 'footer_box_padding_y' => '--footer-box-pad-y', 'footer_box_padding_x' => '--footer-box-pad-x', 'footer_box_radius' => '--footer-box-radius' ) as $opt => $var ) {
				$css = isset( $fb[ $opt ] ) ? unysonplus_css_length( $fb[ $opt ] ) : '';
				if ( $css !== '' ) { $out[ $var ] = $css; }
			}
			if ( ! empty( $fb['footer_box_background'] ) && function_exists( 'unysonplus_background_pro_css_vars' ) ) {
				$out = array_merge( $out, unysonplus_background_pro_css_vars( $fb['footer_box_background'], '--footer-box-bg' ) );
			}
			$fbb = isset( $fb['footer_box_border'] ) ? $fb['footer_box_border'] : null;
			if ( is_array( $fbb ) ) {
				$bw = isset( $fbb['width'] ) ? unysonplus_css_length( $fbb['width'] ) : '';
				$bc = isset( $fbb['color'] ) && function_exists( 'unysonplus_preset_color_to_css' ) ? unysonplus_preset_color_to_css( $fbb['color'] ) : '';
				$bs = ( isset( $fbb['style'] ) && $fbb['style'] !== '' ) ? preg_replace( '/[^a-z]/', '', (string) $fbb['style'] ) : 'solid';
				if ( $bw !== '' && $bc !== '' ) { $out['--footer-box-border'] = $bw . ' ' . $bs . ' ' . $bc; }
			}
			if ( ! empty( $fb['footer_box_shadow'] ) && class_exists( 'FW_Option_Type_Box_Shadow' ) ) {
				$sh = FW_Option_Type_Box_Shadow::to_css( $fb['footer_box_shadow'] );
				if ( $sh !== '' ) { $out['--footer-box-shadow'] = $sh; }
			}
		}

		// Columns kept below 768px. '1' is the historic behaviour, so only emit for a real change.
		$fmc = (string) fw_get_db_settings_option( 'footer_mobile_columns', '1' );
		if ( $fmc !== '' && $fmc !== '1' ) { $out['--footer-mobile-cols'] = (int) $fmc; }
		// A chosen hover colour must not be dimmed by the legacy fade.
		if ( unysonplus_preset_color_to_css( fw_get_db_settings_option( 'footer_link_hover_color' ) ) !== '' ) {
			$out['--footer-link-hover-opacity'] = '1';
		}
		foreach ( $footer_colors as $opt => $var ) {
			// Text/Link are compact palette-preset colours ({predefined,custom});
			// resolve to a CSS colour (tolerates the legacy plain-string value too).
			$val = fw_get_db_settings_option( $opt );
			$css = function_exists( 'unysonplus_preset_color_to_css' )
				? unysonplus_preset_color_to_css( $val )
				: ( is_string( $val ) ? $val : '' );
			if ( $css !== '' ) { $out[ $var ] = $css; }
		}
		// Footer top border — now the combined multi-inline row
		// footer_border_top => { width:{value,unit}, style, color:{predefined,custom} }.
		// Falls back to the legacy flat leaves (footer_border_top_{width,style,color}).
		$fbt = fw_get_db_settings_option( 'footer_border_top' );
		if ( is_array( $fbt ) && ( isset( $fbt['width'] ) || isset( $fbt['color'] ) ) ) {
			$fbt_w = isset( $fbt['width'] ) ? unysonplus_css_length( $fbt['width'] ) : '';
			$fbt_s = ( isset( $fbt['style'] ) && $fbt['style'] !== '' ) ? $fbt['style'] : '';
			$fbt_c = isset( $fbt['color'] ) && function_exists( 'unysonplus_preset_color_to_css' )
				? unysonplus_preset_color_to_css( $fbt['color'] ) : '';
		} else {
			$fbt_w = unysonplus_css_length( fw_get_db_settings_option( 'footer_border_top_width' ) );
			$c_leg = fw_get_db_settings_option( 'footer_border_top_color' );
			$fbt_c = function_exists( 'unysonplus_preset_color_to_css' )
				? unysonplus_preset_color_to_css( $c_leg ) : ( is_string( $c_leg ) ? $c_leg : '' );
			$s_leg = fw_get_db_settings_option( 'footer_border_top_style' );
			$fbt_s = is_string( $s_leg ) && $s_leg !== '' ? $s_leg : '';
		}
		if ( $fbt_w !== '' ) { $out['--footer-border-top-width'] = $fbt_w; }
		if ( $fbt_c !== '' ) { $out['--footer-border-top-color'] = $fbt_c; }
		if ( $fbt_s !== '' ) { $out['--footer-border-top-style'] = $fbt_s; }

		// Top-border EXTENT (Footer → Layout → Top Border Extent). Full = edge to edge
		// (nothing emitted; the border stays on the full-width <footer>). Container =
		// capped at the site container max-width; Custom = an exact width. This feeds
		// --footer-border-top-max, consumed by .footer--btop-contained::before in
		// style.css (max-width only caps, so on a narrow viewport the line still fits).
		// The `.footer--btop-contained` class itself is added in footer.php.
		$fbt_ext  = fw_get_db_settings_option( 'footer_border_top_extent' );
		$fbt_mode = ( is_array( $fbt_ext ) && isset( $fbt_ext['mode'] ) ) ? (string) $fbt_ext['mode'] : 'full';
		if ( $fbt_mode === 'container' ) {
			$out['--footer-border-top-max'] = 'var(--container-max-desktop, var(--site-max-width, 1170px))';
		} elseif ( $fbt_mode === 'custom' ) {
			$cw = isset( $fbt_ext['custom']['footer_border_top_extent_width'] )
				? unysonplus_css_length( $fbt_ext['custom']['footer_border_top_extent_width'] ) : '';
			if ( $cw !== '' ) { $out['--footer-border-top-max'] = $cw; }
		}

		// Footer background — the Background Pro control (colour + gradient + image,
		// video disabled). Emits --footer-bg-color / --footer-bg-image / -position /
		// -repeat / -attachment / -size, consumed by .footer in style.css. The image
		// var already folds any gradient overlay in, so no separate overlay is needed.
		$footer_bg   = fw_get_db_settings_option( 'footer_background', array() );
		$footer_vars = function_exists( 'unysonplus_background_pro_css_vars' )
			? unysonplus_background_pro_css_vars( $footer_bg, '--footer-bg' )
			: array();

		if ( ! empty( $footer_vars ) ) {
			$out = array_merge( $out, $footer_vars );
		} else {
			// Legacy fallback — honour the old footer_bg_color / _bg_image / _bg_overlay
			// values until the new Background Pro control is set, so upgrading an existing
			// site keeps its footer look. (Overlay composited over the image, tinted with
			// the legacy bg color, fallback black.)
			$legacy_color = fw_get_db_settings_option( 'footer_bg_color' );
			if ( ! empty( $legacy_color ) ) { $out['--footer-bg-color'] = $legacy_color; }

			$footer_bg_image = fw_get_db_settings_option( 'footer_bg_image' );
			if ( ! empty( $footer_bg_image['url'] ) ) {
				$f_img     = esc_url_raw( $footer_bg_image['url'] );
				$f_overlay = fw_get_db_settings_option( 'footer_bg_overlay' );
				$f_overlay = ( $f_overlay === null || $f_overlay === '' ) ? 80 : (int) $f_overlay;
				$f_overlay = max( 0, min( 100, $f_overlay ) );
				if ( $f_overlay > 0 && function_exists( 'unysonplus_color_with_alpha' ) ) {
					$f_tint = empty( $legacy_color ) ? 'rgba(0,0,0,1)' : $legacy_color;
					$f_ov   = unysonplus_color_with_alpha( $f_tint, $f_overlay / 100 );
					$out['--footer-bg-image'] = 'linear-gradient(0deg,' . $f_ov . ',' . $f_ov . '),url(' . $f_img . ')';
				} else {
					$out['--footer-bg-image'] = 'url(' . $f_img . ')';
				}
			}
		}
	}
endif;

if ( ! function_exists( 'unysonplus_theme_vars_typography' ) ) :
	/** Typography → body text color + link color / underline tokens. */
	function unysonplus_theme_vars_typography( array &$out ) {
		// Body text color from typography (aliases the existing --body-color emitted by css-tokens.php).
		$typography = fw_get_db_settings_option( 'typography', array() );
		if ( ! empty( $typography['body']['color'] ) ) {
			$out['--color-text'] = $typography['body']['color'];
		}
		// (--font-body / --font-heading + the heading scale come from css-tokens.php
		// — the Typography preset / pairing — not here.)
		// Body/content link colors (Typography — now compact palette presets; resolve
		// {predefined,custom} → var(--color-…)/hex, tolerating a legacy string). Emitted
		// only when set; style.css falls back to --color-primary so unset = current.
		$link_c = function ( $v ) {
			return function_exists( 'unysonplus_preset_color_to_css' ) ? unysonplus_preset_color_to_css( $v ) : ( is_string( $v ) ? $v : '' );
		};
		if ( ! empty( $typography['body_link'] ) && ( $lc = $link_c( $typography['body_link'] ) ) !== '' ) {
			$out['--body-link-color'] = $lc;
		}
		if ( ! empty( $typography['body_link_hover'] ) && ( $lh = $link_c( $typography['body_link_hover'] ) ) !== '' ) {
			$out['--body-link-hover'] = $lh;
		}
		// Body link underline (Typography → Body Link Underline). Drives the
		// text-decoration on prose links in both states; default 'hover' matches the
		// style.css fallbacks (none / underline), so it only emits when overridden.
		$blu = ! empty( $typography['body_link_underline'] ) ? $typography['body_link_underline'] : 'always'; // accessible default: in-text links underline at rest (link-in-text-block audit)
		if ( $blu === 'always' ) {
			$out['--body-link-decoration']       = 'underline';
			$out['--body-link-decoration-hover'] = 'underline';
		} elseif ( $blu === 'never' ) {
			$out['--body-link-decoration']       = 'none';
			$out['--body-link-decoration-hover'] = 'none';
		}
	}
endif;

if ( ! function_exists( 'unysonplus_theme_vars_header_menu' ) ) :
	/** Header → Menu styling → the --menu-* tokens. */
	function unysonplus_theme_vars_header_menu( array &$out ) {
		// Header → Menu styling (maps to the --menu-* tokens consumed by style.css).
		// Colors run through the preset resolver (predefined → a live-linked
		// var(--color-slug), custom → hex, legacy plain-hex string → passthrough).
		$menu = fw_get_db_settings_option( 'header_menu', array() );
		if ( is_array( $menu ) ) {
			// Top-level items.
			$mlc = unysonplus_preset_color_to_css( isset( $menu['menu_link_color'] ) ? $menu['menu_link_color'] : '' );
			if ( $mlc !== '' ) { $out['--menu-link-color'] = $mlc; }
			$mhc = unysonplus_preset_color_to_css( isset( $menu['menu_link_hover_color'] ) ? $menu['menu_link_hover_color'] : '' );
			if ( $mhc !== '' ) { $out['--menu-link-hover'] = $mhc; }
			$mib = unysonplus_preset_color_to_css( isset( $menu['menu_item_bg'] ) ? $menu['menu_item_bg'] : '' );
			if ( $mib !== '' ) { $out['--menu-item-bg'] = $mib; }
			$mihb = unysonplus_preset_color_to_css( isset( $menu['menu_item_hover_bg'] ) ? $menu['menu_item_hover_bg'] : '' );
			if ( $mihb !== '' ) { $out['--menu-item-hover-bg'] = $mihb; }
			$mpx = unysonplus_css_length( isset( $menu['menu_link_padding_x'] ) ? $menu['menu_link_padding_x'] : '' );
			if ( $mpx !== '' ) { $out['--menu-link-pad-x'] = $mpx; }
			$mpy = unysonplus_css_length( isset( $menu['menu_link_padding_y'] ) ? $menu['menu_link_padding_y'] : '' );
			if ( $mpy !== '' ) { $out['--menu-link-pad-y'] = $mpy; }
			$mfs = unysonplus_css_length( isset( $menu['menu_link_font_size'] ) ? $menu['menu_link_font_size'] : '' );
			if ( $mfs !== '' ) { $out['--menu-link-font-size'] = $mfs; }
			// Menu Link Font Weight (select slug '300'..'900'). Empty = the theme default (500).
			$mfw = isset( $menu['menu_link_font_weight'] ) ? preg_replace( '/[^0-9]/', '', (string) $menu['menu_link_font_weight'] ) : '';
			if ( $mfw !== '' ) { $out['--menu-link-font-weight'] = $mfw; }
			// Menu Link Letter Spacing (unit-input) + UPPERCASE (switch) — the `tracking-*` / `uppercase`
			// source utilities have their own native controls so a converted nav keeps its typographic feel.
			$mls = unysonplus_css_length( isset( $menu['menu_link_letter_spacing'] ) ? $menu['menu_link_letter_spacing'] : '' );
			if ( $mls !== '' ) { $out['--menu-link-letter-spacing'] = $mls; }
			if ( isset( $menu['menu_link_uppercase'] ) && ( 'yes' === $menu['menu_link_uppercase'] || true === $menu['menu_link_uppercase'] || '1' === (string) $menu['menu_link_uppercase'] ) ) {
				$out['--menu-link-transform'] = 'uppercase';
			}
			// Menu Font Family (typography-v2, family only). Empty = inherit the body font.
			// Composed with the same system-sans fallback stack the Typography tokens use.
			$mff = isset( $menu['menu_font']['family'] ) ? trim( (string) $menu['menu_font']['family'] ) : '';
			if ( $mff !== '' ) {
				$out['--menu-font-family'] = "'" . $mff . "', -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif";
			}

			// Dropdown / submenu.
			$mdd = unysonplus_preset_color_to_css( isset( $menu['menu_dropdown_bg'] ) ? $menu['menu_dropdown_bg'] : '' );
			if ( $mdd !== '' ) { $out['--menu-dropdown-bg'] = $mdd; }
			$mdl = unysonplus_preset_color_to_css( isset( $menu['menu_dropdown_link'] ) ? $menu['menu_dropdown_link'] : '' );
			if ( $mdl !== '' ) { $out['--menu-dropdown-link'] = $mdl; }
			$mdlh = unysonplus_preset_color_to_css( isset( $menu['menu_dropdown_link_hover'] ) ? $menu['menu_dropdown_link_hover'] : '' );
			if ( $mdlh !== '' ) { $out['--menu-dropdown-link-hover'] = $mdlh; }
			$mdih = unysonplus_preset_color_to_css( isset( $menu['menu_dropdown_item_hover_bg'] ) ? $menu['menu_dropdown_item_hover_bg'] : '' );
			if ( $mdih !== '' ) { $out['--menu-dropdown-item-hover-bg'] = $mdih; }
			$mdw = unysonplus_css_length( isset( $menu['menu_dropdown_width'] ) ? $menu['menu_dropdown_width'] : '' );
			if ( $mdw !== '' ) { $out['--menu-dropdown-width'] = $mdw; }
			$mdr = unysonplus_css_length( isset( $menu['menu_dropdown_radius'] ) ? $menu['menu_dropdown_radius'] : '' );
			if ( $mdr !== '' ) { $out['--menu-dropdown-radius'] = $mdr; }
		}
	}
endif;

if ( ! function_exists( 'unysonplus_theme_vars_mega_menu' ) ) :
	/** Header → Mega Menu → the --mm-* tokens. */
	function unysonplus_theme_vars_mega_menu( array &$out ) {
		// Header → Mega Menu (Panel). Only meaningful when the extension is active,
		// but reading the stored group is harmless either way. Folds into the --mm-*
		// tokens consumed by the extension's baseline stylesheet.
		$mega = fw_get_db_settings_option( 'mega_menu', array() );
		if ( is_array( $mega ) ) {
			// Panel design → shadow + border (+ inset top accent for 'top-accent').
			$mm_designs = array(
				'classic'    => array( '0 8px 24px rgba(0, 0, 0, 0.12)',  '1px solid rgba(0, 0, 0, 0.08)' ),
				'elevated'   => array( '0 18px 50px rgba(0, 0, 0, 0.22)', '1px solid rgba(0, 0, 0, 0.04)' ),
				'bordered'   => array( 'none',                            '1px solid rgba(0, 0, 0, 0.14)' ),
				'minimal'    => array( 'none',                            '0 solid transparent' ),
				'top-accent' => array( '0 10px 30px rgba(0, 0, 0, 0.12), inset 0 3px 0 0 var(--color-primary)', '1px solid rgba(0, 0, 0, 0.06)' ),
			);
			$mm_design = isset( $mega['mm_panel_design'] ) ? (string) $mega['mm_panel_design'] : 'classic';
			if ( isset( $mm_designs[ $mm_design ] ) && $mm_design !== 'classic' ) {
				$out['--mm-panel-shadow'] = $mm_designs[ $mm_design ][0];
				$out['--mm-panel-border'] = $mm_designs[ $mm_design ][1];
			}

			$mpbg = unysonplus_preset_color_to_css( isset( $mega['mm_panel_bg'] ) ? $mega['mm_panel_bg'] : '' );
			if ( $mpbg !== '' ) { $out['--mm-panel-bg'] = $mpbg; }
			$mpr = unysonplus_css_length( isset( $mega['mm_panel_radius'] ) ? $mega['mm_panel_radius'] : '' );
			if ( $mpr !== '' ) { $out['--mm-panel-radius'] = $mpr; }
			$mpp = unysonplus_css_length( isset( $mega['mm_panel_padding'] ) ? $mega['mm_panel_padding'] : '' );
			if ( $mpp !== '' ) { $out['--mm-panel-pad'] = $mpp; }
			$mpfs = unysonplus_css_length( isset( $mega['mm_panel_font_size'] ) ? $mega['mm_panel_font_size'] : '' );
			if ( $mpfs !== '' ) { $out['--mm-panel-font-size'] = $mpfs; }
			$mpmw = unysonplus_css_length( isset( $mega['mm_panel_max_width'] ) ? $mega['mm_panel_max_width'] : '' );
			if ( $mpmw !== '' ) { $out['--mm-panel-max-width'] = $mpmw; }

			// Boxed full-width: the panel becomes a centered card with side margins.
			if ( isset( $mega['mm_full_style'] ) && $mega['mm_full_style'] === 'boxed' ) {
				$out['--mm-full-max']   = ( $mpmw !== '' ) ? $mpmw : '1400px';
				$out['--mm-full-mx']    = 'auto';
				$out['--mm-full-inset'] = '16px';
				$out['--mm-full-pad']   = 'var(--mm-panel-pad, 22px)';
			}
			$mcg = unysonplus_css_length( isset( $mega['mm_col_gap'] ) ? $mega['mm_col_gap'] : '' );
			if ( $mcg !== '' ) { $out['--mm-col-gap'] = $mcg; }

			// Column dividers switch → a hairline + a half-gap inset so text clears the line.
			if ( ! empty( $mega['mm_col_dividers'] ) ) {
				$out['--mm-col-divider']     = '1px solid rgba(0, 0, 0, 0.10)';
				$out['--mm-col-divider-pad'] = 'calc(var(--mm-col-gap) / 2)';
			}

			// Column headings — style treatment (border / transform) + color/size/weight.
			switch ( isset( $mega['mm_heading_style'] ) ? (string) $mega['mm_heading_style'] : 'none' ) {
				case 'underline':
					$out['--mm-heading-border']      = '1px solid rgba(0, 0, 0, 0.12)';
					$out['--mm-heading-pad-bottom']  = '6px';
					break;
				case 'accent':
					$out['--mm-heading-border']      = '2px solid var(--color-primary)';
					$out['--mm-heading-pad-bottom']  = '6px';
					break;
				case 'uppercase':
					$out['--mm-heading-transform']   = 'uppercase';
					$out['--mm-heading-spacing']     = '0.05em';
					break;
			}
			$mhc = unysonplus_preset_color_to_css( isset( $mega['mm_heading_color'] ) ? $mega['mm_heading_color'] : '' );
			if ( $mhc !== '' ) { $out['--mm-heading-color'] = $mhc; }
			$mhs = unysonplus_css_length( isset( $mega['mm_heading_size'] ) ? $mega['mm_heading_size'] : '' );
			if ( $mhs !== '' ) { $out['--mm-heading-size'] = $mhs; }
			$mhw = isset( $mega['mm_heading_weight'] ) ? trim( (string) $mega['mm_heading_weight'] ) : '';
			if ( $mhw !== '' ) { $out['--mm-heading-weight'] = $mhw; }

			// Dropdown items — colors / size / spacing / description / icon.
			$mic = unysonplus_preset_color_to_css( isset( $mega['mm_item_color'] ) ? $mega['mm_item_color'] : '' );
			if ( $mic !== '' ) { $out['--mm-item-color'] = $mic; }
			$mihc = unysonplus_preset_color_to_css( isset( $mega['mm_item_hover_color'] ) ? $mega['mm_item_hover_color'] : '' );
			if ( $mihc !== '' ) { $out['--mm-item-hover-color'] = $mihc; }
			$mis = unysonplus_css_length( isset( $mega['mm_item_size'] ) ? $mega['mm_item_size'] : '' );
			if ( $mis !== '' ) { $out['--mm-item-size'] = $mis; }
			$mig = unysonplus_css_length( isset( $mega['mm_item_gap'] ) ? $mega['mm_item_gap'] : '' );
			if ( $mig !== '' ) { $out['--mm-item-gap'] = $mig; }
			$mdc = unysonplus_preset_color_to_css( isset( $mega['mm_desc_color'] ) ? $mega['mm_desc_color'] : '' );
			if ( $mdc !== '' ) { $out['--mm-desc-color'] = $mdc; }
			$mds = unysonplus_css_length( isset( $mega['mm_desc_size'] ) ? $mega['mm_desc_size'] : '' );
			if ( $mds !== '' ) { $out['--mm-desc-size'] = $mds; }
			$mics = unysonplus_css_length( isset( $mega['mm_icon_size'] ) ? $mega['mm_icon_size'] : '' );
			if ( $mics !== '' ) { $out['--mm-icon-size'] = $mics; }
			$micc = unysonplus_preset_color_to_css( isset( $mega['mm_icon_color'] ) ? $mega['mm_icon_color'] : '' );
			if ( $micc !== '' ) { $out['--mm-icon-color'] = $micc; }

			// Animation — closed-state transform per style (open state resets to none).
			$mm_anim_transforms = array(
				'fade'       => 'none',
				'slide-down' => 'translateY(-8px)',
				'slide-up'   => 'translateY(8px)',
				'zoom'       => 'scale(0.96)',
				'none'       => 'none',
			);
			$mm_anim = isset( $mega['mm_animation'] ) ? (string) $mega['mm_animation'] : '';
			if ( isset( $mm_anim_transforms[ $mm_anim ] ) ) {
				$out['--mm-anim-transform'] = $mm_anim_transforms[ $mm_anim ];
				// 'none' should also kill the timing so it's truly instant.
				if ( $mm_anim === 'none' ) { $out['--mm-anim-duration'] = '0s'; }
			}
			$mas = unysonplus_css_length( isset( $mega['mm_anim_speed'] ) ? $mega['mm_anim_speed'] : '' );
			if ( $mas !== '' ) { $out['--mm-anim-duration'] = $mas; }
			// Hover open delay (Hover mode only).
			$mhd = unysonplus_css_length( isset( $mega['mm_hover_delay'] ) ? $mega['mm_hover_delay'] : '' );
			if ( $mhd !== '' && ( ! isset( $mega['mm_open_on'] ) || $mega['mm_open_on'] === 'hover' ) ) {
				$out['--mm-open-delay'] = $mhd;
			}

			// Responsive (≤782px) — applied via vars inside the extension's mobile
			// media query. Two-up switches to a wrapping grid; the toggles hide bits.
			if ( isset( $mega['mm_mobile_columns'] ) && $mega['mm_mobile_columns'] === '2' ) {
				$out['--mm-mobile-dir']       = 'row';
				$out['--mm-mobile-col-basis'] = 'calc(50% - 4px)';
				$out['--mm-mobile-col-max']   = 'calc(50% - 4px)';
			}
			$mmg = unysonplus_css_length( isset( $mega['mm_mobile_gap'] ) ? $mega['mm_mobile_gap'] : '' );
			if ( $mmg !== '' ) { $out['--mm-mobile-gap'] = $mmg; }
			if ( ! empty( $mega['mm_mobile_hide_desc'] ) )  { $out['--mm-mobile-desc-display'] = 'none'; }
			if ( ! empty( $mega['mm_mobile_hide_icons'] ) ) { $out['--mm-mobile-icon-display'] = 'none'; }
		}
	}
endif;

if ( ! function_exists( 'unysonplus_theme_vars_social' ) ) :
	/** Social tab → the --social-* tokens. */
	function unysonplus_theme_vars_social( array &$out ) {
		// Social icon style (Social tab → social_style). Size/gap → lengths; colors →
		// the preset resolver. Shape / brand / hover-fx ride wrapper classes (set in
		// unysonplus_render_social_icons), not vars.
		$social = fw_get_db_settings_option( 'social_style', array() );
		if ( is_array( $social ) ) {
			$ss = ( isset( $social['group_social_style'] ) && is_array( $social['group_social_style'] ) ) ? $social['group_social_style'] : $social;
			$sz = unysonplus_css_length( isset( $ss['social_icon_size'] ) ? $ss['social_icon_size'] : '' );
			if ( $sz !== '' ) { $out['--social-size'] = $sz; }
			$sg = unysonplus_css_length( isset( $ss['social_icon_gap'] ) ? $ss['social_icon_gap'] : '' );
			if ( $sg !== '' ) { $out['--social-gap'] = $sg; }
			$sc = unysonplus_preset_color_to_css( isset( $ss['social_icon_color'] ) ? $ss['social_icon_color'] : '' );
			if ( $sc !== '' ) { $out['--social-icon-color'] = $sc; }
			$sb = unysonplus_preset_color_to_css( isset( $ss['social_icon_bg'] ) ? $ss['social_icon_bg'] : '' );
			if ( $sb !== '' ) { $out['--social-icon-bg'] = $sb; }
			// The outline styles' ring. Without its own token the border follows the glyph colour, so a faint
			// ring around a bright mark -- a very common chip treatment -- could not be expressed.
			$sbd = unysonplus_preset_color_to_css( isset( $ss['social_icon_border'] ) ? $ss['social_icon_border'] : '' );
			if ( $sbd !== '' ) { $out['--social-icon-border'] = $sbd; }
			$shc = unysonplus_preset_color_to_css( isset( $ss['social_icon_hover_color'] ) ? $ss['social_icon_hover_color'] : '' );
			if ( $shc !== '' ) { $out['--social-icon-hover-color'] = $shc; }
			$shb = unysonplus_preset_color_to_css( isset( $ss['social_icon_hover_bg'] ) ? $ss['social_icon_hover_bg'] : '' );
			if ( $shb !== '' ) { $out['--social-icon-hover-bg'] = $shb; }
		}
	}
endif;

if ( ! function_exists( 'unysonplus_collect_theme_vars' ) ) :
	/**
	 * Build the design-token map. Reads Unyson options when available;
	 * otherwise emits defaults only. Thin orchestrator over the per-concern
	 * helpers above — each appends its keys to $out IN ORDER (order matters:
	 * the CSS cascade and any serialize() of the result both depend on it).
	 *
	 * @return array<string,string>  --custom-property => value
	 */
	function unysonplus_collect_theme_vars() : array {
		$out = array();
		unysonplus_theme_vars_defaults( $out );

		if ( ! function_exists( 'fw_get_db_settings_option' ) ) {
			return $out;
		}

		unysonplus_theme_vars_color_presets( $out );

		// $layout is shared: the header-layout block reads it for the legacy
		// vertical-width fallback, so build it once and pass it to both.
		$layout = unysonplus_theme_vars_build_layout();
		unysonplus_theme_vars_layout( $out, $layout );
		unysonplus_theme_vars_header_layout( $out, $layout );

		unysonplus_theme_vars_header_logo( $out );
		unysonplus_theme_vars_footer( $out );
		unysonplus_theme_vars_typography( $out );
		unysonplus_theme_vars_header_menu( $out );
		unysonplus_theme_vars_mega_menu( $out );
		unysonplus_theme_vars_social( $out );

		// Drop any property that resolved to an empty value. A length saved with an
		// empty number (e.g. { value: '', unit: 'rem' }) becomes '', and an empty
		// custom property doesn't fall back: `var(--widget-spacing, 1.5rem)` then
		// yields nothing, so the sidebar widgets lost all spacing between them.
		return array_filter( $out, static function ( $v ) {
			return '' !== trim( (string) $v );
		} );
	}
endif;

if ( ! function_exists( 'unysonplus_css_length' ) ) :
	/**
	 * Normalize a length value: bare numbers get 'px' appended; anything
	 * already containing a unit (px, em, rem, %, vh, vw) passes through.
	 */
	function unysonplus_css_length( $value ) : string {
		// A unit-input value can arrive as a JSON STRING (e.g. '{"value":"1","unit":"px"}')
		// when it rides inside a `multi-inline` sub-field — that control stores the nested
		// unit-input's raw JSON rather than a decoded { value, unit } array. Decode it so
		// the length resolves instead of leaking the JSON into the CSS (the footer /
		// custom-styling border-width bug). Any other string falls through unchanged.
		if ( is_string( $value ) ) {
			$trimmed = trim( $value );
			if ( isset( $trimmed[0] ) && $trimmed[0] === '{' ) {
				$decoded = json_decode( $trimmed, true );
				if ( is_array( $decoded ) && ( isset( $decoded['value'] ) || isset( $decoded['unit'] ) ) ) {
					$value = $decoded;
				}
			}
		}
		// unit-input value: array( 'value' => '1.5', 'unit' => 'rem' ).
		if ( is_array( $value ) ) {
			if ( class_exists( 'FW_Option_Type_Unit_Input' ) ) {
				return FW_Option_Type_Unit_Input::to_string( $value );
			}
			$num = isset( $value['value'] ) ? trim( (string) $value['value'] ) : '';
			if ( $num === '' ) { return ''; }
			return $num . ( isset( $value['unit'] ) ? (string) $value['unit'] : '' );
		}
		$value = trim( (string) $value );
		if ( $value === '' ) { return ''; }
		return is_numeric( $value ) ? $value . 'px' : $value;
	}
endif;

if ( ! function_exists( 'unysonplus_color_with_alpha' ) ) :
	/**
	 * Convert a CSS color (#hex, rgb(), rgba()) to rgba() with the given
	 * alpha. Returns the input unchanged if the color can't be parsed.
	 */
	function unysonplus_color_with_alpha( string $color, float $alpha ) : string {
		$color = trim( $color );

		if ( preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $color, $m ) ) {
			$hex = $m[1];
			if ( strlen( $hex ) === 3 ) {
				$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
			}
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
			return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, rtrim( rtrim( sprintf( '%.2f', $alpha ), '0' ), '.' ) );
		}

		if ( preg_match( '/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i', $color, $m ) ) {
			return sprintf( 'rgba(%d,%d,%d,%s)', $m[1], $m[2], $m[3], rtrim( rtrim( sprintf( '%.2f', $alpha ), '0' ), '.' ) );
		}

		return $color;
	}
endif;

// Front end: theme vars are compiled into the generated CSS file
// (inc/includes/hf-custom-css.php). Admin keeps the inline emit for the
// page-builder editor preview.
add_action( 'admin_head', 'unysonplus_emit_theme_vars', 20 );
