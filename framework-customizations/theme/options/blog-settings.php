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
					'tab_blog_presets' => [
						'title'   => __( 'Presets', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'blog_presets_box' => [
								'title'   => __( 'Blog Presets', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'blog_presets' => [
										'type'         => 'preset-loader',
										'label'        => __( 'Blog Presets', 'unysonplus' ),
										'desc'         => __( 'Start from a whole-blog look — Classic List, Grid Cards, Magazine, Minimal or Editorial — then fine-tune under Blog Index. Or upload a preset JSON you exported earlier.', 'unysonplus' ),
										'preset_group' => 'blog_index',
									],
								],
							],
						],
					],
					'tab_blog_card' => [
						'title'   => __( 'Card Design', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'blog_card_presets_box' => [
								'title'   => __( 'Card Design Library', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'blog_card_presets' => [
										'type'         => 'preset-loader',
										'label'        => __( 'Card Design', 'unysonplus' ),
										'desc'         => __( 'One-click card looks — Soft, Sharp, Floating, Flat or Framed. These tune radius, shadow, padding and hover on top of the Blog Index card style.', 'unysonplus' ),
										'preset_group' => 'blog_card',
									],
								],
							],
							'blog_card_box' => [
								'title'   => __( 'Card Details', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'blog-card' ),
								],
							],
						],
					],
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
							'blog_single_hero_box' => [
								'title'   => __( 'Header &amp; Hero', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'blog-single-hero' ),
								],
							],
							'blog_single_box' => [
								'title'   => __( 'Content &amp; Meta', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'blog-single' ),
								],
							],
							'blog_single_extras_box' => [
								'title'   => __( 'Elements', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'blog-single-extras' ),
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
