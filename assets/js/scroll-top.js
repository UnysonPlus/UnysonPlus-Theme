/**
 * Unyson+ Theme — Scroll-to-top buttons.
 *
 * Binds to three selectors:
 *   .scroll-to-top         (global floating button rendered by inc/includes/misc.php)
 *   .footer-back-to-top    (inline footer-builder element)
 *   .toplink               (legacy class, kept for back-compat)
 *
 * The floating button reveals once the viewport scrolls past
 * data-offset (px). Clicks smooth-scroll back to top. rAF-throttled.
 */
(function () {
	'use strict';

	function init() {
		var floating = document.querySelector( '.scroll-to-top' );
		var inline   = document.querySelectorAll( '.footer-back-to-top, .toplink' );

		// Inline buttons: always clickable, no visibility logic.
		Array.prototype.forEach.call( inline, function ( btn ) {
			btn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				scrollToTop();
			} );
		} );

		if ( ! floating ) { return; }

		// data-offset is the numeric threshold; data-offset-unit is 'px' or 'vh'.
		// vh is resolved against the viewport height and recomputed on resize.
		var offsetVal = parseFloat( floating.getAttribute( 'data-offset' ) );
		if ( isNaN( offsetVal ) ) { offsetVal = 300; }
		var offsetUnit = floating.getAttribute( 'data-offset-unit' ) === 'vh' ? 'vh' : 'px';
		var offset = 300;
		var ticking = false;

		// Progress-ring design: an SVG circle whose stroke fills with scroll progress.
		var ring    = floating.querySelector( '.scroll-to-top__ring-fill' );
		var ringLen = 0;
		if ( ring && typeof ring.getTotalLength === 'function' ) {
			ringLen = ring.getTotalLength();
			ring.style.strokeDasharray  = String( ringLen );
			ring.style.strokeDashoffset = String( ringLen );
		}

		function computeOffset() {
			offset = offsetUnit === 'vh'
				? ( offsetVal / 100 ) * ( window.innerHeight || document.documentElement.clientHeight || 0 )
				: offsetVal;
		}

		function update() {
			ticking = false;
			var y = window.scrollY || window.pageYOffset || 0;
			if ( y > offset ) {
				floating.hidden = false;
				floating.classList.add( 'is-visible' );
			} else {
				floating.classList.remove( 'is-visible' );
			}
			if ( ring ) {
				var max = ( document.documentElement.scrollHeight - window.innerHeight ) || 1;
				var p   = Math.min( 1, Math.max( 0, y / max ) );
				ring.style.strokeDashoffset = String( ringLen * ( 1 - p ) );
			}
		}

		function onScroll() {
			if ( ticking ) { return; }
			ticking = true;
			window.requestAnimationFrame( update );
		}

		floating.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			scrollToTop();
		} );

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', function () { computeOffset(); update(); }, { passive: true } );
		computeOffset();
		update();
	}

	function scrollToTop() {
		// Respect the OS "reduce motion" preference — jump instead of animating.
		var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		// Defer to Lenis when the Animation Engine's Scroll Loop owns scrolling — a native
		// window.scrollTo fights the smooth-scroll hijack (same handoff navigation.js uses).
		if ( window.__upwLenis && typeof window.__upwLenis.scrollTo === 'function' ) {
			window.__upwLenis.scrollTo( 0, { immediate: reduce } );
			return;
		}
		try {
			window.scrollTo( { top: 0, behavior: reduce ? 'auto' : 'smooth' } );
		} catch ( err ) {
			window.scrollTo( 0, 0 );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
})();
