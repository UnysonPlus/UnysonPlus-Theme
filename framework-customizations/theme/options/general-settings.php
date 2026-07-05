<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

// The Color / Button / Border / Spacing / Table presets are provided by the
// plugin as a dedicated "Components" section in this same Theme Settings page
// (see the plugin's includes/theme-settings-presets.php), stored theme-scoped.
// The old General → Colors pointer tab is therefore retired.

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
							'custom_fonts_box' => [
								'title'   => __( 'Custom Fonts', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'general-fonts' ),
								],
							],
						],
					],
					'tab_sidebar' => [
						'title'   => __( 'Sidebar', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'sidebar_box' => [
								'title'   => __( 'Sidebar', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'general-sidebar' ),
								],
							],
						],
					],
					// Preloader + Scrolling moved to the top-level "Site-wide UX" tab
					// (inc/includes/site-wide-ux.php) — consolidated with Scroll to Top and,
					// when the Animation Engine is active, merged into its richer tab.
					'tab_image_sizes' => [
						'title'   => __( 'Image Sizes', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'image_sizes_box' => [
								'title'   => __( 'Custom Image Sizes', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'general-image-sizes' ),
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
