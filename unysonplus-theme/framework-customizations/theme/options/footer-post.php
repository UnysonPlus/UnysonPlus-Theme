<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

require dirname( __FILE__ ) . '/footer-common.php';

$options = [
	'post_footer_settings' => [
		'type'         => 'multi-picker',
		'label'        => false,
		'desc'         => false,
		'picker'       => [
			'enabled' => [
				'label'        => __( 'Enable Post-Footer', 'unysonplus' ),
				'type'         => 'switch',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no', 'label' => __( 'No', 'unysonplus' ) ],
				'value'        => 'no',
			],
		],
		'choices' => [
			'yes' => $footer_columns_picker( 'post_footer', 5, '1' )
			       + $section_settings( 'post_footer' ),
		],
		'show_borders' => false,
	],
];
