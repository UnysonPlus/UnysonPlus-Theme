<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Site-wide UX — Animation Engine integration.
 *
 * The theme's own "Site-wide UX" settings tab (Preloader · Scrolling · Scroll to Top) is a
 * normal Theme-Settings tab composed in framework-customizations/theme/options/settings.php
 * (from site-wide-ux-settings.php), shown only when the Animation Engine plugin is inactive.
 *
 * This file handles the ENGINE-ACTIVE case: the engine registers its own richer "Site-wide UX"
 * tab (Cursor, Page Transitions, Scroll Progress, Preloader), so here the theme injects only
 * its UNIQUE sub-tabs — Scrolling + Scroll to Top — into that tab via the engine's
 * `upw_anim_engine_module_tabs` filter. The theme's Preloader defers entirely to the engine's,
 * and its basic scroll-progress bar defers to the engine's Scroll Progress (render-gated in
 * inc/includes/layout.php). This filter only runs when the engine is loaded, so no active check
 * is needed. The sub-tab option definitions are reused from the same leaf files, so nothing is
 * duplicated.
 */

add_filter( 'upw_anim_engine_module_tabs', function ( $tabs ) {
	if ( ! is_array( $tabs ) || ! function_exists( 'fw' ) ) {
		return $tabs;
	}

	$subtab = function ( $title, $box_title, $fields ) {
		return array(
			'title'   => $title,
			'type'    => 'tab',
			'options' => array(
				'box' => array( 'title' => $box_title, 'type' => 'box', 'options' => $fields ),
			),
		);
	};

	// Dark Mode toggle — a global end-user UX affordance, so it lives under Site-wide UX
	// (its `misc_dark_mode` storage key is shared with the engine-inactive path).
	$tabs['theme_dark_mode'] = $subtab(
		__( 'Dark Mode', 'unysonplus' ),
		__( 'Dark Mode Toggle', 'unysonplus' ),
		array( fw()->theme->get_options( 'site-wide-ux-dark-mode' ) )
	);
	// Scrolling (smooth scroll + basic scroll-progress bar) — theme-unique alongside the engine.
	$tabs['theme_scrolling'] = $subtab(
		__( 'Scrolling', 'unysonplus' ),
		__( 'Scrolling', 'unysonplus' ),
		array( fw()->theme->get_options( 'site-wide-ux-scroll' ) )
	);
	// Scroll-to-Top button — no engine equivalent.
	$tabs['theme_scroll_top'] = $subtab(
		__( 'Scroll to Top', 'unysonplus' ),
		__( 'Floating Scroll-to-Top Button', 'unysonplus' ),
		array( fw()->theme->get_options( 'site-wide-ux-scroll-top' ) )
	);

	return $tabs;
}, 30 );
