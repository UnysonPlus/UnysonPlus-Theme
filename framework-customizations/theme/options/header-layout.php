<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * HEADER → LAYOUT — the header "chrome": layout mode, container, heights,
 * mobile breakpoint, background and scroll behavior.
 *
 * The header's three rows were split into their own sibling sub-tabs (mirroring
 * the footer's Pre / Main / Post structure), each with its own storage key:
 *   - Top Bar     → `header_topbar`    (header-topbar.php)
 *   - Main Header → `header_main`      (header-main.php)
 *   - Bottom Bar  → `header_bottombar` (header-bottombar.php)
 * The top/bottom bars no longer have an Enable switch — like the footer, a row
 * renders whenever any of its columns has an element. The slot/preset system
 * reads all four ids (see unysonplus_preset_option_ids()); a one-time admin
 * migration (unysonplus_migrate_header_layout()) lifts the legacy single-blob
 * `header_layout` shape into the four keys, for both global settings and any
 * up_header preset post-meta.
 *
 * `header_mode` + `vertical_width` are read via unysonplus_header_layout_get().
 * SVG previews live at assets/svg/layout/*.svg.
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
	'header_layout' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_header_layout' => [
				'type'    => 'group',
				'options' => [

					'header_mode' => [
						'label'   => __( 'Header Layout Mode', 'unysonplus' ),
						'desc'    => __( 'Top: standard horizontal header. Vertical Left/Right: fixed side rail with logo + menu. Off-Canvas Only: hamburger always visible, no top bar. Overlay Fullscreen: hamburger opens a fullscreen menu.', 'unysonplus' ),
						'type'    => 'image-picker',
						'value'   => 'top',
						'choices' => $picker( [
							'top'             => 'header-top.svg',
							'vertical-left'   => 'header-vertical-left.svg',
							'vertical-right'  => 'header-vertical-right.svg',
							'off-canvas-only' => 'header-off-canvas.svg',
							'overlay'         => 'header-overlay.svg',
						] ),
					],

					'vertical_width' => [
						'label' => __( 'Vertical Header Width', 'unysonplus' ),
						'desc'  => __( 'Width of the fixed side rail (when Header Layout Mode = Vertical Left/Right).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '16.25', 'unit' => 'rem' ],
						'min'   => 0,
					],

					'container' => [
						'label'   => __( 'Container', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'container',
						'choices' => [
							'container'       => __( 'Fixed Width', 'unysonplus' ),
							'container-fluid' => __( 'Full Width', 'unysonplus' ),
						],
					],

					'min_height' => [
						'label' => __( 'Main Header Height', 'unysonplus' ),
						'desc'  => __( 'Minimum height of the main header row.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '5', 'unit' => 'rem' ],
						'min'   => 0,
					],

					'mobile_min_height' => [
						'label' => __( 'Mobile Header Height', 'unysonplus' ),
						'desc'  => __( 'Main header height on phones (below 768px). Leave empty to reuse the desktop height.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],

					'mobile_breakpoint' => [
						'label'   => __( 'Collapse to Mobile Menu At', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'lg',
						'choices' => [
							'lg' => __( 'Below 992px (tablet & phone)', 'unysonplus' ),
							'md' => __( 'Below 768px (phone only)', 'unysonplus' ),
						],
						'desc'    => __( 'Screen width below which the inline menu collapses to the hamburger drawer.', 'unysonplus' ),
					],

					'bg_color' => [
						'label' => __( 'Main Header Background', 'unysonplus' ),
						'type'  => 'rgba-color-picker',
						'value' => 'rgba(255, 255, 255, 1)',
					],

					'header_behavior' => [
						'label'   => __( 'Header Behavior', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'static',
						'choices' => [
							'static'              => __( 'Static (scrolls away with the page)', 'unysonplus' ),
							'sticky'              => __( 'Sticky (follows scroll)', 'unysonplus' ),
							'sticky-shrink'       => __( 'Sticky + Shrink on scroll', 'unysonplus' ),
							'hide-on-scroll'      => __( 'Sticky, hide on scroll down / reveal up', 'unysonplus' ),
							'transparent-overlay' => __( 'Transparent over the first section', 'unysonplus' ),
						],
						'desc'    => __( 'How the header behaves on scroll. This supersedes the old Sticky switch and General → Layout → Header Position Behavior. Per-page "Transparent" still overrides it for that page.', 'unysonplus' ),
					],

				],
			],
		],
	],
];
