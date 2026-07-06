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
	// Line-height is UNITLESS (a ratio like 1.5) — NOT px. (Previously `(int)1.5`
	// became `line-height:1px`, crushing the text.)
	if ( isset( $typo['line-height'] ) && is_numeric( $typo['line-height'] ) && $typo['line-height'] !== '' ) {
		$d[] = 'line-height:' . ( $typo['line-height'] + 0 );
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

		$decl = array();

		// Background: color and/or image. When an image is set, fold the overlay
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

		// Typography.
		$typo_css = ! empty( $cs[ $p . '_typography' ] ) ? unysonplus_hf_typography_css( $cs[ $p . '_typography' ] ) : '';
		if ( $typo_css !== '' ) { $decl[] = $typo_css; }

		// Borders.
		$bt_w = ! empty( $cs[ $p . '_border_top_width' ] )    ? unysonplus_hf_css_val( $cs[ $p . '_border_top_width' ] )    : '';
		$bt_c = isset( $cs[ $p . '_border_top_color' ] )      ? $color( $cs[ $p . '_border_top_color' ] )                  : '';
		if ( $bt_w !== '' && $bt_c !== '' ) { $decl[] = 'border-top:' . $bt_w . ' solid ' . $bt_c; }
		$bb_w = ! empty( $cs[ $p . '_border_bottom_width' ] ) ? unysonplus_hf_css_val( $cs[ $p . '_border_bottom_width' ] ) : '';
		$bb_c = isset( $cs[ $p . '_border_bottom_color' ] )   ? $color( $cs[ $p . '_border_bottom_color' ] )               : '';
		if ( $bb_w !== '' && $bb_c !== '' ) { $decl[] = 'border-bottom:' . $bb_w . ' solid ' . $bb_c; }

		if ( ! empty( $decl ) ) {
			$css .= $sel . '{' . implode( ';', $decl ) . '}';
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
	$logo = fw_get_db_settings_option( 'header_logo', array() );
	if ( is_array( $logo ) ) {
		if ( ! empty( $logo['color']['custom'] ) ) {
			$css .= '.site-title a{color:' . unysonplus_hf_css_val( $logo['color']['custom'] ) . '}';
		}
		if ( ! empty( $logo['tagline_color']['custom'] ) ) {
			$css .= '.site-description{color:' . unysonplus_hf_css_val( $logo['tagline_color']['custom'] ) . '}';
		}
	}

	// Misc: scroll-to-top button colors.
	if ( function_exists( 'unysonplus_misc_get' ) ) {
		$st_bg = unysonplus_misc_get( 'scroll_top_bg_color', '' );
		$st_fg = unysonplus_misc_get( 'scroll_top_text_color', '' );
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
