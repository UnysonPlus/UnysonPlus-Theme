<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }

/**
 * Self-hosted custom fonts (General → Typography → Custom Fonts, option
 * `custom_fonts`).
 *
 *  - Emits @font-face into the generated stylesheet (unysonplus_custom_fonts_css(),
 *    pulled in by unysonplus_generated_css() in inc/includes/hf-custom-css.php).
 *  - Registers each family name into the typography pickers so it's selectable
 *    (filters fw_option_type_typography_standard_fonts + the v2 variant).
 */

if ( ! function_exists( 'unysonplus_custom_fonts_list' ) ) :
/**
 * Saved custom fonts, normalized. @return array[] each: family, woff2, woff, weight, style
 */
function unysonplus_custom_fonts_list() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return array(); }
	$raw = fw_get_db_settings_option( 'custom_fonts' );
	if ( empty( $raw ) || ! is_array( $raw ) ) { return array(); }

	$out = array();
	foreach ( $raw as $f ) {
		if ( empty( $f['family'] ) ) { continue; }
		$woff2 = ! empty( $f['woff2']['url'] ) ? $f['woff2']['url'] : '';
		$woff  = ! empty( $f['woff']['url'] ) ? $f['woff']['url'] : '';
		if ( $woff2 === '' && $woff === '' ) { continue; } // nothing to load
		$out[] = array(
			'family' => trim( (string) $f['family'] ),
			'woff2'  => $woff2,
			'woff'   => $woff,
			'weight' => ! empty( $f['weight'] ) ? preg_replace( '/[^0-9a-z]/', '', strtolower( (string) $f['weight'] ) ) : '400',
			'style'  => ( ! empty( $f['style'] ) && $f['style'] === 'italic' ) ? 'italic' : 'normal',
		);
	}
	return $out;
}
endif;

if ( ! function_exists( 'unysonplus_custom_fonts_css' ) ) :
/**
 * @font-face rules for the saved custom fonts. @return string
 */
function unysonplus_custom_fonts_css() {
	$css = '';
	foreach ( unysonplus_custom_fonts_list() as $f ) {
		$src = array();
		if ( $f['woff2'] !== '' ) { $src[] = "url('" . esc_url_raw( $f['woff2'] ) . "') format('woff2')"; }
		if ( $f['woff'] !== '' )  { $src[] = "url('" . esc_url_raw( $f['woff'] ) . "') format('woff')"; }
		if ( empty( $src ) ) { continue; }
		// Family name: strip quotes/braces that could break the declaration.
		$family = trim( preg_replace( '/["{};]/', '', $f['family'] ) );
		if ( $family === '' ) { continue; }
		$css .= "@font-face{font-family:'" . $family . "';src:" . implode( ',', $src )
			. ";font-weight:" . $f['weight'] . ";font-style:" . $f['style'] . ";font-display:swap;}";
	}
	return $css;
}
endif;

if ( ! function_exists( 'unysonplus_register_custom_fonts_in_picker' ) ) :
/**
 * Append custom family names to the typography pickers' "standard" font list so
 * they can be chosen for headings/body and per-section typography.
 *
 * @param array $fonts
 * @return array
 */
function unysonplus_register_custom_fonts_in_picker( $fonts ) {
	if ( ! is_array( $fonts ) ) { return $fonts; }
	foreach ( unysonplus_custom_fonts_list() as $f ) {
		if ( $f['family'] !== '' && ! in_array( $f['family'], $fonts, true ) ) {
			$fonts[] = $f['family'];
		}
	}
	return $fonts;
}
endif;
add_filter( 'fw_option_type_typography_v2_standard_fonts', 'unysonplus_register_custom_fonts_in_picker' );
add_filter( 'fw_option_type_typography_standard_fonts',    'unysonplus_register_custom_fonts_in_picker' );
