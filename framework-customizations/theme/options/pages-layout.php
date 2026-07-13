<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Pages → Layout sub-tab.
 *
 * Site-wide page LAYOUT defaults (sidebar + content width). Stored under the
 * `pages_layout` multi and merged into unysonplus_pages_get() alongside
 * `general_pages` / `pages_hero`, so reads stay key-name stable. These feed the
 * layout cascade (inc/includes/layout.php → unysonplus_resolve_layout): per-page
 * meta → template → THESE globals → per-context / site defaults.
 *
 * Sidebar + width use image-picker diagrams (with the choice labelled in the
 * SVG) to match General → Sidebar / Layout. Tiles live at assets/svg/layout/;
 * the "Global/Inherit" tiles are dashed to read as "defer to the site default".
 * The stored value is still the plain choice key, so nothing about the cascade
 * or saved data changes.
 */

$svg = get_template_directory_uri() . '/assets/svg/layout';

/* {choice => svg-file} → image-picker choices array (small + large previews). */
$picker = function ( array $pairs, $small = 104, $large = 150 ) use ( $svg ) {
	$out = [];
	foreach ( $pairs as $value => $file ) {
		$out[ $value ] = [
			'small' => [ 'height' => $small, 'src' => $svg . '/' . $file ],
			'large' => [ 'height' => $large, 'src' => $svg . '/' . $file ],
		];
	}
	return $out;
};

$options = [
	'pages_layout' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_pages_layout' => [
				'type'    => 'group',
				'options' => [
					'default_sidebar' => [
						'label'   => __( 'Default Sidebar', 'unysonplus' ),
						'desc'    => __( 'Sidebar position for pages that don\'t set their own. "Global" falls back to the Default Page Layout / site-wide sidebar.', 'unysonplus' ),
						'type'    => 'image-picker',
						'value'   => 'inherit',
						'choices' => $picker( [
							'inherit' => 'sb-inherit.svg',
							'none'    => 'sb-none.svg',
							'left'    => 'sb-left.svg',
							'right'   => 'sb-right.svg',
						] ),
					],
					'default_content_width' => [
						'label'   => __( 'Default Content Width', 'unysonplus' ),
						'desc'    => __( 'Reading-column width for pages that don\'t set their own. "Global" uses the theme container.', 'unysonplus' ),
						'type'    => 'image-picker',
						'value'   => 'default',
						'choices' => $picker( [
							'default' => 'cw-global.svg',
							'narrow'  => 'cw-narrow.svg',
							'wide'    => 'cw-wide.svg',
							'full'    => 'cw-full.svg',
						] ),
					],
				],
			],
		],
	],
];
