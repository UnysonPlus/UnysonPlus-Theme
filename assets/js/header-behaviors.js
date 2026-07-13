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

	// A header that slides in/out on scroll IS motion — respect "reduce motion" by leaving it put.
	if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	var lastY   = window.pageYOffset || document.documentElement.scrollTop || 0;
	var ticking = false;

	// Never hide the header while it holds keyboard focus or has an open control (a menu dropdown or
	// the mobile-drawer toggle — both flip aria-expanded="true"); hiding those strands the user on an
	// off-screen, still-focusable element.
	function headerBusy() {
		return header.contains( document.activeElement ) ||
			!! header.querySelector( '[aria-expanded="true"]' );
	}

	function update() {
		var y = window.pageYOffset || document.documentElement.scrollTop || 0;
		// Don't hide until scrolled past the header's own height (so it never slides while still in
		// its resting zone) — falls back to 60px before the header has measured.
		var threshold = Math.max( header.offsetHeight || 0, 60 );
		if ( y > lastY && y > threshold && ! headerBusy() ) {
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
