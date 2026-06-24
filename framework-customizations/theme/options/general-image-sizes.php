<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * General → Image Sizes — define custom WordPress image sizes (theme settings).
 *
 * This file ONLY declares the option schema. The saved sizes are registered with
 * add_image_size() on `after_setup_theme` by inc/includes/image-sizes.php — an
 * options file is loaded only on the settings screen, so registration must NOT
 * live here or the sizes would never exist on the front end.
 */

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
