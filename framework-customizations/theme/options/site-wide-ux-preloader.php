<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Site-wide UX → Preloader sub-tab (leaf).
 *
 * Full-screen loading splash shown until the page finishes loading. Composed into the
 * "Site-wide UX" tab (site-wide-ux-settings.php).
 *
 * Naming: leaf option files are prefixed with the tab that owns them (site-wide-ux-*).
 * Stored under the `general_preloader` multi key — KEPT for back-compat (renaming the FILE
 * never changes the storage key), so `unysonplus_layout_get( 'layout_preloader_*' )` is unchanged.
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
					'layout_preloader_bg_color' => function_exists( 'sc_color_field_compact' )
						? sc_color_field_compact( [ 'label' => __( 'Preloader Background', 'unysonplus' ), 'desc' => __( 'Background color of the preloader splash (when Preloader != None).', 'unysonplus' ), 'kind' => 'bg' ] )
						: [ 'label' => __( 'Preloader Background', 'unysonplus' ), 'type' => 'color-picker', 'value' => '#ffffff' ],
				],
			],
		],
	],
];
