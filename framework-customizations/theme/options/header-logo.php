<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = [
	'header_logo' => [
		'type' => 'multi',
		'label' => false,
		/*'attr' => array(
			'class' => '',
		),*/
		'inner-options' => [
			'name'    	=> [
				'label' => __( 'Logo', 'unysonplus' ),
				'desc'  => __( 'Enter your website\'s name', 'unysonplus' ),
				'type'  => 'text',
				'value' => get_bloginfo( 'name' )
			],
			'color'     => [
				'label' => __( '', 'unysonplus' ),
				'desc' 	=> __( 'Text logo color', 'unysonplus' ),
				'type'  => 'color-picker',
				'value' => '#000000',
			],
			'image'  => [
				'label' => __( 'Logo Upload', 'unysonplus' ),
				'desc'  => __( 'Upload your website logo', 'unysonplus' ),
				'type'  => 'upload',
			],
			'width'                => [
				'label' => __( 'Width', 'unysonplus' ),
				'type'  => 'short-text',
				'value' => '',
				'desc'  => __( 'The logo\'s width. Input value in pixel without the \'px\' sign. i.e.: <strong>260</strong>','unysonplus' ),
			],
			'tagline'   => [
				'label'        => __( 'Hide Tagline', 'unysonplus' ),
				'type'         => 'switch',
				'left-choice'  => [
					'value' => '',
					'label' => __( 'No', 'unysonplus' )
				],
				'right-choice' => [
					'value' => ' d-none',
					'label' => __( 'Yes', 'unysonplus' )
				],
				'value'  	=> '',
				'desc'    => __( 'Select Yes to hide the Tagline','unysonplus' ),
			],
		],
	],			
];