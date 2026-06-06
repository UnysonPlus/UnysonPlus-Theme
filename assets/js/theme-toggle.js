/**
 * Unyson+ Theme — Light/Dark toggle.
 *
 * The boot script in <head> (emitted by inc/includes/misc.php) has
 * already set data-bs-theme + data-theme-mode on <html> before paint.
 * This file handles the 2-state click toggle, persistence, and the
 * prefers-color-scheme listener for visitors still in the initial
 * "auto" state.
 *
 * Click behavior: flips between LIGHT ↔ DARK. The first click from an
 * "auto" initial state switches to the opposite of the system
 * preference (so light system → click → dark). After that, every click
 * toggles light/dark and persists the choice. There's no way to return
 * to "auto" via the button — clearing localStorage resets to auto.
 */
(function () {
	'use strict';

	var STORAGE_KEY = 'unysonplus-theme-mode';
	var html = document.documentElement;

	function init() {
		var button = document.querySelector( '.theme-toggle' );
		if ( ! button ) { return; }

		var mq = window.matchMedia ? window.matchMedia( '(prefers-color-scheme: dark)' ) : null;
		var defaultMode = button.getAttribute( 'data-default-mode' ) || 'auto';

		function persist( mode ) {
			try {
				if ( mode === 'auto' ) { window.localStorage.removeItem( STORAGE_KEY ); }
				else { window.localStorage.setItem( STORAGE_KEY, mode ); }
			} catch ( e ) { /* localStorage unavailable / quota */ }
		}

		function resolve( mode ) {
			if ( mode === 'auto' ) {
				return ( mq && mq.matches ) ? 'dark' : 'light';
			}
			return mode;
		}

		function applyMode( mode ) {
			html.setAttribute( 'data-bs-theme', resolve( mode ) );
			html.setAttribute( 'data-theme-mode', mode );
			persist( mode );
			updateButtonAria( mode );
		}

		function updateButtonAria( mode ) {
			var resolved = resolve( mode );
			var next = resolved === 'dark' ? 'light' : 'dark';
			button.setAttribute( 'aria-label', 'Switch to ' + next + ' mode' );
			button.setAttribute( 'data-next-mode', next );
		}

		function currentMode() {
			return html.getAttribute( 'data-theme-mode' ) || defaultMode;
		}

		button.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			var next = resolve( currentMode() ) === 'dark' ? 'light' : 'dark';
			applyMode( next );
		} );

		// Live-react to system theme changes only when the visitor is still
		// in the "auto" state (i.e. hasn't clicked the toggle yet).
		if ( mq ) {
			var onChange = function () {
				if ( currentMode() === 'auto' ) { applyMode( 'auto' ); }
			};
			if ( typeof mq.addEventListener === 'function' ) {
				mq.addEventListener( 'change', onChange );
			} else if ( typeof mq.addListener === 'function' ) {
				mq.addListener( onChange ); // Safari < 14
			}
		}

		updateButtonAria( currentMode() );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
})();
