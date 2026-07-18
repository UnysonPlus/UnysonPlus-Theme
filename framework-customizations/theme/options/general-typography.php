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
$heading_override = function ( $label, $size, $lh = 1.2, $ls = 0 ) {
	return array(
		'label' => $label,
		'type'  => 'typography',
		'value' => array(
			'family' => '', 'variation' => 'regular',
			'size' => $size, 'line-height' => $lh, 'letter-spacing' => $ls, 'color' => '',
		),
		'components' => array(
			'family' => true, 'size' => true, 'line-height' => true, 'letter-spacing' => true, 'color' => true,
		),
	);
};

// Compact palette-preset colour (kind 'text' → text-{slug} choices); falls back to a
// raw picker only if the shortcodes styling helper isn't loaded.
$link_color = function ( $label, $desc ) {
	if ( function_exists( 'sc_color_field_compact' ) ) {
		return sc_color_field_compact( array( 'label' => $label, 'desc' => $desc, 'kind' => 'text' ) );
	}
	return array( 'label' => $label, 'desc' => $desc, 'type' => 'color-picker', 'value' => '' );
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
				'type'       => 'typography',
				'value'      => array( 'family' => '' ),
				'components' => array( 'family' => true, 'size' => false, 'line-height' => false, 'letter-spacing' => false, 'color' => false ),
			),

			'body' => array(
				'label'      => __( 'Body Font & Text', 'unysonplus' ),
				'desc'       => __( 'The main typography of the site content (paragraphs and lists) — family, size, line-height, colour.', 'unysonplus' ),
				'type'       => 'typography',
				'value'      => array(
					'family' => 'Open Sans', 'variation' => 'regular',
					'size' => 16, 'line-height' => 1.6, 'letter-spacing' => 0, 'color' => '',
				),
				'components' => array(
					'family' => true, 'size' => true, 'line-height' => true, 'letter-spacing' => true, 'color' => true,
				),
			),

			'body_link' => $link_color(
				__( 'Body Link Color', 'unysonplus' ),
				__( 'Link color inside post/page content. Leave empty to use the theme primary color.', 'unysonplus' )
			),
			'body_link_hover' => $link_color(
				__( 'Body Link Hover Color', 'unysonplus' ),
				__( 'Hover color for content links. Leave empty to reuse the link color.', 'unysonplus' )
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
			// Refined default type scale — smaller top, consistent taper, and
			// progressively looser line-height as sizes shrink (tight 1.15 for the
			// display h1 up to a comfortable 1.45 for h6). Slight negative tracking
			// on the three largest headings for a tighter, more premium read. Empty
			// fields keep the preset scale; these values are the no-preset baseline.
			'h1' => $heading_override( __( 'H1 Heading (override)', 'unysonplus' ), 36, 1.15, -0.7 ),
			'h2' => $heading_override( __( 'H2 Heading (override)', 'unysonplus' ), 28, 1.2,  -0.4 ),
			'h3' => $heading_override( __( 'H3 Heading (override)', 'unysonplus' ), 24, 1.3,  -0.2 ),
			'h4' => $heading_override( __( 'H4 Heading (override)', 'unysonplus' ), 20, 1.35, 0 ),
			'h5' => $heading_override( __( 'H5 Heading (override)', 'unysonplus' ), 18, 1.4,  0 ),
			'h6' => $heading_override( __( 'H6 Heading (override)', 'unysonplus' ), 16, 1.45, 0 ),
		),
	),
);
