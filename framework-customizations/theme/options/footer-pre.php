<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * PRE-FOOTER — a promo / CTA / contact band above the main footer.
 *
 * No Enable switch: like the Main Footer, the section renders only when a column
 * has content. The columns are driven by a single Split-Slider (count + widths +
 * names) via unysonplus_footer_columns_field() — up to 6 columns, any ratio.
 *
 * Stored under `pre_footer_columns` ( { pre_footer_split, pre_footer_col_1..6 } ),
 * read in template-parts/footer-builder.php.
 */

$options = [

	// Quick-start: fill the columns with a ready-made pre-footer, then edit the elements.
	'pre_footer_presets' => [
		'type'         => 'preset-loader',
		'label'        => __( 'Pre-Footer Presets', 'unysonplus' ),
		'desc'         => __( 'Populate the columns below with a ready-made pre-footer in one click, then fine-tune each element.', 'unysonplus' ),
		'preset_group' => 'pre_footer_columns',
	],

	'pre_footer_columns' => unysonplus_footer_columns_field( 'pre_footer', 6, 1 ),

	'pre_footer_custom_styling' => unysonplus_footer_custom_styling( 'pre_footer' ),

];
