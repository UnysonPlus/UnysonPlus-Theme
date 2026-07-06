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
					'tab_presets' => [
						'title'   => __( 'Presets', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'pages_presets_box' => [
								'title'   => __( 'Page Presets', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'pages_presets' => [
										'type'         => 'preset-loader',
										'label'        => __( 'Page Presets', 'unysonplus' ),
										'desc'         => __( 'Start from a whole-page default look — Standard, Sidebar, Full-Width Landing, Docs or Boxed Editorial — then fine-tune under Defaults. Or upload a preset JSON you exported earlier.', 'unysonplus' ),
										'preset_group' => 'general_pages',
									],
								],
							],
						],
					],
					'tab_layout' => [
						'title'   => __( 'Layout', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'pages_layout_box' => [
								'title'   => __( 'Page Layout', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'pages-layout' ),
								],
							],
						],
					],
					'tab_hero' => [
						'title'   => __( 'Page Title / Hero', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'pages_hero_box' => [
								'title'   => __( 'Hero Header', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'pages-hero' ),
								],
							],
						],
					],
					'tab_defaults' => [
						'title'   => __( 'Defaults', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'pages_defaults_box' => [
								'title'   => __( 'Header / Footer & Elements', 'unysonplus' ),
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
