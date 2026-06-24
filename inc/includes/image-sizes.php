<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }

/**
 * Register the custom image sizes defined in General → Image Sizes
 * (`theme_image_sizes`, schema in framework-customizations/theme/options/general-image-sizes.php).
 *
 * Runs on after_setup_theme so the sizes exist for every front-end request and in
 * the media library — unlike the options file, which WordPress only loads on the
 * settings screen.
 */

if ( ! function_exists( 'unysonplus_register_custom_image_sizes' ) ) :
function unysonplus_register_custom_image_sizes() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return; }

	$sizes = fw_get_db_settings_option( 'theme_image_sizes' );
	if ( empty( $sizes ) || ! is_array( $sizes ) ) { return; }

	// Saved crop value → add_image_size() $crop argument.
	$crop_map = array(
		'false'         => false,
		'true'          => true,
		'top-left'      => array( 'left', 'top' ),
		'top-center'    => array( 'center', 'top' ),
		'top-right'     => array( 'right', 'top' ),
		'center-left'   => array( 'left', 'center' ),
		'center'        => array( 'center', 'center' ),
		'center-right'  => array( 'right', 'center' ),
		'bottom-left'   => array( 'left', 'bottom' ),
		'bottom-center' => array( 'center', 'bottom' ),
		'bottom-right'  => array( 'right', 'bottom' ),
	);

	foreach ( $sizes as $size ) {
		if ( empty( $size['name'] ) ) { continue; }
		$name   = sanitize_title_with_dashes( $size['name'] );
		$width  = (int) preg_replace( '/[^0-9]/', '', isset( $size['width'] ) ? $size['width'] : '' );
		$height = (int) preg_replace( '/[^0-9]/', '', isset( $size['height'] ) ? $size['height'] : '' );
		if ( $name === '' || ( $width === 0 && $height === 0 ) ) { continue; }
		$crop_key = isset( $size['crop'] ) ? $size['crop'] : 'false';
		$crop     = array_key_exists( $crop_key, $crop_map ) ? $crop_map[ $crop_key ] : false;
		add_image_size( $name, $width, $height, $crop );
	}
}
endif;
add_action( 'after_setup_theme', 'unysonplus_register_custom_image_sizes', 20 );
