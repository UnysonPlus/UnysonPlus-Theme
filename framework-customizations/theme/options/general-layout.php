<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * General → Layout sub-tab.
 *
 * Site-wide layout controls — width mode, site background, container, and the
 * spacing system.
 *
 * Sidebar controls live in their own General → Sidebar sub-tab
 * (`general-sidebar.php`, `general_sidebar`); the preloader lives in
 * General → Preloader (`general-preloader.php`, `general_preloader`); the smooth
 * scroll / scroll-progress controls live in General → Scrolling
 * (`general-scroll.php`, `general_scroll`); and the header layout-mode /
 * vertical-rail-width controls moved to Header → Layout (`header_layout`).
 *
 * Options here are stored under the `general_layout` multi key. The sections
 * are wrapped in `group` containers purely for editor organization — groups
 * flatten on save, so every leaf key stays at the top level of the
 * `general_layout` value. `unysonplus_layout_get()` merges `general_layout`,
 * `general_sidebar`, `general_preloader` and `general_scroll`, so reads of the
 * moved keys are unchanged.
 *
 * Image-picker previews live at assets/svg/layout/*.svg.
 */

$uri = get_template_directory_uri();
$svg = $uri . '/assets/svg/layout';
$pat = $uri . '/images/patterns';

// Cache-bust the preview SVGs by theme version so edits to them (e.g. the
// Site Width Mode labels) show up without a manual hard-refresh.
$svg_ver = wp_get_theme( get_template() )->get( 'Version' );

/* Build the image-picker `choices` array for any pair of {value => svg-filename}. */
$picker = function ( array $pairs, $height_small = 70, $height_large = 140 ) use ( $svg, $svg_ver ) {
	$out = [];
	foreach ( $pairs as $value => $file ) {
		$src = $svg . '/' . $file . ( $svg_ver ? '?v=' . rawurlencode( $svg_ver ) : '' );
		$out[ $value ] = [
			'small' => [ 'height' => $height_small, 'src' => $src ],
			'large' => [ 'height' => $height_large, 'src' => $src ],
		];
	}
	return $out;
};

$options = [
	'general_layout' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [

			/* ============ A. Site Container ============ */
			'group_container' => [
				'type'    => 'group',
				'options' => [
					/* Site Width Mode is a multi-picker: the picker chooses the mode,
					   and only the chosen mode's options are revealed (Boxed → width/
					   alignment/margin; Framed → frame width/color; Full → nothing). */
					'site_width_mode' => [
						'type'   => 'multi-picker',
						'label'  => false,
						'desc'   => false,
						'picker' => [
							'mode' => [
								'label'   => __( 'Site Width Mode', 'unysonplus' ),
								'desc'    => __( 'How the whole site container is laid out. Full-width spans edge to edge; Boxed centers a fixed-width column; Framed adds a colored border around the entire viewport. The options for the chosen mode appear below.', 'unysonplus' ),
								'type'    => 'image-picker',
								'choices' => $picker( [
									'full'   => 'width-full.svg',
									'boxed'  => 'width-boxed.svg',
									'framed' => 'width-framed.svg',
								], 125, 250 ),
							],
						],
						'value'   => [ 'mode' => 'full' ],
						'choices' => [
							'boxed' => [
								'site_boxed_width' => [
									'label' => __( 'Boxed Width', 'unysonplus' ),
									'desc'  => __( 'Maximum width of the boxed container. Recommended range: 1100–1600 px.', 'unysonplus' ),
									'type'  => 'slider',
									'value' => 1320,
									'properties' => [
										'min'  => 980,
										'max'  => 1920,
										'step' => 10,
									],
								],
								'site_boxed_alignment' => [
									'label'   => __( 'Boxed Alignment', 'unysonplus' ),
									'desc'    => __( 'Horizontal alignment of the boxed container.', 'unysonplus' ),
									'type'    => 'image-picker',
									'value'   => 'center',
									'choices' => $picker( [
										'left'   => 'align-left.svg',
										'center' => 'align-center.svg',
										'right'  => 'align-right.svg',
									] ),
								],
								'site_boxed_margin' => [
									'label' => __( 'Site Top/Bottom Margin', 'unysonplus' ),
									'desc'  => __( 'Spacing above and below the boxed container.', 'unysonplus' ),
									'type'  => 'unit-input',
									'units' => [ 'rem', 'px', 'em' ],
									'value' => [ 'value' => '2.5', 'unit' => 'rem' ],
									'min'   => 0,
								],
							],
							'framed' => [
								'site_frame_width' => [
									'label' => __( 'Frame Width', 'unysonplus' ),
									'desc'  => __( 'Thickness of the decorative border.', 'unysonplus' ),
									'type'  => 'unit-input',
									'units' => [ 'rem', 'px', 'em' ],
									'value' => [ 'value' => '1.25', 'unit' => 'rem' ],
									'min'   => 0,
								],
								'site_frame_color' => function_exists( 'sc_color_field_compact' )
									? sc_color_field_compact( [ 'label' => __( 'Frame Color', 'unysonplus' ), 'desc' => __( 'Color of the decorative border. Blank = the default dark frame.', 'unysonplus' ), 'kind' => 'bg' ] )
									: [ 'label' => __( 'Frame Color', 'unysonplus' ), 'type' => 'color-picker', 'value' => '#222222' ],
							],
						],
						'show_borders' => false,
					],
					'site_background' => [
						'label' => __( 'Site Background', 'unysonplus' ),
						'desc'  => __( 'Background behind all content (body). Background Pro: Color / Gradient / Image layers stack (color underneath, gradient over, image over). Video is not applied to the site-wide background.', 'unysonplus' ),
						'type'  => 'background-pro',
					],
					// Modern replacement for the retired tiling-PNG overlay: a reusable CSS/HTML pattern
					// (Theme Settings → Components → Background Patterns) drawn as a fixed, full-page layer
					// behind all content. The plugin's wp_footer render reads this same key. For a tiling
					// IMAGE, use Site Background → Image above (background-pro supports a repeating image).
					'site_background_pattern' => [
						'type'    => 'multi-picker',
						'label'   => __( 'Site Background Pattern', 'unysonplus' ),
						'desc'    => __( 'A reusable CSS/HTML pattern drawn as a fixed, full-page background behind all content. Add / edit patterns in Theme Settings → Components → Background Patterns. For a tiling image instead, use Site Background → Image above.', 'unysonplus' ),
						'popover' => true,
						'value'   => [ 'pattern' => 'none' ],
						'picker'  => [
							'pattern' => [
								'type'    => 'image-picker',
								'label'   => false,
								'choices' => function_exists( 'unysonplus_pattern_imagepicker_choices' ) ? unysonplus_pattern_imagepicker_choices() : [ 'none' => [ 'label' => __( 'None', 'unysonplus' ) ] ],
							],
						],
						'choices'      => [],
						'show_borders' => false,
					],
				],
			],

			/* ============ B. Spacing System ============ */
			'group_spacing' => [
				'type'    => 'group',
				'options' => [
					'layout_section_spacing' => [
						'label'   => __( 'Content Density', 'unysonplus' ),
						'desc'    => __( 'Global density for the theme\'s default vertical rhythm: content-area padding, post/page content spacing, the footer, and the fallback spacing of page-builder sections that have no Top/Bottom Spacing of their own. Does not override a section\'s own Top/Bottom Spacing. Compact = 0.75×, Cozy = 1× (default), Spacious = 1.5×.', 'unysonplus' ),
						'type'    => 'radio',
						'value'   => 'cozy',
						'choices' => [
							'compact'  => __( 'Compact', 'unysonplus' ),
							'cozy'     => __( 'Cozy (default)', 'unysonplus' ),
							'spacious' => __( 'Spacious', 'unysonplus' ),
						],
					],
					'layout_container_gutter' => [
						'label' => __( 'Container Gutter', 'unysonplus' ),
						'desc'  => __( 'Horizontal breathing room on the sides of content (the Bootstrap container gutter). Leave blank for the responsive default (~12px on phones up to ~24px on desktop).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],

					/* Container Width — ONE responsive control (Phone / Tablet / Desktop tabs) wrapping
					   a unit-input, so each device has the number + unit dropdown. Mobile-first: base
					   (Phone) applies at all widths, a blank device inherits the smaller. Prefilled
					   defaults below; emitted as --container-max-* by theme-vars.php → .fw-container /
					   .container in style.css. Full-Width containers ignore it. */
					'layout_container_width' => [
						'type'  => 'responsive',
						'label' => __( 'Container Width', 'unysonplus' ),
						'desc'  => __( 'Max width of the boxed content container per device — use the Phone / Tablet / Desktop tabs. 100% = full width. Full-Width containers ignore this.', 'unysonplus' ),
						'value' => [
							'base' => [ 'value' => '100',  'unit' => '%' ],
							'md'   => [ 'value' => '720',  'unit' => 'px' ],
							'lg'   => [ 'value' => '1170', 'unit' => 'px' ],
						],
						'inner' => [ 'type' => 'unit-input', 'units' => [ 'px', 'rem', 'em', '%' ], 'min' => 0 ],
					],
					'layout_roundness' => [
						'label'   => __( 'Border Roundness', 'unysonplus' ),
						'desc'    => __( 'Global corner rounding applied to cards, buttons, inputs and images (drives the --radius tokens).', 'unysonplus' ),
						'type'    => 'radio',
						'value'   => 'subtle',
						'choices' => [
							'sharp'   => __( 'Sharp (square)', 'unysonplus' ),
							'subtle'  => __( 'Subtle (default)', 'unysonplus' ),
							'rounded' => __( 'Rounded', 'unysonplus' ),
							'soft'    => __( 'Soft', 'unysonplus' ),
						],
					],
					'layout_prose_width' => [
						'label' => __( 'Reading Width (no sidebar)', 'unysonplus' ),
						'desc'  => __( 'Caps the content width of single posts/pages that have no sidebar, for comfortable reading. Leave blank for none.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],
				],
			],

		],
	],
];
