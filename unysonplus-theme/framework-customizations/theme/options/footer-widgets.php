<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
$uri = get_template_directory_uri();

$options = [
	'footer_widgets'  => [
		'type'         => 'multi-picker',
		'label'        => false,
		'desc'         => false,
		'picker'       => [
			'enabled' => [
				'label'        => __( 'Footer Widgets', 'unysonplus' ),
				'type'         => 'switch',
				'right-choice' => [
					'value' => 'yes',
					'label' => __( 'Yes', 'unysonplus' )
				],
				'left-choice'  => [
					'value' => 'no',
					'label' => __( 'No', 'unysonplus' )
				],
				'value'        => 'yes',
				'desc'  => __('Show footer widgets?', 'unysonplus'),
			]
		],
		'choices'      => [
			'yes'  => [
				'widget_group' => [
					'type' => 'group',
					'options' => [
						'style' => [
							'type'         => 'multi-picker',
							'label'        => false,
							'desc'         => false,
							'picker'       => [
								'selected' => [
									'label'   => __('Style', 'unysonplus'),
									'desc'    => __('Set the columns and style', 'unysonplus'),
									'type'    => 'select',
									'value'   => 'col-md-4',
									'choices' => [
										'col-md-12' => __('1 column', 'unysonplus'),
										'col-md-6' 	=> __('2 equal columns', 'unysonplus'),
										'col-md-6-a'=> __('2 columns (2/3 + 1/3)', 'unysonplus'),
										'col-md-6-b'=> __('2 columns (1/3 + 2/3)', 'unysonplus'),
										'col-md-4'  => __('3 equal columns', 'unysonplus'),
										'col-md-4-a'=> __('3 columns (1/4 + 1/2 + 1/4)', 'unysonplus'),
										'col-md-4-b'=> __('3 columns (1/4 + 1/4 + 1/2)', 'unysonplus'),
										'col-md-4-c'=> __('3 columns (1/3 + 1/6 + 1/2)', 'unysonplus'),
										'col-md-3'  => __('4 equal columns', 'unysonplus'),
										'col-md-5' => __('5 equal columns', 'unysonplus'),
									],
								],
							],
							/*'choices'      => array(
								'col-md-6'  => array(
									'img'  => array(
										'type'  => 'html',
										'label' => '',
										'desc' => '',
										'html' => __( '<img src="'.$uri.'/images/options/footer-widget-col-md-6.png">', 'unysonplus' ),
									)
								),
								'col-md-4'  => array(
									'img'  => array(
										'type'  => 'html',
										'label' => '',
										'desc' => '',
										'html' => __( '<img src="'.$uri.'/images/options/footer-widget-col-md-4.png">', 'unysonplus' ),
									)
								),
							), */
						],
						'container' => [
							'label'   => __( 'Container', 'unysonplus' ),
							'type'    => 'image-picker',
							'value'   => 'container',
							'desc'    => __( 'Container layout for the widget.', 'unysonplus' ),
							'choices' => [
								'container' => [
									'small' => [
										'height' => 70,
										'src'    => $uri . '/images/image-picker/container-thumb.png'
									],
									'large' => [
										'height' => 214,
										'src'    => $uri . '/images/image-picker/container.png'
									],
								],
								'container-fluid' => [
									'small' => [
										'height' => 70,
										'src'    => $uri . '/images/image-picker/container-fluid-thumb.png'
									],
									'large' => [
										'height' => 214,
										'src'    => $uri . '/images/image-picker/container-fluid.png'
									],
								],
							],
						], 

					],
				],
			],
		],
		'show_borders' => false,
	],	
];
