<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * Unyson+ Theme Child — functions.php
 *
 * The parent theme (unysonplus-theme) already enqueues this child's style.css
 * as the `child-style` handle and orders it dead last in the cascade — after
 * the framework / shortcode CSS, the parent style.css, and the plugin's
 * generated presets + per-page dynamic CSS — so your overrides always win.
 *
 * The guarded fallback below only fires if you run this child under an OLDER
 * parent that doesn't enqueue `child-style` itself, so the child stylesheet is
 * never loaded twice and is never missing.
 *
 * Add your own child-theme PHP (extra enqueues, hooks, template functions)
 * below the fallback.
 */

add_action( 'wp_enqueue_scripts', function () {
	if ( wp_style_is( 'child-style', 'enqueued' ) || wp_style_is( 'child-style', 'registered' ) ) {
		return; // Parent theme already handled it (and ordered it last).
	}

	wp_enqueue_style(
		'child-style',
		get_stylesheet_uri(),
		array( 'parent-style' ),
		wp_get_theme()->get( 'Version' ),
		'all'
	);
}, 20 );
