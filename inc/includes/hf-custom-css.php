<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }

/**
 * Generated front-end stylesheet (the theme's single dynamic CSS file).
 *
 * Compiles ALL settings-driven CSS into one file under uploads, enqueued after
 * parent-style — so the front end ships no inline <style> blocks or per-element
 * inline styles. Contents (see unysonplus_generated_css()):
 *   1. Typography tokens   (unysonplus_css_tokens_css(), from `typography`)
 *   2. Theme-var :root block (unysonplus_theme_vars_css(), colors/header/footer/layout)
 *   3. Per-section Custom Styling (Header → Top Bar / Main Header / Bottom Bar and
 *      Footer → Pre / Main / Post): background, typography, link color, borders.
 *
 * Class-based bits are NOT in the file: padding rides the `spacing` utility
 * classes and container/CSS-class ride wrapper classes (see
 * unysonplus_hf_section_render_attrs()). The admin keeps the tokens + theme-vars
 * inline (admin_head) for the live page-builder editor preview.
 *
 * The file is rebuilt on settings save (`fw_settings_form_saved`) and lazily when
 * missing. If uploads isn't writable, the same CSS is emitted once as a single
 * <style> block (a stylesheet block, still not per-element inline) as a fallback.
 *
 * Scope is the GLOBAL header/footer (the common case). Per-page presets render
 * the same selectors and inherit this file; true per-preset styling is a future
 * extension.
 */

/* ============================================================
 * Section map + value access
 * ============================================================ */

if ( ! function_exists( 'unysonplus_hf_style_sections' ) ) :
/**
 * Map each stylable section to its CSS selector + its resolved Custom Styling
 * value array ({enabled, yes:{…}}). Header styling is nested inside the row's
 * multi; footer styling is its own top-level option id.
 *
 * @return array[] each: ['selector'=>string,'prefix'=>string,'styling'=>array]
 */
function unysonplus_hf_style_sections() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return array(); }

	$nested = function ( $opt_id, $key ) {
		$v = fw_get_db_settings_option( $opt_id, array() );
		return ( is_array( $v ) && isset( $v[ $key ] ) && is_array( $v[ $key ] ) ) ? $v[ $key ] : array();
	};
	$top = function ( $opt_id ) {
		$v = fw_get_db_settings_option( $opt_id, array() );
		return is_array( $v ) ? $v : array();
	};

	return array(
		array( 'selector' => '.header-topbar',                'prefix' => 'topbar',      'styling' => $nested( 'header_topbar', 'topbar_custom_styling' ) ),
		array( 'selector' => '.header-main',                  'prefix' => 'main',        'styling' => $nested( 'header_main', 'main_custom_styling' ) ),
		array( 'selector' => '.header-bottombar',             'prefix' => 'bottombar',   'styling' => $nested( 'header_bottombar', 'bottombar_custom_styling' ) ),
		array( 'selector' => '.footer-section--pre-footer',   'prefix' => 'pre_footer',  'styling' => $top( 'pre_footer_custom_styling' ) ),
		array( 'selector' => '.footer-section--main-footer',  'prefix' => 'main_footer', 'styling' => $top( 'main_footer_custom_styling' ) ),
		array( 'selector' => '.footer-section--post-footer',  'prefix' => 'post_footer', 'styling' => $top( 'post_footer_custom_styling' ) ),
	);
}
endif;

if ( ! function_exists( 'unysonplus_hf_enabled_styling' ) ) :
/**
 * Return the active styling leaf array for a section, or null if not enabled.
 *
 * @param array $styling The {enabled, yes:{…}} multi-picker value.
 * @return array|null
 */
function unysonplus_hf_enabled_styling( $styling ) {
	if ( ! is_array( $styling ) ) { return null; }
	$on = ! empty( $styling['enabled'] ) && $styling['enabled'] === 'yes';
	if ( ! $on ) { return null; }
	return ( ! empty( $styling['yes'] ) && is_array( $styling['yes'] ) ) ? $styling['yes'] : array();
}
endif;


/* ============================================================
 * Small CSS-safe sanitizers
 * ============================================================ */

if ( ! function_exists( 'unysonplus_hf_css_val' ) ) :
/** Allow only characters valid in a simple CSS value (colors, lengths, urls). */
function unysonplus_hf_css_val( $val ) {
	return trim( preg_replace( '/[^a-zA-Z0-9#(),.%\s\/_\'"-]/', '', (string) $val ) );
}
endif;

if ( ! function_exists( 'unysonplus_hf_border_width' ) ) :
/** A border-width value → CSS length. The width is now a unit-input ({value,unit});
 *  unysonplus_css_length resolves that AND tolerates a legacy typed string ("1px").
 *  Returns '' when blank. */
function unysonplus_hf_border_width( $val ) {
	if ( function_exists( 'unysonplus_css_length' ) ) {
		return unysonplus_css_length( $val );
	}
	return is_array( $val ) ? '' : unysonplus_hf_css_val( $val );
}
endif;

if ( ! function_exists( 'unysonplus_hf_border_style' ) ) :
/** A border-style select value → a safe CSS border style. Defaults to 'solid' (the
 *  behavior before the Style option existed), so borders saved without a style keep
 *  their look. */
function unysonplus_hf_border_style( $val ) {
	$val = is_string( $val ) ? strtolower( trim( $val ) ) : '';
	return in_array( $val, array( 'solid', 'dashed', 'dotted', 'double' ), true ) ? $val : 'solid';
}
endif;

if ( ! function_exists( 'unysonplus_hf_typography_css' ) ) :
/**
 * Convert a typography-v2 value into CSS declarations (no trailing brace).
 * size / line-height / letter-spacing are unitless numbers interpreted as px.
 *
 * @param mixed $typo
 * @return string
 */
function unysonplus_hf_typography_css( $typo ) {
	if ( ! is_array( $typo ) ) { return ''; }
	// Tolerate both the flat value and a {value:…} wrapper.
	if ( isset( $typo['value'] ) && is_array( $typo['value'] ) ) { $typo = $typo['value']; }

	$d = array();
	if ( ! empty( $typo['family'] ) ) {
		$fam = unysonplus_hf_css_val( $typo['family'] );
		// Quote a multi-word family that isn't already quoted.
		if ( $fam !== '' && strpos( $fam, ' ' ) !== false && strpos( $fam, ',' ) === false && $fam[0] !== '"' && $fam[0] !== "'" ) {
			$fam = '"' . $fam . '"';
		}
		if ( $fam !== '' ) { $d[] = 'font-family:' . $fam; }
	}
	// Size: keep decimals + allow an explicit unit (e.g. "1.2rem"); a bare number is px.
	if ( isset( $typo['size'] ) && $typo['size'] !== '' && $typo['size'] !== false ) {
		$d[] = 'font-size:' . ( is_numeric( $typo['size'] ) ? ( $typo['size'] + 0 ) . 'px' : unysonplus_hf_css_val( $typo['size'] ) );
	}
	// Weight / style — prefer the Google-font `variation` (e.g. 700 / 700italic), then
	// the standard-font style + weight fields. (Previously `variation` was ignored, so a
	// bold Google font applied the family but not the weight.)
	$fv      = ! empty( $typo['variation'] ) ? (string) $typo['variation'] : '';
	$fstyle  = '';
	$fweight = 0;
	if ( $fv !== '' ) {
		if ( stripos( $fv, 'italic' ) !== false ) { $fstyle = 'italic'; }
		$fweight = (int) $fv;
	} else {
		if ( ! empty( $typo['style'] ) && $typo['style'] !== 'normal' ) { $fstyle = (string) $typo['style']; }
		if ( ! empty( $typo['weight'] ) ) { $fweight = (int) $typo['weight']; }
	}
	if ( $fweight > 0 )         { $d[] = 'font-weight:' . $fweight; }
	if ( $fstyle === 'italic' ) { $d[] = 'font-style:italic'; }
	// Line-height: typography-v2 stores a bare number that may be EITHER a unitless
	// ratio (e.g. 1.5) OR a pixel value (e.g. 15, matching the px size/letter-spacing
	// fields). Disambiguate by magnitude: < 4 is a ratio, >= 4 is px. Emitting a px
	// value like 15 without a unit made it a 15× multiplier (12px font → 180px-tall
	// rows); forcing px would crush a 1.5 ratio to 1.5px — so we branch.
	if ( isset( $typo['line-height'] ) && is_numeric( $typo['line-height'] ) && $typo['line-height'] !== '' ) {
		$lh    = $typo['line-height'] + 0;
		$d[]   = 'line-height:' . ( ( $lh > 0 && $lh < 4 ) ? $lh : $lh . 'px' );
	}
	// Letter-spacing: keep decimals, in px.
	if ( isset( $typo['letter-spacing'] ) && is_numeric( $typo['letter-spacing'] ) && $typo['letter-spacing'] !== '' ) {
		$d[] = 'letter-spacing:' . ( $typo['letter-spacing'] + 0 ) . 'px';
	}
	if ( ! empty( $typo['color'] ) ) {
		$d[] = 'color:' . unysonplus_hf_css_val( $typo['color'] );
	}
	return implode( ';', $d );
}
endif;


/* ============================================================
 * Build the CSS
 * ============================================================ */

if ( ! function_exists( 'unysonplus_hf_build_css' ) ) :
/**
 * Compile all enabled section styling into a CSS string.
 *
 * @return string
 */
function unysonplus_hf_build_css() {
	$css = '';

	// Resolve a colour value → CSS. Handles the compact preset shape
	// { predefined, custom } (predefined → live var(--color-slug)) and the legacy
	// rgba/hex string, so old saves keep working after the pickers were upgraded.
	$color = function ( $v ) {
		$c = function_exists( 'unysonplus_preset_color_to_css' ) ? unysonplus_preset_color_to_css( $v ) : ( is_string( $v ) ? $v : '' );
		return unysonplus_hf_css_val( $c );
	};

	foreach ( unysonplus_hf_style_sections() as $section ) {
		$cs = unysonplus_hf_enabled_styling( $section['styling'] );
		if ( $cs === null ) { continue; }

		$sel = $section['selector'];
		$p   = $section['prefix'];

		$decl          = array();
		$border_pseudo = ''; // ::before/::after rules for a contained (Container/Custom) border

		// Background: the Background Pro field (color + gradient + image, no video).
		// Falls back to the legacy color/image/overlay fields for sections saved
		// before the switch so their background is preserved.
		$bg_pro   = isset( $cs[ $p . '_background' ] ) && is_array( $cs[ $p . '_background' ] ) ? $cs[ $p . '_background' ] : null;
		$bg_decls = $bg_pro ? unysonplus_hf_background_pro_decls( $bg_pro ) : array();
		if ( ! empty( $bg_decls ) ) {
			$decl = array_merge( $decl, $bg_decls );
		} else {
			// Legacy fields: color and/or image. When an image is set, fold the overlay
			// (color + opacity) into a linear-gradient so no overlay element is needed.
			$bg_color = isset( $cs[ $p . '_bg_color' ] ) ? $color( $cs[ $p . '_bg_color' ] ) : '';
			$bg_image = '';
			if ( ! empty( $cs[ $p . '_bg_image' ]['url'] ) ) {
				$bg_image = esc_url_raw( $cs[ $p . '_bg_image' ]['url'] );
			}
			if ( $bg_image !== '' ) {
				$overlay_pct = isset( $cs[ $p . '_bg_overlay' ] ) ? max( 0, min( 100, (int) $cs[ $p . '_bg_overlay' ] ) ) : 0;
				if ( $overlay_pct > 0 ) {
					$ov_color = $bg_color !== '' ? $bg_color : 'rgba(0,0,0,1)';
					$alpha    = $overlay_pct / 100;
					$ov       = unysonplus_hf_rgba_with_alpha( $ov_color, $alpha );
					$decl[]   = 'background-image:linear-gradient(0deg,' . $ov . ',' . $ov . '),url(' . $bg_image . ')';
				} else {
					$decl[] = 'background-image:url(' . $bg_image . ')';
				}
				$decl[] = 'background-size:cover';
				$decl[] = 'background-position:center';
				if ( $bg_color !== '' ) { $decl[] = 'background-color:' . $bg_color; }
			} elseif ( $bg_color !== '' ) {
				$decl[] = 'background-color:' . $bg_color;
			}
		}

		// Typography.
		$typo_css = ! empty( $cs[ $p . '_typography' ] ) ? unysonplus_hf_typography_css( $cs[ $p . '_typography' ] ) : '';
		if ( $typo_css !== '' ) { $decl[] = $typo_css; }

		// Border: ONE shared row (Width · Style · Color) applied to the edge(s) chosen in
		// Border Sides, at the reach set by Border Extent — mirrors the Footer Layout
		// border. A `multi-inline` row is { width:{value,unit}, style, color:{predefined,
		// custom} }. Tolerates legacy per-side rows (_border_top / _border_bottom) and the
		// flat …_border_{side}_{width,style,color} leaves for older saves. Shows only when
		// the row has both a width and a colour.
		$read_row = function ( $key ) use ( $cs, $p, $color ) {
			$row = isset( $cs[ $p . $key ] ) ? $cs[ $p . $key ] : null;
			if ( is_array( $row ) && ( isset( $row['width'] ) || isset( $row['color'] ) ) ) {
				return array(
					isset( $row['width'] ) ? unysonplus_hf_border_width( $row['width'] ) : '',
					unysonplus_hf_border_style( isset( $row['style'] ) ? $row['style'] : '' ),
					isset( $row['color'] ) ? $color( $row['color'] ) : '',
				);
			}
			return null;
		};
		$legacy_top    = $read_row( '_border_top' );
		$legacy_bottom = $read_row( '_border_bottom' );
		$brow          = $read_row( '_border' );
		if ( $brow === null ) { $brow = $legacy_top; }
		if ( $brow === null ) { $brow = $legacy_bottom; }
		if ( $brow === null ) {
			// Oldest flat shape.
			$fw = isset( $cs[ $p . '_border_top_width' ] ) ? unysonplus_hf_border_width( $cs[ $p . '_border_top_width' ] ) : '';
			$fc = isset( $cs[ $p . '_border_top_color' ] ) ? $color( $cs[ $p . '_border_top_color' ] ) : '';
			if ( $fw !== '' || $fc !== '' ) {
				$brow = array( $fw, unysonplus_hf_border_style( isset( $cs[ $p . '_border_top_style' ] ) ? $cs[ $p . '_border_top_style' ] : '' ), $fc );
			}
		}

		if ( is_array( $brow ) && $brow[0] !== '' && $brow[2] !== '' ) {
			$bval = $brow[0] . ' ' . $brow[1] . ' ' . $brow[2];

			// Sides: any combination of top/right/bottom/left (multi-select image-picker,
			// array value). Tolerates the legacy single-select strings via the normalizer.
			// Default Top; a legacy save that had ONLY a bottom row implies Bottom.
			$sides_raw = isset( $cs[ $p . '_border_sides' ] ) ? $cs[ $p . '_border_sides' ] : '';
			$sides     = function_exists( 'unysonplus_hf_normalize_sides' ) ? unysonplus_hf_normalize_sides( $sides_raw ) : array();
			if ( empty( $sides ) ) {
				$sides = ( $read_row( '_border' ) === null && $legacy_top === null && $legacy_bottom !== null ) ? array( 'bottom' ) : array( 'top' );
			}
			$do_top   = in_array( 'top', $sides, true );
			$do_bot   = in_array( 'bottom', $sides, true );
			$do_left  = in_array( 'left', $sides, true );
			$do_right = in_array( 'right', $sides, true );

			// Left / right are vertical — always real borders (Border Extent, which caps the
			// horizontal reach, doesn't apply to them).
			if ( $do_left )  { $decl[] = 'border-left:' . $bval; }
			if ( $do_right ) { $decl[] = 'border-right:' . $bval; }

			// Extent: full = edge-to-edge on the section; container/custom = a centered
			// pseudo-element capped at the max width (aligns the horizontal line with the
			// content). Only affects the top/bottom edges.
			$ext   = isset( $cs[ $p . '_border_extent' ] ) ? $cs[ $p . '_border_extent' ] : null;
			$emode = ( is_array( $ext ) && isset( $ext['mode'] ) ) ? (string) $ext['mode'] : 'full';
			$emax  = '';
			if ( $emode === 'container' ) {
				$emax = 'var(--container-max-desktop, var(--site-max-width, 1170px))';
			} elseif ( $emode === 'custom' ) {
				$emax = isset( $ext['custom'][ $p . '_border_extent_width' ] ) ? unysonplus_hf_border_width( $ext['custom'][ $p . '_border_extent_width' ] ) : '';
			}

			if ( $emode === 'full' || $emax === '' ) {
				if ( $do_top ) { $decl[] = 'border-top:' . $bval; }
				if ( $do_bot ) { $decl[] = 'border-bottom:' . $bval; }
			} else {
				if ( $do_top || $do_bot ) { $decl[] = 'position:relative'; }
				$pd      = 'content:"";display:block;max-width:' . $emax . ';margin-inline:auto;border-top:' . $bval;
				if ( $do_top ) { $border_pseudo .= $sel . '::before{' . $pd . '}'; }
				if ( $do_bot ) { $border_pseudo .= $sel . '::after{' . $pd . '}'; }
			}
		}

		if ( ! empty( $decl ) ) {
			$css .= $sel . '{' . implode( ';', $decl ) . '}';
		}
		// Contained (Container/Custom) border pseudo-elements, if any.
		if ( $border_pseudo !== '' ) {
			$css .= $border_pseudo;
		}

		// Link color (child anchors).
		$lc = isset( $cs[ $p . '_link_color' ] ) ? $color( $cs[ $p . '_link_color' ] ) : '';
		if ( $lc !== '' ) {
			$css .= $sel . ' a{color:' . $lc . '}';
		}
	}

	$css .= unysonplus_hf_build_global_css();

	return $css;
}
endif;

if ( ! function_exists( 'unysonplus_hf_background_pro_decls' ) ) :
/**
 * Turn a Background Pro value (color + gradient + image; video ignored) into a
 * list of CSS declarations for a section. Reuses the verified
 * unysonplus_background_pro_css_vars() parser and maps its vars to real
 * properties, so the header/footer background matches the Site Background logic.
 *
 * @param array $bg Background Pro option value.
 * @return string[] e.g. array( 'background-color:#fff', 'background-image:url(...)' )
 */
function unysonplus_hf_background_pro_decls( $bg ) {
	$decl = array();
	if ( ! is_array( $bg ) || ! function_exists( 'unysonplus_background_pro_css_vars' ) ) { return $decl; }
	$vars = unysonplus_background_pro_css_vars( $bg, '--hfbg' );
	$map  = array(
		'--hfbg-color'      => 'background-color',
		'--hfbg-image'      => 'background-image',
		'--hfbg-position'   => 'background-position',
		'--hfbg-repeat'     => 'background-repeat',
		'--hfbg-attachment' => 'background-attachment',
		'--hfbg-size'       => 'background-size',
	);
	foreach ( $map as $k => $prop ) {
		if ( isset( $vars[ $k ] ) && $vars[ $k ] !== '' ) {
			$decl[] = $prop . ':' . $vars[ $k ];
		}
	}
	return $decl;
}
endif;

if ( ! function_exists( 'unysonplus_hf_hash' ) ) :
/** Short stable hash for per-instance generated classes. */
function unysonplus_hf_hash( $str ) {
	return substr( md5( (string) $str ), 0, 8 );
}
endif;

if ( ! function_exists( 'unysonplus_cta_button_classes' ) ) :
/**
 * Classes for a header/footer CTA button element. The CTA now rides the theme's
 * button design system: `btn` + the Button Style preset class (e.g. `btn-primary`,
 * `btn-outline-primary`) + the Button Size class (e.g. `btn-lg`), all sourced from
 * Theme Settings → General → Buttons. Those classes carry all the color/size CSS
 * (from the generated button-preset stylesheet), so there are no per-instance
 * inline styles or hash classes here. `header-cta-btn` is kept as a semantic hook.
 *
 * Tolerates the legacy `filled`/`outline`/`pill` value from the old plain-select
 * (mapped to a base `.btn`) so a pre-existing CTA doesn't render class-less.
 *
 * @param array $settings cta_button element settings
 * @return string
 */
function unysonplus_cta_button_classes( $settings ) {
	$classes = array( 'header-cta-btn', 'btn' );

	$style = ! empty( $settings['cta_style'] ) ? (string) $settings['cta_style'] : '';
	// A `btn-*` value is a real preset class; the legacy filled/outline/pill words
	// aren't classes, so they're ignored (bare `.btn` is a sensible default).
	if ( strpos( $style, 'btn-' ) === 0 ) {
		$classes[] = preg_replace( '/[^a-zA-Z0-9_-]/', '', $style );
	}

	$size = ! empty( $settings['cta_size'] ) ? (string) $settings['cta_size'] : '';
	if ( strpos( $size, 'btn-' ) === 0 ) {
		$classes[] = preg_replace( '/[^a-zA-Z0-9_-]/', '', $size );
	}

	return implode( ' ', array_values( array_unique( array_filter( $classes ) ) ) );
}
endif;

if ( ! function_exists( 'unysonplus_footer_logo_class' ) ) :
/** Per-instance class for a footer logo's max-width (no inline). */
function unysonplus_footer_logo_class( $max_width ) {
	$max_width = (string) $max_width;
	if ( $max_width === '' ) { return ''; }
	return 'footer-logo-img--w' . unysonplus_hf_hash( $max_width );
}
endif;

if ( ! function_exists( 'unysonplus_hf_collect_elements' ) ) :
/**
 * Flatten every element configured in the GLOBAL header + footer column configs,
 * so the generator can emit per-instance rules (CTA buttons, footer logos).
 *
 * @return array[] list of element arrays (each has element_type.element + settings)
 */
function unysonplus_hf_collect_elements() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return array(); }
	$out = array();

	$walk_columns = function ( $cols ) use ( &$out ) {
		foreach ( (array) $cols as $col ) {
			if ( ! is_array( $col ) ) { continue; }
			foreach ( $col as $el ) {
				if ( is_array( $el ) && ! empty( $el['element_type']['element'] ) ) { $out[] = $el; }
			}
		}
	};

	// Header rows.
	$topbar = fw_get_db_settings_option( 'header_topbar', array() );
	if ( is_array( $topbar ) )    { $walk_columns( array( $topbar['topbar_left'] ?? null, $topbar['topbar_center'] ?? null, $topbar['topbar_right'] ?? null ) ); }
	$main = fw_get_db_settings_option( 'header_main', array() );
	if ( is_array( $main ) )      { $walk_columns( array( $main['main_left'] ?? null, $main['main_center'] ?? null, $main['main_right'] ?? null ) ); }
	$bottom = fw_get_db_settings_option( 'header_bottombar', array() );
	if ( is_array( $bottom ) )    { $walk_columns( array( $bottom['bottombar_left'] ?? null, $bottom['bottombar_center'] ?? null, $bottom['bottombar_right'] ?? null ) ); }

	// Footer sections: each {prefix}_columns multi-picker → {count, prefix_col_N}.
	foreach ( array( 'pre_footer', 'main_footer', 'post_footer' ) as $fp ) {
		$mp = fw_get_db_settings_option( $fp . '_columns', array() );
		if ( ! is_array( $mp ) ) { continue; }
		$count  = isset( $mp['count'] ) ? (int) $mp['count'] : 0;
		$choice = ( $count && ! empty( $mp[ (string) $count ] ) && is_array( $mp[ (string) $count ] ) ) ? $mp[ (string) $count ] : array();
		$cols   = array();
		for ( $i = 1; $i <= $count; $i++ ) {
			$cols[] = isset( $choice[ $fp . '_col_' . $i ] ) ? $choice[ $fp . '_col_' . $i ] : array();
		}
		$walk_columns( $cols );
	}

	return $out;
}
endif;

if ( ! function_exists( 'unysonplus_hf_build_global_css' ) ) :
/**
 * Rules for global settings-driven styles that used to be inline element styles:
 * site title / tagline colors (Header → Identity), the scroll-to-top button
 * colors (Misc), and per-instance CTA buttons + footer logos.
 *
 * @return string
 */
function unysonplus_hf_build_global_css() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return ''; }
	$css = '';

	// Header → Identity: site title + tagline custom colors.
	// Flattened (Logo Type is a multi-picker; the accessor reads nested + legacy shapes).
	$logo = function_exists( 'unysonplus_header_logo_cfg' ) ? unysonplus_header_logo_cfg() : fw_get_db_settings_option( 'header_logo', array() );
	if ( is_array( $logo ) ) {
		if ( ! empty( $logo['color']['custom'] ) ) {
			$css .= '.site-title a{color:' . unysonplus_hf_css_val( $logo['color']['custom'] ) . '}';
		}
		// Logo Icon custom color (the inline-SVG mark uses currentColor) + size.
		if ( ! empty( $logo['logo_icon_color']['custom'] ) ) {
			$css .= '.site-logo__mark{color:' . unysonplus_hf_css_val( $logo['logo_icon_color']['custom'] ) . '}';
		}
		if ( ! empty( $logo['logo_icon_size']['value'] ) && '' !== $logo['logo_icon_size']['value'] ) {
			$unit = ! empty( $logo['logo_icon_size']['unit'] ) ? preg_replace( '/[^a-z%]/', '', $logo['logo_icon_size']['unit'] ) : 'em';
			$css .= '.site-logo__mark{font-size:' . (float) $logo['logo_icon_size']['value'] . $unit . '}';
		}
		if ( ! empty( $logo['tagline_color']['custom'] ) ) {
			$css .= '.site-description{color:' . unysonplus_hf_css_val( $logo['tagline_color']['custom'] ) . '}';
		}
		// Logo Custom CSS (Custom Logo Layout → Advanced): raw CSS for the lockup, appended last
		// so it can override the generated rules above. Author-scoped to the logo hooks.
		if ( ! empty( $logo['logo_custom_css'] ) && is_string( $logo['logo_custom_css'] ) ) {
			$css .= "\n" . trim( $logo['logo_custom_css'] ) . "\n";
		}
	}

	// Misc: scroll-to-top button colors.
	if ( function_exists( 'unysonplus_misc_get' ) ) {
		$st_bg = unysonplus_misc_get( 'scroll_top_bg_color', '' );
		$st_fg = unysonplus_misc_get( 'scroll_top_text_color', '' );
		if ( function_exists( 'unysonplus_preset_color_to_css' ) ) {
			$st_bg = unysonplus_preset_color_to_css( $st_bg );
			$st_fg = unysonplus_preset_color_to_css( $st_fg );
		}
		$st    = array();
		if ( $st_bg !== '' ) { $st[] = 'background-color:' . unysonplus_hf_css_val( $st_bg ); }
		if ( $st_fg !== '' ) { $st[] = 'color:' . unysonplus_hf_css_val( $st_fg ); }
		if ( $st ) { $css .= '.scroll-to-top{' . implode( ';', $st ) . '}'; }
	}

	// Per-instance: footer logos across the global configs. (CTA buttons no longer
	// need a per-instance rule — they ride the theme's `btn btn-{preset}` classes.)
	$seen = array();
	foreach ( unysonplus_hf_collect_elements() as $el ) {
		$type     = $el['element_type']['element'];
		$settings = ! empty( $el['element_type'][ $type ] ) ? $el['element_type'][ $type ] : array();

		if ( $type === 'footer_logo' ) {
			$w = function_exists( 'unysonplus_css_length' )
				? unysonplus_css_length( ! empty( $settings['footer_logo_width'] ) ? $settings['footer_logo_width'] : '' )
				: ( ! empty( $settings['footer_logo_width'] ) ? $settings['footer_logo_width'] : '' );
			if ( $w === '' ) { $w = '12.5rem'; }
			$cls = unysonplus_footer_logo_class( $w );
			if ( $cls === '' || isset( $seen[ $cls ] ) ) { continue; }
			$seen[ $cls ] = true;
			// `.footer-logo-link` ancestor raises specificity (0,2,0) above
			// WooCommerce core's `.woocommerce-page img` (0,1,1) so the configured
			// width survives on shop-tagged pages.
			$css .= '.footer-logo-link .' . $cls . '{max-width:' . unysonplus_hf_css_val( $w ) . '}';
		}
	}

	return $css;
}
endif;

if ( ! function_exists( 'unysonplus_hf_rgba_with_alpha' ) ) :
/** Reuse the theme's alpha helper when present; otherwise a small inline fallback. */
function unysonplus_hf_rgba_with_alpha( $color, $alpha ) {
	if ( function_exists( 'unysonplus_color_with_alpha' ) ) {
		return unysonplus_color_with_alpha( $color, $alpha );
	}
	$color = trim( (string) $color );
	if ( preg_match( '/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/', $color, $m ) ) {
		return 'rgba(' . (int) $m[1] . ',' . (int) $m[2] . ',' . (int) $m[3] . ',' . round( $alpha, 3 ) . ')';
	}
	if ( preg_match( '/^#([0-9a-f]{6})$/i', $color, $m ) ) {
		$h = $m[1];
		return 'rgba(' . hexdec( substr( $h, 0, 2 ) ) . ',' . hexdec( substr( $h, 2, 2 ) ) . ',' . hexdec( substr( $h, 4, 2 ) ) . ',' . round( $alpha, 3 ) . ')';
	}
	return $color;
}
endif;


if ( ! function_exists( 'unysonplus_generated_css' ) ) :
/**
 * The full body of the generated front-end stylesheet: typography tokens +
 * theme-var `:root` block + the per-section custom-styling rules. These were
 * previously three inline <style> blocks; unifying them into the one generated
 * file is what keeps the front end free of inline CSS.
 *
 * Order: tokens, then theme-vars, then section rules. The file loads after
 * parent-style (and before child-style), so all of it wins over style.css
 * defaults while a child theme can still override.
 *
 * @return string
 */
function unysonplus_generated_css() {
	$css = '';
	// @font-face first so custom families resolve wherever they're used.
	if ( function_exists( 'unysonplus_custom_fonts_css' ) ) { $css .= unysonplus_custom_fonts_css(); }
	if ( function_exists( 'unysonplus_css_tokens_css' ) ) { $css .= unysonplus_css_tokens_css(); }
	if ( function_exists( 'unysonplus_theme_vars_css' ) ) { $css .= unysonplus_theme_vars_css(); }
	$css .= unysonplus_hf_build_css();
	return $css;
}
endif;


/* ============================================================
 * File write + enqueue + regeneration
 * ============================================================ */

if ( ! function_exists( 'unysonplus_hf_css_paths' ) ) :
/** @return array{dir:string,file:string,url:string,legacy:string}|null */
function unysonplus_hf_css_paths() {
	$up = wp_upload_dir();
	if ( ! empty( $up['error'] ) || empty( $up['basedir'] ) ) { return null; }
	$dir = trailingslashit( $up['basedir'] ) . 'unysonplus';
	return array(
		'dir'    => $dir,
		'file'   => $dir . '/unysonplus-generated.css',
		'url'    => trailingslashit( $up['baseurl'] ) . 'unysonplus/unysonplus-generated.css',
		'legacy' => $dir . '/header-footer.css', // pre-rename name; removed on write
	);
}
endif;

if ( ! function_exists( 'unysonplus_hf_write_css_file' ) ) :
/**
 * Write the given CSS to the generated file. Returns true on success, false if it
 * couldn't be written (caller then uses the inline-style-block fallback).
 *
 * @param string $css
 * @return bool
 */
function unysonplus_hf_write_css_file( $css ) {
	$paths = unysonplus_hf_css_paths();
	if ( $paths === null ) { return false; }

	if ( ! is_dir( $paths['dir'] ) ) {
		if ( ! wp_mkdir_p( $paths['dir'] ) ) { return false; }
	}
	// Empty CSS still writes an empty file (keeps enqueue + mtime coherent).
	$ok = false !== @file_put_contents( $paths['file'], (string) $css );
	// Remove the pre-rename file so it isn't left orphaned in uploads.
	if ( $ok && ! empty( $paths['legacy'] ) && file_exists( $paths['legacy'] ) ) {
		@unlink( $paths['legacy'] );
	}
	return $ok;
}
endif;

if ( ! function_exists( 'unysonplus_hf_regenerate_css' ) ) :
/**
 * Proactively rebuild the file (on settings save / Customizer save) so the first
 * front-end load afterwards is already fresh. The enqueue path also self-heals
 * via a content hash, so this is an optimization, not the only trigger.
 */
function unysonplus_hf_regenerate_css() {
	$css = unysonplus_generated_css();
	if ( unysonplus_hf_write_css_file( $css ) ) {
		update_option( 'unysonplus_hf_css_hash', md5( $css ), false );
	}
}
endif;
add_action( 'fw_settings_form_saved', 'unysonplus_hf_regenerate_css', 20 );
add_action( 'customize_save_after',   'unysonplus_hf_regenerate_css', 20 );

if ( ! function_exists( 'unysonplus_hf_uploads_servable' ) ) :
/**
 * Whether the uploads dir is local to THIS site, i.e. its public URL will
 * resolve. On a cloned / migrated site the `upload_path` option can point at
 * another install's folder (basedir outside this site), so the generated file is
 * written somewhere the site's baseurl can't serve → a 404, and no styling
 * applies. When that's detected we skip the file and emit the CSS inline instead,
 * so settings always take effect regardless of a broken uploads config.
 *
 * @param string $dir Uploads basedir/unysonplus.
 * @return bool
 */
function unysonplus_hf_uploads_servable( $dir ) {
	$dir = wp_normalize_path( (string) $dir );
	if ( $dir === '' ) { return false; }
	$roots = array();
	if ( defined( 'WP_CONTENT_DIR' ) ) { $roots[] = untrailingslashit( wp_normalize_path( WP_CONTENT_DIR ) ); }
	if ( defined( 'ABSPATH' ) )        { $roots[] = untrailingslashit( wp_normalize_path( ABSPATH ) ); }
	foreach ( $roots as $r ) {
		if ( $r !== '' && strpos( $dir, $r ) === 0 ) { return true; }
	}
	return false;
}
endif;

if ( ! function_exists( 'unysonplus_hf_enqueue_css' ) ) :
/**
 * Enqueue the generated stylesheet, keeping it fresh by content hash. The CSS is
 * (re)built every front-end load — the same work the old inline emitters did —
 * and only written to disk when its hash changes, so there is no staleness and
 * no added compute cost, while the browser caches the file across page loads.
 * Falls back to a single <style> block when the file can't be written OR the
 * uploads dir isn't local to the site (cloned-site misconfig). Also enqueues any
 * Google fonts chosen in section typography.
 */
function unysonplus_hf_enqueue_css() {
	$css   = unysonplus_generated_css();
	$paths = unysonplus_hf_css_paths();

	$enqueued_file = false;
	if ( $paths !== null && unysonplus_hf_uploads_servable( $paths['dir'] ) ) {
		$hash = md5( $css );
		if ( get_option( 'unysonplus_hf_css_hash' ) !== $hash || ! file_exists( $paths['file'] ) ) {
			if ( unysonplus_hf_write_css_file( $css ) ) {
				update_option( 'unysonplus_hf_css_hash', $hash, false );
			}
		}
		if ( file_exists( $paths['file'] ) ) {
			$ver = (string) @filemtime( $paths['file'] );
			// Depend on the theme stylesheet (handle: parent-style) so the tokens +
			// vars + custom styling override theme defaults. static.php's stylesheet
			// orderer keeps this in the "after-theme" layer to avoid a dependency cycle.
			wp_enqueue_style( 'unysonplus-hf-custom', $paths['url'], array( 'parent-style' ), $ver ? $ver : null );
			$enqueued_file = true;
		}
	}

	// Fallback: emit the CSS as one <style> block (a stylesheet block, not
	// per-element inline) when the uploads dir isn't writable.
	if ( ! $enqueued_file && $css !== '' ) {
		wp_register_style( 'unysonplus-hf-custom-inline', false, array( 'parent-style' ) );
		wp_enqueue_style( 'unysonplus-hf-custom-inline' );
		wp_add_inline_style( 'unysonplus-hf-custom-inline', $css );
	}

	unysonplus_hf_enqueue_google_fonts();
}
endif;
add_action( 'wp_enqueue_scripts', 'unysonplus_hf_enqueue_css', 30 );

if ( ! function_exists( 'unysonplus_hf_enqueue_google_fonts' ) ) :
/** Enqueue a combined Google Fonts stylesheet for any section typography that uses one. */
function unysonplus_hf_enqueue_google_fonts() {
	$families = array();
	foreach ( unysonplus_hf_style_sections() as $section ) {
		$cs = unysonplus_hf_enabled_styling( $section['styling'] );
		if ( $cs === null ) { continue; }
		$typo = ! empty( $cs[ $section['prefix'] . '_typography' ] ) ? $cs[ $section['prefix'] . '_typography' ] : array();
		if ( isset( $typo['value'] ) && is_array( $typo['value'] ) ) { $typo = $typo['value']; }
		if ( ! empty( $typo['google_font'] ) && ! empty( $typo['family'] ) ) {
			$weight = ! empty( $typo['weight'] ) ? preg_replace( '/[^0-9]/', '', (string) $typo['weight'] ) : '';
			$families[ $typo['family'] ] = $typo['family'] . ( $weight !== '' ? ':' . $weight : '' );
		}
	}
	if ( empty( $families ) ) { return; }

	$url = 'https://fonts.googleapis.com/css?family=' . implode( '%7C', array_map( 'rawurlencode', array_values( $families ) ) ) . '&display=swap';
	wp_enqueue_style( 'unysonplus-hf-google-fonts', $url, array(), null );
}
endif;


/* ============================================================
 * Render helper — wrapper container + classes (no inline styles)
 * ============================================================ */

if ( ! function_exists( 'unysonplus_flatten_spacing_classes' ) ) :
/**
 * Flatten a `spacing` option value into its Bootstrap utility class list
 * (base + md/lg layers). Mirrors the shortcodes extension's walker so the theme
 * has no hard dependency on it.
 *
 * @param mixed $spacing
 * @return string space-joined class list
 */
function unysonplus_flatten_spacing_classes( $spacing ) {
	if ( ! is_array( $spacing ) ) { return ''; }
	$classes = array();
	$layers  = array( $spacing );
	if ( ! empty( $spacing['advanced'] ) && is_array( $spacing['advanced'] ) ) {
		foreach ( array( 'md', 'lg' ) as $dev ) {
			if ( isset( $spacing['advanced'][ $dev ] ) ) { $layers[] = $spacing['advanced'][ $dev ]; }
		}
	}
	foreach ( $layers as $layer ) {
		foreach ( array( 'margin', 'padding' ) as $box ) {
			if ( empty( $layer[ $box ] ) || ! is_array( $layer[ $box ] ) ) { continue; }
			foreach ( $layer[ $box ] as $val ) {
				$val = preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $val );
				if ( $val !== '' ) { $classes[] = $val; }
			}
		}
	}
	return implode( ' ', array_unique( $classes ) );
}
endif;

if ( ! function_exists( 'unysonplus_hf_section_render_attrs' ) ) :
/**
 * Resolve the wrapper container + extra classes for a section from its Custom
 * Styling block. No inline styles — bg/typography/border/link are in the
 * generated CSS file; only container/css-class/padding are class-based here.
 *
 * @param array  $styling            The {enabled, yes:{…}} value.
 * @param string $prefix             topbar|main|bottombar|pre_footer|…
 * @param string $fallback_container Default container class.
 * @return array{container:string,class:string} `class` is a leading-spaced, escaped string ('' when none).
 */
function unysonplus_hf_section_render_attrs( $styling, $prefix, $fallback_container = 'container' ) {
	$cs = unysonplus_hf_enabled_styling( $styling );

	$container = ( $cs && ! empty( $cs[ $prefix . '_container' ] ) ) ? $cs[ $prefix . '_container' ] : $fallback_container;
	$container = preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $container );
	if ( $container === '' ) { $container = $fallback_container; }

	$classes = array();
	if ( $cs ) {
		if ( ! empty( $cs[ $prefix . '_css_class' ] ) ) {
			$classes[] = sanitize_html_class( $cs[ $prefix . '_css_class' ], '' ) !== ''
				? trim( preg_replace( '/[^a-zA-Z0-9_\- ]/', '', $cs[ $prefix . '_css_class' ] ) )
				: '';
		}
		if ( ! empty( $cs[ $prefix . '_padding' ] ) ) {
			$pad = unysonplus_flatten_spacing_classes( $cs[ $prefix . '_padding' ] );
			if ( $pad !== '' ) { $classes[] = $pad; }
		}
	}
	$classes = array_filter( $classes );
	$class   = $classes ? ' ' . esc_attr( implode( ' ', $classes ) ) : '';

	return array( 'container' => $container, 'class' => $class );
}
endif;

if ( ! function_exists( 'unysonplus_hf_migrate_border_widths' ) ) :
/**
 * One-time migration: the header/footer Custom Styling border widths changed from a
 * plain text field ("1px") to a unit-input ({value,unit}). A blank value needs nothing
 * (both render empty), and the frontend consumer already tolerates a legacy string, but
 * a value someone TYPED would render blank in the editor and be lost on the next save.
 * This converts any typed string width to {value,unit} across every Custom Styling store
 * (the three header rows + the four footer sections) so those values survive. Idempotent
 * and gated by an option flag, so it runs once. Only writes a store when it actually
 * changes something, so sites with no typed widths are untouched.
 */
function unysonplus_hf_migrate_border_widths() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}
	if ( get_option( 'unysonplus_hf_border_width_migrated' ) ) {
		return;
	}

	// "1px" / "2 rem" / "3" → {value,unit}; already-array or blank left as-is.
	$to_unit = function ( $v ) {
		if ( is_array( $v ) ) { return $v; }
		$v = trim( (string) $v );
		if ( $v === '' ) { return $v; }
		if ( preg_match( '/^(-?[0-9]*\.?[0-9]+)\s*(px|em|rem|%)?$/i', $v, $m ) ) {
			return array( 'value' => $m[1], 'unit' => ( isset( $m[2] ) && $m[2] !== '' ) ? strtolower( $m[2] ) : 'px' );
		}
		return $v; // unparseable → leave (consumer still tolerates it)
	};

	// Convert the two width leaves inside a { enabled, yes:{…} } styling array. Returns
	// [ $changed_bool, $styling ].
	$convert = function ( $styling, $prefix ) use ( $to_unit ) {
		$changed = false;
		if ( is_array( $styling ) && isset( $styling['yes'] ) && is_array( $styling['yes'] ) ) {
			foreach ( array( $prefix . '_border_top_width', $prefix . '_border_bottom_width' ) as $f ) {
				if ( isset( $styling['yes'][ $f ] ) && ! is_array( $styling['yes'][ $f ] ) && trim( (string) $styling['yes'][ $f ] ) !== '' ) {
					$styling['yes'][ $f ] = $to_unit( $styling['yes'][ $f ] );
					$changed              = true;
				}
			}
		}
		return array( $changed, $styling );
	};

	// Header styling is nested inside the row's multi; footer/copyright are top-level ids.
	$nested = array(
		'header_topbar'    => array( 'key' => 'topbar_custom_styling',    'p' => 'topbar' ),
		'header_main'      => array( 'key' => 'main_custom_styling',      'p' => 'main' ),
		'header_bottombar' => array( 'key' => 'bottombar_custom_styling', 'p' => 'bottombar' ),
	);
	foreach ( $nested as $opt_id => $info ) {
		$root = fw_get_db_settings_option( $opt_id, array() );
		if ( ! is_array( $root ) || ! isset( $root[ $info['key'] ] ) || ! is_array( $root[ $info['key'] ] ) ) {
			continue;
		}
		list( $changed, $styling ) = $convert( $root[ $info['key'] ], $info['p'] );
		if ( $changed ) {
			$root[ $info['key'] ] = $styling; // preserve the rest of the row (columns etc.)
			fw_set_db_settings_option( $opt_id, $root );
		}
	}

	$top = array(
		'pre_footer_custom_styling'  => 'pre_footer',
		'main_footer_custom_styling' => 'main_footer',
		'post_footer_custom_styling' => 'post_footer',
		'copyright_custom_styling'   => 'copyright',
	);
	foreach ( $top as $opt_id => $prefix ) {
		$styling = fw_get_db_settings_option( $opt_id, array() );
		if ( ! is_array( $styling ) ) {
			continue;
		}
		list( $changed, $styling ) = $convert( $styling, $prefix );
		if ( $changed ) {
			fw_set_db_settings_option( $opt_id, $styling );
		}
	}

	update_option( 'unysonplus_hf_border_width_migrated', 1 );
}
add_action( 'admin_init', 'unysonplus_hf_migrate_border_widths' );
endif;

if ( ! function_exists( 'unysonplus_hf_migrate_border_rows' ) ) :
/**
 * One-time migration: the header/footer Custom Styling border controls changed from
 * three separate leaves per side ({prefix}_border_{side}_{width,style,color}) to a
 * single combined `multi-inline` row ({prefix}_border_{side} => {width,style,color}).
 * The frontend consumer already tolerates the old flat leaves, but the NEW combined
 * control would render empty for pre-combine saves (so the value would look lost in the
 * editor and be dropped on the next save). This folds any flat leaves into the combined
 * array across every Custom Styling store (three header rows + four footer sections) and
 * removes the old leaves. Idempotent, gated by an option flag, and only writes a store
 * when it actually changes something — sites with no custom borders are untouched.
 */
function unysonplus_hf_migrate_border_rows() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}
	if ( get_option( 'unysonplus_hf_border_rows_migrated' ) ) {
		return;
	}

	// Fold {prefix}_border_{side}_{width,style,color} into {prefix}_border_{side}.
	// Returns [ $changed_bool, $values ] where $values is the leaf-id map ($styling['yes']
	// for header rows, the whole $styling for footer sections).
	$convert = function ( $values, $prefix ) {
		$changed = false;
		if ( ! is_array( $values ) ) {
			return array( false, $values );
		}
		foreach ( array( 'top', 'bottom' ) as $side ) {
			$combined = $prefix . '_border_' . $side;
			// Skip if already combined (has the array shape).
			if ( isset( $values[ $combined ] ) && is_array( $values[ $combined ] )
				&& ( isset( $values[ $combined ]['width'] ) || isset( $values[ $combined ]['color'] ) ) ) {
				continue;
			}
			$w_key = $prefix . '_border_' . $side . '_width';
			$s_key = $prefix . '_border_' . $side . '_style';
			$c_key = $prefix . '_border_' . $side . '_color';
			$has   = isset( $values[ $w_key ] ) || isset( $values[ $s_key ] ) || isset( $values[ $c_key ] );
			if ( ! $has ) {
				continue;
			}
			$width = isset( $values[ $w_key ] ) ? $values[ $w_key ] : array( 'value' => '', 'unit' => 'px' );
			if ( ! is_array( $width ) ) {
				$width = array( 'value' => trim( (string) $width ), 'unit' => 'px' );
			}
			$color = isset( $values[ $c_key ] ) ? $values[ $c_key ] : array( 'predefined' => '', 'custom' => '' );
			if ( is_string( $color ) ) {
				$color = array( 'predefined' => $color, 'custom' => '' );
			} elseif ( ! is_array( $color ) ) {
				$color = array( 'predefined' => '', 'custom' => '' );
			}
			$values[ $combined ] = array(
				'width' => $width,
				'style' => isset( $values[ $s_key ] ) && $values[ $s_key ] !== '' ? $values[ $s_key ] : 'solid',
				'color' => $color,
			);
			unset( $values[ $w_key ], $values[ $s_key ], $values[ $c_key ] );
			$changed = true;
		}
		return array( $changed, $values );
	};

	// Header styling is nested inside the row's multi; footer/copyright are top-level ids.
	$nested = array(
		'header_topbar'    => array( 'key' => 'topbar_custom_styling',    'p' => 'topbar' ),
		'header_main'      => array( 'key' => 'main_custom_styling',      'p' => 'main' ),
		'header_bottombar' => array( 'key' => 'bottombar_custom_styling', 'p' => 'bottombar' ),
	);
	foreach ( $nested as $opt_id => $info ) {
		$root = fw_get_db_settings_option( $opt_id, array() );
		if ( ! is_array( $root ) || ! isset( $root[ $info['key'] ] ) || ! is_array( $root[ $info['key'] ] )
			|| ! isset( $root[ $info['key'] ]['yes'] ) || ! is_array( $root[ $info['key'] ]['yes'] ) ) {
			continue;
		}
		list( $changed, $yes ) = $convert( $root[ $info['key'] ]['yes'], $info['p'] );
		if ( $changed ) {
			$root[ $info['key'] ]['yes'] = $yes;
			fw_set_db_settings_option( $opt_id, $root );
		}
	}

	$top = array(
		'pre_footer_custom_styling'  => 'pre_footer',
		'main_footer_custom_styling' => 'main_footer',
		'post_footer_custom_styling' => 'post_footer',
		'copyright_custom_styling'   => 'copyright',
	);
	foreach ( $top as $opt_id => $prefix ) {
		$styling = fw_get_db_settings_option( $opt_id, array() );
		if ( ! is_array( $styling ) || ! isset( $styling['yes'] ) || ! is_array( $styling['yes'] ) ) {
			continue;
		}
		list( $changed, $yes ) = $convert( $styling['yes'], $prefix );
		if ( $changed ) {
			$styling['yes'] = $yes;
			fw_set_db_settings_option( $opt_id, $styling );
		}
	}

	update_option( 'unysonplus_hf_border_rows_migrated', 1 );
}
add_action( 'admin_init', 'unysonplus_hf_migrate_border_rows' );
endif;

if ( ! function_exists( 'unysonplus_hf_migrate_border_sides' ) ) :
/**
 * One-time migration: the Custom Styling border changed from TWO per-side rows
 * ({prefix}_border_top + {prefix}_border_bottom) to ONE shared row ({prefix}_border)
 * plus a {prefix}_border_sides picker (Top / Bottom / Both). The frontend consumer
 * already tolerates the old per-side rows, but the new controls would render empty for
 * pre-conversion saves. This folds the old rows into the shared border + sides across
 * every Custom Styling store and removes the old per-side rows. Runs at priority 20,
 * AFTER the rows migration (default 10), so it sees the combined per-side rows.
 * Idempotent, flag-gated; only writes a store it actually changes.
 */
function unysonplus_hf_migrate_border_sides() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}
	if ( get_option( 'unysonplus_hf_border_sides_migrated' ) ) {
		return;
	}

	$is_row = function ( $v ) {
		return is_array( $v ) && ( isset( $v['width'] ) || isset( $v['color'] ) || isset( $v['style'] ) );
	};

	// Fold {prefix}_border_top / _border_bottom into {prefix}_border + {prefix}_border_sides.
	$convert = function ( $values, $prefix ) use ( $is_row ) {
		if ( ! is_array( $values ) ) {
			return array( false, $values );
		}
		// Already migrated (shared row present)?
		if ( $is_row( isset( $values[ $prefix . '_border' ] ) ? $values[ $prefix . '_border' ] : null ) ) {
			return array( false, $values );
		}
		$top     = isset( $values[ $prefix . '_border_top' ] ) ? $values[ $prefix . '_border_top' ] : null;
		$bot     = isset( $values[ $prefix . '_border_bottom' ] ) ? $values[ $prefix . '_border_bottom' ] : null;
		$has_top = $is_row( $top );
		$has_bot = $is_row( $bot );
		if ( ! $has_top && ! $has_bot ) {
			return array( false, $values );
		}
		$values[ $prefix . '_border' ]       = $has_top ? $top : $bot;
		$values[ $prefix . '_border_sides' ] = ( $has_top && $has_bot ) ? 'both' : ( $has_top ? 'top' : 'bottom' );
		unset( $values[ $prefix . '_border_top' ], $values[ $prefix . '_border_bottom' ] );
		return array( true, $values );
	};

	$nested = array(
		'header_topbar'    => array( 'key' => 'topbar_custom_styling',    'p' => 'topbar' ),
		'header_main'      => array( 'key' => 'main_custom_styling',      'p' => 'main' ),
		'header_bottombar' => array( 'key' => 'bottombar_custom_styling', 'p' => 'bottombar' ),
	);
	foreach ( $nested as $opt_id => $info ) {
		$root = fw_get_db_settings_option( $opt_id, array() );
		if ( ! is_array( $root ) || ! isset( $root[ $info['key'] ]['yes'] ) || ! is_array( $root[ $info['key'] ]['yes'] ) ) {
			continue;
		}
		list( $changed, $yes ) = $convert( $root[ $info['key'] ]['yes'], $info['p'] );
		if ( $changed ) {
			$root[ $info['key'] ]['yes'] = $yes;
			fw_set_db_settings_option( $opt_id, $root );
		}
	}

	$top_stores = array(
		'pre_footer_custom_styling'  => 'pre_footer',
		'main_footer_custom_styling' => 'main_footer',
		'post_footer_custom_styling' => 'post_footer',
		'copyright_custom_styling'   => 'copyright',
	);
	foreach ( $top_stores as $opt_id => $prefix ) {
		$styling = fw_get_db_settings_option( $opt_id, array() );
		if ( ! is_array( $styling ) || ! isset( $styling['yes'] ) || ! is_array( $styling['yes'] ) ) {
			continue;
		}
		list( $changed, $yes ) = $convert( $styling['yes'], $prefix );
		if ( $changed ) {
			$styling['yes'] = $yes;
			fw_set_db_settings_option( $opt_id, $styling );
		}
	}

	update_option( 'unysonplus_hf_border_sides_migrated', 1 );
}
add_action( 'admin_init', 'unysonplus_hf_migrate_border_sides', 20 );
endif;

if ( ! function_exists( 'unysonplus_hf_migrate_border_sides_array' ) ) :
/**
 * One-time migration: the Border Sides control became a MULTI-select image-picker
 * (top / right / bottom / left, value is an ARRAY) — it previously stored a single
 * string ('top' | 'bottom' | 'both'). The frontend consumers normalize the legacy
 * strings at read-time, but the multi-select renders empty for a string value, so this
 * folds every stored {prefix}_border_sides string into an array ('both' → ['top',
 * 'bottom']) across the header/footer Custom Styling stores AND the top-level Footer
 * Layout footer_border_sides. Runs at priority 21, after the string-producing sides
 * migration (20). Idempotent, flag-gated; only writes a store it actually changes.
 */
function unysonplus_hf_migrate_border_sides_array() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}
	if ( get_option( 'unysonplus_hf_border_sides_array_migrated' ) ) {
		return;
	}

	$to_array = function ( $v ) {
		if ( is_array( $v ) ) { return null; } // already an array — leave as-is
		if ( ! is_string( $v ) || $v === '' ) { return null; }
		if ( $v === 'both' ) { return array( 'top', 'bottom' ); }
		if ( in_array( $v, array( 'top', 'right', 'bottom', 'left' ), true ) ) { return array( $v ); }
		return null;
	};

	// Custom Styling stores: {prefix}_border_sides lives in the .yes sub-array.
	$nested = array(
		'header_topbar'    => array( 'key' => 'topbar_custom_styling',    'p' => 'topbar' ),
		'header_main'      => array( 'key' => 'main_custom_styling',      'p' => 'main' ),
		'header_bottombar' => array( 'key' => 'bottombar_custom_styling', 'p' => 'bottombar' ),
	);
	foreach ( $nested as $opt_id => $info ) {
		$root = fw_get_db_settings_option( $opt_id, array() );
		if ( ! is_array( $root ) || ! isset( $root[ $info['key'] ]['yes'][ $info['p'] . '_border_sides' ] ) ) {
			continue;
		}
		$new = $to_array( $root[ $info['key'] ]['yes'][ $info['p'] . '_border_sides' ] );
		if ( $new !== null ) {
			$root[ $info['key'] ]['yes'][ $info['p'] . '_border_sides' ] = $new;
			fw_set_db_settings_option( $opt_id, $root );
		}
	}

	$top_stores = array(
		'pre_footer_custom_styling'  => 'pre_footer',
		'main_footer_custom_styling' => 'main_footer',
		'post_footer_custom_styling' => 'post_footer',
		'copyright_custom_styling'   => 'copyright',
	);
	foreach ( $top_stores as $opt_id => $prefix ) {
		$styling = fw_get_db_settings_option( $opt_id, array() );
		if ( ! is_array( $styling ) || ! isset( $styling['yes'][ $prefix . '_border_sides' ] ) ) {
			continue;
		}
		$new = $to_array( $styling['yes'][ $prefix . '_border_sides' ] );
		if ( $new !== null ) {
			$styling['yes'][ $prefix . '_border_sides' ] = $new;
			fw_set_db_settings_option( $opt_id, $styling );
		}
	}

	// Footer Layout top-level sides.
	$new = $to_array( fw_get_db_settings_option( 'footer_border_sides' ) );
	if ( $new !== null ) {
		fw_set_db_settings_option( 'footer_border_sides', $new );
	}

	update_option( 'unysonplus_hf_border_sides_array_migrated', 1 );
}
add_action( 'admin_init', 'unysonplus_hf_migrate_border_sides_array', 21 );
endif;

if ( ! function_exists( 'unysonplus_hf_migrate_standalone_borders' ) ) :
/**
 * One-time migration for the other Theme Settings borders that moved from separate
 * width/style/color leaves to a single combined `multi-inline` row:
 *   - Footer Layout → Top Border: footer_border_top_{width,style,color} → footer_border_top
 *   - General → Sidebar → Border:  layout_sidebar_border_{width,color}   → layout_sidebar_border
 * The consumers (theme-vars.php) already fall back to the legacy leaves, but the new
 * combined control would render empty for pre-combine saves; this folds the leaves into
 * the combined array so the editor reflects the saved value. Idempotent, gated by an
 * option flag, only writes when it actually changes something.
 */
function unysonplus_hf_migrate_standalone_borders() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}
	if ( get_option( 'unysonplus_hf_standalone_borders_migrated' ) ) {
		return;
	}

	$to_unit = function ( $v ) {
		if ( is_array( $v ) ) { return $v; }
		$v = trim( (string) $v );
		return $v === '' ? array( 'value' => '', 'unit' => 'px' ) : array( 'value' => $v, 'unit' => 'px' );
	};
	$to_color = function ( $v ) {
		if ( is_array( $v ) ) { return $v; }
		return is_string( $v ) && $v !== '' ? array( 'predefined' => $v, 'custom' => '' ) : array( 'predefined' => '', 'custom' => '' );
	};

	// Footer Top Border — top-level leaves.
	$existing = fw_get_db_settings_option( 'footer_border_top' );
	$already  = is_array( $existing ) && ( isset( $existing['width'] ) || isset( $existing['color'] ) );
	if ( ! $already ) {
		$w = fw_get_db_settings_option( 'footer_border_top_width' );
		$s = fw_get_db_settings_option( 'footer_border_top_style' );
		$c = fw_get_db_settings_option( 'footer_border_top_color' );
		if ( ( is_array( $w ) && $w !== array() && ( ! isset( $w['value'] ) || $w['value'] !== '' ) )
			|| ( is_string( $w ) && trim( $w ) !== '' )
			|| ( is_array( $c ) && ( ! empty( $c['predefined'] ) || ! empty( $c['custom'] ) ) )
			|| ( is_string( $c ) && $c !== '' ) ) {
			fw_set_db_settings_option( 'footer_border_top', array(
				'width' => $to_unit( $w ),
				'style' => is_string( $s ) && $s !== '' ? $s : 'solid',
				'color' => $to_color( $c ),
			) );
		}
	}

	// Sidebar Border — leaves live inside the general_sidebar option array.
	$sidebar = fw_get_db_settings_option( 'general_sidebar', array() );
	if ( is_array( $sidebar )
		&& ! ( isset( $sidebar['layout_sidebar_border'] ) && is_array( $sidebar['layout_sidebar_border'] )
			&& ( isset( $sidebar['layout_sidebar_border']['width'] ) || isset( $sidebar['layout_sidebar_border']['color'] ) ) ) ) {
		$w = isset( $sidebar['layout_sidebar_border_width'] ) ? $sidebar['layout_sidebar_border_width'] : '';
		$c = isset( $sidebar['layout_sidebar_border_color'] ) ? $sidebar['layout_sidebar_border_color'] : '';
		$has = ( is_array( $w ) && $w !== array() && ( ! isset( $w['value'] ) || $w['value'] !== '' ) )
			|| ( is_string( $w ) && trim( $w ) !== '' )
			|| ( is_array( $c ) && ( ! empty( $c['predefined'] ) || ! empty( $c['custom'] ) ) )
			|| ( is_string( $c ) && $c !== '' );
		if ( $has ) {
			$sidebar['layout_sidebar_border'] = array(
				'width' => $to_unit( $w ),
				'style' => 'solid',
				'color' => $to_color( $c ),
			);
			unset( $sidebar['layout_sidebar_border_width'], $sidebar['layout_sidebar_border_color'] );
			fw_set_db_settings_option( 'general_sidebar', $sidebar );
		}
	}

	update_option( 'unysonplus_hf_standalone_borders_migrated', 1 );
}
add_action( 'admin_init', 'unysonplus_hf_migrate_standalone_borders' );
endif;
