<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Pages → Page Title / Hero sub-tab.
 *
 * Site-wide default Hero header (the full-width banner at the top of a page).
 * Stored under `pages_hero` and merged into unysonplus_pages_get(); consumed by
 * inc/includes/layout.php (unysonplus_get_page_hero_data / unysonplus_render_page_hero),
 * where a per-page Hero (Page → Page Settings → Hero Header) still overrides these.
 *
 * The image/height keys keep their historical ids (default_page_header_image /
 * default_page_header_height) so existing saved values + consumers stay valid.
 */

// Overlay colour — preset-linked (house style), guarded when the shortcodes helper
// isn't loaded. Resolved to CSS by layout.php via unysonplus_preset_color_to_css().
$overlay_color_field = function_exists( 'sc_color_field_compact' )
	? sc_color_field_compact( [
		'label'  => __( 'Default Overlay Color', 'unysonplus' ),
		'desc'   => __( 'Tint laid over the hero image for legible text. Pair with the opacity below.', 'unysonplus' ),
		'kind'   => 'bg',
		'picker' => 'rgba-color-picker',
	] )
	: [ 'label' => __( 'Default Overlay Color', 'unysonplus' ), 'type' => 'rgba-color-picker', 'value' => '' ];

$options = [
	'pages_hero' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_pages_hero' => [
				'type'    => 'group',
				'options' => [
					'default_page_header_image' => [
						'label' => __( 'Default Hero Image', 'unysonplus' ),
						'desc'  => __( 'Full-width banner shown at the top of pages that have no per-page Hero image. Leave empty for no hero by default.', 'unysonplus' ),
						'type'  => 'upload',
						'value' => [],
					],
					'default_page_header_height' => [
						'label'   => __( 'Default Hero Height', 'unysonplus' ),
						'type'    => 'radio',
						'value'   => 'auto',
						'choices' => [
							'auto'       => __( 'Auto', 'unysonplus' ),
							'small'      => __( 'Small (220px)', 'unysonplus' ),
							'medium'     => __( 'Medium (380px)', 'unysonplus' ),
							'large'      => __( 'Large (560px)', 'unysonplus' ),
							'fullscreen' => __( 'Fullscreen (100vh)', 'unysonplus' ),
						],
					],
					'default_hero_align' => [
						'label'   => __( 'Default Title Position', 'unysonplus' ),
						'desc'    => __( 'Vertical position of the page title within the hero.', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'center',
						'choices' => [
							'top'    => __( 'Top', 'unysonplus' ),
							'center' => __( 'Center', 'unysonplus' ),
							'bottom' => __( 'Bottom', 'unysonplus' ),
						],
					],
					'default_hero_overlay_color'   => $overlay_color_field,
					'default_hero_overlay_opacity' => [
						'label'      => __( 'Default Overlay Opacity', 'unysonplus' ),
						'desc'       => __( '0 = transparent, 100 = opaque. Only used when an Overlay Color is set.', 'unysonplus' ),
						'type'       => 'slider',
						'value'      => 0,
						'properties' => [ 'min' => 0, 'max' => 100, 'step' => 5 ],
					],
				],
			],
		],
	],
];
