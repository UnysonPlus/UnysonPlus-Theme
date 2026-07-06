<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Blog → Single Post sub-tab.
 *
 * Site-wide defaults for single `post` views (single.php / content-single.php).
 * Stored under the `blog_single` multi. Read at runtime via
 * unysonplus_single_get() in inc/includes/blog.php, where each toggle is also
 * overridable per-post through the Post Settings meta box (post-options.php).
 */

$options = [
	'blog_single' => [
		'type'          => 'multi',
		'label'         => false,
		'desc'          => false,
		'inner-options' => [
			'single_sidebar' => [
				'label'   => __( 'Sidebar', 'unysonplus' ),
				'desc'    => __( 'Sidebar for single posts. "Inherit" uses the General → Layout default.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (use global)', 'unysonplus' ),
					'none'    => __( 'No sidebar', 'unysonplus' ),
					'left'    => __( 'Left', 'unysonplus' ),
					'right'   => __( 'Right', 'unysonplus' ),
				],
			],
			'single_featured_image' => [
				'label'        => __( 'Featured Image', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'single_featured_position' => [
				'label'   => __( 'Featured Image Position', 'unysonplus' ),
				'type'    => 'radio',
				'value'   => 'below-title',
				'choices' => [
					'above-title' => __( 'Above title', 'unysonplus' ),
					'below-title' => __( 'Below title', 'unysonplus' ),
				],
			],
			'single_meta' => [
				'label'   => __( 'Post Meta', 'unysonplus' ),
				'type'    => 'checkboxes',
				'value'   => [ 'date' => true, 'author' => true, 'category' => true, 'tags' => false, 'comments' => false, 'reading_time' => false ],
				'choices' => [
					'date'         => __( 'Date', 'unysonplus' ),
					'author'       => __( 'Author', 'unysonplus' ),
					'category'     => __( 'Category', 'unysonplus' ),
					'tags'         => __( 'Tags', 'unysonplus' ),
					'comments'     => __( 'Comment count', 'unysonplus' ),
					'reading_time' => __( 'Reading time', 'unysonplus' ),
				],
			],
			'single_author_box' => [
				'label'        => __( 'Author Box', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'single_related' => [
				'label'        => __( 'Related Posts', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'single_related_count' => [
				'label'   => __( 'Related Posts Count', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '3',
				'choices' => [ '2' => '2', '3' => '3', '4' => '4' ],
			],
			'single_related_by' => [
				'label'   => __( 'Relate By', 'unysonplus' ),
				'type'    => 'radio',
				'value'   => 'category',
				'choices' => [
					'category' => __( 'Shared category', 'unysonplus' ),
					'tag'      => __( 'Shared tag', 'unysonplus' ),
				],
			],
			'single_related_style' => [
				'label'   => __( 'Related Posts Style', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'grid',
				'choices' => [
					'grid'     => __( 'Grid (cards)', 'unysonplus' ),
					'list'     => __( 'List (rows)', 'unysonplus' ),
					'carousel' => __( 'Carousel (scroll)', 'unysonplus' ),
				],
			],
			'single_related_ratio' => [
				'label'   => __( 'Related Image Ratio', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '16-9',
				'choices' => [
					'16-9' => '16:9',
					'4-3'  => '4:3',
					'1-1'  => '1:1 (square)',
				],
			],
			'single_post_nav' => [
				'label'        => __( 'Previous / Next Navigation', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
		],
	],
];
