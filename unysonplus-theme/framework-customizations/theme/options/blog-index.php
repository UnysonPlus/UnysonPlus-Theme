<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Blog → Blog Index sub-tab.
 *
 * Controls the posts listing (the blog home `index.php` and archives via
 * `archive.php`). Stored under the `blog_index` multi so values persist
 * reliably. Read at runtime via unysonplus_blog_get( $key, $default ) in
 * inc/includes/blog.php, which wires them through the theme's loop hooks
 * (unysonplus_before_loop / _after_loop / _entry_header) and the listing
 * card template-parts/content.php.
 */

$options = [
	'blog_index' => [
		'type'          => 'multi',
		'label'         => false,
		'desc'          => false,
		'inner-options' => [
			'blog_layout' => [
				'label'   => __( 'Layout', 'unysonplus' ),
				'desc'    => __( 'How the posts listing is arranged.', 'unysonplus' ),
				'type'    => 'radio',
				'value'   => 'list',
				'choices' => [
					'list'    => __( 'List (stacked)', 'unysonplus' ),
					'grid'    => __( 'Grid', 'unysonplus' ),
					'masonry' => __( 'Masonry', 'unysonplus' ),
				],
			],
			'blog_columns' => [
				'label'   => __( 'Columns', 'unysonplus' ),
				'desc'    => __( 'Number of columns for Grid / Masonry layouts.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '2',
				'choices' => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4' ],
			],
			'blog_featured_image' => [
				'label'        => __( 'Featured Image', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'blog_image_ratio' => [
				'label'   => __( 'Image Ratio', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '16-9',
				'choices' => [
					'original' => __( 'Original', 'unysonplus' ),
					'16-9'     => '16:9',
					'4-3'      => '4:3',
					'1-1'      => '1:1 (square)',
				],
			],
			'blog_content' => [
				'label'   => __( 'Post Content', 'unysonplus' ),
				'type'    => 'radio',
				'value'   => 'excerpt',
				'choices' => [
					'excerpt' => __( 'Excerpt', 'unysonplus' ),
					'full'    => __( 'Full content', 'unysonplus' ),
				],
			],
			'blog_excerpt_length' => [
				'label' => __( 'Excerpt Length (words)', 'unysonplus' ),
				'type'  => 'short-text',
				'value' => '30',
			],
			'blog_meta' => [
				'label'   => __( 'Post Meta', 'unysonplus' ),
				'desc'    => __( 'Which meta to show under each post in the listing.', 'unysonplus' ),
				'type'    => 'checkboxes',
				'value'   => [ 'date' => true, 'author' => true, 'category' => true, 'comments' => false, 'reading_time' => false ],
				'choices' => [
					'date'         => __( 'Date', 'unysonplus' ),
					'author'       => __( 'Author', 'unysonplus' ),
					'category'     => __( 'Category', 'unysonplus' ),
					'comments'     => __( 'Comment count', 'unysonplus' ),
					'reading_time' => __( 'Reading time', 'unysonplus' ),
				],
			],
			'blog_read_more' => [
				'label' => __( 'Read More Text', 'unysonplus' ),
				'type'  => 'text',
				'value' => __( 'Read more', 'unysonplus' ),
			],
			'blog_pagination' => [
				'label'   => __( 'Pagination', 'unysonplus' ),
				'type'    => 'radio',
				'value'   => 'numbers',
				'choices' => [
					'numbers'   => __( 'Numbered', 'unysonplus' ),
					'prev_next' => __( 'Older / Newer', 'unysonplus' ),
				],
			],
		],
	],
];
