<?php if ( ! defined( 'FW' ) ) {

	die( 'Forbidden' );
}

$fw_upw_pal = function_exists( 'fw_upw_icon_palette' )
	? fw_upw_icon_palette()
	: array( 'accent' => '#3858e9' );  // plugin absent: fall back to the brand blue



/* Compact palette-preset colour field (falls back to a raw picker if the shortcodes
   helper isn't loaded). kind 'text' → text-{slug} choices. */
$footer_color = function ( $label, $desc ) {
	if ( function_exists( 'sc_color_field_compact' ) ) {
		return sc_color_field_compact( [ 'label' => $label, 'desc' => $desc, 'kind' => 'text' ] );
	}
	return [ 'label' => $label, 'desc' => $desc, 'type' => 'color-picker', 'value' => '' ];
};

/* Spacing-scale select: choices are the live Spacing Scale steps (Theme Settings →
   General → Spacing), value = the step's size string (e.g. "1.5rem"), consumed as
   --footer-pad-* by theme-vars.php (unysonplus_css_length passes it through). */
$footer_spacing = function ( $label, $desc ) {
	$choices = [ '' => __( 'Default (theme)', 'unysonplus' ) ];
	if ( function_exists( 'unysonplus_get_spacing_scale' ) ) {
		foreach ( unysonplus_get_spacing_scale() as $step ) {
			$raw = isset( $step['size'] ) ? $step['size'] : '';
			// A scale step's size is normally a CSS-length string, but a custom step saved via a
			// unit-input carries a { value, unit } array — compose it rather than casting (which
			// would emit an "Array to string conversion" warning).
			if ( is_array( $raw ) ) {
				$raw = ( isset( $raw['value'] ) && $raw['value'] !== '' ) ? $raw['value'] . ( isset( $raw['unit'] ) ? $raw['unit'] : '' ) : '';
			}
			$size = (string) $raw;
			if ( $size === '' ) { continue; }
			$choices[ $size ] = ( $size === '0' ) ? __( 'None', 'unysonplus' ) : $size;
		}
	}
	// No scale available → keep a usable unit-input rather than an empty select.
	if ( count( $choices ) <= 1 ) {
		return [ 'label' => $label, 'desc' => $desc, 'type' => 'unit-input', 'units' => [ 'rem', 'px', 'em' ], 'value' => [ 'value' => '', 'unit' => 'rem' ], 'min' => 0 ];
	}
	return [ 'label' => $label, 'desc' => $desc, 'type' => 'select', 'value' => '', 'choices' => $choices ];
};

/* Border-Sides preview tiles — a mini footer box with an accent line on the top edge,
   bottom edge, or both, caption baked in (inline data-URI SVG, same approach as the
   Container / menu-style tiles). */
$sides_svg = function ( $variant, $label ) use ( $fw_upw_pal ) {
	$accent = $fw_upw_pal['accent']; $line = '#c3c4c7';
	$box  = '<rect x="14" y="8" width="76" height="26" rx="3" fill="none" stroke="' . $line . '" stroke-width="1.5"/>';
	$top  = in_array( $variant, [ 'top', 'both' ], true )    ? '<rect x="14" y="7"  width="76" height="3" rx="1.5" fill="' . $accent . '"/>' : '';
	$bot  = in_array( $variant, [ 'bottom', 'both' ], true ) ? '<rect x="14" y="32" width="76" height="3" rx="1.5" fill="' . $accent . '"/>' : '';
	$text = '<text x="52" y="47" text-anchor="middle" font-family="-apple-system,Segoe UI,Roboto,sans-serif" font-size="10" fill="#50575e">' . $label . '</text>';
	$svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 104 52" width="104" height="52">' . $box . $top . $bot . $text . '</svg>';
	return 'data:image/svg+xml,' . rawurlencode( $svg );
};
$sides_choice = function ( $variant, $label ) use ($sides_svg, $fw_upw_pal) {
	$uri = $sides_svg( $variant, $label );
	return [ 'small' => [ 'height' => 52, 'src' => $uri ], 'large' => [ 'height' => 74, 'src' => $uri ] ];
};

$options = [
	// Footer layout MODE — normal band vs. overlaid on the last section.
	'group_footer_mode' => [
		'type'    => 'group',
		'title'   => __( 'Footer Mode', 'unysonplus' ),
		'options' => [
			'footer_overlay_last_section' => [
				'label'        => __( 'Overlay on Last Section', 'unysonplus' ),
				'desc'         => __( 'Pin the footer to the BOTTOM of the page\'s last full-height section and overlay it, instead of rendering a separate band below the page — so the footer sits on the final scene. Ideal for cinematic / scroll-story pages, and it removes any blank tail after a pinned last section. For legibility over your last section set the footer Text / Link colours (below) and a Background scrim, and make that last section hold (not fade) at its end.', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
		],
	],
	// Background Color + Image + Overlay are now one Background Pro control (Video
	// disabled): colour, gradient and image (with position / size / repeat). To tint
	// an image, layer a semi-transparent colour or gradient on top — that replaces
	// the old separate "Overlay Opacity" slider. Consumed as --footer-bg-* by
	// theme-vars.php (legacy footer_bg_color/image/overlay still honoured as a
	// fallback until this is set, so existing footers keep their look).
	'footer_background' => [
		'label'   => __( 'Background', 'unysonplus' ),
		'desc'    => __( 'Footer background — colour, gradient and/or image (with position, size, repeat and scroll/fixed). For an image overlay, add a semi-transparent colour or gradient on top of the image.', 'unysonplus' ),
		'type'    => 'background-pro',
		'disable' => [ 'video' ],
	],
	'group_footer_colors' => [
		'type'    => 'group',
		'title'   => __( 'Colors', 'unysonplus' ),
		'options' => [
			'footer_text_color' => $footer_color(
				__( 'Text Color', 'unysonplus' ),
				__( 'Default text color for the entire footer. Pick a palette preset or a custom colour.', 'unysonplus' )
			),
			'footer_link_color' => $footer_color(
				__( 'Link Color', 'unysonplus' ),
				__( 'Default link color for the entire footer. Pick a palette preset or a custom colour.', 'unysonplus' )
			),
			/* 90% of real footers change their link COLOUR on hover; the theme previously only faded
			   the rest colour, so a distinct hover colour could not be expressed. Empty keeps the fade. */
			'footer_link_hover_color' => $footer_color(
				__( 'Link Hover Color', 'unysonplus' ),
				__( 'Link colour on hover / focus. Leave empty to keep the default subtle fade of the Link Color.', 'unysonplus' )
			),
		],
	],
	'group_footer_border' => [
		'type'    => 'group',
		'title'   => __( 'Top Border', 'unysonplus' ),
		'options' => [
			// ONE shared border (width · style · colour) applied to the edges chosen in
			// "Border Sides" below — users won't set different widths per edge, and a
			// one-off can override with CSS. Stored under the legacy footer_border_top key
			// (no migration); consumed as --footer-border-top-* by theme-vars.php, which
			// also tolerates the flat footer_border_top_{width,style,color}.
			'footer_border_top' => function_exists( 'unysonplus_hf_border_row_field' )
				? unysonplus_hf_border_row_field(
					__( 'Border', 'unysonplus' ),
					__( 'The footer border line — width · style · colour, like the CSS shorthand 1px solid #000. Choose which edges it applies to below. Shows only when both a width and a colour are set.', 'unysonplus' )
				)
				: [
					'label' => __( 'Border Width', 'unysonplus' ),
					'type'  => 'unit-input',
					'units' => [ 'px', 'rem', 'em' ],
					'value' => [ 'value' => '', 'unit' => 'px' ],
					'min'   => 0,
				],

			// Which edge(s) get the border — any combination of top/right/bottom/left
			// (multi-select image-picker, value is an array). Default Top preserves the
			// previous top-only behavior; footer.php maps this to .footer--b{t,r,b,l}.
			'footer_border_sides' => function_exists( 'unysonplus_hf_border_sides_field' )
				? unysonplus_hf_border_sides_field( [ 'top' ] )
				: [
					'type'    => 'image-picker',
					'label'   => __( 'Border Sides', 'unysonplus' ),
					'value'   => 'top',
					'choices' => [
						'top'    => $sides_choice( 'top',    __( 'Top', 'unysonplus' ) ),
						'bottom' => $sides_choice( 'bottom', __( 'Bottom', 'unysonplus' ) ),
						'both'   => $sides_choice( 'both',   __( 'Both', 'unysonplus' ) ),
					],
				],

			// How far the border runs horizontally (applies to whichever sides are on).
			// Full Width = edge to edge (default, unchanged); Container = aligned with the
			// site content; Custom = an exact centered max-width. INLINE multi-picker:
			// label/desc on the picker sub-option, top-level false, default in `value`,
			// only "Custom" reveals a sub-option (CLAUDE.md).
			'footer_border_top_extent' => [
				'type'   => 'multi-picker',
				'label'  => false,
				'desc'   => false,
				'picker' => [
					'mode' => [
						'label'   => __( 'Border Extent', 'unysonplus' ),
						'desc'    => __( 'How far the border runs across the page. Full Width spans edge to edge; Container aligns it with the site content; Custom sets an exact centered width.', 'unysonplus' ),
						'type'    => 'select',
						'choices' => [
							'full'      => __( 'Full Width', 'unysonplus' ),
							'container' => __( 'Container Width', 'unysonplus' ),
							'custom'    => __( 'Custom Width', 'unysonplus' ),
						],
					],
				],
				'value'   => [ 'mode' => 'full' ],
				'choices' => [
					'custom' => [
						'footer_border_top_extent_width' => [
							'label' => __( 'Custom Border Width', 'unysonplus' ),
							'desc'  => __( 'Maximum width of the centered border line, e.g. 800px or 60%.', 'unysonplus' ),
							'type'  => 'unit-input',
							'units' => [ 'px', 'rem', 'em', '%' ],
							'value' => [ 'value' => '', 'unit' => 'px' ],
							'min'   => 0,
						],
					],
				],
				'show_borders' => false,
			],
		],
	],
	'group_footer_spacing' => [
		'type'    => 'group',
		'title'   => __( 'Spacing', 'unysonplus' ),
		'options' => [
			'footer_padding_top'    => $footer_spacing(
				__( 'Padding Top', 'unysonplus' ),
				__( 'Space above the footer content — above the Pre-Footer. "Default" uses the theme value.', 'unysonplus' )
			),
			'footer_padding_bottom' => $footer_spacing(
				__( 'Padding Bottom', 'unysonplus' ),
				__( 'Space below the footer content — below the Post-Footer and above the Copyright bar (which sits flush at the very bottom). "Default" uses the theme value.', 'unysonplus' )
			),
			/* The two selects above are constrained to the site spacing scale, which tops out at 8rem.
			   Measured across 279 real footer padding values, 95% land on a scale step but the rest are
			   large (160-240px) and clamped to the ceiling, losing up to 112px. These overrides win when
			   filled, exactly like the header's numeric design overrides. */
			'footer_padding_top_custom' => [
				'label' => __( 'Custom Padding Top', 'unysonplus' ),
				'desc'  => __( 'Exact padding above the footer content. Overrides the Padding Top step above when set — use it for a value the spacing scale cannot reach.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px', 'rem', 'em' ],
				'value' => [ 'value' => '', 'unit' => 'px' ],
				'min'   => 0,
			],
			'footer_padding_bottom_custom' => [
				'label' => __( 'Custom Padding Bottom', 'unysonplus' ),
				'desc'  => __( 'Exact padding below the footer content. Overrides the Padding Bottom step above when set.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px', 'rem', 'em' ],
				'value' => [ 'value' => '', 'unit' => 'px' ],
				'min'   => 0,
			],
			/* Gap BETWEEN footer columns. Was a fixed 40px (grid) / 32px (equal) / 28px (auto); 80% of
			   measured footers use something else, most commonly 48px. */
			'footer_col_gap' => [
				'label' => __( 'Column Gap', 'unysonplus' ),
				'desc'  => __( 'Horizontal (and stacked vertical) gap between footer columns. Leave empty for the theme default.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px', 'rem', 'em' ],
				'value' => [ 'value' => '', 'unit' => 'px' ],
				'min'   => 0,
			],
			/* Footer columns stacked unconditionally under 768px. 11% of measured footers keep a
			   two-column pair on a phone; 1 column is the previous behaviour. */
			'footer_mobile_columns' => [
				'label'   => __( 'Columns on Mobile', 'unysonplus' ),
				'desc'    => __( 'How many columns the footer rows keep below 768px. One column (stacked) is the default.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '1',
				'choices' => [
					'1' => __( '1 — stacked (default)', 'unysonplus' ),
					'2' => __( '2 columns', 'unysonplus' ),
				],
			],
		],
	],
	/* BOXED BODY — the footer's content (Pre / Main / Post, optionally the Copyright bar) inside ONE inset
	   panel: a capped width with a side gutter, its own padding, background, border, radius and shadow, on
	   the .footer__body wrapper. The "card footer" pattern (a bordered / tinted panel floating on the footer
	   background) is common in modern layouts and had no native expression — a per-bar Custom Styling paints
	   a full-bleed section, and nothing could wrap several bars in one box. Off by default (no change). */
	'group_footer_box' => [
		'type'    => 'group',
		'title'   => __( 'Boxed Body', 'unysonplus' ),
		'options' => [
			'footer_body_box' => [
				'type'   => 'multi-picker',
				'label'  => false,
				'desc'   => false,
				'picker' => [
					'enabled' => [
						'label'        => __( 'Boxed Body', 'unysonplus' ),
						'desc'         => __( 'Wrap the footer content (Pre / Main / Post-Footer) in one inset panel with its own padding, background, border, radius and shadow — the footer background stays behind it, and the footer Padding Top/Bottom becomes the space around the panel.', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
					],
				],
				'choices' => [
					'yes' => [
						'footer_box_max_width' => [
							'label' => __( 'Max Width', 'unysonplus' ),
							'desc'  => __( 'The panel\'s maximum width. Leave empty to use the site Container Width.', 'unysonplus' ),
							'type'  => 'unit-input',
							'units' => [ 'px', 'rem', '%' ],
							'value' => [ 'value' => '', 'unit' => 'px' ],
							'min'   => 0,
						],
						'footer_box_gutter' => [
							'label' => __( 'Side Gutter', 'unysonplus' ),
							'desc'  => __( 'Minimum space between the panel and the viewport edges (the panel is width: min(Max Width, 100% - 2 × gutter)).', 'unysonplus' ),
							'type'  => 'unit-input',
							'units' => [ 'px', 'rem' ],
							'value' => [ 'value' => '16', 'unit' => 'px' ],
							'min'   => 0,
						],
						'footer_box_padding_y' => [
							'label' => __( 'Padding (vertical)', 'unysonplus' ),
							'desc'  => __( 'Inner space above and below the panel content.', 'unysonplus' ),
							'type'  => 'unit-input',
							'units' => [ 'px', 'rem' ],
							'value' => [ 'value' => '32', 'unit' => 'px' ],
							'min'   => 0,
						],
						'footer_box_padding_x' => [
							'label' => __( 'Padding (horizontal)', 'unysonplus' ),
							'desc'  => __( 'Inner space on the left and right of the panel content. The bars inside the panel use this as their gutter (a Full Width bar adds none of its own).', 'unysonplus' ),
							'type'  => 'unit-input',
							'units' => [ 'px', 'rem' ],
							'value' => [ 'value' => '32', 'unit' => 'px' ],
							'min'   => 0,
						],
						'footer_box_background' => [
							'label'   => __( 'Panel Background', 'unysonplus' ),
							'desc'    => __( 'The panel\'s own fill — colour, gradient and/or image — painted over the footer background.', 'unysonplus' ),
							'type'    => 'background-pro',
							'disable' => [ 'video' ],
						],
						'footer_box_border' => function_exists( 'unysonplus_hf_border_row_field' )
							? unysonplus_hf_border_row_field(
								__( 'Panel Border', 'unysonplus' ),
								__( 'Width · style · colour on all four edges of the panel. Shows only when both a width and a colour are set.', 'unysonplus' )
							)
							: [ 'type' => 'hidden', 'value' => '' ],
						'footer_box_radius' => [
							'label' => __( 'Corner Radius', 'unysonplus' ),
							'desc'  => __( 'Rounds the panel corners.', 'unysonplus' ),
							'type'  => 'unit-input',
							'units' => [ 'px', 'rem' ],
							'value' => [ 'value' => '', 'unit' => 'px' ],
							'min'   => 0,
						],
						'footer_box_shadow' => [
							'label' => __( 'Panel Shadow', 'unysonplus' ),
							'desc'  => __( 'A drop shadow under the panel.', 'unysonplus' ),
							'type'  => 'box-shadow',
						],
						'footer_box_copyright_inside' => [
							'label'        => __( 'Copyright Inside the Panel', 'unysonplus' ),
							'desc'         => __( 'On: the Copyright bar renders inside the panel, after the Post-Footer (its own Custom Styling border becomes the panel\'s bottom divider). Off: it stays a flush full-width bar under the panel.', 'unysonplus' ),
							'type'         => 'switch',
							'value'        => 'no',
							'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
							'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
						],
					],
				],
			],
		],
	],
	'footer_css_class' => [
		'label' => __( 'Custom CSS Class', 'unysonplus' ),
		'type'  => 'text',
		'value' => '',
		'desc'  => __( 'Add custom CSS class(es) to the footer wrapper.', 'unysonplus' ),
	],
];
