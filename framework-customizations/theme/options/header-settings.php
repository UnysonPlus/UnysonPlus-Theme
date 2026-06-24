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
						'title'   => __( 'Layout', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'header_layout_box' => [
								'title'   => __( 'Layout Settings', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'header-layout' ),
								],
							],
						],
					],
					'tab_menu' => [
						'title'   => __( 'Menu', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'header_menu_box' => [
								'title'   => __( 'Navigation Menu', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'header-menu' ),
								],
							],
						],
					],
					'tab_topbar' => [
						'title'   => __( 'Top Bar', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'header_topbar_box' => [
								'title'   => __( 'Top Bar', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'header-topbar' ),
								],
							],
						],
					],
					'tab_main' => [
						'title'   => __( 'Main Header', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'header_main_box' => [
								'title'   => __( 'Main Header', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'header-main' ),
								],
							],
						],
					],
					'tab_bottombar' => [
						'title'   => __( 'Bottom Bar', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'header_bottombar_box' => [
								'title'   => __( 'Bottom Bar', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'header-bottombar' ),
								],
							],
						],
					],
				]
			],
		]
	]
];