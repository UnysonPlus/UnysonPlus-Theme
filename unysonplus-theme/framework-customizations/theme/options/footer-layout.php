<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = [
	'footer_bg_color' => [
		'label' => __( 'Background Color', 'unysonplus' ),
		'type'  => 'rgba-color-picker',
		'value' => '',
		'desc'  => __( 'e.g. rgba(33, 37, 41, 1). Applied to the entire footer wrapper.', 'unysonplus' ),
	],
	'footer_bg_image' => [
		'label' => __( 'Background Image', 'unysonplus' ),
		'type'  => 'upload',
		'desc'  => __( 'A single background image spanning the full footer.', 'unysonplus' ),
	],
	'footer_bg_overlay' => [
		'label' => __( 'Background Overlay Opacity', 'unysonplus' ),
		'type'  => 'slider',
		'value' => 80,
		'properties' => [
			'min' => 0,
			'max' => 100,
			'step' => 5,
		],
		'desc' => __( 'Overlay opacity over the background image (0 = transparent, 100 = solid). Only used when a background image is set.', 'unysonplus' ),
	],
	'footer_text_color' => [
		'label' => __( 'Text Color', 'unysonplus' ),
		'type'  => 'color-picker',
		'value' => '',
		'desc'  => __( 'e.g. #ffffff. Default text color for the entire footer.', 'unysonplus' ),
	],
	'footer_link_color' => [
		'label' => __( 'Link Color', 'unysonplus' ),
		'type'  => 'color-picker',
		'value' => '',
		'desc'  => __( 'e.g. #adb5bd. Default link color for the entire footer.', 'unysonplus' ),
	],
	'footer_padding_top' => [
		'label' => __( 'Padding Top', 'unysonplus' ),
		'desc'  => __( 'Outer padding above the footer content. Blank uses the theme default.', 'unysonplus' ),
		'type'  => 'unit-input',
		'units' => [ 'rem', 'px', 'em' ],
		'value' => [ 'value' => '', 'unit' => 'rem' ],
		'min'   => 0,
	],
	'footer_padding_bottom' => [
		'label' => __( 'Padding Bottom', 'unysonplus' ),
		'desc'  => __( 'Outer padding below the footer content. Blank uses the theme default.', 'unysonplus' ),
		'type'  => 'unit-input',
		'units' => [ 'rem', 'px', 'em' ],
		'value' => [ 'value' => '', 'unit' => 'rem' ],
		'min'   => 0,
	],
	'footer_css_class' => [
		'label' => __( 'Custom CSS Class', 'unysonplus' ),
		'type'  => 'text',
		'value' => '',
		'desc'  => __( 'Add custom CSS class(es) to the footer wrapper.', 'unysonplus' ),
	],
];
