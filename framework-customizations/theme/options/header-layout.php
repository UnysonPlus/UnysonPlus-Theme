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
			'group_header_layout' => [
				'type'    => 'group',
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

					/* --- Off-Canvas / drawer panel ---
					   The slide-in panel is SHARED: it is the whole menu in Off-Canvas mode
					   and the mobile drawer in every other mode, so these two options serve
					   both. Content uses the same element list as a header column (Menu,
					   Snippet, CTA Button, Social Icons, Custom HTML, …) — empty keeps the
					   historical default (the Off-Canvas menu), so existing sites don't
					   change. Rendered by unysonplus_render_drawer_content() /
					   unysonplus_render_menu_toggle() in inc/includes/header-builder.php. */
					'offcanvas_content' => array_merge(
						unysonplus_header_column( __( 'Off-Canvas Content', 'unysonplus' ), [] ),
						[
							'desc' => __( 'What the off-canvas / mobile drawer panel shows. Add any header element — <b>Menu</b>, <b>Snippet</b> (any shortcode / custom markup), CTA Button, Social Icons, Custom HTML — and order them freely. <b>Leave empty for the default</b>: the Off-Canvas menu (falling back to Primary).', 'unysonplus' ),
						]
					),
					/* Trigger + Close icons on one inline row (multi-inline). Open = the
					   toggle button that reveals the panel; Close = the X inside it.
					   Saved shape: [ 'open' => <icon>, 'close' => <icon> ] — a legacy
					   scalar (the old single icon-v2 value) is still honored as 'open'
					   by unysonplus_render_menu_toggle(). Empty 'open' → hamburger bars;
					   empty 'close' → the classic &times;. */
					'offcanvas_trigger_icon' => [
						'label' => __( 'Trigger & Close Icons', 'unysonplus' ),
						'type'  => 'multi-inline',
						'value' => [ 'open' => '', 'close' => '' ],
						'desc'  => __( '<b>Open</b> = the button that reveals the off-canvas / mobile drawer panel (default: hamburger bars). <b>Close</b> = the button inside the open panel (default: &times;). Both apply in every header mode. Leave either empty for its default.', 'unysonplus' ),
						'fw_multi_options' => [
							'open'  => [ 'type' => 'icon-v2', 'title' => __( 'Open', 'unysonplus' ) ],
							'close' => [ 'type' => 'icon-v2', 'title' => __( 'Close', 'unysonplus' ) ],
						],
					],

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

					'min_height' => [
						'label' => __( 'Main Header Height', 'unysonplus' ),
						'desc'  => __( 'Minimum height of the main header row.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '5', 'unit' => 'rem' ],
						'min'   => 0,
					],

					'mobile_min_height' => [
						'label' => __( 'Mobile Header Height', 'unysonplus' ),
						'desc'  => __( 'Main header height on phones (below 768px). Leave empty to reuse the desktop height.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],

					'mobile_breakpoint' => [
						'label'   => __( 'Collapse to Mobile Menu At', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'lg',
						'choices' => [
							'lg' => __( 'Below 992px (tablet & phone)', 'unysonplus' ),
							'md' => __( 'Below 768px (phone only)', 'unysonplus' ),
						],
						'desc'    => __( 'Screen width below which the inline menu collapses to the hamburger drawer.', 'unysonplus' ),
					],

					/* --- Scroll behavior --- */
					'header_behavior' => [
						'label'   => __( 'Header Behavior', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'static',
						'choices' => [
							'static'              => __( 'Static (scrolls away with the page)', 'unysonplus' ),
							'sticky'              => __( 'Sticky (follows scroll)', 'unysonplus' ),
							'sticky-shrink'       => __( 'Sticky + Shrink on scroll', 'unysonplus' ),
							'hide-on-scroll'      => __( 'Sticky, hide on scroll down / reveal up', 'unysonplus' ),
							'transparent-overlay' => __( 'Transparent over the first section', 'unysonplus' ),
						],
						'desc'    => __( 'How the header behaves on scroll. This supersedes the old Sticky switch and General → Layout → Header Position Behavior. Per-page "Transparent" still overrides it for that page.', 'unysonplus' ),
					],
					'sticky_shrink_height' => [
						'label' => __( 'Shrunk Logo Height', 'unysonplus' ),
						'desc'  => __( 'Logo height once the header shrinks (Behavior = Sticky + Shrink). Leave empty for the default (40px).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],

					/* --- Appearance / chrome. The toggles apply on top of any mode/design;
					   tiny class-gated CSS in header-footer-builder.css — no conditional partial. */
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

					'header_border'        => $toggle_field( __( 'Header Border', 'unysonplus' ), __( 'A hairline rule under the header.', 'unysonplus' ) ),
					'header_shadow'        => $toggle_field( __( 'Header Shadow', 'unysonplus' ), __( 'A soft drop shadow that lifts the header off the page.', 'unysonplus' ) ),
					'header_glass'         => $toggle_field( __( 'Translucent / Glass', 'unysonplus' ), __( 'A frosted, semi-transparent header background (backdrop blur).', 'unysonplus' ) ),
					'header_uppercase_nav' => $toggle_field( __( 'Uppercase Navigation', 'unysonplus' ), __( 'Uppercase the primary menu links with a touch of letter-spacing.', 'unysonplus' ) ),

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

						/* --- Mobile (below 768px). Per-element device visibility lives on
						   each element; these are quick header-wide mobile controls. --- */
						'mobile_drawer_side' => [
							'label'   => __( 'Mobile Menu Side', 'unysonplus' ),
							'desc'    => __( 'Which side the mobile navigation drawer slides in from.', 'unysonplus' ),
							'type'    => 'select',
							'value'   => 'right',
							'choices' => [
								'right' => __( 'Right', 'unysonplus' ),
								'left'  => __( 'Left', 'unysonplus' ),
							],
						],
						'nav_scrollspy' => [
							'label'        => __( 'Scroll Spy', 'unysonplus' ),
							'desc'         => __( 'One-page navigation: highlight the menu item for the section currently in view, and smooth-scroll to it on click (landing below the sticky header). Works in every header mode — Top, Vertical and the Overlay / Off-canvas drawers. Give each Section a CSS ID (its Advanced tab) and point menu items (Custom Links) at #that-id. Leave off for normal multi-page sites.', 'unysonplus' ),
							'type'         => 'switch',
							'value'        => 'no',
							'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
							'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
						],
						'mobile_hide_topbar'    => $toggle_field( __( 'Hide Top Bar on Mobile', 'unysonplus' ), __( 'Hide the entire Top Bar row on small screens (below 768px).', 'unysonplus' ) ),
						'mobile_hide_bottombar' => $toggle_field( __( 'Hide Bottom Bar on Mobile', 'unysonplus' ), __( 'Hide the entire Bottom Bar row on small screens (below 768px).', 'unysonplus' ) ),

				],
			],
		],
	],
];
