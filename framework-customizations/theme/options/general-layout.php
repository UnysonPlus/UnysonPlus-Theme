<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * General → Layout sub-tab.
 *
 * Site-wide layout controls — width mode, alternate header layouts
 * (vertical / off-canvas / overlay), sidebar position, spacing scale,
 * and UX polish (preloader / smooth scroll / scroll progress).
 *
 * All options stored under the `general_layout` multi key. The four sections
 * are wrapped in `group` containers purely for editor organization — groups
 * flatten on save, so every leaf key stays at the top level of the
 * `general_layout` value and `unysonplus_layout_get()` reads are unchanged.
 *
 * Image-picker previews live at assets/svg/layout/*.svg.
 */

$uri = get_template_directory_uri();
$svg = $uri . '/assets/svg/layout';
$pat = $uri . '/images/patterns';

/* Build the image-picker `choices` array for any pair of {value => svg-filename}. */
$picker = function ( array $pairs, $height_small = 70, $height_large = 140 ) use ( $svg ) {
	$out = [];
	foreach ( $pairs as $value => $file ) {
		$out[ $value ] = [
			'small' => [ 'height' => $height_small, 'src' => $svg . '/' . $file ],
			'large' => [ 'height' => $height_large, 'src' => $svg . '/' . $file ],
		];
	}
	return $out;
};

/* Same shape but accepts arbitrary src paths (used for the bg pattern picker
 * which reuses the existing images/patterns/ PNGs). */
$picker_paths = function ( array $pairs, $height_small = 70, $height_large = 140 ) {
	$out = [];
	foreach ( $pairs as $value => $src ) {
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
					'site_width_mode' => [
						'label'   => __( 'Site Width Mode', 'unysonplus' ),
						'desc'    => __( 'How the whole site container is laid out. Full-width spans edge to edge; Boxed centers a fixed-width column; Framed adds a colored border around the entire viewport.', 'unysonplus' ),
						'type'    => 'image-picker',
						'value'   => 'full',
						'choices' => $picker( [
							'full'   => 'width-full.svg',
							'boxed'  => 'width-boxed.svg',
							'framed' => 'width-framed.svg',
						] ),
					],
					'site_boxed_width' => [
						'label' => __( 'Boxed Width', 'unysonplus' ),
						'desc'  => __( 'Maximum width of the boxed container (when Site Width Mode = Boxed). Recommended range: 1100–1600 px.', 'unysonplus' ),
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
						'desc'    => __( 'Horizontal alignment of the boxed container (when Site Width Mode = Boxed).', 'unysonplus' ),
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
						'desc'  => __( 'Spacing above and below the boxed container (when Site Width Mode = Boxed).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '2.5', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'site_frame_width' => [
						'label' => __( 'Frame Width', 'unysonplus' ),
						'desc'  => __( 'Thickness of the decorative border (when Site Width Mode = Framed).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '1.25', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'site_frame_color' => [
						'label' => __( 'Frame Color', 'unysonplus' ),
						'desc'  => __( 'Color of the decorative border (when Site Width Mode = Framed).', 'unysonplus' ),
						'type'  => 'color-picker',
						'value' => '#222222',
					],
					'site_background' => [
						'label' => __( 'Site Background', 'unysonplus' ),
						'desc'  => __( 'Background behind all content (body). Background Pro: Color / Gradient / Image layers stack (color underneath, gradient over, image over). Video is not applied to the site-wide background.', 'unysonplus' ),
						'type'  => 'background-pro',
					],
					'site_bg_pattern' => [
						'label'   => __( 'Background Pattern Overlay', 'unysonplus' ),
						'desc'    => __( 'Repeating pattern layered above the site background color/image. Pick a preset, or use "Custom" to upload your own tiling image.', 'unysonplus' ),
						'type'    => 'background-image',
						'value'   => 'none',
						// background-image choices: each is { icon (preview), css }.
						// The "Custom" upload tile is provided by the option type itself.
						'choices' => ( function () use ( $pat ) {
							$defs = [
								'none'            => [ 'no_pattern.jpg', null ],
								'dots'            => [ 'dots_pattern_preview.jpg', 'dots_pattern.png' ],
								'vertical_lines'  => [ 'vertical_lines_pattern_preview.jpg', 'vertical_lines_pattern.png' ],
								'noise'           => [ 'noise_pattern_preview.jpg', 'noise_pattern.png' ],
								'romb'            => [ 'romb_pattern_preview.jpg', 'romb_pattern.png' ],
								'square'          => [ 'square_pattern_preview.jpg', 'square_pattern.png' ],
								'waves'           => [ 'waves_pattern_preview.jpg', 'waves_pattern.png' ],
								'diagonal_top'    => [ 'diagonal_top_to_bottom_pattern_preview.jpg', 'diagonal_top_to_bottom_pattern.png' ],
								'diagonal_bottom' => [ 'diagonal_bottom_to_top_pattern_preview.jpg', 'diagonal_bottom_to_top_pattern.png' ],
							];
							$out = [];
							foreach ( $defs as $key => $files ) {
								list( $preview, $full ) = $files;
								$out[ $key ] = [
									'icon' => $pat . '/' . $preview,
									'css'  => ( null === $full )
										? [ 'background-image' => 'none' ]
										: [
											'background-image'  => 'url("' . $pat . '/' . $full . '")',
											'background-repeat' => 'repeat',
										],
								];
							}
							return $out;
						} )(),
					],
				],
			],

			/* ============ B. Header & Sidebar Layout ============ */
			'group_header_sidebar' => [
				'type'    => 'group',
				'options' => [
					'layout_header_mode' => [
						'label'   => __( 'Header Layout Mode', 'unysonplus' ),
						'desc'    => __( 'Top: standard horizontal header. Vertical Left/Right: fixed side rail with logo + menu. Off-Canvas Only: hamburger always visible, no top bar. Overlay Fullscreen: hamburger opens a fullscreen menu.', 'unysonplus' ),
						'type'    => 'image-picker',
						'value'   => 'top',
						'choices' => $picker( [
							'top'              => 'header-top.svg',
							'vertical-left'    => 'header-vertical-left.svg',
							'vertical-right'   => 'header-vertical-right.svg',
							'off-canvas-only'  => 'header-off-canvas.svg',
							'overlay'          => 'header-overlay.svg',
						] ),
					],
					'layout_vertical_width' => [
						'label' => __( 'Vertical Header Width', 'unysonplus' ),
						'desc'  => __( 'Width of the fixed side rail (when Header Layout Mode = Vertical Left/Right).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '16.25', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'layout_header_position' => [
						'label'   => __( 'Header Position Behavior', 'unysonplus' ),
						'desc'    => __( 'Static: header scrolls with content. Sticky: header sticks to viewport top on scroll. Transparent Overlay: header sits on top of the first section with no background.', 'unysonplus' ),
						'type'    => 'radio',
						'value'   => 'static',
						'choices' => [
							'static'                          => __( 'Static (scrolls with content)', 'unysonplus' ),
							'sticky'                          => __( 'Sticky (follows scroll)', 'unysonplus' ),
							'transparent-overlay-first-section' => __( 'Transparent Overlay (on first section)', 'unysonplus' ),
						],
					],
					'layout_sidebar_position' => [
						'label'   => __( 'Default Sidebar Position', 'unysonplus' ),
						'desc'    => __( 'Theme-wide default for where the sidebar renders on pages/posts. Individual posts/pages can override via their own meta options.', 'unysonplus' ),
						'type'    => 'image-picker',
						'value'   => 'right',
						'choices' => $picker( [
							'none'  => 'sidebar-none.svg',
							'left'  => 'sidebar-left.svg',
							'right' => 'sidebar-right.svg',
						] ),
					],
					'layout_sidebar_width' => [
						'label' => __( 'Sidebar Width', 'unysonplus' ),
						'desc'  => __( 'Width of the sidebar column (when Sidebar Position is Left or Right).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '18.75', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'layout_sidebar_gap' => [
						'label' => __( 'Content / Sidebar Gap', 'unysonplus' ),
						'desc'  => __( 'Horizontal gap between the content and the sidebar column.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '2.5', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'layout_sidebar_sticky' => [
						'label'        => __( 'Sticky Sidebar', 'unysonplus' ),
						'desc'         => __( 'Make the sidebar follow the page as it scrolls (desktop only).', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
					],
					'layout_mobile_breakpoint' => [
						'label' => __( 'Mobile Menu Breakpoint', 'unysonplus' ),
						'desc'  => __( 'Below this viewport width, vertical/overlay header modes collapse to a top + hamburger layout. Usually set in px.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '992', 'unit' => 'px' ],
						'min'   => 0,
					],
				],
			],

			/* ============ C. Spacing System ============ */
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
					'layout_container_max_width' => [
						'label' => __( 'Container Max Width', 'unysonplus' ),
						'desc'  => __( 'Overrides the Bootstrap container max-width.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '1320', 'unit' => 'px' ],
						'min'   => 0,
					],
					'layout_container_gutter' => [
						'label' => __( 'Container Gutter', 'unysonplus' ),
						'desc'  => __( 'Horizontal padding inside .container.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '1.5', 'unit' => 'rem' ],
						'min'   => 0,
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

			/* ============ D. UX Polish ============ */
			'group_ux' => [
				'type'    => 'group',
				'options' => [
					'layout_preloader_style' => [
						'label'   => __( 'Preloader', 'unysonplus' ),
						'desc'    => __( 'Full-screen splash shown until the page finishes loading.', 'unysonplus' ),
						'type'    => 'image-picker',
						'value'   => 'none',
						'choices' => $picker( [
							'none'    => 'preloader-none.svg',
							'spinner' => 'preloader-spinner.svg',
							'logo'    => 'preloader-logo.svg',
						] ),
					],
					'layout_preloader_bg_color' => [
						'label' => __( 'Preloader Background', 'unysonplus' ),
						'desc'  => __( 'Background color of the preloader splash (when Preloader != None).', 'unysonplus' ),
						'type'  => 'color-picker',
						'value' => '#ffffff',
					],
					'layout_smooth_scroll' => [
						'label'        => __( 'Smooth Scroll for Anchor Links', 'unysonplus' ),
						'desc'         => __( 'Enables CSS scroll-behavior: smooth for in-page anchor navigation.', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
					],
					'layout_scroll_progress' => [
						'label'        => __( 'Scroll Progress Bar', 'unysonplus' ),
						'desc'         => __( 'Thin gradient bar at the top of the viewport that fills as the user scrolls.', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
					],
					'layout_scroll_progress_color' => [
						'label' => __( 'Scroll Progress Bar Color', 'unysonplus' ),
						'desc'  => __( 'Color of the scroll progress bar (when enabled).', 'unysonplus' ),
						'type'  => 'color-picker',
						'value' => '#0d6efd',
					],
				],
			],

		],
	],
];
