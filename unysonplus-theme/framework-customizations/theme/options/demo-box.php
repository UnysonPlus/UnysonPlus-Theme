<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = [
	'demo' => [
		'title'   => __( 'Demo Options', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'sub_tab_1' => [
				'title'   => __( 'Without Box', 'unysonplus' ),
				'type'    => 'tab',
				'options' => [
					fw()->theme->get_options( 'demo-2' ),
				],
			],
			'sub_tab_2' => [
				'title'   => __( 'With Box', 'unysonplus' ),
				'type'    => 'tab',
				'options' => [
					'demo_box' => [
						'title'   => __( 'Box', 'unysonplus' ),
						'type'    => 'box',
						'options' => [
							fw()->theme->get_options( 'demo' ),
						],
					],
				],
			],
		],
	],
];
