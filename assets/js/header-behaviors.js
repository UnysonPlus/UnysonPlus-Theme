/**
 * Unyson+ Theme — builder header scroll behaviors.
 *
 * Sticky shadow + sticky-shrink are handled in CSS off the `.is-stuck` class that
 * navigation.js already toggles on `#masthead.header-sticky`. This file adds the
 * one behavior CSS can't do alone: "hide on scroll down, reveal on scroll up".
 */
( function () {
	'use strict';

	var header = document.getElementById( 'masthead' );
	if ( ! header || header.getAttribute( 'data-hf-behavior' ) !== 'hide-on-scroll' ) {
		return;
	}

	var lastY     = window.pageYOffset || document.documentElement.scrollTop || 0;
	var threshold = 80; // don't hide until scrolled past the header-ish zone
	var ticking   = false;

	function update() {
		var y = window.pageYOffset || document.documentElement.scrollTop || 0;
		if ( y > lastY && y > threshold ) {
			header.classList.add( 'is-hidden' );
		} else {
			header.classList.remove( 'is-hidden' );
		}
		lastY   = y < 0 ? 0 : y;
		ticking = false;
	}

	window.addEventListener( 'scroll', function () {
		if ( ! ticking ) {
			window.requestAnimationFrame( update );
			ticking = true;
		}
	}, { passive: true } );
}() );
