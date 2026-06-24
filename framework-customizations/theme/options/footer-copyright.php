<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * COPYRIGHT — explicit nested arrays. Shared leaves from
 * inc/includes/header-footer-option-helpers.php. Enabled by default; max 3
 * columns, default 1. Column 1 (every count) defaults to a Text element holding
 * the copyright line — the Text renderer resolves {{current_year}} (see footer-builder.php),
 * so no dedicated "Copyright Text" element is needed.
 *
 * Option IDs: copyright_settings → copyright_columns (count + copyright_layout +
 * copyright_col_1..N) + copyright_custom_styling.
 */

/* Default content for Column 1: a Text element with the copyright line. */
$copyright_default = [ [
	'element_type' => [
		'element' => 'text',
		'text'    => [
			'text_content' => '&copy; {{current_year}} ' . get_bloginfo( 'name' ) . '. All rights reserved.',
		],
	],
] ];

$options = [
	'copyright_settings' => [
		'type'   => 'multi-picker',
		'label'  => false,
		'desc'   => false,
		'picker' => [
			'enabled' => [
				'label'        => __( 'Enable Copyright Section', 'unysonplus' ),
				'type'         => 'switch',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
				'value'        => 'yes',
			],
		],
		'choices' => [
			'yes' => [

				'copyright_columns' => [
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
							],
							'desc'    => __( 'Add and reorder footer element. Drag to sort.', 'unysonplus' ),
						],
					],
					'choices' => [
						'1' => [
							'copyright_col_1' => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ), $copyright_default ),
						],
						'2' => [
							'copyright_layout' => unysonplus_footer_ratio_picker( 'copyright', 2 ),
							'copyright_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ), $copyright_default ),
							'copyright_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
						],
						'3' => [
							'copyright_layout' => unysonplus_footer_ratio_picker( 'copyright', 3 ),
							'copyright_col_1'  => unysonplus_footer_column( __( 'Column 1', 'unysonplus' ), $copyright_default ),
							'copyright_col_2'  => unysonplus_footer_column( __( 'Column 2', 'unysonplus' ) ),
							'copyright_col_3'  => unysonplus_footer_column( __( 'Column 3', 'unysonplus' ) ),
						],
					],
					'show_borders' => false,
				],

				'copyright_custom_styling' => unysonplus_footer_custom_styling( 'copyright' ),

			],
		],
		'show_borders' => false,
	],
];
