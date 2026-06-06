<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

//$uri = get_template_directory_uri();

$options = [
	'review_options' => [
		'type' => 'multi',
		'label' => false,
		/*'attr' => array(
			'class' => '',
		),*/
		'inner-options' => [
			'rating'	=> [
				'type'  => 'slider',
				'value' => 4,
				'properties' => [
					'min' => 0,
					'max' => 5,
					'step' => .5, // Set slider step. Always > 0. Could be fractional.
				],
				/* 'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => 'rating_meta',
				), */
				'label' => __('Rating', 'unysonplus'),
			],
			'affiliate_link' => [
				'label' => __( 'Affiliate Link', 'unysonplus' ),
				'type'  => 'text',
				'desc'  => false,
			],
			'site_offer' => [
				'label' => __( 'Site Offer', 'unysonplus' ),
				'type'  => 'text',
				'desc'  => false,
			],
			'bonus' => [
				'label' => __( 'Bonus', 'unysonplus' ),
				'type'  => 'text',
				'desc'  => false,
			],
			'free_spins' => [
				'label' => __( 'Free Spins', 'unysonplus' ),
				'type'  => 'text',
				'desc'  => false,
			],
			'stats' => [
				'label' => __( 'Stats', 'unysonplus' ),
				'type'  => 'wp-editor',
				'desc'  => false,
			],
			'pros' => [
				'label' => __( 'Pros', 'unysonplus' ),
				'type'  => 'wp-editor',
				'desc'  => false,
			],
			'cons' => [
				'label' => __( 'Cons', 'unysonplus' ),
				'type'  => 'wp-editor',
				'desc'  => false,
			],
			'terms' => [
				'label' => __( 'Terms Apply', 'unysonplus' ),
				'type'  => 'wp-editor',
				'desc'  => false,
			],
			'logo_bg_color'              => [
				'label' => __( 'BG Color', 'unysonplus' ),
				'type'  => 'color-picker',
				'value' => '#ffffff',
				'desc'  => __( 'Logo Background Color', 'unysonplus' ),
			],
		],
	],
];
