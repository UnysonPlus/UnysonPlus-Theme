<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * HEADER → MEGA MENU — global styling for every Mega Menu panel.
 *
 * Only registered when the Mega Menu extension is active (see header-settings.php).
 * These are SITE-WIDE defaults; per-panel overrides still live in the menu item's
 * "Settings" modal (Appearance → Menus).
 *
 * Values fold into --mm-* CSS custom properties in inc/includes/theme-vars.php,
 * which the extension's baseline stylesheet (framework/extensions/megamenu/
 * static/css/frontend.css) consumes. Every var has a hard-coded fallback there,
 * so an unset field simply keeps the extension default. Stored under `mega_menu`.
 *
 * Colors use the palette-preset compact control (sc_color_field_compact) so they
 * track Theme Settings → General → Colors, matching header-menu.php.
 *
 * PHASE 1 covers the dropdown Panel (design / background / radius / padding /
 * column gap / dividers). Later phases add animation, behavior, heading & item
 * layout, and responsive controls to this same file.
 */

/* Palette-preset color control (kind 'bg' → bg-{slug} choices). Falls back to a
   raw color-picker if the shortcodes helper isn't loaded. Mirrors header-menu.php. */
$color_field = function ( $label, $desc, $kind, $fallback = '' ) {
	if ( function_exists( 'sc_color_field_compact' ) ) {
		return sc_color_field_compact( array( 'label' => $label, 'desc' => $desc, 'kind' => $kind ) );
	}
	return array( 'label' => $label, 'desc' => $desc, 'type' => 'color-picker', 'value' => $fallback );
};

/* Panel-design preview tiles — a mini dropdown panel showing the container
   treatment (shadow / border / top accent). Inline data-URI SVG (no asset files
   → no file-URL cache issue), caption baked in. Same visual language as the
   Header → Menu "Dropdown Design" tiles so the two read as a family. */
$panel_style_svg = function ( $variant, $label ) {
	$w = 120; $h = 76; $accent = '#2271b1'; $grey = '#c3c8cf';
	$px = 26; $py = 12; $pw = 68; $ph = 46; $rx = 6;

	$shadow = ''; $stroke = 'none'; $sw = 0; $topbar = '';
	switch ( $variant ) {
		case 'elevated':
			$shadow = '<rect x="' . ( $px + 2 ) . '" y="' . ( $py + 6 ) . '" width="' . $pw . '" height="' . $ph . '" rx="' . $rx . '" fill="#0f172a" opacity="0.24"/>';
			break;
		case 'bordered':
			$stroke = $grey; $sw = 1.5;
			break;
		case 'minimal':
			// Flat: faint stroke in the PREVIEW only so a borderless white panel stays visible on the tile.
			$stroke = '#eceef0'; $sw = 1;
			break;
		case 'top-accent':
			$shadow = '<rect x="' . ( $px + 1 ) . '" y="' . ( $py + 4 ) . '" width="' . $pw . '" height="' . $ph . '" rx="' . $rx . '" fill="#0f172a" opacity="0.10"/>';
			$topbar = '<rect x="' . $px . '" y="' . $py . '" width="' . $pw . '" height="4" rx="2" fill="' . $accent . '"/>';
			break;
		case 'classic':
		default:
			$shadow = '<rect x="' . ( $px + 1 ) . '" y="' . ( $py + 4 ) . '" width="' . $pw . '" height="' . $ph . '" rx="' . $rx . '" fill="#0f172a" opacity="0.12"/>';
			$stroke = '#e2e4e7'; $sw = 1;
			break;
	}

	$panel = '<rect x="' . $px . '" y="' . $py . '" width="' . $pw . '" height="' . $ph . '" rx="' . $rx . '" fill="#ffffff" stroke="' . $stroke . '" stroke-width="' . $sw . '"/>';
	// Two columns of item lines to read as a "mega" panel (vs. the single column of the Menu tile).
	$items = '';
	$iy = $py + 11;
	foreach ( array( 0, 1, 2 ) as $i ) {
		$items .= '<rect x="' . ( $px + 8 )  . '" y="' . ( $iy + $i * 11 ) . '" width="24" height="5" rx="2.5" fill="' . $grey . '"/>';
		$items .= '<rect x="' . ( $px + 40 ) . '" y="' . ( $iy + $i * 11 ) . '" width="24" height="5" rx="2.5" fill="' . $grey . '"/>';
	}
	$text = '<text x="' . ( $w / 2 ) . '" y="' . ( $h - 4 ) . '" text-anchor="middle" font-family="-apple-system,Segoe UI,Roboto,sans-serif" font-size="10" fill="#50575e">' . $label . '</text>';
	$svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $w . ' ' . $h . '" width="' . $w . '" height="' . $h . '">' . $shadow . $panel . $topbar . $items . $text . '</svg>';
	return 'data:image/svg+xml,' . rawurlencode( $svg );
};
$panel_style_choice = function ( $variant, $label ) use ( $panel_style_svg ) {
	$uri = $panel_style_svg( $variant, $label );
	return array(
		'small' => array( 'height' => 76,  'src' => $uri ),
		'large' => array( 'height' => 110, 'src' => $uri ),
	);
};

$options = [
	// Quick-start preset loader (applies to the whole `mega_menu` group).
	'mega_menu_presets' => [
		'type'         => 'preset-loader',
		'label'        => __( 'Mega Menu Presets', 'unysonplus' ),
		'desc'         => __( 'Start from a predefined look, then fine-tune the options below. Or upload a preset JSON you exported earlier.', 'unysonplus' ),
		'preset_group' => 'mega_menu',
	],
	'mega_menu' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [

			/* Dropdown panel container. */
			'group_mm_panel' => [
				'type'    => 'group',
				'options' => [
					'mm_panel_design' => [
						'type'    => 'image-picker',
						'label'   => __( 'Panel Design', 'unysonplus' ),
						'desc'    => __( 'Overall look of the mega menu dropdown panel. Classic is a soft-shadowed card; Elevated deepens the shadow; Bordered swaps the shadow for a hairline border; Minimal is flat (no shadow or border); Top Accent adds a colored bar across the top (the site primary color).', 'unysonplus' ),
						'value'   => 'classic',
						'choices' => [
							'classic'    => $panel_style_choice( 'classic',    __( 'Classic', 'unysonplus' ) ),
							'elevated'   => $panel_style_choice( 'elevated',   __( 'Elevated', 'unysonplus' ) ),
							'bordered'   => $panel_style_choice( 'bordered',   __( 'Bordered', 'unysonplus' ) ),
							'minimal'    => $panel_style_choice( 'minimal',    __( 'Minimal', 'unysonplus' ) ),
							'top-accent' => $panel_style_choice( 'top-accent', __( 'Top Accent', 'unysonplus' ) ),
						],
					],
					'mm_panel_bg' => $color_field(
						__( 'Panel Background', 'unysonplus' ),
						__( 'Background color of the mega menu dropdown panel. Leave empty for white.', 'unysonplus' ),
						'bg'
					),
					'mm_panel_radius' => [
						'label' => __( 'Panel Corner Radius', 'unysonplus' ),
						'desc'  => __( 'Corner rounding of the dropdown panel. Leave empty for square corners.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],
					'mm_panel_padding' => [
						'label' => __( 'Panel Padding', 'unysonplus' ),
						'desc'  => __( 'Inner spacing of the dropdown panel. Leave empty for the default (16px).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],
					'mm_panel_font_size' => [
						'label' => __( 'Base Font Size', 'unysonplus' ),
						'desc'  => __( 'Base text size for the whole panel — item links, subtitles, descriptions and rich content all scale from this. Leave empty for the default (~15px). Heading and item sizes below can still override.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'mm_full_style' => [
						'type'    => 'select',
						'label'   => __( 'Full-width Panel Style', 'unysonplus' ),
						'desc'    => __( 'How a FULL-WIDTH mega panel fills the screen. "Edge to edge" spans the whole viewport (with the content centered inside). "Boxed" makes the panel itself a centered card with margins on the sides — nicer on large monitors.', 'unysonplus' ),
						'value'   => 'fullbleed',
						'choices' => [
							'fullbleed' => __( 'Edge to edge', 'unysonplus' ),
							'boxed'     => __( 'Boxed (centered card)', 'unysonplus' ),
						],
					],
					'mm_panel_max_width' => [
						'label' => __( 'Full-width / Boxed Width', 'unysonplus' ),
						'desc'  => __( 'Maximum width of a full-width mega panel: in "Edge to edge" it caps and centers the columns; in "Boxed" it is the width of the centered card. Leave empty for the default (1400px).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', '%' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],
					'mm_col_gap' => [
						'label' => __( 'Column Gap', 'unysonplus' ),
						'desc'  => __( 'Horizontal space between columns inside a row. Leave empty for the default (24px).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],
					'mm_col_dividers' => [
						'label' => __( 'Column Dividers', 'unysonplus' ),
						'desc'  => __( 'Show a thin vertical line between columns.', 'unysonplus' ),
						'type'  => 'switch',
						'value' => false,
					],
				],
			],

			/* Column headings — the column's own (first) link, shown above its items. */
			'group_mm_heading' => [
				'type'    => 'group',
				'options' => [
					'mm_heading_style' => [
						'type'    => 'select',
						'label'   => __( 'Heading Style', 'unysonplus' ),
						'desc'    => __( 'Treatment for each column heading. Underline adds a subtle bottom rule; Accent adds a colored underline (site primary color); Uppercase sets small-caps letter-spacing.', 'unysonplus' ),
						'value'   => 'none',
						'choices' => [
							'none'      => __( 'None (plain bold)', 'unysonplus' ),
							'underline' => __( 'Underline', 'unysonplus' ),
							'accent'    => __( 'Accent Underline', 'unysonplus' ),
							'uppercase' => __( 'Uppercase', 'unysonplus' ),
						],
					],
					'mm_heading_color' => $color_field(
						__( 'Heading Color', 'unysonplus' ),
						__( 'Color of the column headings. Leave empty for the theme default.', 'unysonplus' ),
						'text'
					),
					'mm_heading_size' => [
						'label' => __( 'Heading Font Size', 'unysonplus' ),
						'desc'  => __( 'Font size of the column headings. Leave empty for the theme default.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'mm_heading_weight' => [
						'type'    => 'select',
						'label'   => __( 'Heading Weight', 'unysonplus' ),
						'value'   => '',
						'choices' => [
							''    => __( 'Default', 'unysonplus' ),
							'400' => __( 'Normal (400)', 'unysonplus' ),
							'500' => __( 'Medium (500)', 'unysonplus' ),
							'600' => __( 'Semibold (600)', 'unysonplus' ),
							'700' => __( 'Bold (700)', 'unysonplus' ),
							'800' => __( 'Extrabold (800)', 'unysonplus' ),
						],
					],
				],
			],

			/* Dropdown items (the links + descriptions inside each column). */
			'group_mm_items' => [
				'type'    => 'group',
				'options' => [
					'mm_item_color' => $color_field(
						__( 'Item Link Color', 'unysonplus' ),
						__( 'Color of the links inside columns. Leave empty for the theme default.', 'unysonplus' ),
						'text'
					),
					'mm_item_hover_color' => $color_field(
						__( 'Item Hover / Active Color', 'unysonplus' ),
						__( 'Color of a column link on hover and for the current page. Leave empty for the theme default.', 'unysonplus' ),
						'text'
					),
					'mm_item_size' => [
						'label' => __( 'Item Font Size', 'unysonplus' ),
						'desc'  => __( 'Font size of the column links. Leave empty for the theme default.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'mm_item_gap' => [
						'label' => __( 'Item Spacing', 'unysonplus' ),
						'desc'  => __( 'Extra vertical space between items in a column. Leave empty for none.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],
					'mm_desc_color' => $color_field(
						__( 'Description Color', 'unysonplus' ),
						__( 'Color of the secondary description text shown beneath an item. Leave empty for the theme default.', 'unysonplus' ),
						'text'
					),
					'mm_desc_size' => [
						'label' => __( 'Description Font Size', 'unysonplus' ),
						'desc'  => __( 'Font size of item description text. Leave empty for the default (0.85em).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'em', 'rem', 'px' ],
						'value' => [ 'value' => '', 'unit' => 'em' ],
						'min'   => 0,
					],
					'mm_icon_size' => [
						'label' => __( 'Icon Size', 'unysonplus' ),
						'desc'  => __( 'Size of item icons. Leave empty to match the link text.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'em', 'rem', 'px' ],
						'value' => [ 'value' => '', 'unit' => 'em' ],
						'min'   => 0,
					],
					'mm_icon_color' => $color_field(
						__( 'Icon Color', 'unysonplus' ),
						__( 'Color of item icons. Leave empty to match the link color.', 'unysonplus' ),
						'text'
					),
				],
			],

			/* Animation & open behavior. */
			'group_mm_behavior' => [
				'type'    => 'group',
				'options' => [
					'mm_animation' => [
						'type'    => 'select',
						'label'   => __( 'Open Animation', 'unysonplus' ),
						'desc'    => __( 'How the panel appears. Fade cross-fades in place; Slide Down / Up glides in from above / below; Zoom scales up from the top.', 'unysonplus' ),
						'value'   => 'slide-up',
						'choices' => [
							'fade'       => __( 'Fade', 'unysonplus' ),
							'slide-down' => __( 'Slide Down', 'unysonplus' ),
							'slide-up'   => __( 'Slide Up', 'unysonplus' ),
							'zoom'       => __( 'Zoom', 'unysonplus' ),
							'none'       => __( 'None (instant)', 'unysonplus' ),
						],
					],
					'mm_anim_speed' => [
						'label' => __( 'Animation Speed', 'unysonplus' ),
						'desc'  => __( 'Duration of the open/close transition. Leave empty for the default (180ms).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'ms', 's' ],
						'value' => [ 'value' => '', 'unit' => 'ms' ],
						'min'   => 0,
					],
					'mm_open_on' => [
						'type'    => 'select',
						'label'   => __( 'Open On', 'unysonplus' ),
						'desc'    => __( 'Hover opens the panel when the pointer is over the menu item (with keyboard focus support). Click requires a click/tap on the item to toggle the panel — better when the top-level item is not itself a real page.', 'unysonplus' ),
						'value'   => 'hover',
						'choices' => [
							'hover' => __( 'Hover', 'unysonplus' ),
							'click' => __( 'Click', 'unysonplus' ),
						],
					],
					'mm_hover_delay' => [
						'label'   => __( 'Hover Open Delay', 'unysonplus' ),
						'desc'    => __( 'Small delay before the panel opens on hover, so brushing past a menu item does not flash the panel. Leave empty for none. Only applies in Hover mode.', 'unysonplus' ),
						'type'    => 'unit-input',
						'units'   => [ 'ms', 's' ],
						'value'   => [ 'value' => '', 'unit' => 'ms' ],
						'min'     => 0,
						'show_if' => [ 'mm_open_on' => 'hover' ],
					],
				],
			],

			/* Responsive — how the panel behaves on small screens (≤782px). On the
			   theme, the mobile menu lives in the off-canvas drawer; these tune the
			   stacked layout there and standalone alike. */
			'group_mm_responsive' => [
				'type'    => 'group',
				'options' => [
					'mm_mobile_columns' => [
						'type'    => 'select',
						'label'   => __( 'Mobile Columns', 'unysonplus' ),
						'desc'    => __( 'How many columns to show inside a mega panel on small screens. One is a simple stacked list; Two keeps a compact grid.', 'unysonplus' ),
						'value'   => '1',
						'choices' => [
							'1' => __( 'One (stacked)', 'unysonplus' ),
							'2' => __( 'Two (grid)', 'unysonplus' ),
						],
					],
					'mm_mobile_gap' => [
						'label' => __( 'Mobile Spacing', 'unysonplus' ),
						'desc'  => __( 'Space between stacked columns on small screens. Leave empty for the default (8px).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],
					'mm_mobile_hide_desc' => [
						'label' => __( 'Hide Descriptions on Mobile', 'unysonplus' ),
						'desc'  => __( 'Hide item description text on small screens to keep the mobile menu compact.', 'unysonplus' ),
						'type'  => 'switch',
						'value' => false,
					],
					'mm_mobile_hide_icons' => [
						'label' => __( 'Hide Icons on Mobile', 'unysonplus' ),
						'desc'  => __( 'Hide item icons on small screens.', 'unysonplus' ),
						'type'  => 'switch',
						'value' => false,
					],
				],
			],
		],
	],
];
