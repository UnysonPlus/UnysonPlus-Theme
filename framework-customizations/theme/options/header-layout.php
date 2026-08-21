<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * HEADER → LAYOUT — the header "chrome": layout mode, container, heights,
 * mobile breakpoint, background and scroll behavior.
 *
 * The header's three rows were split into their own sibling sub-tabs (mirroring
 * the footer's Pre / Main / Post structure), each with its own storage key:
 *   - Top Bar     → `header_topbar`    (header-topbar.php)
 *   - Main Header → `header_main`      (header-main.php)
 *   - Bottom Bar  → `header_bottombar` (header-bottombar.php)
 * The top/bottom bars no longer have an Enable switch — like the footer, a row
 * renders whenever any of its columns has an element. The slot/preset system
 * reads all four ids (see unysonplus_preset_option_ids()); a one-time admin
 * migration (unysonplus_migrate_header_layout()) lifts the legacy single-blob
 * `header_layout` shape into the four keys, for both global settings and any
 * up_header preset post-meta.
 *
 * `header_mode` + `vertical_width` are read via unysonplus_header_layout_get().
 * SVG previews live at assets/svg/layout/*.svg.
 */

$uri      = get_template_directory_uri();
$svg      = $uri . '/assets/svg/layout';
$svg_path = get_template_directory() . '/assets/svg/layout';

/* Build the image-picker `choices` array for any pair of {value => svg-filename}.
   The visible caption is baked INTO each SVG (the image-picker plugin's show_label is
   off); an optional {value => label} map adds the same text as the <option> label for
   accessibility. Tiles are a touch taller now to fit the baked caption band.

   IMPORTANT: append a filemtime cache-buster to the file URL. These SVGs are plain
   file references (not data-URIs), so without it the browser serves an indefinitely
   cached older copy — which is why an edited SVG (e.g. adding the label band) doesn't
   show until a hard refresh. The ?v=<mtime> changes whenever the art changes. */
$picker = function ( array $pairs, array $labels = array(), $height_small = 80, $height_large = 156 ) use ( $svg, $svg_path ) {
	$out = [];
	foreach ( $pairs as $value => $file ) {
		$ver = @filemtime( $svg_path . '/' . $file ); // phpcs:ignore -- optional cache-buster
		$src = $svg . '/' . $file . ( $ver ? '?v=' . $ver : '' );
		$out[ $value ] = [
			'small' => [ 'height' => $height_small, 'src' => $src ],
			'large' => [ 'height' => $height_large, 'src' => $src ],
		];
		if ( isset( $labels[ $value ] ) ) {
			$out[ $value ]['label'] = $labels[ $value ];
		}
	}
	return $out;
};

/* The Vertical rail width — revealed only under the two Vertical modes (below).
   Defined once and reused in both choice reveals. */
$vertical_width_field = [
	'label' => __( 'Vertical Header Width', 'unysonplus' ),
	'desc'  => __( 'Width of the fixed side rail.', 'unysonplus' ),
	'type'  => 'unit-input',
	'units' => [ 'rem', 'px', 'em' ],
	'value' => [ 'value' => '16.25', 'unit' => 'rem' ],
	'min'   => 0,
];

/* Header-design thumbnails — a mini header preview (logo dot + menu bars) showing the
   design's container treatment. Inline data-URI SVG (no asset files → no file-URL cache
   issue). Each non-classic design ships a CSS partial (assets/css/header/designs/<slug>.css)
   loaded ONLY when active (see inc/static.php). 'classic' has no partial. */
$design_svg = function ( $variant, $label ) {
	$w = 120; $h = 54; $barH = 22; $by = 9; $cy = $by + $barH / 2;
	$bx = ( $variant === 'classic' ) ? 6 : 16;   // pill/card float inset; classic is flush
	$bw = $w - 2 * $bx;
	$rx = ( $variant === 'pill' ) ? 11 : ( ( $variant === 'card' ) ? 6 : 3 );
	$shadow = '';
	if ( $variant === 'pill' ) {
		$shadow = '<rect x="' . ( $bx + 1 ) . '" y="' . ( $by + 3 ) . '" width="' . $bw . '" height="' . $barH . '" rx="' . $rx . '" fill="#0f172a" opacity="0.14"/>';
	} elseif ( $variant === 'card' ) {
		$shadow = '<rect x="' . ( $bx + 2 ) . '" y="' . ( $by + 4 ) . '" width="' . $bw . '" height="' . $barH . '" rx="' . $rx . '" fill="#0f172a" opacity="0.20"/>';
	}
	$bar = '<rect x="' . $bx . '" y="' . $by . '" width="' . $bw . '" height="' . $barH . '" rx="' . $rx . '" fill="#ffffff" stroke="#dcdcde"/>';
	if ( $variant === 'centered' ) {
		$inner  = '<circle cx="' . ( $w / 2 ) . '" cy="' . $cy . '" r="4" fill="#2271b1"/>';
		$inner .= '<rect x="' . ( $bx + 8 ) . '" y="' . ( $cy - 1.5 ) . '" width="14" height="3" rx="1.5" fill="#9aa7b5"/>';
		$inner .= '<rect x="' . ( $bx + 26 ) . '" y="' . ( $cy - 1.5 ) . '" width="14" height="3" rx="1.5" fill="#9aa7b5"/>';
		$inner .= '<rect x="' . ( $w - $bx - 22 ) . '" y="' . ( $cy - 1.5 ) . '" width="14" height="3" rx="1.5" fill="#9aa7b5"/>';
		$inner .= '<rect x="' . ( $w - $bx - 40 ) . '" y="' . ( $cy - 1.5 ) . '" width="14" height="3" rx="1.5" fill="#9aa7b5"/>';
	} else {
		$inner = '<circle cx="' . ( $bx + 12 ) . '" cy="' . $cy . '" r="4" fill="#2271b1"/>';
		foreach ( array( 20, 40, 60 ) as $off ) {
			$inner .= '<rect x="' . ( $w - $bx - $off ) . '" y="' . ( $cy - 1.5 ) . '" width="16" height="3" rx="1.5" fill="#9aa7b5"/>';
		}
	}
	$text = '<text x="' . ( $w / 2 ) . '" y="' . ( $h - 4 ) . '" text-anchor="middle" font-family="-apple-system,Segoe UI,Roboto,sans-serif" font-size="10" fill="#50575e">' . $label . '</text>';
	$svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $w . ' ' . $h . '" width="' . $w . '" height="' . $h . '">' . $shadow . $bar . $inner . $text . '</svg>';
	return 'data:image/svg+xml,' . rawurlencode( $svg );
};
$design_choice = function ( $variant, $label ) use ( $design_svg ) {
	$uri = $design_svg( $variant, $label );
	return array(
		'small' => array( 'height' => 54,  'src' => $uri ),
		'large' => array( 'height' => 104, 'src' => $uri ),
	);
};
/* Header Design — an INLINE multi-picker (the Top mode's structural container
   treatment, housed in the Top reveal below). The picker tiles pick the design; each
   design reveals its OWN options (roundness / shadow / inset / spacing), which drive
   the design's static CSS partial via CSS custom properties (see theme-vars.php).
   Saved shape: [ 'design' => 'pill', 'pill' => [ …sub-options… ] ]. Inline-picker
   rules: label/desc live on the picker sub-option; the top level is false. */
$design_select = function ( $label, array $choices, $default ) {
	return [ 'type' => 'select', 'label' => $label, 'value' => $default, 'choices' => $choices ];
};
$design_field = [
	'type'         => 'multi-picker',
	'label'        => false,
	'desc'         => false,
	'show_borders' => false,
	'value'        => [ 'design' => 'classic' ],
	'picker'       => [
		'design' => [
			'type'    => 'image-picker',
			'label'   => __( 'Header Design', 'unysonplus' ),
			'desc'    => __( 'Structural treatment for the header. Classic is a standard full-width bar; Floating Pill and Elevated Card float the nav row inside the header; Centered stacks a centered logo above centered navigation (for a nav-logo-nav split, keep Classic and place the logo in the center column with menus in the left/right columns). Each design reveals its own options below; only the chosen design\'s CSS is loaded.', 'unysonplus' ),
			'choices' => [
				'classic'  => $design_choice( 'classic',  __( 'Classic', 'unysonplus' ) ),
				'pill'     => $design_choice( 'pill',     __( 'Floating Pill', 'unysonplus' ) ),
				'card'     => $design_choice( 'card',     __( 'Elevated Card', 'unysonplus' ) ),
				'centered' => $design_choice( 'centered', __( 'Centered', 'unysonplus' ) ),
			],
		],
	],
	'choices' => [
		'pill' => [
			'pill_radius' => $design_select( __( 'Roundness', 'unysonplus' ), [ 'full' => __( 'Full (pill)', 'unysonplus' ), 'large' => __( 'Large', 'unysonplus' ), 'medium' => __( 'Medium', 'unysonplus' ) ], 'full' ),
			'pill_inset'  => $design_select( __( 'Side Inset', 'unysonplus' ), [ 'none' => __( 'None', 'unysonplus' ), 'small' => __( 'Small', 'unysonplus' ), 'large' => __( 'Large', 'unysonplus' ) ], 'none' ),
			'pill_shadow' => $design_select( __( 'Shadow', 'unysonplus' ), [ 'soft' => __( 'Soft', 'unysonplus' ), 'medium' => __( 'Medium', 'unysonplus' ), 'strong' => __( 'Strong', 'unysonplus' ) ], 'medium' ),
		],
		'card' => [
			'card_radius' => $design_select( __( 'Corner Radius', 'unysonplus' ), [ 'small' => __( 'Small', 'unysonplus' ), 'medium' => __( 'Medium', 'unysonplus' ), 'large' => __( 'Large', 'unysonplus' ) ], 'medium' ),
			'card_shadow' => $design_select( __( 'Shadow', 'unysonplus' ), [ 'soft' => __( 'Soft', 'unysonplus' ), 'medium' => __( 'Medium', 'unysonplus' ), 'strong' => __( 'Strong', 'unysonplus' ) ], 'medium' ),
		],
		'centered' => [
			'centered_gap' => $design_select( __( 'Spacing', 'unysonplus' ), [ 'tight' => __( 'Tight', 'unysonplus' ), 'normal' => __( 'Normal', 'unysonplus' ), 'roomy' => __( 'Roomy', 'unysonplus' ) ], 'normal' ),
		],
	],
];

/* Universal chrome toggles — apply on top of ANY design/mode. Tiny class-gated CSS
   lives in the always-loaded header-footer-builder.css (no conditional partial). */
$toggle_field = function ( $label, $desc ) {
	return [
		'label'        => $label,
		'desc'         => $desc,
		'type'         => 'switch',
		'value'        => 'no',
		'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
		'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
	];
};

/* Overlay Fullscreen — style variant, housed in the Overlay reveal below.
   Read via unysonplus_header_layout_get( 'overlay_style' ) (registered as a
   reveal-housed key in inc/includes/layout.php). */
// Popover image multi-picker (matches the Animation Engine pickers). POPOVER
// rule: the visible label/desc live on the TOP LEVEL; the picker sub-option is
// label:false. Saved shape: [ 'style' => 'panel'|'radial' ] — read via
// unysonplus_header_layout_get( 'overlay_style' ), which unwraps the picker id.
$overlay_corner_field = [
	'label'   => __( 'Grow From', 'unysonplus' ),
	'desc'    => __( 'Which viewport corner the concentric rings expand from.', 'unysonplus' ),
	'type'    => 'select',
	'value'   => 'tr',
	'choices' => [
		'tr' => __( 'Top Right', 'unysonplus' ),
		'tl' => __( 'Top Left', 'unysonplus' ),
		'br' => __( 'Bottom Right', 'unysonplus' ),
		'bl' => __( 'Bottom Left', 'unysonplus' ),
	],
];

// Concentric Color Mode — how the rings colour themselves FROM the Overlay
// Background colour. Read in the router → drawer class primary-navigation-drawer--cc-*.
$overlay_color_mode_field = [
	'label'   => __( 'Color Mode', 'unysonplus' ),
	'desc'    => __( 'How the overlay is coloured from the Overlay Background colour. On Concentric it recolours the rings; on Panel & Radial it spreads the palette across the menu labels. Shade: single colour. Tint: lightened. Aurora: gentle hue drift. Rainbow: full hue spectrum. Mono: greyscale. Duotone: blend into a second colour. Alternating: striped / dimmed. Glass: translucent, frosted.', 'unysonplus' ),
	'type'    => 'select',
	'value'   => 'shade',
	'choices' => [
		'shade'       => __( 'Shade', 'unysonplus' ),
		'tint'        => __( 'Tint', 'unysonplus' ),
		'aurora'      => __( 'Aurora', 'unysonplus' ),
		'rainbow'     => __( 'Rainbow', 'unysonplus' ),
		'mono'        => __( 'Mono', 'unysonplus' ),
		'duotone'     => __( 'Duotone', 'unysonplus' ),
		'alternating' => __( 'Alternating', 'unysonplus' ),
		'glass'       => __( 'Glass', 'unysonplus' ),
	],
];

// Duotone Second Color — the inner-ring colour the Duotone mode blends toward (the
// outer rings stay the Overlay Background colour). Only used when Color Mode = Duotone.
// Preset-linked color control per house style (falls back to a plain picker if the
// shortcodes helper isn't loaded). Resolved to --cc-duotone-color by theme-vars.php.
$overlay_duotone_color_field = function_exists( 'sc_color_field_compact' )
	? sc_color_field_compact( [ 'label' => __( 'Duotone Second Color', 'unysonplus' ), 'desc' => __( 'Inner-ring colour for the Duotone color mode.', 'unysonplus' ), 'kind' => 'bg' ] )
	: [ 'label' => __( 'Duotone Second Color', 'unysonplus' ), 'desc' => __( 'Inner-ring colour for the Duotone color mode.', 'unysonplus' ), 'type' => 'color-picker', 'value' => '#ec4899' ];

// Concentric Background Opacity — lets the page show THROUGH the rings (100 = solid,
// current look). Because the discs stack, the effect is strongest at the outer edge
// and densest at the centre. Emitted as --cc-bg-opacity by theme-vars.php; labels
// stay fully opaque. Read via the slider (0-100), converted to 0-1 in CSS.
$overlay_bg_opacity_field = [
	'label'      => __( 'Background Opacity', 'unysonplus' ),
	'desc'       => __( 'How opaque the rings are. Lower values let the page behind show through (100 = solid). Labels stay fully visible.', 'unysonplus' ),
	'type'       => 'slider',
	'value'      => 100,
	'properties' => [ 'min' => 20, 'max' => 100, 'step' => 5 ],
];

// Radial disc fill — a Background Pro (Video disabled) for the circular disc the
// Radial style wraps the menu around. Consumed as --radial-disc-color / -image by
// theme-vars.php. (Concentric needs NO circle fill of its own: its rings cover the
// whole screen, so they use the Overlay Background below.)
$radial_disc_bg_field = [
	'label'   => __( 'Circle Background', 'unysonplus' ),
	'desc'    => __( 'Fill for the radial disc — colour, gradient or image. Leave empty for the theme primary colour.', 'unysonplus' ),
	'type'    => 'background-pro',
	'disable' => [ 'video' ],
];

$overlay_style_field = [
	'type'         => 'multi-picker',
	'popover'      => true,
	'label'        => __( 'Overlay Style', 'unysonplus' ),
	'desc'         => __( 'Panel: the fullscreen menu shows as a plain centered list. Radial: the menu items wrap around a bold circular disc with the logo at its center. Concentric: nested filled rings expand from a corner, one menu item per ring.', 'unysonplus' ),
	'show_borders' => false,
	'value'        => [ 'style' => 'panel' ],
	'picker'       => [
		'style' => [
			'type'    => 'image-picker',
			'label'   => false,
			'value'   => 'panel',
			'choices' => $picker( [
				'panel'      => 'overlay-style-panel.svg',
				'radial'     => 'overlay-style-radial.svg',
				'concentric' => 'overlay-style-concentric.svg',
			], [
				'panel'      => __( 'Panel', 'unysonplus' ),
				'radial'     => __( 'Radial', 'unysonplus' ),
				'concentric' => __( 'Concentric', 'unysonplus' ),
			] ),
		],
	],
	'choices' => [
		'radial'     => [ 'radial_disc_bg' => $radial_disc_bg_field ],
		'concentric' => [ 'overlay_corner' => $overlay_corner_field ],
	],
];

// Vertical rail Side — popover picker (Left / Right) revealed by the merged
// Vertical mode. Read via unysonplus_header_vertical_side(). Reuses the existing
// vertical mode SVGs as the Left/Right tiles.
$side_field = [
	'type'         => 'multi-picker',
	'popover'      => true,
	'label'        => __( 'Rail Side', 'unysonplus' ),
	'desc'         => __( 'Which side of the screen the vertical rail sits on.', 'unysonplus' ),
	'show_borders' => false,
	'value'        => [ 'side' => 'left' ],
	'picker'       => [
		'side' => [
			'type'    => 'image-picker',
			'label'   => false,
			'value'   => 'left',
			'choices' => $picker( [
				'left'  => 'header-vertical-left.svg',
				'right' => 'header-vertical-right.svg',
			], [
				'left'  => __( 'Left', 'unysonplus' ),
				'right' => __( 'Right', 'unysonplus' ),
			] ),
		],
	],
	'choices' => [],
];

// Overlay Fullscreen background — a Background Pro (color / gradient / image; Video
// disabled — pointless behind a menu). Consumed as --overlay-bg-* by theme-vars.php.
// For Panel it's the backdrop; for Concentric it also fills the screen-covering
// rings (an image applies full-screen, not per ring).
$overlay_bg_field = [
	'label'   => __( 'Overlay Background', 'unysonplus' ),
	'desc'    => __( 'Background of the fullscreen overlay menu — color, gradient and/or image. For Concentric it fills the rings (image applies full-screen). Leave empty to use the style default.', 'unysonplus' ),
	'type'    => 'background-pro',
	'disable' => [ 'video' ],
];

$options = [

	// Quick-start Header Presets — the same preset-loader control the Menu &
	// Typography tabs use. Picking one sets the layout mode + design + behaviour +
	// chrome toggles below (then fine-tune). See settings-presets.php ('header_layout').
	'header_presets' => [
		'type'         => 'preset-loader',
		'label'        => __( 'Header Presets', 'unysonplus' ),
		'desc'         => __( 'Start from a whole-header look — bar, floating pill, centered, transparent, vertical rail or fullscreen overlay — then fine-tune below.', 'unysonplus' ),
		'preset_group' => 'header_layout',
	],

	'header_layout' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_layout_mode' => [
				'type'    => 'group',
				'title'   => __( 'Header Layout Mode', 'unysonplus' ),
				'options' => [

					/* Header Layout Mode as an INLINE multi-picker: the picker tile grid
					   picks the mode, and each mode reveals only its own relevant options
					   (e.g. Vertical Width only for the two Vertical modes). Inline rules:
					   label/desc live on the picker sub-option; the top level is false.
					   Saved shape: [ 'mode' => 'top', '<mode>' => [ …revealed… ] ]. */
					'header_mode' => [
						'type'         => 'multi-picker',
						'label'        => false,
						'desc'         => false,
						'show_borders' => false,
						'value'        => [ 'mode' => 'top' ],
						'picker'       => [
							'mode' => [
								'type'    => 'image-picker',
								'label'   => __( 'Header Layout Mode', 'unysonplus' ),
								'desc'    => __( 'Top: standard horizontal header. Vertical: fixed side rail with logo + menu (choose Left/Right below). Off-Canvas Only: hamburger always visible, no top bar. Overlay Fullscreen: hamburger opens a fullscreen menu.', 'unysonplus' ),
								'choices' => $picker( [
									'top'             => 'header-top.svg',
									'vertical'        => 'header-vertical.svg',
									'off-canvas-only' => 'header-off-canvas.svg',
									'overlay'         => 'header-overlay.svg',
								], [
									'top'             => __( 'Top', 'unysonplus' ),
									'vertical'        => __( 'Vertical Menu', 'unysonplus' ),
									'off-canvas-only' => __( 'Off-Canvas', 'unysonplus' ),
									'overlay'         => __( 'Overlay', 'unysonplus' ),
								] ),
							],
						],
						'choices' => [
							'top'      => [ 'header_design' => $design_field ],
							'vertical' => [ 'vertical_side' => $side_field, 'vertical_width' => $vertical_width_field ],
							'overlay'  => [ 'overlay_style' => $overlay_style_field, 'overlay_color_mode' => $overlay_color_mode_field, 'overlay_duotone_color' => $overlay_duotone_color_field, 'overlay_bg_opacity' => $overlay_bg_opacity_field, 'overlay_background' => $overlay_bg_field ],
						],
					],


				],
			],
			'group_structure' => [
				'type'    => 'group',
				'title'   => __( 'Structure & Dimensions', 'unysonplus' ),
				'options' => [
					/* --- Structure & dimensions --- */
					'container' => [
						'label'   => __( 'Container', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'container',
						'choices' => [
							'container'       => __( 'Fixed Width', 'unysonplus' ),
							'container-fluid' => __( 'Full Width', 'unysonplus' ),
						],
					],

					'container_width' => [
						'label' => __( 'Container Width', 'unysonplus' ),
						'desc'  => __( 'CONTENT width of the header bar when Container = Fixed Width. Leave empty to align with the site-wide Container Width (General → Layout). Like that setting, this is the content width — the gutter sits outside it — so a header narrower than the body (a common source pattern) can be matched exactly without touching the global.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],

					'min_height' => [
						'label' => __( 'Main Header Height', 'unysonplus' ),
						'desc'  => __( 'Minimum height of the main header row.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '5', 'unit' => 'rem' ],
						'min'   => 0,
					],


				],
			],
			'group_behavior' => [
				'type'    => 'group',
				'title'   => __( 'Position & Motion', 'unysonplus' ),
				'options' => [
					/* --- Behavior: POSITION + motion. The two-state model (see the Header Layout
					   doc): position is orthogonal to the At-top / On-scroll appearance below, so any
					   combination (e.g. transparent overlay + shrink on scroll) is possible. --- */
					'header_position' => [
						'label'   => __( 'Header Position', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'static',
						'choices' => [
							'static'  => __( 'Static (scrolls away with the page)', 'unysonplus' ),
							'sticky'  => __( 'Sticky (pins to the top on scroll)', 'unysonplus' ),
							'overlay' => __( 'Transparent overlay (sits over the first section, then pins)', 'unysonplus' ),
						],
						'desc'    => __( 'How the header is positioned. Shrink / hide / a different look on scroll are separate options below, so any combination is possible. Per-page "Transparent" still overrides this for that page.', 'unysonplus' ),
					],
					'header_hide_on_scroll' => $toggle_field( __( 'Hide on scroll down', 'unysonplus' ), __( 'Slide the header up out of view when scrolling down, reveal on scroll up (Sticky / Overlay only).', 'unysonplus' ) ),

				],
			],
			'group_attop' => [
				'type'    => 'group',
				'title'   => __( 'Appearance — At Top', 'unysonplus' ),
				'options' => [
					/* --- Appearance — AT TOP (the resting look). Composes with any position/design;
					   tiny class-gated CSS in header-footer-builder.css. --- */
					// Main Header Background — preset-linked colour (house style): a preset
					// dropdown tied to Theme Settings → Colors PLUS a custom picker.
					// Guarded so it falls back to a plain picker if the shortcodes helper
					// isn't loaded. Empty = transparent (shows the page behind); a set
					// colour overrides. Resolved to --header-bg by theme-vars.php (which
					// tolerates the legacy rgba-string shape from older saves).
					'bg_color' => function_exists( 'sc_color_field_compact' )
						? sc_color_field_compact( [
							'label' => __( 'Main Header Background', 'unysonplus' ),
							'desc'  => __( 'Leave empty for a transparent header (the page shows behind). Pick a preset or a custom colour to fill it.', 'unysonplus' ),
							'kind'  => 'bg',
						] )
						: [
							'label' => __( 'Main Header Background', 'unysonplus' ),
							'desc'  => __( 'Leave empty for a transparent header. Set a color to fill it.', 'unysonplus' ),
							'type'  => 'rgba-color-picker',
							'value' => '',
						],

					'header_glass'         => $toggle_field( __( 'Translucent / Glass', 'unysonplus' ), __( 'A frosted, semi-transparent header background (backdrop blur) at rest.', 'unysonplus' ) ),
					'header_border'        => $toggle_field( __( 'Header Border', 'unysonplus' ), __( 'A hairline rule under the header.', 'unysonplus' ) ),
					'header_shadow'        => $toggle_field( __( 'Header Shadow', 'unysonplus' ), __( 'A soft drop shadow that lifts the header off the page.', 'unysonplus' ) ),
					'header_uppercase_nav' => $toggle_field( __( 'Uppercase Navigation', 'unysonplus' ), __( 'Uppercase the primary menu links with a touch of letter-spacing.', 'unysonplus' ) ),

				],
			],
			'group_onscroll' => [
				'type'    => 'group',
				'title'   => __( 'Appearance — On Scroll', 'unysonplus' ),
				'options' => [
					/* --- Appearance — ON SCROLL. Turn the master switch on to give the header a
					   DIFFERENT look once it sticks; leave it off and the scrolled header keeps the
					   At-top look. Every field composes independently (glass-on-scroll-only, shrink,
					   a solid scrolled bar, …). --- */
					'header_scroll_change' => $toggle_field( __( 'Change appearance on scroll', 'unysonplus' ), __( 'When on, the options below REPLACE the At-top look once the header sticks. When off, the header looks the same scrolled.', 'unysonplus' ) ),
					'scroll_bg_color' => function_exists( 'sc_color_field_compact' )
						? sc_color_field_compact( [
							'label' => __( 'Scrolled Background', 'unysonplus' ),
							'desc'  => __( 'Header fill once stuck (needs Change appearance on scroll). Empty = keep the At-top background.', 'unysonplus' ),
							'kind'  => 'bg',
						] )
						: [
							'label' => __( 'Scrolled Background', 'unysonplus' ),
							'desc'  => __( 'Header fill once stuck. Empty = keep the At-top background.', 'unysonplus' ),
							'type'  => 'rgba-color-picker',
							'value' => '',
						],
					'scroll_glass'  => $toggle_field( __( 'Glass on scroll', 'unysonplus' ), __( 'Frost the header once stuck — e.g. a clear header over the hero that frosts on scroll.', 'unysonplus' ) ),
					'scroll_border' => $toggle_field( __( 'Border on scroll', 'unysonplus' ), __( 'Add the hairline rule once stuck.', 'unysonplus' ) ),
					'scroll_shadow' => $toggle_field( __( 'Shadow on scroll', 'unysonplus' ), __( 'Add the drop shadow once stuck.', 'unysonplus' ) ),
					'scroll_shrink' => $toggle_field( __( 'Shrink logo on scroll', 'unysonplus' ), __( 'Tighten the header padding and shrink the logo once stuck.', 'unysonplus' ) ),
					'sticky_shrink_height' => [
						'label' => __( 'Shrunk Logo Height', 'unysonplus' ),
						'desc'  => __( 'Logo height once the header shrinks (Shrink logo on scroll). Leave empty for the default (40px).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],

				],
			],
			'group_rowalign' => [
				'type'    => 'group',
				'title'   => __( 'Row Alignment & Spacing', 'unysonplus' ),
				'options' => [
					/* --- Row alignment / element spacing (applies to all header rows). --- */
					'header_valign' => [
						'label'   => __( 'Vertical Alignment', 'unysonplus' ),
						'desc'    => __( 'How elements align vertically within each header row.', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'center',
						'choices' => [
							'top'    => __( 'Top', 'unysonplus' ),
							'center' => __( 'Center', 'unysonplus' ),
							'bottom' => __( 'Bottom', 'unysonplus' ),
						],
					],
					'header_element_gap' => [
						'label' => __( 'Element Gap', 'unysonplus' ),
						'desc'  => __( 'Space between elements within a header column (e.g. between the logo and menu). Leave empty for the default.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],


				],
			],
		],
	],

	/* Scroll Spy — one-page navigation (active-section highlight + smooth scroll). NOT
	   mobile-specific (works in every header mode), so it lives here, not in Mobile & Tablet.
	   A TOP-LEVEL key (this `group` is a leaf container that doesn't nest storage), read in
	   inc/includes/layout.php and emitted by the Site Converter. */
	'grp_nav' => [
		'type'    => 'group',
		'title'   => __( 'Navigation', 'unysonplus' ),
		'options' => [
			'nav_scrollspy' => [
				'label'        => __( 'Scroll Spy', 'unysonplus' ),
				'desc'         => __( 'One-page navigation: highlight the menu item for the section currently in view, and smooth-scroll to it on click (landing below the sticky header). Works in every header mode. Give each Section a CSS ID (its Advanced tab) and point menu items (Custom Links) at #that-id. Leave off for normal multi-page sites.', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
		],
	],
];
