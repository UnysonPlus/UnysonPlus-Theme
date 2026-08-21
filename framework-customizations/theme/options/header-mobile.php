<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Header → Mobile & Tablet.
 *
 * Dedicated home for mobile/tablet header behaviour. Options are organised into FW
 * `group` CONTAINERS (visual boxes only — a group does NOT nest storage, so every key
 * below stays a flat TOP-LEVEL Theme Settings key read via fw_get_db_settings_option).
 * Each group carries a native `title` (and optional `desc`) — the FW group container
 * renders those when set (see FW_Container_Type_Group::_render()).
 *
 * Consumed by: template-parts/header-builder.php (classes), inc/includes/theme-vars.php
 * (--drawer-* / --mobile-bar-bg / --toggle-* / --close-* vars), inc/includes/header-builder.php
 * (toggle/close/drawer-content render), inc/includes/layout.php (nav_scrollspy), navigation.js
 * (submenu modes, dismissal), style.css + header-footer-builder.css.
 */

$options = [

	// Quick-start ready-made mobile looks (Classic Drawer / App Bar / Fullscreen Overlay),
	// same preset-loader control the Layout & Menu tabs use. See settings-presets.php
	// ('header_mobile' — a FLAT group of top-level keys).
	'mobile_presets' => [
		'type'         => 'preset-loader',
		'label'        => __( 'Mobile Presets', 'unysonplus' ),
		'desc'         => __( 'Start from a ready-made mobile look — then fine-tune the groups below.', 'unysonplus' ),
		'preset_group' => 'header_mobile',
	],

	/* ── Group: Layout & Mobile Bar ─────────────────────────────────────────── */
	'grp_mobile_layout' => [
		'type'    => 'group',
		'title'   => __( 'Layout & Mobile Bar', 'unysonplus' ),
		'desc'    => __( 'A different <strong>mobile logo</strong> is available under <strong>Header &rarr; Identity &rarr; Mobile Logo</strong>.', 'unysonplus' ),
		'options' => [
			'mobile_header_layout' => [
				'type'    => 'select',
				'label'   => __( 'Mobile Header Layout', 'unysonplus' ),
				'desc'    => __( 'How the logo and hamburger arrange once the header collapses to mobile. "Logo center, toggle below" renders a two-row mobile header. Assumes the logo is in the header\'s start column and the toggle on the end (the default placement).', 'unysonplus' ),
				'value'   => 'default',
				'choices' => [
					'default'      => __( 'Logo left · Toggle right', 'unysonplus' ),
					'toggle-left'  => __( 'Toggle left · Logo center', 'unysonplus' ),
					'center-below' => __( 'Logo center · Toggle below', 'unysonplus' ),
					'logo-right'   => __( 'Logo right · Toggle left', 'unysonplus' ),
				],
			],
			'mobile_bar_bg' => [
				'type'  => 'color-picker',
				'label' => __( 'Mobile Bar Background', 'unysonplus' ),
				'desc'  => __( 'Background colour of the collapsed header bar on mobile. Leave empty to keep the desktop header background. Useful when the desktop header is transparent over a hero but the mobile bar needs a solid fill.', 'unysonplus' ),
				'value' => '',
			],
		],
	],

	/* ── Group: Collapse & Dimensions ───────────────────────────────────────── */
	'grp_mobile_collapse' => [
		'type'    => 'group',
		'title'   => __( 'Collapse & Dimensions', 'unysonplus' ),
		'options' => [
			'mobile_breakpoint' => [
				'label'   => __( 'Collapse to Mobile Menu At', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'lg',
				'choices' => [
					'lg' => __( 'Below 992px (tablet & phone)', 'unysonplus' ),
					'md' => __( 'Below 768px (phone only)', 'unysonplus' ),
				],
				'desc'    => __( 'Screen width below which the inline menu collapses to the hamburger drawer. (The Custom Collapse Width below overrides this when set.)', 'unysonplus' ),
			],
			'mobile_breakpoint_px' => [
				'label' => __( 'Custom Collapse Width', 'unysonplus' ),
				'desc'  => __( 'Exact screen width (px) below which the menu collapses to the hamburger, overriding the md/lg preset above. Leave empty to use the preset. Tip: match this to your menu’s natural wrap point.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px' ],
				'value' => [ 'value' => '', 'unit' => 'px' ],
				'min'   => 320,
				'max'   => 1600,
			],
			'mobile_min_height' => [
				'label' => __( 'Mobile Header Height', 'unysonplus' ),
				'desc'  => __( 'Main header height on phones (below 768px). Leave empty to reuse the desktop height.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'rem', 'px', 'em' ],
				'value' => [ 'value' => '', 'unit' => 'rem' ],
				'min'   => 0,
			],
		],
	],

	/* ── Group: Hamburger / Toggle ──────────────────────────────────────────── */
	'grp_toggle' => [
		'type'    => 'group',
		'title'   => __( 'Hamburger / Toggle', 'unysonplus' ),
		'options' => [
			'mobile_toggle_style' => [
				'type'    => 'select',
				'label'   => __( 'Toggle Icon Style', 'unysonplus' ),
				'desc'    => __( 'Shape of the built-in menu toggle. (A custom Open icon set below overrides this.)', 'unysonplus' ),
				'value'   => 'bars',
				'choices' => [
					'bars' => __( 'Bars (classic hamburger)', 'unysonplus' ),
					'thin' => __( 'Thin bars', 'unysonplus' ),
					'dots' => __( 'Dots', 'unysonplus' ),
				],
			],
			'mobile_toggle_animate' => [
				'type'         => 'switch',
				'label'        => __( 'Animate to X on open', 'unysonplus' ),
				'desc'         => __( 'Morph the bars into a close (X) when the drawer opens. Turn off for a static icon.', 'unysonplus' ),
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
			'mobile_toggle_size' => [
				'label' => __( 'Toggle Size', 'unysonplus' ),
				'desc'  => __( 'Width/height of the toggle button. Leave empty for the default (44px).', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px', 'rem', 'em' ],
				'value' => [ 'value' => '', 'unit' => 'px' ],
				'min'   => 0,
			],
			'mobile_toggle_color' => [
				'type'  => 'color-picker',
				'label' => __( 'Toggle Colour', 'unysonplus' ),
				'desc'  => __( 'Colour of the toggle bars/dots and label. Leave empty for the theme text colour.', 'unysonplus' ),
				'value' => '',
			],
			'mobile_toggle_label' => [
				'type'  => 'text',
				'label' => __( 'Toggle Label', 'unysonplus' ),
				'desc'  => __( 'Optional text beside the icon, e.g. “Menu”. Leave empty for icon only.', 'unysonplus' ),
				'value' => '',
			],
		],
	],

	/* ── Group: Drawer Appearance ───────────────────────────────────────────── */
	'grp_drawer_appearance' => [
		'type'    => 'group',
		'title'   => __( 'Drawer Appearance', 'unysonplus' ),
		'options' => [
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
			'drawer_bg' => [
				'label' => __( 'Drawer Background', 'unysonplus' ),
				'desc'  => __( 'Background colour of the mobile / off-canvas drawer panel. Leave empty for the theme surface colour.', 'unysonplus' ),
				'type'  => 'color-picker',
				'value' => '',
			],
			'drawer_link_color' => [
				'label' => __( 'Drawer Link Colour', 'unysonplus' ),
				'desc'  => __( 'Menu link colour inside the drawer. Leave empty to auto-pick a legible colour from the drawer background (so it never inherits the hero-header palette).', 'unysonplus' ),
				'type'  => 'color-picker',
				'value' => '',
			],
			'drawer_link_active_color' => [
				'label' => __( 'Drawer Active / Hover Colour', 'unysonplus' ),
				'desc'  => __( 'Colour for the current / hovered drawer link. Leave empty to use the theme accent.', 'unysonplus' ),
				'type'  => 'color-picker',
				'value' => '',
			],
			'drawer_link_size' => [
				'label' => __( 'Drawer Link Size', 'unysonplus' ),
				'desc'  => __( 'Font size of the drawer menu links. Leave empty for the default.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'rem', 'px', 'em' ],
				'value' => [ 'value' => '', 'unit' => 'rem' ],
				'min'   => 0,
			],
			'drawer_align' => [
				'label'   => __( 'Drawer Item Alignment', 'unysonplus' ),
				'desc'    => __( 'How drawer menu items align horizontally.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'left',
				'choices' => [
					'left'   => __( 'Left', 'unysonplus' ),
					'center' => __( 'Center', 'unysonplus' ),
					'right'  => __( 'Right', 'unysonplus' ),
				],
			],
			'drawer_item_spacing' => [
				'label' => __( 'Drawer Item Spacing', 'unysonplus' ),
				'desc'  => __( 'Vertical space between drawer menu items. Leave empty for the default.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'rem', 'px', 'em' ],
				'value' => [ 'value' => '', 'unit' => 'rem' ],
				'min'   => 0,
			],
			'drawer_dividers' => [
				'label'        => __( 'Drawer Item Dividers', 'unysonplus' ),
				'desc'         => __( 'Show a hairline rule between drawer menu items.', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
		],
	],

	/* ── Group: Drawer Motion & Scrim ───────────────────────────────────────── */
	'grp_drawer_motion' => [
		'type'    => 'group',
		'title'   => __( 'Drawer Motion & Scrim', 'unysonplus' ),
		'options' => [
			'drawer_animation' => [
				'type'    => 'select',
				'label'   => __( 'Open Animation', 'unysonplus' ),
				'desc'    => __( 'How the drawer panel appears. Slide is the classic side panel; Fade cross-fades in place; Fullscreen covers the whole viewport.', 'unysonplus' ),
				'value'   => 'slide',
				'choices' => [
					'slide'      => __( 'Slide in (side panel)', 'unysonplus' ),
					'fade'       => __( 'Fade in', 'unysonplus' ),
					'fullscreen' => __( 'Fullscreen overlay', 'unysonplus' ),
				],
			],
			'drawer_width' => [
				'label' => __( 'Drawer Width', 'unysonplus' ),
				'desc'  => __( 'Width of the slide-in panel. Leave empty for the default (min(360px, 85vw)). Ignored for the Fullscreen animation.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px', 'rem', 'vw', '%' ],
				'value' => [ 'value' => '', 'unit' => 'px' ],
				'min'   => 0,
			],
			'drawer_radius' => [
				'label' => __( 'Drawer Corner Radius', 'unysonplus' ),
				'desc'  => __( 'Rounds the panel’s inner corners. Leave empty for square.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px', 'rem' ],
				'value' => [ 'value' => '', 'unit' => 'px' ],
				'min'   => 0,
			],
			'drawer_scrim_color' => [
				'type'  => 'color-picker',
				'label' => __( 'Scrim Colour', 'unysonplus' ),
				'desc'  => __( 'Colour of the dim overlay behind the drawer. Leave empty for the default (black).', 'unysonplus' ),
				'value' => '',
			],
			'drawer_scrim_opacity' => [
				'type'       => 'slider',
				'label'      => __( 'Scrim Opacity', 'unysonplus' ),
				'desc'       => __( 'How dark the overlay behind the drawer is (0 = none, 100 = solid).', 'unysonplus' ),
				'value'      => 50,
				'properties' => [ 'min' => 0, 'max' => 100, 'step' => 5 ],
			],
			'drawer_scrim_blur' => [
				'label' => __( 'Scrim Blur', 'unysonplus' ),
				'desc'  => __( 'Frosted backdrop blur behind the drawer (0 = off). Not supported on some older browsers.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px' ],
				'value' => [ 'value' => '', 'unit' => 'px' ],
				'min'   => 0,
			],
		],
	],

	/* ── Group: Dismissal ───────────────────────────────────────────────────── */
	'grp_dismissal' => [
		'type'    => 'group',
		'title'   => __( 'Dismissal', 'unysonplus' ),
		'options' => [
			'drawer_close_on_click' => [
				'type'         => 'switch',
				'label'        => __( 'Close on Link Click', 'unysonplus' ),
				'desc'         => __( 'Automatically close the drawer when a menu link is tapped (submenu openers are excluded). Tapping the scrim always closes it.', 'unysonplus' ),
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
			'drawer_swipe_close' => [
				'type'         => 'switch',
				'label'        => __( 'Swipe to Close', 'unysonplus' ),
				'desc'         => __( 'Let users swipe the drawer toward its edge to dismiss it.', 'unysonplus' ),
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
		],
	],

	/* ── Group: Close Button ────────────────────────────────────────────────── */
	'grp_close_button' => [
		'type'    => 'group',
		'title'   => __( 'Close Button', 'unysonplus' ),
		'options' => [
			'drawer_close_style' => [
				'type'    => 'select',
				'label'   => __( 'Close Style', 'unysonplus' ),
				'desc'    => __( 'The built-in close control. (A custom Close icon overrides this.)', 'unysonplus' ),
				'value'   => 'default',
				'choices' => [
					'default' => __( '× (cross)', 'unysonplus' ),
					'arrow'   => __( '‹ (arrow)', 'unysonplus' ),
					'text'    => __( '“Close” text', 'unysonplus' ),
				],
			],
			'drawer_close_position' => [
				'type'    => 'select',
				'label'   => __( 'Close Position', 'unysonplus' ),
				'desc'    => __( 'In-panel sits inside the drawer’s top corner; Floating pins to the viewport corner, over the scrim.', 'unysonplus' ),
				'value'   => 'in-panel',
				'choices' => [
					'in-panel' => __( 'In panel', 'unysonplus' ),
					'floating' => __( 'Floating (viewport corner)', 'unysonplus' ),
				],
			],
			'drawer_close_size' => [
				'label' => __( 'Close Size', 'unysonplus' ),
				'desc'  => __( 'Size of the close control. Leave empty for the default.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px', 'rem', 'em' ],
				'value' => [ 'value' => '', 'unit' => 'px' ],
				'min'   => 0,
			],
			'drawer_close_color' => [
				'type'  => 'color-picker',
				'label' => __( 'Close Colour', 'unysonplus' ),
				'desc'  => __( 'Colour of the close control. Leave empty for the theme text colour.', 'unysonplus' ),
				'value' => '',
			],
		],
	],

	/* ── Group: Drawer Submenus ─────────────────────────────────────────────── */
	'grp_submenus' => [
		'type'    => 'group',
		'title'   => __( 'Drawer Submenus', 'unysonplus' ),
		'options' => [
			'mobile_submenu_mode' => [
				'type'    => 'select',
				'label'   => __( 'Submenu Behaviour', 'unysonplus' ),
				'desc'    => __( 'How child menus open in the drawer. Accordion expands them in place (the + / − toggle). Flyout slides the child list in as a panel with a “‹ Back” row (best for deeper menus). Expand-all shows every level as a static indented tree.', 'unysonplus' ),
				'value'   => 'accordion',
				'choices' => [
					'accordion'  => __( 'Accordion (expand in place)', 'unysonplus' ),
					'flyout'     => __( 'Flyout (sliding panels + Back)', 'unysonplus' ),
					'expand-all' => __( 'Expand all (static tree)', 'unysonplus' ),
				],
			],
			'mobile_submenu_parent_link' => [
				'type'         => 'switch',
				'label'        => __( 'Parent Items Are Links', 'unysonplus' ),
				'desc'         => __( 'On: tapping a parent’s text navigates to its page; the + / chevron opens the submenu. Off: tapping anywhere on a parent row just opens its submenu (toggle-only). Ignored in Expand-all.', 'unysonplus' ),
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Links', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Toggle only', 'unysonplus' ) ],
			],
		],
	],

	/* ── Group: Off-Canvas / Drawer Content ─────────────────────────────────── */
	'grp_offcanvas' => [
		'type'    => 'group',
		'title'   => __( 'Off-Canvas / Drawer Content', 'unysonplus' ),
		'options' => [
			'offcanvas_content' => array_merge(
				function_exists( 'unysonplus_header_column' ) ? unysonplus_header_column( __( 'Off-Canvas Content', 'unysonplus' ), [] ) : array( 'type' => 'html', 'label' => __( 'Off-Canvas Content', 'unysonplus' ), 'html' => '' ),
				[
					'desc' => __( 'What the off-canvas / mobile drawer panel shows. Add any header element — <b>Menu</b>, <b>Snippet</b> (any shortcode / custom markup), CTA Button, Social Icons, Custom HTML — and order them freely. <b>Leave empty for the default</b>: the Off-Canvas menu (falling back to Primary).', 'unysonplus' ),
				]
			),
			'offcanvas_trigger_icon' => [
				'label' => __( 'Trigger & Close Icons', 'unysonplus' ),
				'type'  => 'multi-inline',
				'value' => [ 'open' => '', 'close' => '' ],
				'desc'  => __( '<b>Open</b> = the button that reveals the off-canvas / mobile drawer panel (default: hamburger bars). <b>Close</b> = the button inside the open panel (default: &times;). Both apply in every header mode. Leave either empty for its default.', 'unysonplus' ),
				'fw_multi_options' => [
					'open'  => [ 'type' => 'icon', 'title' => __( 'Open', 'unysonplus' ) ],
					'close' => [ 'type' => 'icon', 'title' => __( 'Close', 'unysonplus' ) ],
				],
			],
		],
	],

	/* ── Group: Touch Targets & Search ──────────────────────────────────────── */
	'grp_extras' => [
		'type'    => 'group',
		'title'   => __( 'Touch Targets & Search', 'unysonplus' ),
		'options' => [
			'drawer_item_min_height' => [
				'label' => __( 'Drawer Item Min Height', 'unysonplus' ),
				'desc'  => __( 'Minimum tap-target height for each drawer menu item. Aim for ≥ 44–48px for comfortable touch. Leave empty for the default.', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px', 'rem', 'em' ],
				'value' => [ 'value' => '', 'unit' => 'px' ],
				'min'   => 0,
			],
			'drawer_search' => [
				'type'         => 'switch',
				'label'        => __( 'Search in Drawer', 'unysonplus' ),
				'desc'         => __( 'Show a search form at the top of the mobile / off-canvas drawer.', 'unysonplus' ),
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
		],
	],

	/* ── Group: Visibility ──────────────────────────────────────────────────── */
	'grp_visibility' => [
		'type'    => 'group',
		'title'   => __( 'Visibility', 'unysonplus' ),
		'options' => [
			'mobile_hide_topbar' => [
				'label'        => __( 'Hide Top Bar on Mobile', 'unysonplus' ),
				'desc'         => __( 'Hide the entire Top Bar row on small screens (below 768px).', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
			'mobile_hide_bottombar' => [
				'label'        => __( 'Hide Bottom Bar on Mobile', 'unysonplus' ),
				'desc'         => __( 'Hide the entire Bottom Bar row on small screens (below 768px).', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
		],
	],

	/* ── Group: Bottom Navigation (Phase 3) ─────────────────────────────────── */
	'grp_bottom_nav' => [
		'type'    => 'group',
		'title'   => __( 'Bottom Navigation', 'unysonplus' ),
		'options' => [
			'mobile_bottom_nav' => [
				'type'         => 'switch',
				'label'        => __( 'Bottom Tab Bar', 'unysonplus' ),
				'desc'         => __( 'Show a fixed bottom tab bar on phones — an app-like alternative to (or companion for) the hamburger drawer. It uses the first few top-level items of your Primary menu.', 'unysonplus' ),
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
			'mobile_bottom_nav_max' => [
				'label'   => __( 'Max Items', 'unysonplus' ),
				'desc'    => __( 'How many top-level Primary-menu items the bottom bar shows (thumb-reach best practice is ≤ 5).', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '5',
				'choices' => [ '3' => '3', '4' => '4', '5' => '5' ],
			],
			'mobile_bottom_nav_labels' => [
				'type'         => 'switch',
				'label'        => __( 'Show Labels', 'unysonplus' ),
				'desc'         => __( 'Show the item text under each tab. Turn off for an icon-only bar (items need an icon).', 'unysonplus' ),
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
		],
	],

	/* ── Group: Per-Device (Phase 3) ────────────────────────────────────────── */
	'grp_per_device' => [
		'type'    => 'group',
		'title'   => __( 'Per-Device', 'unysonplus' ),
		'options' => [
			'mobile_safe_area' => [
				'type'         => 'switch',
				'label'        => __( 'iOS Safe-Area Padding', 'unysonplus' ),
				'desc'         => __( 'Pad the sticky header, the drawer and the bottom bar for the notch / home indicator on modern phones (env(safe-area-inset)).', 'unysonplus' ),
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
			'mobile_disable_sticky' => [
				'type'         => 'switch',
				'label'        => __( 'Disable Sticky on Mobile', 'unysonplus' ),
				'desc'         => __( 'Keep the header static on phones even when it is Sticky on desktop (frees vertical space on small screens).', 'unysonplus' ),
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
			],
		],
	],

];
