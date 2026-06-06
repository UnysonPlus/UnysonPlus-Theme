<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = [
	'pages_settings_container' => [
		'title'   => __( 'Pages', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'pages' => [
				'title'   => __( 'Pages Settings', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					'tab_defaults' => [
						'title'   => __( 'Defaults', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'pages_defaults_box' => [
								'title'   => __( 'Pages Defaults', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'general-pages' ),
								],
							],
						],
					],
				],
			],
		],
	],
];
