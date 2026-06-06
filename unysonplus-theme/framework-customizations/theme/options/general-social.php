<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Social Profiles — site-wide social links consumed by the header
 * (Social Icons element) and footer. Lives under its own top-level
 * "Social" tab (social-settings.php). Stored under `social_profiles`.
 */

$defaults = [
	[ 'name' => 'Facebook',  'link' => 'https://facebook.com/'  ],
	[ 'name' => 'X',         'link' => 'https://x.com/'         ],
	[ 'name' => 'Instagram', 'link' => 'https://instagram.com/' ],
];

$options = [
	'social_profiles' => [
		'label'        => false,
		'type'         => 'addable-box',
		'value'        => $defaults,
		'box-options'  => [
			'name' => [
				'label' => __( 'Name', 'unysonplus' ),
				'desc'  => __( 'Network name (used as the link label / aria-label).', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			],
			'link' => [
				'label' => __( 'URL', 'unysonplus' ),
				'desc'  => __( 'Full profile URL, including https://', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			],
			'icon' => [
				'type'         => 'icon-v2',
				'preview_size' => 'medium',
				'modal_size'   => 'medium',
				'label'        => __( 'Icon', 'unysonplus' ),
				'desc'         => __( 'Pick an icon for this profile.', 'unysonplus' ),
			],
			'new_tab' => [
				'label'        => __( 'Open in New Tab', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
				'desc'         => __( 'Open the link in a new browser tab.', 'unysonplus' ),
			],
		],
		'template' => '<p><strong>{{- name }}</strong><br>{{- link }}</p>',
	],
];
