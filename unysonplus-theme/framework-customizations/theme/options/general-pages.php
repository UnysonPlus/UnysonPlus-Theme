<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * General → Pages sub-tab.
 *
 * Site-wide defaults that apply to every "page" post type. Wrapped in a
 * `general_pages` multi so the values persist reliably (Unyson treats
 * loose top-level options inside boxes as transient).
 *
 * Read at runtime via unysonplus_pages_get( $key, $default ) — see
 * inc/includes/layout.php. The 5-level cascade puts these between
 * per-template overrides and the General → Layout defaults, so a page
 * without an explicit template falls back to whatever the editor picks
 * here, but assigning a Template Name to a page still wins.
 */

$options = [
	'general_pages' => [
		'type'   => 'multi',
		'label'  => false,
		'desc'   => false,
		'inner-options' => [
			'default_page_layout' => [
				'label'   => __( 'Default Page Layout', 'unysonplus' ),
				'desc'    => __( 'Which named template should the default page.php behave like? Per-page Template selections still override this.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'default',
				'choices' => [
					'default'        => __( 'Default (no sidebar)', 'unysonplus' ),
					'sidebar-right'  => __( 'Right Sidebar', 'unysonplus' ),
					'sidebar-left'   => __( 'Left Sidebar', 'unysonplus' ),
					'full-width'     => __( 'Full Width', 'unysonplus' ),
					'boxed-narrow'   => __( 'Boxed Narrow', 'unysonplus' ),
				],
			],
			'default_page_header_image' => [
				'label' => __( 'Default Hero Header Image', 'unysonplus' ),
				'desc'  => __( 'Used when a page has no per-page Hero Header image set.', 'unysonplus' ),
				'type'  => 'upload',
				'value' => [],
			],
			'default_page_header_height' => [
				'label'   => __( 'Default Hero Header Height', 'unysonplus' ),
				'type'    => 'radio',
				'value'   => 'auto',
				'choices' => [
					'auto'   => __( 'Auto', 'unysonplus' ),
					'small'  => __( 'Small (220px)', 'unysonplus' ),
					'medium' => __( 'Medium (380px)', 'unysonplus' ),
					'large'  => __( 'Large (560px)', 'unysonplus' ),
				],
			],
			'pages_show_breadcrumbs' => [
				'label'        => __( 'Show Breadcrumbs on Pages', 'unysonplus' ),
				'type'         => 'switch',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'No',  'unysonplus' ) ],
				'value'        => 'no',
			],
			'pages_show_featured_image' => [
				'label'        => __( 'Show Featured Image on Pages', 'unysonplus' ),
				'type'         => 'switch',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'No',  'unysonplus' ) ],
				'value'        => 'yes',
			],
		],
	],
];
