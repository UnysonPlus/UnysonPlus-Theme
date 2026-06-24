<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * General → Typography → Custom Fonts.
 *
 * Self-hosted web fonts. Each entry uploads a .woff2 (plus optional .woff
 * fallback) and a family name. inc/includes/custom-fonts.php emits the matching
 * @font-face into the generated stylesheet AND registers each family name into
 * the typography pickers (via fw_option_type_typography*_standard_fonts) so it's
 * selectable for headings/body and per-section typography. Stored: `custom_fonts`.
 */

$options = [
	'custom_fonts' => [
		'label'       => false,
		'type'        => 'addable-box',
		'desc'        => __( 'Add self-hosted fonts. After saving, each family becomes selectable in the Typography font pickers.', 'unysonplus' ),
		'value'       => [],
		'box-options' => [
			'family' => [
				'label' => __( 'Font Family Name', 'unysonplus' ),
				'desc'  => __( 'The name you will pick in the font selectors, e.g. "Brand Sans".', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			],
			'woff2' => [
				'label' => __( 'Font File (.woff2)', 'unysonplus' ),
				'type'  => 'upload',
				'desc'  => __( 'Recommended modern format.', 'unysonplus' ),
			],
			'woff' => [
				'label' => __( 'Fallback (.woff) — optional', 'unysonplus' ),
				'type'  => 'upload',
			],
			'weight' => [
				'label'   => __( 'Weight', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '400',
				'choices' => [
					'100' => '100', '200' => '200', '300' => '300', '400' => '400 (normal)',
					'500' => '500', '600' => '600', '700' => '700 (bold)', '800' => '800', '900' => '900',
				],
			],
			'style' => [
				'label'   => __( 'Style', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'normal',
				'choices' => [
					'normal' => __( 'Normal', 'unysonplus' ),
					'italic' => __( 'Italic', 'unysonplus' ),
				],
			],
		],
		'template' => '{{- family }}',
	],
];
