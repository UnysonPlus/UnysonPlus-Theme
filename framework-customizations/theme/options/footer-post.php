<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * POST-FOOTER — explicit nested arrays. Shared leaves from
 * inc/includes/header-footer-option-helpers.php.
 *
 * No Enable switch: like the Main Footer, the section simply doesn't render when
 * no column has content. Default: 1 column, max 5, columns start empty.
 *
 * Option IDs: post_footer_columns (count + post_footer_layout + post_footer_col_1..N)
 * + post_footer_custom_styling.
 */

$options = [

	'post_footer_columns' => [
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
				'post_footer_col_1' => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
			],
			'2' => [
				'post_footer_layout' => unysonplus_footer_ratio_picker( 'post_footer', 2 ),
				'post_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'post_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
			],
			'3' => [
				'post_footer_layout' => unysonplus_footer_ratio_picker( 'post_footer', 3 ),
				'post_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'post_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
				'post_footer_col_3'  => unysonplus_footer_column( __( 'Column 3', 'unysonplus' ) ),
			],
			'4' => [
				'post_footer_layout' => unysonplus_footer_ratio_picker( 'post_footer', 4 ),
				'post_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'post_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
				'post_footer_col_3'  => unysonplus_footer_column( __( 'Column 3', 'unysonplus' ) ),
				'post_footer_col_4'  => unysonplus_footer_column( __( 'Column 4', 'unysonplus' ) ),
			],
			'5' => [
				'post_footer_layout' => unysonplus_footer_ratio_picker( 'post_footer', 5 ),
				'post_footer_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ) ),
				'post_footer_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
				'post_footer_col_3'  => unysonplus_footer_column( __( 'Column 3', 'unysonplus' ) ),
				'post_footer_col_4'  => unysonplus_footer_column( __( 'Column 4', 'unysonplus' ) ),
				'post_footer_col_5'  => unysonplus_footer_column( __( 'Column 5', 'unysonplus' ) ),
			],
		],
		'show_borders' => false,
	],

	'post_footer_custom_styling' => unysonplus_footer_custom_styling( 'post_footer' ),

];
