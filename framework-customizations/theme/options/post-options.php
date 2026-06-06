<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Per-post overrides (Post → Post Settings meta box).
 *
 * Stored under the `post_options` multi on each post. Every control defaults
 * to "Default", meaning "inherit the global Blog → Single Post setting". Read
 * at runtime via unysonplus_single_enabled() / the sidebar override in
 * inc/includes/blog.php — per-post wins over the global only when not "Default".
 */

$_toggle = function ( $label, $desc = '' ) {
	return [
		'label'   => $label,
		'desc'    => $desc,
		'type'    => 'select',
		'value'   => 'default',
		'choices' => [
			'default' => __( 'Default (use global)', 'unysonplus' ),
			'show'    => __( 'Show', 'unysonplus' ),
			'hide'    => __( 'Hide', 'unysonplus' ),
		],
	];
};

$options = [
	'post_options' => [
		'type'          => 'multi',
		'label'         => false,
		'desc'          => false,
		'inner-options' => [
			'post_sidebar' => [
				'label'   => __( 'Sidebar', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'default',
				'choices' => [
					'default' => __( 'Default (use global)', 'unysonplus' ),
					'none'    => __( 'No sidebar', 'unysonplus' ),
					'left'    => __( 'Left', 'unysonplus' ),
					'right'   => __( 'Right', 'unysonplus' ),
				],
			],
			'post_featured_image' => $_toggle( __( 'Featured Image', 'unysonplus' ) ),
			'post_author_box'     => $_toggle( __( 'Author Box', 'unysonplus' ) ),
			'post_related'        => $_toggle( __( 'Related Posts', 'unysonplus' ) ),
			'post_nav'            => $_toggle( __( 'Previous / Next Navigation', 'unysonplus' ) ),
		],
	],
];
