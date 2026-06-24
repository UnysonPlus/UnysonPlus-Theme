<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * MAIN FOOTER — explicit nested arrays. Shared leaf pieces (element popup, row
 * template, one column, ratio picker, custom styling) come from
 * inc/includes/header-footer-option-helpers.php.
 *
 * Unlike the other sections, Main Footer has NO enable switch (it always renders
 * when it has columns) — its options sit at the top level. Default: 3 columns,
 * max 5, columns start empty.
 *
 * Option IDs: main_footer_columns (count + main_footer_layout + main_footer_col_1..N)
 * and main_footer_custom_styling. (Read in inc/includes/footer-builder.php.)
 */

$options = [

	'main_footer_columns' => [
		'type'   => 'multi-picker',
		'label'  => false,
		'desc'   => false,
		'picker' => [
			'count' => [
				'type'    => 'select',
				'label'   => __( 'Number of Columns', 'unysonplus' ),
				'value'   => '3',
				'choices' => [
					'1' => __( '1 Column', 'unysonplus' ),
					'2' => __( '2 Columns', 'unysonplus' ),
					'3' => __( '3 Columns', 'unysonplus' ),
					'4' => __( '4 Columns', 'unysonplus' ),
					'5' => __( '5 Columns', 'unysonplus' ),
				],
				'desc'    => __( 'Add and reorder footer element. Drag to sort.', 'unysonplus' ),
			],
		],
		'choices' => [
			'1' => [
				'main_footer_col_1' => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
			],
			'2' => [
				'main_footer_layout' => unysonplus_footer_ratio_picker( 'main_footer', 2 ),
				'main_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'main_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
			],
			'3' => [
				'main_footer_layout' => unysonplus_footer_ratio_picker( 'main_footer', 3 ),
				'main_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'main_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
				'main_footer_col_3'  => unysonplus_footer_column( __( 'Column 3', 'unysonplus' ) ),
			],
			'4' => [
				'main_footer_layout' => unysonplus_footer_ratio_picker( 'main_footer', 4 ),
				'main_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'main_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
				'main_footer_col_3'  => unysonplus_footer_column( __( 'Column 3', 'unysonplus' ) ),
				'main_footer_col_4'  => unysonplus_footer_column( __( 'Column 4', 'unysonplus' ) ),
			],
			'5' => [
				'main_footer_layout' => unysonplus_footer_ratio_picker( 'main_footer', 5 ),
				'main_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'main_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
				'main_footer_col_3'  => unysonplus_footer_column( __( 'Column 3', 'unysonplus' ) ),
				'main_footer_col_4'  => unysonplus_footer_column( __( 'Column 4', 'unysonplus' ) ),
				'main_footer_col_5'  => unysonplus_footer_column( __( 'Column 5', 'unysonplus' ) ),
			],
		],
		'show_borders' => false,
	],

	'main_footer_custom_styling' => unysonplus_footer_custom_styling( 'main_footer' ),

];
