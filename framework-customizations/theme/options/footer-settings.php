<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = [
	'footer_container' => [
		'title'   => __( 'Footer', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'footer' => [
				'title'   => __( 'Footer Settings', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					'tab_layout' => [
						'title'   => __( 'Footer Layout', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'footer_widget_box' => [
							'title'   => __( 'Overall Footer Layout', 'unysonplus' ),
							'type'    => 'box',
							'options' => [
								fw()->theme->get_options( 'footer-layout' ),
								],
							],
						],
					],
					
					'tab_pre' => [
						'title'   => __( 'Pre-Footer', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'footer_widget_box' => [
								'title'   => __( 'Pre Footer', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'footer-pre' ),
								],
							],
						],
					],
					'tab_main' => [
						'title'   => __( 'Main Footer', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'footer_menu_box' => [
								'title'   => __( 'Main Footer', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'footer-main' ),
								],
							],
						],
					], 
					'tab_post' => [
						'title'   => __( 'Post-Footer', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'footer_widget_box' => [
								'title'   => __( 'Post Footer', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'footer-post' ),
								],
							],
						],
					],
					'tab_copyright' => [
						'title'   => __( 'Copyright', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'footer_copyright_box' => [
								'title'   => __( 'Footer Copyright Settings', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'footer-copyright' ),
								],
							],
						],
					],

				],
			],
		],
	],
];
