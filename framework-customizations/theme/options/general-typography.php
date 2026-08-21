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

			'grp_typo_fonts' => array(
				'type'    => 'group',
				'title'   => __( 'Fonts', 'unysonplus' ),
				'options' => array(
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

				),
			),
			'grp_typo_scale' => array(
				'type'    => 'group',
				'title'   => __( 'Type Scale (fluid)', 'unysonplus' ),
				'options' => array(
			'type_scale_enable' => array(
				'label'        => __( 'Fluid Heading Scale', 'unysonplus' ),
				'desc'         => __( 'Drive H1–H6 from one fluid modular scale (built from the Body size × the ratio below) instead of the fixed per-heading sizes. Sizes scale smoothly across every screen — no breakpoint jumps — and stay zoom-accessible. When on, the "Heading Sizes (overrides)" sizes below are ignored (their line-height, letter-spacing and colour still apply).', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'left-choice'  => array( 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ),
				'right-choice' => array( 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ),
			),
			'type_scale_ratio' => array(
				'label'   => __( 'Scale Ratio (desktop)', 'unysonplus' ),
				'desc'    => __( 'Step-to-step size multiplier on large screens. Larger = more dramatic jumps between heading levels.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '1.25',
				'choices' => array(
					'1.067' => __( 'Minor Second — 1.067 (subtle)', 'unysonplus' ),
					'1.125' => __( 'Major Second — 1.125', 'unysonplus' ),
					'1.2'   => __( 'Minor Third — 1.2', 'unysonplus' ),
					'1.25'  => __( 'Major Third — 1.25 (balanced)', 'unysonplus' ),
					'1.333' => __( 'Perfect Fourth — 1.333', 'unysonplus' ),
					'1.414' => __( 'Augmented Fourth — 1.414', 'unysonplus' ),
					'1.5'   => __( 'Perfect Fifth — 1.5', 'unysonplus' ),
					'1.618' => __( 'Golden Ratio — 1.618 (dramatic)', 'unysonplus' ),
				),
			),
			'type_scale_ratio_mobile' => array(
				'label'   => __( 'Scale Ratio (mobile)', 'unysonplus' ),
				'desc'    => __( 'A gentler multiplier at the small end, so big headings shrink more than body text on phones. Usually one step below the desktop ratio.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '1.2',
				'choices' => array(
					'1.067' => __( 'Minor Second — 1.067', 'unysonplus' ),
					'1.125' => __( 'Major Second — 1.125', 'unysonplus' ),
					'1.2'   => __( 'Minor Third — 1.2 (balanced)', 'unysonplus' ),
					'1.25'  => __( 'Major Third — 1.25', 'unysonplus' ),
					'1.333' => __( 'Perfect Fourth — 1.333', 'unysonplus' ),
				),
			),
				),
			),
			'grp_typo_headings' => array(
				'type'    => 'group',
				'title'   => __( 'Per-Heading Styling', 'unysonplus' ),
				'options' => array(
			'h1' => $heading_override( __( 'H1 Heading (override)', 'unysonplus' ), 36, 1.15, -0.7 ),
			'h2' => $heading_override( __( 'H2 Heading (override)', 'unysonplus' ), 28, 1.2,  -0.4 ),
			'h3' => $heading_override( __( 'H3 Heading (override)', 'unysonplus' ), 24, 1.3,  -0.2 ),
			'h4' => $heading_override( __( 'H4 Heading (override)', 'unysonplus' ), 20, 1.35, 0 ),
			'h5' => $heading_override( __( 'H5 Heading (override)', 'unysonplus' ), 18, 1.4,  0 ),
			'h6' => $heading_override( __( 'H6 Heading (override)', 'unysonplus' ), 16, 1.45, 0 ),
				),
			),
			// Content Links last — a secondary detail, kept out of the primary Fonts → Scale → Headings flow.
			'grp_typo_links' => array(
				'type'    => 'group',
				'title'   => __( 'Content Links', 'unysonplus' ),
				'options' => array(
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
				// Default = Always underlined: content links should be distinguishable without relying on
				// colour alone (WCAG 1.4.1). The "(default)" marker sits on the ACTUAL default value so the
				// label can never drift from the stored value again.
				'value'   => 'always',
				'choices' => array(
					'hover'  => __( 'On hover only', 'unysonplus' ),
					'always' => __( 'Always underlined (default)', 'unysonplus' ),
					'never'  => __( 'Never underlined', 'unysonplus' ),
				),
			),
				),
			),
		),
	),
);
