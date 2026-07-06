<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * HEADER → MENU — primary navigation styling.
 *
 * Maps to the `--menu-*` CSS custom properties that style.css already consumes
 * (link color, hover/active color, link padding, dropdown background, plus the
 * item background + item style). Values are folded into the generated front-end
 * stylesheet by inc/includes/theme-vars.php; unset fields fall back to the
 * style.css defaults. Stored under `header_menu`.
 *
 * Colors use the palette-preset compact control (sc_color_field_compact) so they
 * track Theme Settings → General → Colors; a `predefined` value resolves to a
 * live-linked var(--color-{slug}) in theme-vars.php, a `custom` hex passes
 * through, and a legacy plain-hex string (from before the switch) is tolerated —
 * so no migration is needed. Item Style is an image-picker whose value becomes a
 * `body.menu-style-{slug}` class (see unysonplus_layout_body_classes) that
 * style.css keys the hover/active treatment off.
 */

/* Palette-preset color control (kind 'text' → text-{slug} choices, 'bg' →
   bg-{slug}). Falls back to a raw color-picker if the shortcodes helper isn't
   loaded (shortcodes extension inactive). */
$color_field = function ( $label, $desc, $kind, $fallback = '' ) {
	if ( function_exists( 'sc_color_field_compact' ) ) {
		return sc_color_field_compact( array( 'label' => $label, 'desc' => $desc, 'kind' => $kind ) );
	}
	return array( 'label' => $label, 'desc' => $desc, 'type' => 'color-picker', 'value' => $fallback );
};

/* Item-style preview tiles — a tiny inline data-URI SVG per style (no asset
   files → no file-URL cache issue). Each tile shows a 3-item mini nav with the
   middle item rendered in the style; the caption is baked into the SVG (the
   image-picker's show_label is off, matching header-layout.php's design tiles). */
$menu_style_svg = function ( $variant, $label ) {
	$w = 132; $h = 58; $accent = '#2271b1'; $grey = '#c3c8cf';
	$iy = 16; $ih = 7; $irx = 3.5; $iw = 30; $ax = 51; // middle (active) item x

	// Inactive side items.
	$bars  = '<rect x="10" y="' . $iy . '" width="' . $iw . '" height="' . $ih . '" rx="' . $irx . '" fill="' . $grey . '"/>';
	$bars .= '<rect x="92" y="' . $iy . '" width="' . $iw . '" height="' . $ih . '" rx="' . $irx . '" fill="' . $grey . '"/>';

	$treat = ''; $active_fill = $accent;
	switch ( $variant ) {
		case 'underline':
		case 'underline-grow':
			$treat = '<rect x="' . $ax . '" y="26" width="' . $iw . '" height="2" rx="1" fill="' . $accent . '"/>';
			break;
		case 'pill':
			$treat = '<rect x="47" y="13" width="38" height="13" rx="6.5" fill="' . $accent . '"/>';
			$active_fill = '#ffffff';
			break;
		case 'box':
			$treat = '<rect x="47" y="13" width="38" height="13" rx="3" fill="' . $accent . '"/>';
			$active_fill = '#ffffff';
			break;
		case 'outline':
			$treat = '<rect x="47" y="12.5" width="38" height="14" rx="3" fill="none" stroke="' . $accent . '" stroke-width="1.5"/>';
			break;
		case 'bottom-bar':
			$treat = '<rect x="47" y="27" width="38" height="3" rx="1.5" fill="' . $accent . '"/>';
			break;
		case 'top-bar':
			$treat = '<rect x="47" y="10" width="38" height="3" rx="1.5" fill="' . $accent . '"/>';
			break;
		case 'highlight':
			$treat = '<rect x="47" y="13" width="38" height="13" rx="3" fill="' . $accent . '" opacity="0.16"/>';
			break;
		// 'none' → color only, no treatment.
	}

	$active = '<rect x="' . $ax . '" y="' . $iy . '" width="' . $iw . '" height="' . $ih . '" rx="' . $irx . '" fill="' . $active_fill . '"/>';
	// Fills sit BEHIND the active label; lines/borders sit in front.
	$mid   = in_array( $variant, array( 'pill', 'box', 'highlight' ), true ) ? $treat . $active : $active . $treat;
	$text  = '<text x="' . ( $w / 2 ) . '" y="' . ( $h - 6 ) . '" text-anchor="middle" font-family="-apple-system,Segoe UI,Roboto,sans-serif" font-size="10" fill="#50575e">' . $label . '</text>';
	$svg   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $w . ' ' . $h . '" width="' . $w . '" height="' . $h . '">' . $bars . $mid . $text . '</svg>';
	return 'data:image/svg+xml,' . rawurlencode( $svg );
};
$menu_style_choice = function ( $variant, $label ) use ( $menu_style_svg ) {
	$uri = $menu_style_svg( $variant, $label );
	return array(
		'small' => array( 'height' => 58, 'src' => $uri ),
		'large' => array( 'height' => 92, 'src' => $uri ),
	);
};

/* Dropdown-design preview tiles — a mini dropdown panel (3 item lines) showing
   the overall container treatment (shadow / border / top accent). Same inline
   data-URI approach as the item-style tiles; caption baked in. */
$dropdown_style_svg = function ( $variant, $label ) {
	$w = 120; $h = 76; $accent = '#2271b1'; $grey = '#c3c8cf';
	$px = 26; $py = 10; $pw = 68; $ph = 46; $rx = 6;

	$shadow = ''; $stroke = 'none'; $sw = 0; $topbar = '';
	switch ( $variant ) {
		case 'elevated':
			$shadow = '<rect x="' . ( $px + 2 ) . '" y="' . ( $py + 5 ) . '" width="' . $pw . '" height="' . $ph . '" rx="' . $rx . '" fill="#0f172a" opacity="0.20"/>';
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
			$shadow = '<rect x="' . ( $px + 1 ) . '" y="' . ( $py + 4 ) . '" width="' . $pw . '" height="' . $ph . '" rx="' . $rx . '" fill="#0f172a" opacity="0.10"/>';
			$stroke = '#e2e4e7'; $sw = 1;
			break;
	}

	$panel = '<rect x="' . $px . '" y="' . $py . '" width="' . $pw . '" height="' . $ph . '" rx="' . $rx . '" fill="#ffffff" stroke="' . $stroke . '" stroke-width="' . $sw . '"/>';
	$items = '';
	$iy = $py + 10;
	foreach ( array( 0, 1, 2 ) as $i ) {
		$items .= '<rect x="' . ( $px + 9 ) . '" y="' . ( $iy + $i * 11 ) . '" width="' . ( $pw - 26 ) . '" height="5" rx="2.5" fill="' . $grey . '"/>';
	}
	$text = '<text x="' . ( $w / 2 ) . '" y="' . ( $h - 4 ) . '" text-anchor="middle" font-family="-apple-system,Segoe UI,Roboto,sans-serif" font-size="10" fill="#50575e">' . $label . '</text>';
	$svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $w . ' ' . $h . '" width="' . $w . '" height="' . $h . '">' . $shadow . $panel . $topbar . $items . $text . '</svg>';
	return 'data:image/svg+xml,' . rawurlencode( $svg );
};
$dropdown_style_choice = function ( $variant, $label ) use ( $dropdown_style_svg ) {
	$uri = $dropdown_style_svg( $variant, $label );
	return array(
		'small' => array( 'height' => 76, 'src' => $uri ),
		'large' => array( 'height' => 110, 'src' => $uri ),
	);
};

$options = [
	// Quick-start preset loader (applies to the whole `header_menu` group). Sits at
	// the top of the box so a user can pick a starting look, then tweak below.
	'menu_presets' => [
		'type'         => 'preset-loader',
		'label'        => __( 'Menu Presets', 'unysonplus' ),
		'desc'         => __( 'Start from a predefined look, then fine-tune the options below. Or upload a preset JSON you exported earlier.', 'unysonplus' ),
		'preset_group' => 'header_menu',
	],
	'header_menu' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_menu' => [
				'type'    => 'group',
				'options' => [

					/* Item style — the hover/active treatment for top-level nav
					   items. Value → body.menu-style-{slug} class (style.css). */
					'menu_item_style' => [
						'type'    => 'image-picker',
						'label'   => __( 'Menu Item Style', 'unysonplus' ),
						'desc'    => __( 'How each top-level menu item reacts on hover and for the current page. The fills (Pill, Box, Highlight) use the Item Hover / Active Background below; Underline and the accent bars use the Hover / Active Color.', 'unysonplus' ),
						'value'   => 'none',
						'choices' => [
							'none'           => $menu_style_choice( 'none',           __( 'None', 'unysonplus' ) ),
							'underline-grow' => $menu_style_choice( 'underline-grow', __( 'Underline', 'unysonplus' ) ),
							'underline'      => $menu_style_choice( 'underline',      __( 'Underline (static)', 'unysonplus' ) ),
							'pill'           => $menu_style_choice( 'pill',           __( 'Pill', 'unysonplus' ) ),
							'box'            => $menu_style_choice( 'box',            __( 'Box', 'unysonplus' ) ),
							'outline'        => $menu_style_choice( 'outline',        __( 'Outline', 'unysonplus' ) ),
							'bottom-bar'     => $menu_style_choice( 'bottom-bar',     __( 'Bottom Bar', 'unysonplus' ) ),
							'top-bar'        => $menu_style_choice( 'top-bar',        __( 'Top Bar', 'unysonplus' ) ),
							'highlight'      => $menu_style_choice( 'highlight',      __( 'Highlight', 'unysonplus' ) ),
						],
					],

					/* Colors — palette presets (track Theme Settings → Colors). */
					'menu_link_color'       => $color_field( __( 'Menu Link Color', 'unysonplus' ), __( 'Color of top-level menu links. Leave empty to use the body text color.', 'unysonplus' ), 'text' ),
					'menu_link_hover_color' => $color_field( __( 'Menu Link Hover / Active Color', 'unysonplus' ), __( 'Color of menu links on hover and for the current page. Also the accent for the Underline / Bar styles. Leave empty to use the primary color.', 'unysonplus' ), 'text' ),
					'menu_item_bg'          => $color_field( __( 'Item Background', 'unysonplus' ), __( 'Background of each top-level menu item in its normal (un-hovered) state. Leave empty for transparent.', 'unysonplus' ), 'bg' ),
					'menu_item_hover_bg'    => $color_field( __( 'Item Hover / Active Background', 'unysonplus' ), __( 'Fill used by the Pill, Box and Highlight item styles on hover and for the current page. Leave empty for a subtle default tint.', 'unysonplus' ), 'bg' ),

					/* Spacing. */
					'menu_link_padding_x' => [
						'label' => __( 'Link Horizontal Spacing', 'unysonplus' ),
						'desc'  => __( 'Left/right padding inside each menu link.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'menu_link_padding_y' => [
						'label' => __( 'Link Vertical Spacing', 'unysonplus' ),
						'desc'  => __( 'Top/bottom padding inside each menu link.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],

				],
			],

			/* Dropdown / submenu panel. */
			'group_submenu' => [
				'type'    => 'group',
				'options' => [
					'menu_dropdown_style' => [
						'type'    => 'image-picker',
						'label'   => __( 'Dropdown Design', 'unysonplus' ),
						'desc'    => __( 'Overall look of the dropdown panel. Classic is a soft-shadowed card; Elevated deepens the shadow; Bordered swaps the shadow for a hairline border; Minimal is flat (no shadow or border); Top Accent adds a colored bar (the Hover / Active Color) across the top.', 'unysonplus' ),
						'value'   => 'classic',
						'choices' => [
							'classic'    => $dropdown_style_choice( 'classic',    __( 'Classic', 'unysonplus' ) ),
							'elevated'   => $dropdown_style_choice( 'elevated',   __( 'Elevated', 'unysonplus' ) ),
							'bordered'   => $dropdown_style_choice( 'bordered',   __( 'Bordered', 'unysonplus' ) ),
							'minimal'    => $dropdown_style_choice( 'minimal',    __( 'Minimal', 'unysonplus' ) ),
							'top-accent' => $dropdown_style_choice( 'top-accent', __( 'Top Accent', 'unysonplus' ) ),
						],
					],
					'menu_dropdown_bg'            => $color_field( __( 'Dropdown Background', 'unysonplus' ), __( 'Background color of sub-menu dropdown panels.', 'unysonplus' ), 'bg' ),
					'menu_dropdown_link'          => $color_field( __( 'Dropdown Link Color', 'unysonplus' ), __( 'Color of links inside dropdown panels. Leave empty to use the body text color.', 'unysonplus' ), 'text' ),
					'menu_dropdown_link_hover'    => $color_field( __( 'Dropdown Link Hover Color', 'unysonplus' ), __( 'Color of a dropdown link on hover and for the current page. Leave empty to use the Hover / Active Color.', 'unysonplus' ), 'text' ),
					'menu_dropdown_item_hover_bg' => $color_field( __( 'Dropdown Item Hover Background', 'unysonplus' ), __( 'Background of a dropdown item on hover and for the current page. Leave empty for a subtle default tint.', 'unysonplus' ), 'bg' ),
					'menu_dropdown_width' => [
						'label' => __( 'Dropdown Width', 'unysonplus' ),
						'desc'  => __( 'Minimum width of dropdown panels. Leave empty for the default (220px).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],
					'menu_dropdown_radius' => [
						'label' => __( 'Dropdown Corner Radius', 'unysonplus' ),
						'desc'  => __( 'Corner rounding of dropdown panels. Leave empty for the theme default.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px', 'rem', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],
				],
			],
		],
	],
];
