<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * PRE-FOOTER — explicit nested arrays. Shared leaves from
 * inc/includes/header-footer-option-helpers.php.
 *
 * No Enable switch: like the Main Footer, the section simply doesn't render when
 * no column has content (the section renderer's has-content guard handles it).
 * Default: 1 column, max 5, columns start empty.
 *
 * Option IDs: pre_footer_columns (count + pre_footer_layout + pre_footer_col_1..N)
 * + pre_footer_custom_styling. (Read in template-parts/footer-builder.php.)
 */

$options = [

	'pre_footer_columns' => [
		'type'   => 'multi-picker',
		'label'  => false,
		'desc'   => false,
		'picker' => [
			'count' => [
				'type'    => 'select',
				'label'   => __( 'Number of Columns', 'unysonplus' ),
				'value'   => '1',
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
				'pre_footer_col_1' => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
			],
			'2' => [
				'pre_footer_layout' => unysonplus_footer_ratio_picker( 'pre_footer', 2 ),
				'pre_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'pre_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
			],
			'3' => [
				'pre_footer_layout' => unysonplus_footer_ratio_picker( 'pre_footer', 3 ),
				'pre_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'pre_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
				'pre_footer_col_3'  => unysonplus_footer_column( __( 'Column 3', 'unysonplus' ) ),
			],
			'4' => [
				'pre_footer_layout' => unysonplus_footer_ratio_picker( 'pre_footer', 4 ),
				'pre_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'pre_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
				'pre_footer_col_3'  => unysonplus_footer_column( __( 'Column 3', 'unysonplus' ) ),
				'pre_footer_col_4'  => unysonplus_footer_column( __( 'Column 4', 'unysonplus' ) ),
			],
			'5' => [
				'pre_footer_layout' => unysonplus_footer_ratio_picker( 'pre_footer', 5 ),
				'pre_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'pre_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
				'pre_footer_col_3'  => unysonplus_footer_column( __( 'Column 3', 'unysonplus' ) ),
				'pre_footer_col_4'  => unysonplus_footer_column( __( 'Column 4', 'unysonplus' ) ),
				'pre_footer_col_5'  => unysonplus_footer_column( __( 'Column 5', 'unysonplus' ) ),
			],
		],
		'show_borders' => false,
	],

	'pre_footer_custom_styling' => unysonplus_footer_custom_styling( 'pre_footer' ),

];
