<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

require dirname( __FILE__ ) . '/footer-common.php';

// Copyright section: default to 1 column with a Copyright Text element pre-populated.
// Any additional columns added by the user start empty.
$copyright_col_defaults = function ( $c, $n ) {
	if ( $c === 1 ) {
		// Inner-option default (copyright_content) is baked in so the
		// addable-popup stores it from the first render. Without this,
		// the renderer sees an empty string and exits early — user has
		// to manually open + save the popup to make it appear.
		return [ [
			'element_type' => [
				'element'        => 'copyright_text',
				'copyright_text' => [
					'copyright_content' => '&copy; {year} ' . get_bloginfo( 'name' ) . '. All rights reserved.',
				],
			],
		] ];
	}
	return [];
};

$options = [
	'copyright_settings' => [
		'type'         => 'multi-picker',
		'label'        => false,
		'desc'         => false,
		'picker'       => [
			'enabled' => [
				'label'        => __( 'Enable Copyright Section', 'unysonplus' ),
				'type'         => 'switch',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no', 'label' => __( 'No', 'unysonplus' ) ],
				'value'        => 'yes',
			],
		],
		'choices' => [
			'yes' => $footer_columns_picker( 'copyright', 3, '1', $copyright_col_defaults )
			       + $section_settings( 'copyright' ),
		],
		'show_borders' => false,
	],
];
