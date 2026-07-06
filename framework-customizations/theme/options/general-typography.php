<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * General → Typography.
 *
 * A Typography Preset (curated heading + body pairing with a size scale) drives the
 * whole site in one pick — mirroring the Color Presets. Choose "Custom" to set your
 * own Heading Font + Body Font, with optional Per-Heading overrides.
 *
 * Resolved by inc/includes/css-tokens.php (unysonplus_typography_config): a preset
 * sets --font-heading / --font-body + the h1–h6 scale; Custom uses the fields here.
 * Google fonts for the effective families load via inc/hooks.php.
 */

// A per-heading override (typography-v2). Family empty = inherit the Heading Font /
// preset; size/line-height/etc. empty = keep the preset scale / theme default.
$heading_override = function ( $label, $size ) {
	return array(
		'label' => $label,
		'type'  => 'typography-v2',
		'value' => array(
			'family' => '', 'variation' => 'regular',
			'size' => $size, 'line-height' => 1.2, 'letter-spacing' => 0, 'color' => '',
		),
		'components' => array(
			'family' => true, 'size' => true, 'line-height' => true, 'letter-spacing' => true, 'color' => true,
		),
	);
};

$options = array(

	// Quick-start Typography Presets — the same preset-loader control the Menu tab
	// uses. Picking one fills the Heading Font + Body + heading size scale below (then
	// you fine-tune). Applies to the whole `typography` group; see settings-presets.php.
	'typography_presets' => array(
		'type'         => 'preset-loader',
		'label'        => __( 'Typography Presets', 'unysonplus' ),
		'desc'         => __( 'Start from a curated heading + body font pairing, then fine-tune below. Or upload a preset JSON you exported earlier.', 'unysonplus' ),
		'preset_group' => 'typography',
	),

	'typography' => array(
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => array(

			'heading_font' => array(
				'label'      => __( 'Heading Font', 'unysonplus' ),
				'desc'       => __( 'Font family for all headings (H1–H6). Leave empty to inherit the body font.', 'unysonplus' ),
				'type'       => 'typography-v2',
				'value'      => array( 'family' => '' ),
				'components' => array( 'family' => true, 'size' => false, 'line-height' => false, 'letter-spacing' => false, 'color' => false ),
			),

			'body' => array(
				'label'      => __( 'Body Font & Text', 'unysonplus' ),
				'desc'       => __( 'The main typography of the site content (paragraphs and lists) — family, size, line-height, colour.', 'unysonplus' ),
				'type'       => 'typography-v2',
				'value'      => array(
					'family' => 'Open Sans', 'variation' => 'regular',
					'size' => 16, 'line-height' => 1.6, 'letter-spacing' => 0, 'color' => '',
				),
				'components' => array(
					'family' => true, 'size' => true, 'line-height' => true, 'letter-spacing' => true, 'color' => true,
				),
			),

			'body_link' => array(
				'label' => __( 'Body Link Color', 'unysonplus' ),
				'desc'  => __( 'Link color inside post/page content. Leave empty to use the theme primary color.', 'unysonplus' ),
				'type'  => 'color-picker',
				'value' => '',
			),
			'body_link_hover' => array(
				'label' => __( 'Body Link Hover Color', 'unysonplus' ),
				'desc'  => __( 'Hover color for content links. Leave empty to reuse the link color.', 'unysonplus' ),
				'type'  => 'color-picker',
				'value' => '',
			),
			'body_link_underline' => array(
				'label'   => __( 'Body Link Underline', 'unysonplus' ),
				'desc'    => __( 'Underline style for links inside post/page content.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'hover',
				'choices' => array(
					'hover'  => __( 'On hover only (default)', 'unysonplus' ),
					'always' => __( 'Always underlined', 'unysonplus' ),
					'never'  => __( 'Never underlined', 'unysonplus' ),
				),
			),

			/* --- Per-Heading Overrides (Advanced) — fine-tune individual headings on
			   top of the Preset / Heading Font. Any empty field keeps the preset scale
			   / theme default; family empty inherits the Heading Font. Kept FLAT (not
			   in a box) so the `multi` container still stores each h1–h6 value. --- */
			'h1' => $heading_override( __( 'H1 Heading (override)', 'unysonplus' ), 40 ),
			'h2' => $heading_override( __( 'H2 Heading (override)', 'unysonplus' ), 32 ),
			'h3' => $heading_override( __( 'H3 Heading (override)', 'unysonplus' ), 28 ),
			'h4' => $heading_override( __( 'H4 Heading (override)', 'unysonplus' ), 24 ),
			'h5' => $heading_override( __( 'H5 Heading (override)', 'unysonplus' ), 20 ),
			'h6' => $heading_override( __( 'H6 Heading (override)', 'unysonplus' ), 16 ),
		),
	),
);
