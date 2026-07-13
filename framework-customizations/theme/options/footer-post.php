<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * POST-FOOTER — a slim strip below the main footer (secondary menu, social, etc.).
 *
 * No Enable switch: renders only when a column has content. Columns are driven by a
 * single Split-Slider (count + widths + names) via unysonplus_footer_columns_field()
 * — up to 6 columns, any ratio.
 *
 * Stored under `post_footer_columns` ( { post_footer_split, post_footer_col_1..6 } ),
 * read in template-parts/footer-builder.php.
 */

$options = [

	// Quick-start: fill the columns with a ready-made post-footer, then edit the elements.
	'post_footer_presets' => [
		'type'         => 'preset-loader',
		'label'        => __( 'Post-Footer Presets', 'unysonplus' ),
		'desc'         => __( 'Populate the columns below with a ready-made post-footer in one click, then fine-tune each element.', 'unysonplus' ),
		'preset_group' => 'post_footer_columns',
	],

	'post_footer_columns' => unysonplus_footer_columns_field( 'post_footer', 6, 1 ),

	'post_footer_custom_styling' => unysonplus_footer_custom_styling( 'post_footer' ),

];
