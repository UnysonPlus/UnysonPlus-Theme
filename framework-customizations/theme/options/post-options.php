<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Per-post overrides (Post → Post Settings meta box).
 *
 * Stored under the `post_options` multi on each post. Every control defaults to
 * "Global", meaning "inherit the matching Blog → Single Post setting"; a post
 * only diverges where the editor deliberately overrides. Resolved at runtime via
 * unysonplus_single_enabled() / the sidebar + header-style overrides in
 * inc/includes/blog.php — per-post wins over the global unless left on Global.
 *
 * Mirrors the global Single Post tabs (Header & Hero / Content & Meta /
 * Elements / Related), grouped here by label order for a compact meta box.
 */

$_toggle = function ( $label, $desc = '' ) {
	return [
		'label'   => $label,
		'desc'    => $desc,
		'type'    => 'select',
		'value'   => 'default',
		'choices' => [
			'default' => __( 'Global', 'unysonplus' ),
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

			// --- Header & Hero ---
			'post_header_style' => [
				'label'   => __( 'Header Style', 'unysonplus' ),
				'desc'    => __( 'Override the title area for this post.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'default',
				'choices' => [
					'default'  => __( 'Global', 'unysonplus' ),
					'standard' => __( 'Standard (title above content)', 'unysonplus' ),
					'hero'     => __( 'Hero (title over featured image)', 'unysonplus' ),
				],
			],
			'post_progress_bar' => $_toggle( __( 'Reading Progress Bar', 'unysonplus' ) ),

			// --- Content & Meta ---
			'post_sidebar' => [
				'label'   => __( 'Sidebar', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'default',
				'choices' => [
					'default' => __( 'Global', 'unysonplus' ),
					'none'    => __( 'No sidebar', 'unysonplus' ),
					'left'    => __( 'Left', 'unysonplus' ),
					'right'   => __( 'Right', 'unysonplus' ),
				],
			],
			'post_featured_image' => $_toggle( __( 'Featured Image', 'unysonplus' ) ),
			'post_author_box'     => $_toggle( __( 'Author Box', 'unysonplus' ) ),

			// --- Elements ---
			'post_breadcrumbs' => $_toggle( __( 'Breadcrumbs', 'unysonplus' ) ),
			'post_toc'         => $_toggle( __( 'Table of Contents', 'unysonplus' ) ),
			'post_share'       => $_toggle( __( 'Share Buttons', 'unysonplus' ) ),
			'post_tags'        => $_toggle( __( 'Tag Row', 'unysonplus' ) ),
			'post_comments'    => $_toggle( __( 'Comments', 'unysonplus' ) ),

			// --- Related & Navigation ---
			'post_related' => $_toggle( __( 'Related Posts', 'unysonplus' ) ),
			'post_nav'     => $_toggle( __( 'Previous / Next Navigation', 'unysonplus' ) ),
		],
	],
];
