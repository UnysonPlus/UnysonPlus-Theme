<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Social main tab.
 *
 * Dedicated home for site-wide social profiles (moved out of General, since
 * they're consumed by both the header and the footer). Activated from
 * settings.php. Options live in general-social.php (`social_profiles`).
 */

$options = [
	'social_settings_container' => [
		'title'   => __( 'Social', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'social' => [
				'title'   => __( 'Social Profiles', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					fw()->theme->get_options( 'general-social' ),
				],
			],
		],
	],
];
