<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Footer → Watermark tab: the oversized footer wordmark ("giant footer text"). Rendered by
 * inc/includes/footer-wordmark.php. Its own tab because it carries ~20 options; the option ids are
 * unchanged from when it lived under Footer Layout, so saved settings carry over.
 */

/* Compact palette-preset colour field (falls back to a raw picker if the shortcodes helper isn't loaded). */
$footer_color = function ( $label, $desc ) {
	if ( function_exists( 'sc_color_field_compact' ) ) {
		return sc_color_field_compact( [ 'label' => $label, 'desc' => $desc, 'kind' => 'text' ] );
	}
	return [ 'label' => $label, 'desc' => $desc, 'type' => 'color-picker', 'value' => '' ];
};

$options = [
	// Watermark Text — an oversized wordmark across the footer (the "giant footer text" style).
	'group_footer_wordmark' => [
		'type'    => 'group',
		'options' => [
			'footer_wordmark' => [
				'type'   => 'multi-picker',
				'label'  => false,
				'desc'   => false,
				'picker' => [
					'enabled' => [
						'label'        => __( 'Watermark Text', 'unysonplus' ),
						'desc'         => __( 'An oversized wordmark (your site name, other text or your logo) set edge to edge across the footer as decoration. It is sized to fit the width on every screen, and hidden from screen readers and search engines.', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
					],
				],
				'choices' => [
					'yes' => [
						// Show: a picker that reveals only the chosen source's field.
						'footer_wordmark_source' => [
							'type'    => 'multi-picker',
							'label'   => false,
							'desc'    => false,
							'picker'  => [
								'source' => [
									'label'   => __( 'Show', 'unysonplus' ),
									'type'    => 'select',
									'value'   => 'text',
									'choices' => [ 'text' => __( 'Text', 'unysonplus' ), 'image' => __( 'Logo / image (SVG or PNG)', 'unysonplus' ) ],
								],
							],
							'choices' => [
								'text'  => [
									'footer_wordmark_text' => [
										'label' => __( 'Text', 'unysonplus' ),
										'desc'  => __( 'Leave empty to use the Site Title. One to three words read best: the line never wraps.', 'unysonplus' ),
										'type'  => 'text',
										'value' => '',
									],
								],
								'image' => [
									'footer_wordmark_image' => [
										'label'       => __( 'Logo / Image', 'unysonplus' ),
										'desc'        => __( 'A wide, single-colour SVG works best. Strength, Placement and Crop apply; the text styles below do not.', 'unysonplus' ),
										'type'        => 'upload',
										'images_only' => true,
									],
								],
							],
						],
						'footer_wordmark_placement' => [
							'label'   => __( 'Placement', 'unysonplus' ),
							'type'    => 'select',
							'value'   => 'after',
							'choices' => [
								'before'           => __( 'Before the footer content (top of the footer)', 'unysonplus' ),
								'behind-body'      => __( 'Behind the Pre / Main / Post-Footer', 'unysonplus' ),
								'before-copyright' => __( 'Between the footer content and the copyright', 'unysonplus' ),
								'after'            => __( 'After the copyright (bottom of the page)', 'unysonplus' ),
								'behind-all'       => __( 'Behind the whole footer, copyright included', 'unysonplus' ),
							],
						],
						'footer_wordmark_valign' => [
							'label'   => __( 'Vertical Position (behind)', 'unysonplus' ),
							'desc'    => __( 'For the two "Behind" placements.', 'unysonplus' ),
							'type'    => 'select',
							'value'   => 'bottom',
							'choices' => [ 'top' => __( 'Top', 'unysonplus' ), 'center' => __( 'Centre', 'unysonplus' ), 'bottom' => __( 'Bottom', 'unysonplus' ) ],
						],
						'footer_wordmark_crop' => [
							'label'      => __( 'Crop', 'unysonplus' ),
							'desc'       => __( 'How much of the letters is cut off, in % of their height: at the top for "Before", otherwise at the bottom. 0 shows them whole.', 'unysonplus' ),
							'type'       => 'slider',
							'value'      => 22,
							'properties' => [ 'min' => 0, 'max' => 60, 'step' => 1 ],
						],
						'footer_wordmark_width' => [
							'label'   => __( 'Width', 'unysonplus' ),
							'type'    => 'select',
							'value'   => 'full',
							'choices' => [
								'full'      => __( 'Full width (edge to edge)', 'unysonplus' ),
								'container' => __( 'Container width (lines up with the footer columns)', 'unysonplus' ),
								'custom'    => __( 'Custom maximum width', 'unysonplus' ),
							],
						],
						'footer_wordmark_max_width' => [
							'label'      => __( 'Custom Max Width (px)', 'unysonplus' ),
							'type'       => 'slider',
							'value'      => 1200,
							'properties' => [ 'min' => 200, 'max' => 2400, 'step' => 10 ],
						],
						'footer_wordmark_style' => [
							'label'   => __( 'Style', 'unysonplus' ),
							'type'    => 'select',
							'value'   => 'fade',
							'choices' => [
								'fade'         => __( 'Fade (fills at the top, fades out downwards)', 'unysonplus' ),
								'fill'         => __( 'Solid (one colour)', 'unysonplus' ),
								'gradient'     => __( 'Gradient (two colours)', 'unysonplus' ),
								'outline'      => __( 'Outline (letter edges only)', 'unysonplus' ),
								'outline-fade' => __( 'Outline that fades out downwards', 'unysonplus' ),
							],
						],
						'footer_wordmark_color' => $footer_color(
							__( 'Colour', 'unysonplus' ),
							__( 'Empty = the footer text colour. For Gradient, the start colour.', 'unysonplus' )
						),
						'footer_wordmark_color_2' => $footer_color(
							__( 'Second Colour (gradient)', 'unysonplus' ),
							__( 'The gradient end colour. Empty = transparent.', 'unysonplus' )
						),
						'footer_wordmark_angle' => [
							'label'      => __( 'Gradient Angle', 'unysonplus' ),
							'type'       => 'slider',
							'value'      => 90,
							'properties' => [ 'min' => 0, 'max' => 360, 'step' => 5 ],
						],
						'footer_wordmark_opacity' => [
							'label'      => __( 'Strength', 'unysonplus' ),
							'desc'       => __( 'How visible it is, in %. Watermarks usually sit between 5 and 15; outlines need a little more; a bold statement wordmark can go to 100.', 'unysonplus' ),
							'type'       => 'slider',
							'value'      => 10,
							'properties' => [ 'min' => 2, 'max' => 100, 'step' => 1 ],
						],
						'footer_wordmark_stroke' => [
							'label'      => __( 'Outline Thickness (px)', 'unysonplus' ),
							'type'       => 'slider',
							'value'      => 1,
							'properties' => [ 'min' => 0.5, 'max' => 6, 'step' => 0.5 ],
						],
						'footer_wordmark_font' => [
							'label'   => __( 'Font', 'unysonplus' ),
							'type'    => 'select',
							'value'   => 'heading',
							'choices' => [ 'heading' => __( 'Heading font', 'unysonplus' ), 'body' => __( 'Body font', 'unysonplus' ) ],
						],
						'footer_wordmark_weight' => [
							'label'   => __( 'Weight', 'unysonplus' ),
							'type'    => 'select',
							'value'   => '800',
							'choices' => [ '300' => '300', '400' => '400', '500' => '500', '600' => '600', '700' => '700', '800' => '800', '900' => '900' ],
						],
						'footer_wordmark_tracking' => [
							'label'      => __( 'Letter Spacing', 'unysonplus' ),
							'desc'       => __( 'In hundredths of the letter size: negative pulls letters together, positive spreads them.', 'unysonplus' ),
							'type'       => 'slider',
							'value'      => -2,
							'properties' => [ 'min' => -10, 'max' => 30, 'step' => 1 ],
						],
						'footer_wordmark_uppercase' => [
							'label'        => __( 'Uppercase', 'unysonplus' ),
							'type'         => 'switch',
							'value'        => 'yes',
							'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
							'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
						],
						'footer_wordmark_italic' => [
							'label'        => __( 'Italic', 'unysonplus' ),
							'type'         => 'switch',
							'value'        => 'no',
							'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
							'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
						],
						// Effect: a picker whose choices can each reveal their own settings. The theme offers
						// None and Reveal; an extension adds more (with their settings) via the filters in
						// inc/includes/footer-wordmark.php.
						'footer_wordmark_effect' => [
							'type'    => 'multi-picker',
							'label'   => false,
							'desc'    => false,
							'picker'  => [
								'effect' => [
									'label'   => __( 'Effect', 'unysonplus' ),
									'desc'    => __( 'Motion is skipped for visitors who ask for reduced motion. More effects appear here when the Animation Engine extension is active.', 'unysonplus' ),
									'type'    => 'select',
									'value'   => 'none',
									'choices' => function_exists( 'unysonplus_footer_wordmark_effect_choices' ) ? unysonplus_footer_wordmark_effect_choices() : [ 'none' => __( 'None', 'unysonplus' ) ],
								],
							],
							'choices' => function_exists( 'unysonplus_footer_wordmark_effect_options' ) ? unysonplus_footer_wordmark_effect_options() : [],
						],
						'footer_wordmark_hide' => [
							'label'   => __( 'Hide On', 'unysonplus' ),
							'type'    => 'checkboxes',
							'value'   => [],
							'choices' => [ 'hide-xs' => __( 'Mobile', 'unysonplus' ), 'hide-sm' => __( 'Tablet', 'unysonplus' ), 'hide-md' => __( 'Desktop', 'unysonplus' ) ],
						],
						'footer_wordmark_link' => [
							'label' => __( 'Link (optional)', 'unysonplus' ),
							'desc'  => __( 'Makes the wordmark a link, e.g. to the home page. It stays hidden from screen readers and out of the keyboard order, so keep a normal link to the same place in the footer.', 'unysonplus' ),
							'type'  => 'text',
							'value' => '',
						],
					],
				],
			],
		],
	],
];
