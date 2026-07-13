<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * HEADER → MAIN HEADER — the always-on header row with three inline slots
 * (left / center / right). Defaults: logo on the left, primary menu on the
 * right.
 *
 * Stored under the `header_main` multi key. Column option IDs (main_left /
 * main_center / main_right) are read in template-parts/header-builder.php. The
 * header is inline (left → right), so each slot is a single element list — no
 * column-count/ratio picker like the (block-stacked) footer needs.
 */

$options = [
	// Quick-start: arrange the three slots (logo / menu / CTA / search) in one click.
	'main_presets' => [
		'type'         => 'preset-loader',
		'label'        => __( 'Main Header Presets', 'unysonplus' ),
		'desc'         => __( 'Arrange the columns below with a ready-made header layout in one click, then fine-tune each element.', 'unysonplus' ),
		'preset_group' => 'header_main',
	],
	'header_main' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_main' => [
				'type'    => 'group',
				'options' => [
					'main_left'   => unysonplus_header_column(
						__( 'Main Header — Left Column', 'unysonplus' ),
						[ [ 'element_type' => [ 'element' => 'logo' ] ] ]
					),
					'main_center' => unysonplus_header_column( __( 'Main Header — Center Column', 'unysonplus' ) ),
					'main_right'  => unysonplus_header_column(
						__( 'Main Header — Right Column', 'unysonplus' ),
						[ [ 'element_type' => [ 'element' => 'menu_area', 'menu_area' => [ 'menu_location' => 'primary' ] ] ] ]
					),
					'main_custom_styling' => unysonplus_hf_custom_styling( 'main' ),
				],
			],
		],
	],
];
