<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = [
	'general_settings_container' => [
		'title'   => __( 'General', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'general' => [
				'title'   => __( 'General Settings', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					'tab_layout' => [
						'title'   => __( 'Layout', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'tab_options_box' => [
								'title'   => __( 'Theme Layout', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'general-layout' ),
								],
							],
						],
					],
					'tab_typography' => [
						'title'   => __( 'Typography', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'typography_box' => [
								'title'   => __( 'Typography', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'general-typography' ),
								],
							],
						],
					],
					/*
					 * Color Presets, Spacing/Gap, Font Sizes, Buttons, Borders and
					 * Tables presets are now owned by the PLUGIN and edited at
					 * Unyson+ → Extensions → Shortcodes → Settings (stored
					 * theme-independently in fw_ext_settings_options:shortcodes).
					 * The theme still consumes them via the unysonplus_get_*()
					 * getters, so site styling is unchanged.
					 */
				],
			],
		],
	],
];
