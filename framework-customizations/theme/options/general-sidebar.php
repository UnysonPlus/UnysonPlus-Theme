<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * General → Sidebar sub-tab.
 *
 * Theme-wide default sidebar placement and dimensions. Split out of the old
 * General → Layout "Header & Sidebar" section so each concern has its own tab.
 *
 * Stored under the `general_sidebar` multi key. `unysonplus_layout_get()` merges
 * `general_layout` + `general_sidebar` + `general_preloader`, so every read site
 * keeps calling `unysonplus_layout_get( 'layout_sidebar_*' )` unchanged.
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

$options = [
	'general_sidebar' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_sidebar' => [
				'type'    => 'group',
				'options' => [
					'layout_sidebar_position' => [
						'label'   => __( 'Default Sidebar Position', 'unysonplus' ),
						'desc'    => __( 'Theme-wide default for where the sidebar renders on pages/posts. Individual posts/pages can override via their own meta options.', 'unysonplus' ),
						'type'    => 'image-picker',
						'value'   => 'right',
						'choices' => $picker( [
							'none'  => 'sidebar-none.svg',
							'left'  => 'sidebar-left.svg',
							'right' => 'sidebar-right.svg',
						] ),
					],
					'layout_sidebar_width' => [
						'label' => __( 'Sidebar Width', 'unysonplus' ),
						'desc'  => __( 'Width of the sidebar column (when Sidebar Position is Left or Right).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '18.75', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'layout_sidebar_gap' => [
						'label' => __( 'Content / Sidebar Gap', 'unysonplus' ),
						'desc'  => __( 'Horizontal gap between the content and the sidebar column.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '2.5', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'layout_sidebar_sticky' => [
						'label'        => __( 'Sticky Sidebar', 'unysonplus' ),
						'desc'         => __( 'Make the sidebar follow the page as it scrolls (desktop only).', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
					],
				],
			],
		],
	],
];
