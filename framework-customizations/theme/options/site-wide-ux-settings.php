<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Site-wide UX settings tab.
 *
 * Consolidates the theme's site-wide "chrome"/UX features — Preloader, Scrolling (smooth
 * scroll + a basic scroll-progress bar) and the Scroll-to-Top button — into one top-level
 * tab (they used to be scattered across General → Preloader, General → Scrolling and
 * Miscellaneous → Scroll to Top).
 *
 * Composed into the settings page by settings.php, but ONLY when the Animation Engine
 * plugin is inactive: when it's active, the engine registers its own richer "Site-wide UX"
 * tab (Cursor, Page Transitions, Scroll Progress, Preloader) and the theme injects only its
 * UNIQUE sub-tabs (Scrolling, Scroll to Top) into it — see inc/includes/site-wide-ux.php.
 *
 * Field ids / storage keys are unchanged (general_preloader, general_scroll, misc_scroll_top),
 * so the render getters (unysonplus_layout_get / unysonplus_misc_get) need no changes.
 */

// Base sub-tabs owned by the theme. Extensions (e.g. the Chat extension) append their
// own sub-tabs via the `unysonplus_site_wide_ux_tabs` filter — the engine-inactive twin
// of the Animation Engine's `upw_anim_engine_module_tabs`, so a Site-wide UX feature can
// live entirely in an extension yet still render under this tab.
$site_wide_ux_tabs = [
	'tab_dark_mode' => [
		'title'   => __( 'Dark Mode', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'dark_mode_box' => [
				'title'   => __( 'Dark Mode Toggle', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					fw()->theme->get_options( 'site-wide-ux-dark-mode' ),
				],
			],
		],
	],
	'tab_preloader' => [
		'title'   => __( 'Preloader', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'preloader_box' => [
				'title'   => __( 'Preloader', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					fw()->theme->get_options( 'site-wide-ux-preloader' ),
				],
			],
		],
	],
	'tab_scrolling' => [
		'title'   => __( 'Scrolling', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'scroll_box' => [
				'title'   => __( 'Scrolling', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					fw()->theme->get_options( 'site-wide-ux-scroll' ),
				],
			],
		],
	],
	'tab_scroll_top' => [
		'title'   => __( 'Scroll to Top', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'scroll_top_box' => [
				'title'   => __( 'Floating Scroll-to-Top Button', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					fw()->theme->get_options( 'site-wide-ux-scroll-top' ),
				],
			],
		],
	],
];

$site_wide_ux_tabs = apply_filters( 'unysonplus_site_wide_ux_tabs', $site_wide_ux_tabs );

$options = [
	'site_wide_ux_container' => [
		'title'   => __( 'Site-wide UX', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'site_wide_ux' => [
				'title'   => __( 'Site-wide User Experience', 'unysonplus' ),
				'type'    => 'box',
				'options' => $site_wide_ux_tabs,
			],
		],
	],
];
