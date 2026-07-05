<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Site-wide UX → Scrolling sub-tab (leaf).
 *
 * Scroll-related UX polish — smooth anchor scrolling and a basic scroll-progress bar.
 * Composed into the "Site-wide UX" tab (site-wide-ux-settings.php).
 *
 * Naming: leaf option files are prefixed with the tab that owns them (site-wide-ux-*).
 * Stored under the `general_scroll` multi key — KEPT for back-compat (renaming the FILE never
 * changes the storage key), so `unysonplus_layout_get( 'layout_smooth_scroll' /
 * 'layout_scroll_progress*' )` is unchanged.
 */

$options = [
	'general_scroll' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_scroll' => [
				'type'    => 'group',
				'options' => [
					'layout_smooth_scroll' => [
						'label'        => __( 'Smooth Scroll for Anchor Links', 'unysonplus' ),
						'desc'         => __( 'Enables CSS scroll-behavior: smooth for in-page anchor navigation.', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
					],
					'layout_scroll_progress' => [
						'label'        => __( 'Scroll Progress Bar', 'unysonplus' ),
						'desc'         => __( 'Thin gradient bar at the top of the viewport that fills as the user scrolls.', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
					],
					'layout_scroll_progress_color' => [
						'label' => __( 'Scroll Progress Bar Color', 'unysonplus' ),
						'desc'  => __( 'Color of the scroll progress bar (when enabled).', 'unysonplus' ),
						'type'  => 'color-picker',
						'value' => '#0d6efd',
					],
				],
			],
		],
	],
];
