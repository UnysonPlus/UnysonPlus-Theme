<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * General → Sidebar sub-tab.
 *
 * Theme-wide sidebar placement, per-context defaults, responsive/sticky behaviour
 * and styling. Split out of the old General → Layout "Header & Sidebar" section.
 *
 * Stored under the `general_sidebar` multi key. `unysonplus_layout_get()` merges
 * `general_layout` + `general_sidebar` + `general_preloader`, so every read site
 * keeps calling `unysonplus_layout_get( 'layout_sidebar_*' )` unchanged. Per-context
 * defaults feed `unysonplus_resolve_layout( 'sidebar' )`; styling/behaviour feed the
 * `--sidebar-*` / `--widget-*` CSS vars (theme-vars.php) + sidebar body classes.
 *
 * Image-picker previews live at assets/svg/layout/*.svg.
 */

$uri = get_template_directory_uri();
$svg = $uri . '/assets/svg/layout';

/* Build the image-picker `choices` array for any pair of {value => svg-filename}. */
$picker = function ( array $pairs, $height_small = 70, $height_large = 140 ) use ( $svg ) {
	$out = [];
	foreach ( $pairs as $value => $file ) {
		$out[ $value ] = [
			'small' => [ 'height' => $height_small, 'src' => $svg . '/' . $file ],
			'large' => [ 'height' => $height_large, 'src' => $svg . '/' . $file ],
		];
	}
	return $out;
};

/* Per-context default selector (Inherit / None / Left / Right). */
$ctx_select = function ( $label, $desc ) {
	return [
		'label'   => $label,
		'desc'    => $desc,
		'type'    => 'select',
		'value'   => 'inherit',
		'choices' => [
			'inherit' => __( 'Inherit global default', 'unysonplus' ),
			'none'    => __( 'No Sidebar', 'unysonplus' ),
			'left'    => __( 'Left', 'unysonplus' ),
			'right'   => __( 'Right', 'unysonplus' ),
		],
	];
};

/* Palette-preset color control (falls back to a raw picker if the shortcodes helper
   isn't loaded). kind 'bg' → bg-{slug} choices, 'text' → text-{slug}. */
$color = function ( $label, $desc, $kind ) {
	if ( function_exists( 'sc_color_field_compact' ) ) {
		return sc_color_field_compact( [ 'label' => $label, 'desc' => $desc, 'kind' => $kind ] );
	}
	return [ 'label' => $label, 'desc' => $desc, 'type' => 'color-picker', 'value' => '' ];
};

$options = [
	'general_sidebar' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [

			/* ===== Placement ===== */
			'group_sidebar' => [
				'type'    => 'group',
				'options' => [
					'layout_sidebar_position' => [
						'label'   => __( 'Default Sidebar Position', 'unysonplus' ),
						'desc'    => __( 'Theme-wide default for where the sidebar renders. Per-context defaults (below) and individual post/page meta can override it.', 'unysonplus' ),
						'type'    => 'image-picker',
						'value'   => 'right',
						'choices' => $picker( [
							'none'  => 'sidebar-none.svg',
							'left'  => 'sidebar-left.svg',
							'right' => 'sidebar-right.svg',
						] ),
					],

					/* Per-context overrides of the global default — each Inherits unless set. */
					'layout_sidebar_context_post'    => $ctx_select( __( 'Single Posts', 'unysonplus' ), __( 'Sidebar for single blog posts.', 'unysonplus' ) ),
					'layout_sidebar_context_archive' => $ctx_select( __( 'Blog & Archives', 'unysonplus' ), __( 'Sidebar for the blog index and category / tag / author / date / CPT archives.', 'unysonplus' ) ),
					'layout_sidebar_context_search'  => $ctx_select( __( 'Search Results', 'unysonplus' ), __( 'Sidebar for the search results page.', 'unysonplus' ) ),
					'layout_sidebar_context_404'     => $ctx_select( __( '404 Page', 'unysonplus' ), __( 'Sidebar for the "not found" page.', 'unysonplus' ) ),

					'layout_sidebar_width' => [
						'label' => __( 'Sidebar Width', 'unysonplus' ),
						'desc'  => __( 'Width of the sidebar column (when Position is Left or Right). Accepts rem/px/em or a percentage of the row.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em', '%' ],
						'value' => [ 'value' => '18.75', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'layout_sidebar_gap' => [
						'label' => __( 'Content / Sidebar Gap', 'unysonplus' ),
						'desc'  => __( 'Horizontal gap between the content and the sidebar column.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em', '%' ],
						'value' => [ 'value' => '2.5', 'unit' => 'rem' ],
						'min'   => 0,
					],
				],
			],

			/* ===== Responsive & Sticky ===== */
			'group_sidebar_responsive' => [
				'type'    => 'group',
				'options' => [
					'layout_sidebar_sticky' => [
						'label'        => __( 'Sticky Sidebar', 'unysonplus' ),
						'desc'         => __( 'Make the sidebar follow the page as it scrolls (desktop only).', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
					],
					'layout_sidebar_sticky_offset' => [
						'label' => __( 'Sticky Offset', 'unysonplus' ),
						'desc'  => __( 'Gap from the top of the viewport where the sticky sidebar pins (leave room for a sticky header).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '24', 'unit' => 'px' ],
						'min'   => 0,
					],
					'layout_sidebar_mobile_order' => [
						'label'   => __( 'Mobile Order', 'unysonplus' ),
						'desc'    => __( 'When the layout stacks on smaller screens, whether the sidebar sits below or above the content.', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'below',
						'choices' => [
							'below' => __( 'Below content', 'unysonplus' ),
							'above' => __( 'Above content', 'unysonplus' ),
						],
					],
					'layout_sidebar_mobile_hide' => [
						'label'        => __( 'Hide on Mobile', 'unysonplus' ),
						'desc'         => __( 'Hide the sidebar entirely on stacked (mobile) layouts; content spans full width.', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
					],
					'layout_sidebar_collapse_bp' => [
						'label'   => __( 'Stack Below', 'unysonplus' ),
						'desc'    => __( 'Screen width at which the content + sidebar stop sitting side by side and stack.', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'lg',
						'choices' => [
							'lg' => __( '992px (tablet & phone)', 'unysonplus' ),
							'md' => __( '768px (phone only)', 'unysonplus' ),
						],
					],
				],
			],

			/* ===== Styling ===== */
			'group_sidebar_style' => [
				'type'    => 'group',
				'options' => [
					'layout_sidebar_bg'           => $color( __( 'Sidebar Background', 'unysonplus' ), __( 'Background of the sidebar column. Leave empty for transparent.', 'unysonplus' ), 'bg' ),
					'layout_sidebar_padding' => [
						'label' => __( 'Sidebar Padding', 'unysonplus' ),
						'desc'  => __( 'Inner padding on the sidebar column (useful with a background or border).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'layout_sidebar_border_width' => [
						'label' => __( 'Border Width', 'unysonplus' ),
						'desc'  => __( 'Border around the sidebar column. Leave 0 for none.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],
					'layout_sidebar_border_color' => $color( __( 'Border Color', 'unysonplus' ), __( 'Color of the sidebar border (when Border Width is set).', 'unysonplus' ), 'bg' ),
					'layout_sidebar_radius' => [
						'label' => __( 'Corner Radius', 'unysonplus' ),
						'desc'  => __( 'Rounding of the sidebar column corners (with a background or border).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],
					'layout_sidebar_widget_spacing' => [
						'label' => __( 'Widget Spacing', 'unysonplus' ),
						'desc'  => __( 'Vertical gap between widgets in the sidebar.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'layout_sidebar_widget_title_size' => [
						'label' => __( 'Widget Title Size', 'unysonplus' ),
						'desc'  => __( 'Font size of widget titles.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'layout_sidebar_widget_title_weight' => [
						'label'   => __( 'Widget Title Weight', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '',
						'choices' => [
							''    => __( 'Default', 'unysonplus' ),
							'400' => __( 'Normal (400)', 'unysonplus' ),
							'500' => __( 'Medium (500)', 'unysonplus' ),
							'600' => __( 'Semibold (600)', 'unysonplus' ),
							'700' => __( 'Bold (700)', 'unysonplus' ),
						],
					],
					'layout_sidebar_widget_title_uppercase' => [
						'label'        => __( 'Uppercase Widget Titles', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
					],
					'layout_sidebar_widget_title_color' => $color( __( 'Widget Title Color', 'unysonplus' ), __( 'Color of widget titles. Leave empty to inherit the body heading color.', 'unysonplus' ), 'text' ),
				],
			],
		],
	],
];
