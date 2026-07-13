<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * MAIN FOOTER — the always-on footer body (widget / link columns).
 *
 * The columns are driven by a single Split-Slider (count + widths + names) via
 * unysonplus_footer_columns_field() — up to 6 columns, any ratio (default 3 equal).
 * Renders whenever a column has content.
 *
 * Stored under `main_footer_columns` ( { main_footer_split, main_footer_col_1..6 } ),
 * read in inc/includes/footer-builder.php.
 */

$options = [

	// Quick-start: fill the columns with a ready-made footer, then edit the elements.
	'main_footer_presets' => [
		'type'         => 'preset-loader',
		'label'        => __( 'Main Footer Presets', 'unysonplus' ),
		'desc'         => __( 'Populate the columns below with a ready-made footer layout in one click, then fine-tune each element.', 'unysonplus' ),
		'preset_group' => 'main_footer_columns',
	],

	'main_footer_columns' => unysonplus_footer_columns_field( 'main_footer', 6, 3 ),

	'main_footer_custom_styling' => unysonplus_footer_custom_styling( 'main_footer' ),

];
