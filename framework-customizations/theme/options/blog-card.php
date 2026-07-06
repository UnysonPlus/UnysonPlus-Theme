<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Blog → Card Design sub-tab.
 *
 * Fine visual tuning for the listing card — corner radius, shadow depth, inner
 * padding and a hover accent. These apply on top of the Blog Index "Card Style"
 * (they read best on Boxed / Bordered cards). Stored under the `blog_card` multi
 * and merged into unysonplus_blog_get(); the values are emitted as
 * `--post-card-*` custom properties on the listing wrapper (inc/includes/blog.php)
 * and consumed in style.css. Drive the whole set in one click from the Card
 * Design preset library above.
 */

$options = [
	'blog_card' => [
		'type'          => 'multi',
		'label'         => false,
		'desc'          => false,
		'inner-options' => [
			'blog_card_radius' => [
				'label'   => __( 'Corner Radius', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'md',
				'choices' => [
					'none' => __( 'Square (0)', 'unysonplus' ),
					'sm'   => __( 'Small', 'unysonplus' ),
					'md'   => __( 'Medium', 'unysonplus' ),
					'lg'   => __( 'Large', 'unysonplus' ),
					'xl'   => __( 'Extra large', 'unysonplus' ),
				],
			],
			'blog_card_shadow' => [
				'label'   => __( 'Shadow Depth', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'sm',
				'choices' => [
					'none' => __( 'None', 'unysonplus' ),
					'sm'   => __( 'Subtle', 'unysonplus' ),
					'md'   => __( 'Medium', 'unysonplus' ),
					'lg'   => __( 'Deep', 'unysonplus' ),
				],
			],
			'blog_card_padding' => [
				'label'   => __( 'Inner Padding', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'normal',
				'choices' => [
					'compact' => __( 'Compact', 'unysonplus' ),
					'normal'  => __( 'Normal', 'unysonplus' ),
					'roomy'   => __( 'Roomy', 'unysonplus' ),
				],
			],
			'blog_card_hover_accent' => [
				'label'        => __( 'Accent Border on Hover', 'unysonplus' ),
				'desc'         => __( 'Draw a primary-colored top border on the card when hovered.', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
			],
		],
	],
];
