<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = [
	'theme_image_sizes'  => [
		'label'        => false,
		'type'         => 'addable-box',
		'value'        => [
			[
				'name'		=> 'Custom Size 1',
				'width'		=> 450,
				'height' 	=> 250,
				'crop'		=> false,
			],
		],
		//'box-controls' => array('custom' => '<small class="dashicons dashicons-smiley" title="Custom"></small>',),
		'box-options' => [
			'name'   => [
				'label' => __( 'Name', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			],
			'width'  => [
				'label' => __( 'Width', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			],
			'height'  => [
				'label' => __( 'Height', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			],
			'crop'  => [
				'label' => __( 'Crop', 'unysonplus' ),
				'type'  => 'select',
				'value' => 'false',
				'choices' => [
        	'false' 			=> __('No Crop', 'unysonplus'),
					'true' 				=> __('Cropped', 'unysonplus'),
					'top-left' 		=> __('Top Left', 'unysonplus'),
					'top-center'	=> __('Top Center', 'unysonplus'),
					'top-right'		=> __('Top Right', 'unysonplus'),
					'center-left'	=> __('Center Left', 'unysonplus'),
					'center'			=> __('Center', 'unysonplus'),
					'center-right'=> __('Center Right', 'unysonplus'),
					'bottom-left'	=> __('Bottom Left', 'unysonplus'),
					'bottom-center'=> __('Bottom Center', 'unysonplus'),
					'bottom-right'=> __('Bottom Right', 'unysonplus'),
				],
			],
		],
		'template' => '{{- name }}: {{- width }} x {{- height }}',
		//'limit' => 3,
	],
];

/**
 * Map a saved crop value to the add_image_size() $crop argument.
 * (Replaces a previously broken nested-ternary chain whose orphaned
 *  statements meant most positional crops never applied.)
 */
$crop_map = [
	'false'         => false,
	'true'          => true,
	'top-left'      => [ 'left', 'top' ],
	'top-center'    => [ 'center', 'top' ],
	'top-right'     => [ 'right', 'top' ],
	'center-left'   => [ 'left', 'center' ],
	'center'        => [ 'center', 'center' ],
	'center-right'  => [ 'right', 'center' ],
	'bottom-left'   => [ 'left', 'bottom' ],
	'bottom-center' => [ 'center', 'bottom' ],
	'bottom-right'  => [ 'right', 'bottom' ],
];

$theme_image_sizes = fw_get_db_settings_option('theme_image_sizes');
if( !empty($theme_image_sizes)) {
	foreach( $theme_image_sizes as $key => $value ) {
		$name		= sanitize_title_with_dashes($theme_image_sizes[$key]['name']);
		$width 	= preg_replace('/[^0-9]/', '', $theme_image_sizes[$key]['width']);
		$height	= preg_replace('/[^0-9]/', '', $theme_image_sizes[$key]['height']);
		$crop_key = isset( $theme_image_sizes[$key]['crop'] ) ? $theme_image_sizes[$key]['crop'] : 'false';
		$crop 	= array_key_exists( $crop_key, $crop_map ) ? $crop_map[ $crop_key ] : '';
		add_image_size( $name, $width, $height, $crop );
	}
}
