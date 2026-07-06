<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Blog → Single Post → Elements sub-tab.
 *
 * The extra content pieces around a single post — breadcrumbs, a table of
 * contents, share buttons, a tag row and the comments area. Stored under the
 * `blog_single_extras` multi and merged into unysonplus_single_get()
 * (inc/includes/blog.php). Each is off unless it earns its place.
 */

$options = [
	'blog_single_extras' => [
		'type'          => 'multi',
		'label'         => false,
		'desc'          => false,
		'inner-options' => [
			'single_breadcrumbs' => [
				'label'        => __( 'Breadcrumbs', 'unysonplus' ),
				'desc'         => __( 'A Home › Category › Post trail above the title.', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'single_toc' => [
				'label'        => __( 'Table of Contents', 'unysonplus' ),
				'desc'         => __( 'Auto-built from the post’s H2 / H3 headings and inserted at the top of the content.', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'single_toc_title' => [
				'label' => __( 'Table of Contents Title', 'unysonplus' ),
				'type'  => 'text',
				'value' => __( 'In this article', 'unysonplus' ),
			],
			'single_share' => [
				'label'        => __( 'Share Buttons', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'single_share_position' => [
				'label'   => __( 'Share Buttons Position', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'bottom',
				'choices' => [
					'top'    => __( 'Above content', 'unysonplus' ),
					'bottom' => __( 'Below content', 'unysonplus' ),
					'both'   => __( 'Above and below', 'unysonplus' ),
				],
			],
			'single_share_networks' => [
				'label'   => __( 'Share Networks', 'unysonplus' ),
				'type'    => 'checkboxes',
				'value'   => [ 'x' => true, 'facebook' => true, 'linkedin' => true, 'copy' => true ],
				'choices' => [
					'x'        => __( 'X (Twitter)', 'unysonplus' ),
					'facebook' => __( 'Facebook', 'unysonplus' ),
					'linkedin' => __( 'LinkedIn', 'unysonplus' ),
					'whatsapp' => __( 'WhatsApp', 'unysonplus' ),
					'copy'     => __( 'Copy link', 'unysonplus' ),
				],
			],
			'single_tags' => [
				'label'        => __( 'Tag Row', 'unysonplus' ),
				'desc'         => __( 'A styled row of the post’s tags below the content.', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'single_comments' => [
				'label'        => __( 'Comments', 'unysonplus' ),
				'desc'         => __( 'Show the comments area (still respects WordPress’ per-post “Allow comments”).', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
		],
	],
];
