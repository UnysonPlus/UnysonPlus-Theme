/**
 * Unyson+ Theme — General Layout runtime.
 *
 * Two responsibilities:
 *
 * 1. Preloader fade-out: removes body.preloader-active once window load
 *    finishes (or after a 6s safety timeout if 'load' never fires).
 *
 * 2. Scroll progress bar: updates .scroll-progress__bar scaleX based on
 *    page scroll position. rAF-throttled.
 */
(function () {
	'use strict';

	function initPreloader() {
		if ( ! document.body.classList.contains( 'preloader-active' ) ) { return; }

		var done = false;
		function finish() {
			if ( done ) { return; }
			done = true;
			document.body.classList.remove( 'preloader-active' );
		}

		if ( document.readyState === 'complete' ) {
			finish();
		} else {
			window.addEventListener( 'load', finish );
			// Safety net — never trap users behind a stuck preloader.
			window.setTimeout( finish, 6000 );
		}
	}

	function initScrollProgress() {
		var bar = document.querySelector( '.scroll-progress__bar' );
		if ( ! bar ) { return; }

		var ticking = false;
		function update() {
			ticking = false;
			var doc = document.documentElement;
			var scrolled = doc.scrollTop || document.body.scrollTop || 0;
			var total = ( doc.scrollHeight - doc.clientHeight ) || 0;
			var pct = total > 0 ? Math.min( 1, scrolled / total ) : 0;
			bar.style.transform = 'scaleX(' + pct.toFixed( 4 ) + ')';
		}
		function onScroll() {
			if ( ticking ) { return; }
			ticking = true;
			window.requestAnimationFrame( update );
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', onScroll, { passive: true } );
		update();
	}

	/**
	 * Auto-stack floating buttons (scroll-to-top + theme-toggle, plus the Chat
	 * extension's .upw-chat button when present) when they share the same corner.
	 * JS sets --stack-position: 1, 2, … on each button after the first; CSS reads
	 * the variable in the corner-modifier rules to compute the offset from the corner.
	 * The .upw-chat selectors are a harmless no-op when the Chat extension is inactive.
	 */
	function initFloatingButtonStack() {
		var corners = {
			'bottom-right': [], 'bottom-left': [],
			'top-right':    [], 'top-left':    []
		};

		var nodes = document.querySelectorAll( '.scroll-to-top, .theme-toggle, .upw-chat' );
		Array.prototype.forEach.call( nodes, function ( el ) {
			var corner = null;
			if      ( el.classList.contains( 'theme-toggle--top-right' ) )    corner = 'top-right';
			else if ( el.classList.contains( 'theme-toggle--top-left' ) )     corner = 'top-left';
			else if ( el.classList.contains( 'theme-toggle--bottom-left' ) )  corner = 'bottom-left';
			else if ( el.classList.contains( 'theme-toggle--bottom-right' ) ) corner = 'bottom-right';
			else if ( el.classList.contains( 'scroll-to-top--left' ) )        corner = 'bottom-left';
			else if ( el.classList.contains( 'scroll-to-top--right' ) )       corner = 'bottom-right';
			else if ( el.classList.contains( 'upw-chat--left' ) )             corner = 'bottom-left';
			else if ( el.classList.contains( 'upw-chat--right' ) )            corner = 'bottom-right';
			if ( corner ) { corners[ corner ].push( el ); }
		} );

		Object.keys( corners ).forEach( function ( corner ) {
			corners[ corner ].forEach( function ( el, idx ) {
				if ( idx > 0 ) { el.style.setProperty( '--stack-position', idx ); }
			} );
		} );
	}

	function init() {
		initPreloader();
		initScrollProgress();
		initFloatingButtonStack();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
})();
