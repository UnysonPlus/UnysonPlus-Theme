<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Scroll-to-Top button options (leaf).
 *
 * Stored under the `misc_scroll_top` multi key (unchanged from when it lived in the
 * Miscellaneous tab, so `unysonplus_misc_get( 'scroll_top_*' )` still resolves). Composed
 * into the "Site-wide UX" tab (site-wide-ux-settings.php) and, when the Animation Engine is
 * active, injected into its tab (inc/includes/site-wide-ux.php).
 */

$options = [
	'misc_scroll_top' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'scroll_top_enable' => [
				'label' => __( 'Enable', 'unysonplus' ),
				'desc'  => __( 'Show a fixed-position scroll-to-top button on every page.', 'unysonplus' ),
				'type'  => 'switch',
				'value' => 'no',
			],
			'scroll_top_position' => [
				'label'   => __( 'Position', 'unysonplus' ),
				'type'    => 'radio',
				'value'   => 'right',
				'choices' => [
					'right' => __( 'Bottom-right', 'unysonplus' ),
					'left'  => __( 'Bottom-left', 'unysonplus' ),
				],
			],
			'scroll_top_design' => [
				'label'   => __( 'Design', 'unysonplus' ),
				'desc'    => __( 'Shape / style of the button. Circle, Square and Progress Ring are icon-only (the label is hidden). "Progress Ring" wraps the icon in a ring that fills as you scroll — a nice pairing with the Animation Engine.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'rounded',
				'choices' => [
					'rounded' => __( 'Rounded (default)', 'unysonplus' ),
					'circle'  => __( 'Circle', 'unysonplus' ),
					'square'  => __( 'Square', 'unysonplus' ),
					'pill'    => __( 'Pill', 'unysonplus' ),
					'outline' => __( 'Outline', 'unysonplus' ),
					'ring'    => __( 'Progress Ring', 'unysonplus' ),
				],
			],
			'scroll_top_size' => [
				'label'   => __( 'Size', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'medium',
				'choices' => [
					'small'  => __( 'Small', 'unysonplus' ),
					'medium' => __( 'Medium', 'unysonplus' ),
					'large'  => __( 'Large', 'unysonplus' ),
				],
			],
			'scroll_top_offset' => [
				'label' => __( 'Show after scrolling', 'unysonplus' ),
				'desc'  => __( 'Distance scrolled before the button appears. px is an absolute distance; vh is a fraction of the screen height (100vh = one full screen).', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px', 'vh' ],
				'value' => [ 'value' => '300', 'unit' => 'px' ],
				'min'   => 0,
			],
			'scroll_top_text' => [
				'label' => __( 'Button label', 'unysonplus' ),
				'desc'  => __( 'Optional text shown next to the arrow icon. Leave empty for icon-only.', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			],
			'scroll_top_bg_color' => function_exists( 'sc_color_field_compact' )
				? sc_color_field_compact( [ 'label' => __( 'Background color', 'unysonplus' ), 'kind' => 'bg' ] )
				: [ 'label' => __( 'Background color', 'unysonplus' ), 'type' => 'color-picker', 'value' => '' ],
			'scroll_top_text_color' => function_exists( 'sc_color_field_compact' )
				? sc_color_field_compact( [ 'label' => __( 'Icon / text color', 'unysonplus' ), 'kind' => 'text' ] )
				: [ 'label' => __( 'Icon / text color', 'unysonplus' ), 'type' => 'color-picker', 'value' => '#ffffff' ],
		],
	],
];
