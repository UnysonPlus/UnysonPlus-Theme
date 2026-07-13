<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * COPYRIGHT — the bottom-most footer strip. Enabled by default; columns are driven
 * by a single Split-Slider (count + widths + names) via unysonplus_footer_columns_field()
 * — max 3 columns, any ratio (default 1). Column 1 defaults to a Text element holding
 * the copyright line ({{current_year}} is resolved by the Text renderer).
 *
 * Stored under `copyright_settings` → { enabled, 'yes' → { copyright_columns: {
 * copyright_split, copyright_col_1..3 } } }.
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
	// Quick-start: fill the copyright columns with a ready-made layout, then edit.
	'copyright_presets' => [
		'type'         => 'preset-loader',
		'label'        => __( 'Copyright Presets', 'unysonplus' ),
		'desc'         => __( 'Set the copyright row layout in one click, then fine-tune each element.', 'unysonplus' ),
		'preset_group' => 'copyright_settings',
	],
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
				'copyright_columns'        => unysonplus_footer_columns_field( 'copyright', 3, 1, $copyright_default ),
				'copyright_custom_styling' => unysonplus_footer_custom_styling( 'copyright' ),
			],
		],
		'show_borders' => false,
	],
];
