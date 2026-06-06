<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Blog main tab.
 *
 * Sub-tabs are added per phase:
 *   - Blog Index   (blog-index.php)     — the posts listing.            [done]
 *   - Single Post  (blog-single.php)    — single post layout/extras.    [phase 2]
 *   - Archives     (blog-archives.php)  — category/tag/author/search.   [phase 3]
 *
 * Activated from settings.php. Runtime consumer: inc/includes/blog.php.
 */

$options = [
	'blog_settings_container' => [
		'title'   => __( 'Blog', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'blog' => [
				'title'   => __( 'Blog Settings', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					'tab_blog_index' => [
						'title'   => __( 'Blog Index', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'blog_index_box' => [
								'title'   => __( 'Posts Listing', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'blog-index' ),
								],
							],
						],
					],
					'tab_blog_single' => [
						'title'   => __( 'Single Post', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'blog_single_box' => [
								'title'   => __( 'Single Post', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'blog-single' ),
								],
							],
						],
					],
					'tab_blog_archives' => [
						'title'   => __( 'Archives & Search', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'blog_archives_box' => [
								'title'   => __( 'Archives &amp; Search', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'blog-archives' ),
								],
							],
						],
					],
				],
			],
		],
	],
];
