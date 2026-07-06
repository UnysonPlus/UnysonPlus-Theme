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
 */

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
						'desc'    => __( 'Sidebar position for pages that don\'t set their own. "Inherit" falls back to the Default Page Layout / global sidebar.', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'inherit',
						'choices' => [
							'inherit' => __( 'Inherit', 'unysonplus' ),
							'none'    => __( 'None (full content)', 'unysonplus' ),
							'left'    => __( 'Left', 'unysonplus' ),
							'right'   => __( 'Right', 'unysonplus' ),
						],
					],
					'default_content_width' => [
						'label'   => __( 'Default Content Width', 'unysonplus' ),
						'desc'    => __( 'Reading-column width for pages that don\'t set their own.', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'default',
						'choices' => [
							'default' => __( 'Default (theme container)', 'unysonplus' ),
							'narrow'  => __( 'Narrow (~720px)', 'unysonplus' ),
							'wide'    => __( 'Wide (100%)', 'unysonplus' ),
							'full'    => __( 'Full (edge-to-edge)', 'unysonplus' ),
						],
					],
				],
			],
		],
	],
];
