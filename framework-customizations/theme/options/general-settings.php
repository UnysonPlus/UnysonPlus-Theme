<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

// Color palette / buttons / borders / spacing presets are owned by the plugin
// (stored theme-independently, shared with the page builder). The Colors tab below
// points there so they're discoverable from Theme Settings.
$colors_settings_url = function_exists( 'sc_theme_settings_url' )
	? sc_theme_settings_url( 'colors' )
	: admin_url( 'admin.php?page=fw-extensions&sub-page=extension&extension=shortcodes' );

$colors_pointer_html = '<div style="max-width:70ch">'
	. '<p><strong>' . esc_html__( 'Color palette, buttons, borders &amp; spacing presets are managed in the Unyson+ plugin.', 'unysonplus' ) . '</strong></p>'
	. '<p>' . esc_html__( 'They are stored independently of the theme (so they survive a theme switch) and are shared with the page builder, which is why they live outside Theme Settings.', 'unysonplus' ) . '</p>'
	. '<p><a class="button button-primary" href="' . esc_url( $colors_settings_url ) . '">' . esc_html__( 'Open Color &amp; Button Settings', 'unysonplus' ) . '</a></p>'
	. '</div>';

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
					'tab_colors' => [
						'title'   => __( 'Colors', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'colors_box' => [
								'title'   => __( 'Colors &amp; Buttons', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'colors_pointer' => [
										'type'  => 'html-full',
										'label' => false,
										'html'  => $colors_pointer_html,
									],
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
					'tab_preloader' => [
						'title'   => __( 'Preloader', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'preloader_box' => [
								'title'   => __( 'Preloader', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'general-preloader' ),
								],
							],
						],
					],
					'tab_scroll' => [
						'title'   => __( 'Scrolling', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'scroll_box' => [
								'title'   => __( 'Scrolling', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'general-scroll' ),
								],
							],
						],
					],
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
