<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * General → Preloader sub-tab.
 *
 * Full-screen loading splash shown until the page finishes loading. Split out of
 * the old General → Layout "UX Polish" section into its own tab.
 *
 * Stored under the `general_preloader` multi key. `unysonplus_layout_get()`
 * merges `general_layout` + `general_sidebar` + `general_preloader`, so reads of
 * `layout_preloader_*` are unchanged.
 *
 * Image-picker previews live at assets/svg/layout/*.svg.
 */

$uri = get_template_directory_uri();
$svg = $uri . '/assets/svg/layout';

/* Build the image-picker `choices` array for any pair of {value => svg-filename}. */
$picker = function ( array $pairs, $height_small = 70, $height_large = 140 ) use ( $svg ) {
	$out = [];
	foreach ( $pairs as $value => $file ) {
		$out[ $value ] = [
			'small' => [ 'height' => $height_small, 'src' => $svg . '/' . $file ],
			'large' => [ 'height' => $height_large, 'src' => $svg . '/' . $file ],
		];
	}
	return $out;
};

$options = [
	'general_preloader' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_preloader' => [
				'type'    => 'group',
				'options' => [
					'layout_preloader_style' => [
						'label'   => __( 'Preloader', 'unysonplus' ),
						'desc'    => __( 'Full-screen splash shown until the page finishes loading.', 'unysonplus' ),
						'type'    => 'image-picker',
						'value'   => 'none',
						'choices' => $picker( [
							'none'    => 'preloader-none.svg',
							'spinner' => 'preloader-spinner.svg',
							'logo'    => 'preloader-logo.svg',
						] ),
					],
					'layout_preloader_bg_color' => [
						'label' => __( 'Preloader Background', 'unysonplus' ),
						'desc'  => __( 'Background color of the preloader splash (when Preloader != None).', 'unysonplus' ),
						'type'  => 'color-picker',
						'value' => '#ffffff',
					],
				],
			],
		],
	],
];
