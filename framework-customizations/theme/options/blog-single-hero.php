<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Blog → Single Post → Header & Hero sub-tab.
 *
 * The single-post title area. Stored under the `blog_single_hero` multi and
 * merged into unysonplus_single_get() (inc/includes/blog.php). When the header
 * style is "Hero", the featured image becomes a full-width banner with the
 * title + meta laid over it (reusing the Pages hero look); otherwise the title
 * sits above the content as a standard header.
 */

$hero_color = function_exists( 'sc_color_field_compact' )
	? sc_color_field_compact( array(
		'label'  => __( 'Hero Overlay Color', 'unysonplus' ),
		'desc'   => __( 'Tint over the hero image so the title stays readable.', 'unysonplus' ),
		'kind'   => 'bg',
		'picker' => 'rgba-color-picker',
	) )
	: array( 'label' => __( 'Hero Overlay Color', 'unysonplus' ), 'type' => 'color-picker', 'value' => '' );

$options = [
	'blog_single_hero' => [
		'type'          => 'multi',
		'label'         => false,
		'desc'          => false,
		'inner-options' => [
			'single_header_style' => [
				'label'   => __( 'Header Style', 'unysonplus' ),
				'desc'    => __( 'How the post title area is presented.', 'unysonplus' ),
				'type'    => 'radio',
				'value'   => 'standard',
				'choices' => [
					'standard' => __( 'Standard (title above content)', 'unysonplus' ),
					'hero'     => __( 'Hero (title over featured image)', 'unysonplus' ),
				],
			],
			'single_hero_height' => [
				'label'   => __( 'Hero Height', 'unysonplus' ),
				'type'    => 'radio',
				'value'   => 'medium',
				'choices' => [
					'small'      => __( 'Small (300px)', 'unysonplus' ),
					'medium'     => __( 'Medium (440px)', 'unysonplus' ),
					'large'      => __( 'Large (600px)', 'unysonplus' ),
					'fullscreen' => __( 'Fullscreen (100vh)', 'unysonplus' ),
				],
			],
			'single_hero_align' => [
				'label'   => __( 'Hero Content Position', 'unysonplus' ),
				'type'    => 'radio',
				'value'   => 'bottom',
				'choices' => [
					'top'    => __( 'Top', 'unysonplus' ),
					'center' => __( 'Center', 'unysonplus' ),
					'bottom' => __( 'Bottom', 'unysonplus' ),
				],
			],
			'single_hero_overlay_color'   => $hero_color,
			'single_hero_overlay_opacity' => [
				'label'      => __( 'Hero Overlay Opacity', 'unysonplus' ),
				'desc'       => __( '0 = transparent, 100 = opaque.', 'unysonplus' ),
				'type'       => 'slider',
				'value'      => 45,
				'properties' => [ 'min' => 0, 'max' => 100, 'step' => 5 ],
			],
			'single_progress_bar' => [
				'label'        => __( 'Reading Progress Bar', 'unysonplus' ),
				'desc'         => __( 'A thin bar fixed to the top of the screen that fills as the reader scrolls the post.', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
		],
	],
];
