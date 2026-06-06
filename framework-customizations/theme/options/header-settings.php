<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = [
	'header_settings_container' => [
		'title'   => __( 'Header', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'header' => [
				'title'   => __( 'Header Settings', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					'tab_options' => [
						'title'   => __( 'Identity', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'tab_options_box' => [
								'title'   => __( 'Site Identity', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'header-identity' ),
								],
							],
						],
					],
					'tab_layout' => [
						'title'   => __( 'Layout', 'unyson' ),
						'type'    => 'tab',
						'options' => [
							'header_layout_box' => [
								'title'   => __( 'Layout Settings', 'unyson' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'header-layout' ),
								],
							],
						],
					],		
				]
			],
		]
	]
];