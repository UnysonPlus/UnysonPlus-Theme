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

$header_preset_choices = [ '' => __( 'Default (Theme Settings header)', 'unysonplus' ) ];
$footer_preset_choices = [ '' => __( 'Default (Theme Settings footer)', 'unysonplus' ) ];
if ( function_exists( 'unysonplus_preset_choices' ) ) {
	$header_preset_choices += unysonplus_preset_choices( 'up_header' );
	$footer_preset_choices += unysonplus_preset_choices( 'up_footer' );
}

$options = [
	'general_pages' => [
		'type'   => 'multi',
		'label'  => false,
		'desc'   => false,
		'inner-options' => [
			'default_header_preset' => [
				'label'   => __( 'Site-Wide Header Preset', 'unysonplus' ),
				'desc'    => __( 'Use a Header Preset across the whole site by default. Per-content selections (Header & Footer box on a page/post) still override this. "Default" uses the header configured under Theme Settings → Header.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '',
				'choices' => $header_preset_choices,
			],
			'default_footer_preset' => [
				'label'   => __( 'Site-Wide Footer Preset', 'unysonplus' ),
				'desc'    => __( 'Use a Footer Preset across the whole site by default. Per-content selections still override this. "Default" uses the footer configured under Theme Settings → Footer.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '',
				'choices' => $footer_preset_choices,
			],
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
			// (Hero image / height moved to Pages → Page Title / Hero — `pages-hero.php`.)
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
