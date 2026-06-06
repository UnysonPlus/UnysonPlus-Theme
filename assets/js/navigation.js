/**
 * Unyson+ Theme — Header navigation.
 *
 * - Off-canvas mobile drawer (slide-in panel, scrim, focus trap, ESC close).
 * - Desktop dropdown menus (click toggle + outside-click + ESC close).
 * - Mobile submenu accordions inside the drawer.
 * - Sticky-header shadow toggle via IntersectionObserver.
 * - Strips Bootstrap dropdown data-attrs that may have been injected
 *   upstream (legacy walker), so our handlers don't double-fire.
 */
(function () {
	'use strict';

	var DRAWER_ID    = 'primary-navigation-drawer';
	var DRAWER_OPEN  = 'is-open';
	var BODY_LOCK    = 'menu-drawer-open';
	var SUBMENU_OPEN = 'is-open';

	var drawer       = document.getElementById( DRAWER_ID );
	var header       = document.getElementById( 'masthead' );
	var lastTrigger  = null;

	/* ---------- Strip legacy Bootstrap dropdown attrs ---------- */
	function stripBootstrapDropdownAttrs() {
		var nodes = document.querySelectorAll( '.primary-menu [data-bs-toggle="dropdown"]' );
		Array.prototype.forEach.call( nodes, function ( el ) {
			el.removeAttribute( 'data-bs-toggle' );
			el.classList.remove( 'dropdown-toggle' );
		} );
	}

	/* ---------- Drawer ---------- */
	function openDrawer( triggerEl ) {
		if ( ! drawer ) { return; }
		lastTrigger = triggerEl || document.activeElement;
		drawer.hidden = false;
		drawer.setAttribute( 'aria-hidden', 'false' );
		// Force reflow so the transition fires.
		void drawer.offsetWidth;
		drawer.classList.add( DRAWER_OPEN );
		document.body.classList.add( BODY_LOCK );

		Array.prototype.forEach.call(
			document.querySelectorAll( '[aria-controls="' + DRAWER_ID + '"]' ),
			function ( btn ) { btn.setAttribute( 'aria-expanded', 'true' ); }
		);

		var firstFocusable = getFocusable( drawer )[0];
		if ( firstFocusable ) { firstFocusable.focus(); }

		document.addEventListener( 'keydown', onDrawerKeydown );
	}

	function closeDrawer() {
		if ( ! drawer ) { return; }
		drawer.classList.remove( DRAWER_OPEN );
		drawer.setAttribute( 'aria-hidden', 'true' );
		document.body.classList.remove( BODY_LOCK );

		Array.prototype.forEach.call(
			document.querySelectorAll( '[aria-controls="' + DRAWER_ID + '"]' ),
			function ( btn ) { btn.setAttribute( 'aria-expanded', 'false' ); }
		);

		document.removeEventListener( 'keydown', onDrawerKeydown );

		window.setTimeout( function () {
			if ( ! drawer.classList.contains( DRAWER_OPEN ) ) {
				drawer.hidden = true;
				if ( lastTrigger && typeof lastTrigger.focus === 'function' ) {
					lastTrigger.focus();
				}
			}
		}, 250 );
	}

	function onDrawerKeydown( e ) {
		if ( e.key === 'Escape' ) { e.preventDefault(); closeDrawer(); return; }
		if ( e.key !== 'Tab' )    { return; }

		var focusables = getFocusable( drawer );
		if ( focusables.length === 0 ) { return; }

		var first = focusables[0];
		var last  = focusables[ focusables.length - 1 ];

		if ( e.shiftKey && document.activeElement === first ) {
			e.preventDefault(); last.focus();
		} else if ( ! e.shiftKey && document.activeElement === last ) {
			e.preventDefault(); first.focus();
		}
	}

	function getFocusable( root ) {
		var selector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
		return Array.prototype.filter.call(
			root.querySelectorAll( selector ),
			function ( el ) { return el.offsetParent !== null; }
		);
	}

	function bindDrawer() {
		if ( ! drawer ) { return; }

		Array.prototype.forEach.call(
			document.querySelectorAll( '[aria-controls="' + DRAWER_ID + '"]' ),
			function ( btn ) {
				btn.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					if ( drawer.classList.contains( DRAWER_OPEN ) ) { closeDrawer(); }
					else { openDrawer( btn ); }
				} );
			}
		);

		Array.prototype.forEach.call(
			drawer.querySelectorAll( '[data-drawer-close]' ),
			function ( el ) { el.addEventListener( 'click', closeDrawer ); }
		);
	}

	/* ---------- Dropdown menus (desktop) ---------- */
	function bindDropdowns() {
		var parents = document.querySelectorAll( '.primary-menu .menu-item-has-children' );
		Array.prototype.forEach.call( parents, function ( parent ) {
			var anchor = parent.querySelector( ':scope > a' );
			if ( ! anchor ) { return; }

			// Append a dedicated submenu toggle button inside the drawer (mobile),
			// so users can open submenus without navigating away.
			if ( parent.closest( '#' + DRAWER_ID ) ) {
				var btn = document.createElement( 'button' );
				btn.type = 'button';
				btn.className = 'submenu-toggle';
				btn.setAttribute( 'aria-expanded', 'false' );
				btn.setAttribute( 'aria-label', 'Toggle submenu' );
				parent.insertBefore( btn, anchor.nextSibling );

				btn.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					var open = parent.classList.toggle( SUBMENU_OPEN );
					btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
				} );
				return;
			}

			// Desktop: click the link toggles the submenu; href still works via
			// keyboard / right-click. We only intercept primary mouse clicks.
			anchor.addEventListener( 'click', function ( e ) {
				if ( e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey ) { return; }
				e.preventDefault();
				var willOpen = ! parent.classList.contains( SUBMENU_OPEN );
				closeAllDropdowns();
				if ( willOpen ) { parent.classList.add( SUBMENU_OPEN ); }
			} );
		} );

		document.addEventListener( 'click', function ( e ) {
			if ( ! e.target.closest( '.primary-menu .menu-item-has-children' ) ) {
				closeAllDropdowns();
			}
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) { closeAllDropdowns(); }
		} );
	}

	function closeAllDropdowns() {
		Array.prototype.forEach.call(
			document.querySelectorAll( '.primary-menu .menu-item-has-children.' + SUBMENU_OPEN ),
			function ( el ) {
				// Don't close drawer-internal submenus (mobile accordions).
				if ( el.closest( '#' + DRAWER_ID ) ) { return; }
				el.classList.remove( SUBMENU_OPEN );
			}
		);
	}

	/* ---------- Sticky-header shadow ---------- */
	function bindStickyShadow() {
		if ( ! header || ! header.classList.contains( 'header-sticky' ) ) { return; }
		if ( ! ( 'IntersectionObserver' in window ) ) { return; }

		var sentinel = document.createElement( 'div' );
		sentinel.style.cssText = 'position:absolute;top:0;left:0;width:1px;height:1px;pointer-events:none;';
		header.parentNode.insertBefore( sentinel, header );

		var io = new IntersectionObserver( function ( entries ) {
			header.classList.toggle( 'is-stuck', ! entries[0].isIntersecting );
		}, { threshold: 0 } );
		io.observe( sentinel );
	}

	/* ---------- Boot ---------- */
	function init() {
		stripBootstrapDropdownAttrs();
		bindDrawer();
		bindDropdowns();
		bindStickyShadow();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
})();
