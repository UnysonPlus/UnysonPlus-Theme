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
		if ( $grad ) { $images[] = $grad; }
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

if ( ! function_exists( 'unysonplus_collect_theme_vars' ) ) :
	/**
	 * Build the design-token map. Reads Unyson options when available;
	 * otherwise emits defaults only.
	 *
	 * @return array<string,string>  --custom-property => value
	 */
	function unysonplus_collect_theme_vars() : array {
		$unyson = function_exists( 'fw_get_db_settings_option' );

		// Defaults — used when Unyson is inactive or an option is empty.
		$out = array(
			'--color-primary'     => '#0d6efd',
			'--color-accent'      => '#6610f2',
			'--color-text'        => '#212529',
			'--color-muted'       => '#6c757d',
			'--color-bg'          => '#ffffff',

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
			'--menu-dropdown-link-hover'   => 'var(--menu-link-hover)',
			'--menu-dropdown-item-hover-bg'=> 'rgba(0, 0, 0, 0.05)',
			'--menu-dropdown-width'        => '220px',
			'--menu-dropdown-radius'       => 'var(--radius)',

			'--site-max-width'    => '1320px',

			/* General → Layout defaults (Phase 2-4) */
			'--site-bg-color'         => '#ffffff',
			'--site-margin'           => '40px',
			'--site-frame-width'      => '0px',
			'--site-frame-color'      => '#222222',
			'--site-bg-image'         => 'none',
			'--container-gutter'      => '1.5rem',
			'--section-spacing-scale' => '1',
			'--preloader-bg'          => '#ffffff',
			'--scroll-progress-color' => 'var(--color-primary)',
			'--sidebar-width'         => '300px',
			'--vertical-header-width' => '260px',
		);

		if ( ! $unyson ) {
			return $out;
		}

		/* Color design-tokens come from the user's Color Presets (Theme Settings →
		 * General → Color Presets) — the SAME source the plugin uses for its
		 * `.bg-{slug}` / `.text-{slug}` utilities and `:root --color-{slug}` vars.
		 * Without this the theme's hardcoded defaults (e.g. --color-accent: #6610f2)
		 * print later in <head> and override the user's preset (#fd7e14) on every
		 * `.bg-accent` element. Only the tokens that mirror a preset are pulled in;
		 * --color-text stays driven by Typography, --color-bg by the layout. */
		if ( function_exists( 'unysonplus_color_preset_slug_map' ) ) {
			$color_presets = unysonplus_color_preset_slug_map();
			foreach ( array( 'primary', 'accent', 'muted' ) as $cslug ) {
				if ( ! empty( $color_presets[ $cslug ] ) ) {
					$out[ '--color-' . $cslug ] = $color_presets[ $cslug ];
				}
			}
		}

		/* General → Layout / Sidebar / Preloader (the tab was split into three
		 * storage keys; merge them so the reads below are key-name stable). */
		$layout = array();
		foreach ( array( 'general_layout', 'general_sidebar', 'general_preloader', 'general_scroll', 'general_base' ) as $layout_opt ) {
			$layout_raw = fw_get_db_settings_option( $layout_opt, array() );
			if ( is_array( $layout_raw ) ) { $layout = array_merge( $layout, $layout_raw ); }
		}
		if ( ! empty( $layout ) ) {
			$lget = function ( $k, $d = '' ) use ( $layout ) {
				return ( isset( $layout[ $k ] ) && $layout[ $k ] !== '' && $layout[ $k ] !== null ) ? $layout[ $k ] : $d;
			};

			// Site Width Mode sub-options now live in the site_width_mode multi-picker
			// (boxed / framed groups); read them via the width helper. site_boxed_width
			// is the sole writer of --site-max-width (the old layout_container_max_width
			// collision was removed — it clobbered Boxed Width and never styled .container).
			$wget = function_exists( 'unysonplus_width_get' ) ? 'unysonplus_width_get' : null;
			if ( $wget ) {
				if ( ( $v = $wget( 'site_boxed_margin' ) ) !== '' ) { $out['--site-margin']      = unysonplus_css_length( $v ); }
				if ( ( $v = $wget( 'site_frame_width' ) ) !== '' )  { $out['--site-frame-width'] = unysonplus_css_length( $v ); }
				if ( ( $v = $wget( 'site_frame_color' ) ) !== '' )  { $out['--site-frame-color'] = $v; }
				if ( ( $v = $wget( 'site_boxed_width' ) ) !== '' )  { $out['--site-max-width']   = unysonplus_css_length( $v ); }
			}
			if ( ( $v = $lget( 'layout_container_gutter' ) ) !== '' )    { $out['--container-gutter'] = unysonplus_css_length( $v ); }
			if ( ( $v = $lget( 'layout_sidebar_width' ) ) !== '' )       { $out['--sidebar-width']    = unysonplus_css_length( $v ); }
			if ( ( $v = $lget( 'layout_preloader_bg_color' ) ) !== '' )  { $out['--preloader-bg']     = $v; }
			if ( ( $v = $lget( 'layout_scroll_progress_color' ) ) !== '' ) { $out['--scroll-progress-color'] = $v; }

			// Section spacing scale
			$scale_map = array( 'compact' => '0.75', 'cozy' => '1', 'spacious' => '1.5' );
			$scale_key = $lget( 'layout_section_spacing', 'cozy' );
			if ( isset( $scale_map[ $scale_key ] ) ) {
				$out['--section-spacing-scale'] = $scale_map[ $scale_key ];
			}

			// Site background (Background Pro: color + gradient + image layers).
			// Video is intentionally NOT applied to the site-wide body background.
			$bg = ( isset( $layout['site_background'] ) && is_array( $layout['site_background'] ) ) ? $layout['site_background'] : array();
			if ( $bg ) {
				// Color (custom hex/rgba or theme preset) via the same resolver the rest of the theme uses.
				$color_val = fw_akg( 'color/value', $bg );
				if ( is_array( $color_val ) && function_exists( 'unysonplus_get_option_color_picker' ) ) {
					$color = unysonplus_get_option_color_picker( $color_val );
					if ( is_string( $color ) && '' !== $color ) { $out['--site-bg-color'] = $color; }
				}

				// background-image stack: image on top of gradient.
				$images = array();

				$img_url = fw_akg( 'image/src/url', $bg );
				if ( $img_url ) {
					$images[] = 'url(' . esc_url_raw( $img_url ) . ')';

					// Per-image presentation overrides (style.css supplies fallbacks otherwise).
					$pos      = fw_akg( 'image/position', $bg, 'center center' );
					$rep      = fw_akg( 'image/repeat', $bg, 'no-repeat' );
					$att      = fw_akg( 'image/attachment', $bg, 'scroll' );
					$size_sel = fw_akg( 'image/size/selected', $bg, 'cover' );
					$size     = ( 'custom' === $size_sel ) ? fw_akg( 'image/size/custom', $bg, 'auto' ) : $size_sel;
					if ( $pos )  { $out['--site-bg-position']   = $pos; }
					if ( $rep )  { $out['--site-bg-repeat']     = $rep; }
					if ( $att )  { $out['--site-bg-attachment'] = $att; }
					if ( $size ) { $out['--site-bg-size']       = $size; }
				}

				$stops = fw_akg( 'gradient/data/stops', $bg );
				if ( is_array( $stops ) && count( $stops ) >= 2
					&& class_exists( 'FW_Option_Type_Gradient_V2' )
					&& method_exists( 'FW_Option_Type_Gradient_V2', 'to_css' ) ) {
					$grad = FW_Option_Type_Gradient_V2::to_css( fw_akg( 'gradient/data', $bg ) );
					if ( $grad ) { $images[] = $grad; }
				}

				if ( $images ) {
					$out['--site-bg-image'] = implode( ', ', $images );
				}
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
			$round = $lget( 'layout_roundness', 'subtle' );
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
			if ( ( $v = $lget( 'layout_sidebar_border_width' ) ) !== '' ) { $out['--sidebar-border-width'] = unysonplus_css_length( $v ); }
			$sbc = unysonplus_preset_color_to_css( $lget( 'layout_sidebar_border_color' ) );
			if ( $sbc !== '' ) { $out['--sidebar-border-color'] = $sbc; }
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
			if ( ( $v = $lget( 'layout_container_width_desktop' ) ) !== '' ) { $out['--container-max-desktop'] = unysonplus_css_length( $v ); }
			if ( ( $v = $lget( 'layout_container_width_tablet' ) ) !== '' )  { $out['--container-max-tablet']  = unysonplus_css_length( $v ); }
			if ( ( $v = $lget( 'layout_container_width_mobile' ) ) !== '' )  { $out['--container-max-mobile']  = unysonplus_css_length( $v ); }
		}

		// Header layout — container, min_height, bg_color, topbar.
		$header_layout = fw_get_db_settings_option( 'header_layout', array() );
		if ( is_array( $header_layout ) ) {
			$hbg = isset( $header_layout['bg_color'] ) ? unysonplus_preset_color_to_css( $header_layout['bg_color'] ) : '';
			if ( $hbg !== '' ) {
				$out['--header-bg']        = $hbg;
				// color-mix keeps the ~95% sticky tint working for a preset var() too.
				$out['--header-sticky-bg'] = 'color-mix(in srgb, ' . $hbg . ' 95%, transparent)';
			}
			$hmh = unysonplus_css_length( isset( $header_layout['min_height'] ) ? $header_layout['min_height'] : '' );
			if ( $hmh !== '' ) { $out['--header-min-height'] = $hmh; }

			$hmh_mobile = unysonplus_css_length( isset( $header_layout['mobile_min_height'] ) ? $header_layout['mobile_min_height'] : '' );
			if ( $hmh_mobile !== '' ) { $out['--header-min-height-mobile'] = $hmh_mobile; }

			// Sticky-shrink logo height (Behavior = Sticky + Shrink); CSS falls back to 40px.
			$shrink = unysonplus_css_length( isset( $header_layout['sticky_shrink_height'] ) ? $header_layout['sticky_shrink_height'] : '' );
			if ( $shrink !== '' ) { $out['--header-shrink-logo'] = $shrink; }

			// Header row vertical alignment + element gap (all header rows/columns).
			$valign_map = array( 'top' => 'flex-start', 'center' => 'center', 'bottom' => 'flex-end' );
			$valign = isset( $header_layout['header_valign'] ) ? (string) $header_layout['header_valign'] : '';
			if ( isset( $valign_map[ $valign ] ) && $valign !== 'center' ) { $out['--header-valign'] = $valign_map[ $valign ]; }
			$egap = unysonplus_css_length( isset( $header_layout['header_element_gap'] ) ? $header_layout['header_element_gap'] : '' );
			if ( $egap !== '' ) { $out['--header-element-gap'] = $egap; }

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

			// Logo width (Header → Identity) — an explicit display width overrides
			// the header-height cap (see .site-title img in style.css).
			$header_logo = fw_get_db_settings_option( 'header_logo', array() );
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
			}

		// Footer — direct settings keys (used by footer.php). Length values
		// get unit normalization; color values pass through.
		$footer_lengths = array(
			'footer_padding_top'    => '--footer-pad-top',
			'footer_padding_bottom' => '--footer-pad-bottom',
		);
		$footer_colors = array(
			'footer_bg_color'    => '--footer-bg',
			'footer_text_color'  => '--footer-color',
			'footer_link_color'  => '--footer-link-color',
		);
		foreach ( $footer_lengths as $opt => $var ) {
			$css = unysonplus_css_length( fw_get_db_settings_option( $opt ) );
			if ( $css !== '' ) { $out[ $var ] = $css; }
		}
		foreach ( $footer_colors as $opt => $var ) {
			$val = fw_get_db_settings_option( $opt );
			if ( ! empty( $val ) ) { $out[ $var ] = $val; }
		}

		$footer_bg_image = fw_get_db_settings_option( 'footer_bg_image' );
		if ( ! empty( $footer_bg_image['url'] ) ) {
			$f_img = esc_url_raw( $footer_bg_image['url'] );
			// Overlay opacity (0-100, default 80) composited over the image so footer
			// text stays readable; tinted with the footer bg color (fallback black).
			$f_overlay = fw_get_db_settings_option( 'footer_bg_overlay' );
			$f_overlay = ( $f_overlay === null || $f_overlay === '' ) ? 80 : (int) $f_overlay;
			$f_overlay = max( 0, min( 100, $f_overlay ) );
			if ( $f_overlay > 0 && function_exists( 'unysonplus_color_with_alpha' ) ) {
				$f_tint = fw_get_db_settings_option( 'footer_bg_color' );
				if ( empty( $f_tint ) ) { $f_tint = 'rgba(0,0,0,1)'; }
				$f_ov = unysonplus_color_with_alpha( $f_tint, $f_overlay / 100 );
				$out['--footer-bg-image'] = 'linear-gradient(0deg,' . $f_ov . ',' . $f_ov . '),url(' . $f_img . ')';
			} else {
				$out['--footer-bg-image'] = 'url(' . $f_img . ')';
			}
		}

		// Body text color from typography (aliases the existing --body-color emitted by css-tokens.php).
		$typography = fw_get_db_settings_option( 'typography', array() );
		if ( ! empty( $typography['body']['color'] ) ) {
			$out['--color-text'] = $typography['body']['color'];
		}
		// (--font-body / --font-heading + the heading scale come from css-tokens.php
		// — the Typography preset / pairing — not here.)
		// Body/content link colors (Typography). Emitted only when set; style.css
		// falls back to --color-primary so unset = current behavior.
		if ( ! empty( $typography['body_link'] ) ) {
			$out['--body-link-color'] = $typography['body_link'];
		}
		if ( ! empty( $typography['body_link_hover'] ) ) {
			$out['--body-link-hover'] = $typography['body_link_hover'];
		}
		// Body link underline (Typography → Body Link Underline). Drives the
		// text-decoration on prose links in both states; default 'hover' matches the
		// style.css fallbacks (none / underline), so it only emits when overridden.
		$blu = ! empty( $typography['body_link_underline'] ) ? $typography['body_link_underline'] : 'hover';
		if ( $blu === 'always' ) {
			$out['--body-link-decoration']       = 'underline';
			$out['--body-link-decoration-hover'] = 'underline';
		} elseif ( $blu === 'never' ) {
			$out['--body-link-decoration']       = 'none';
			$out['--body-link-decoration-hover'] = 'none';
		}

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
			$shc = unysonplus_preset_color_to_css( isset( $ss['social_icon_hover_color'] ) ? $ss['social_icon_hover_color'] : '' );
			if ( $shc !== '' ) { $out['--social-icon-hover-color'] = $shc; }
			$shb = unysonplus_preset_color_to_css( isset( $ss['social_icon_hover_bg'] ) ? $ss['social_icon_hover_bg'] : '' );
			if ( $shb !== '' ) { $out['--social-icon-hover-bg'] = $shb; }
		}

		return $out;
	}
endif;

if ( ! function_exists( 'unysonplus_css_length' ) ) :
	/**
	 * Normalize a length value: bare numbers get 'px' appended; anything
	 * already containing a unit (px, em, rem, %, vh, vw) passes through.
	 */
	function unysonplus_css_length( $value ) : string {
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
