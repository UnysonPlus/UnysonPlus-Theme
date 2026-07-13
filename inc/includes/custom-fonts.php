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

if ( ! function_exists( 'unysonplus_allow_font_uploads' ) ) :
/**
 * Allow web-font uploads. WordPress blocks .woff2 / .woff (and .ttf / .otf) uploads by
 * default, which makes the Custom Fonts uploader above UNUSABLE — the Media Library
 * rejects the file ("Sorry, this file type is not permitted for security reasons.").
 * Permit the web-font formats for users who can manage the site (the only ones who reach
 * Theme Settings → Custom Fonts). Fonts are inert assets, so the added surface is minimal.
 *
 * @param array $mimes
 * @return array
 */
function unysonplus_allow_font_uploads( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['woff']  = 'font/woff';
		$mimes['woff2'] = 'font/woff2';
		$mimes['ttf']   = 'font/ttf';
		$mimes['otf']   = 'font/otf';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'unysonplus_allow_font_uploads' );
endif;

if ( ! function_exists( 'unysonplus_custom_fonts_admin_preview' ) ) :
/**
 * Load the custom-font @font-face in wp-admin too. The Typography (typography-v2)
 * Font Face dropdown previews each option in its OWN font-family, and custom
 * families are added to that list — but the front-end generated stylesheet (which
 * carries the @font-face) isn't enqueued in admin, so the preview would fall back
 * to a system font. Printing the @font-face here makes the picker WYSIWYG.
 */
function unysonplus_custom_fonts_admin_preview() {
	$css = unysonplus_custom_fonts_css();
	if ( $css === '' ) { return; }
	wp_register_style( 'unysonplus-custom-fonts-admin', false );
	wp_enqueue_style( 'unysonplus-custom-fonts-admin' );
	wp_add_inline_style( 'unysonplus-custom-fonts-admin', $css );
}
add_action( 'admin_enqueue_scripts', 'unysonplus_custom_fonts_admin_preview' );
endif;

if ( ! function_exists( 'unysonplus_font_filetype_fix' ) ) :
/**
 * Back-fill the ext/type for web fonts when PHP's finfo real-MIME sniff can't identify them
 * (woff2's `wOF2` / woff's `wOFF` signatures often resolve to application/octet-stream, which
 * makes wp_check_filetype_and_ext() return false and the upload fail even after upload_mimes
 * allows the extension). Only for site managers; only when the sniff came back empty.
 *
 * @param array  $data     { ext, type, proper_filename }
 * @param string $file
 * @param string $filename
 * @param array  $mimes
 * @return array
 */
function unysonplus_font_filetype_fix( $data, $file, $filename, $mimes ) {
	if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) { return $data; }
	if ( ! current_user_can( 'manage_options' ) ) { return $data; }
	$map = array( 'woff' => 'font/woff', 'woff2' => 'font/woff2', 'ttf' => 'font/ttf', 'otf' => 'font/otf' );
	$ext = strtolower( pathinfo( (string) $filename, PATHINFO_EXTENSION ) );
	if ( isset( $map[ $ext ] ) ) {
		$data['ext']  = $ext;
		$data['type'] = $map[ $ext ];
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'unysonplus_font_filetype_fix', 10, 4 );
endif;
