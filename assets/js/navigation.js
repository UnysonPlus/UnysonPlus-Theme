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

			// Desktop: submenus open on HOVER (and keyboard :focus-within) via CSS
			// — see .primary-menu .menu-item-has-children:hover > .sub-menu. The
			// parent link is left alone so a click navigates to its own page,
			// matching the header-builder nav behaviour. The navwalker prints a
			// static aria-expanded="false" on the parent link; keep it ACCURATE
			// for screen readers by syncing it to the real open state (focus +
			// hover) so assistive tech announces expanded/collapsed correctly.
			var setExpanded = function ( open ) {
				anchor.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			};
			parent.addEventListener( 'focusin',  function () { setExpanded( true ); } );
			parent.addEventListener( 'focusout', function ( e ) {
				if ( ! parent.contains( e.relatedTarget ) ) { setExpanded( false ); }
			} );
			parent.addEventListener( 'mouseenter', function () { setExpanded( true ); } );
			parent.addEventListener( 'mouseleave', function () { setExpanded( false ); } );
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

	/* ---------- Ring overlays (Radial + Concentric): index the menu items ---------- */
	// Sets --count + --i on the primary menu so the CSS can position each top-level
	// item (by angle for Radial, by ring size for Concentric). Radial houses the menu
	// inside a .__disc wrapper; Concentric puts the <ul> straight in the panel — so we
	// key off the <ul> itself and set --count on it (custom props inherit to the <li>).
	// No-op unless a ring overlay variant is active.
	function initRingMenus() {
		// Every overlay style (Panel / Radial / Concentric) gets --i + --count so the
		// Color Modes can spread the palette across items (rings, disc labels, or list).
		Array.prototype.forEach.call(
			document.querySelectorAll( '.primary-navigation-drawer--overlay .primary-menu' ),
			function ( ul ) {
				var count = ul.children.length;
				ul.style.setProperty( '--count', count );
				// Radial's disc::before divider reads --count too, and can't inherit it
				// from the <ul> (a child) — set it on the disc wrapper when present.
				var disc = ul.closest( '.primary-navigation-drawer__disc' );
				if ( disc ) { disc.style.setProperty( '--count', count ); }
				// Concentric makes the whole ring band the link, so the label text has to
				// live in its own span that can be positioned at the band midpoint.
				var concentric = !! ul.closest( '.primary-navigation-drawer--concentric' );
				Array.prototype.forEach.call( ul.children, function ( li, idx ) {
					li.style.setProperty( '--i', idx );
					if ( concentric ) { wrapConcentricLabel( li ); }
				} );
			}
		);
		sizeConcentricRings();
	}

	// Wrap a concentric item's <a> label text in <span class="cc-label"> (once), so
	// the label can be positioned at the band midpoint while the <a> itself is the
	// full-viewport clipped hit-disc. Finds the item's DIRECT <a> (not sub-menu links).
	function wrapConcentricLabel( li ) {
		var a = null;
		Array.prototype.some.call( li.children, function ( c ) {
			if ( c.tagName === 'A' ) { a = c; return true; }
			return false;
		} );
		if ( ! a || a.querySelector( '.cc-label' ) ) { return; }
		var span = document.createElement( 'span' );
		span.className = 'cc-label';
		while ( a.firstChild ) { span.appendChild( a.firstChild ); }
		a.appendChild( span );
	}

	// Concentric needs the exact drawer DIAGONAL (px) so the outer ring reaches the
	// far corner and the rings divide the diagonal evenly (filling the screen). CSS
	// can't compute hypot(w,h), so we set --reach here and refresh it on resize. The
	// drawer sits below the WP admin bar (--admin-bar-offset), so subtract that from
	// the height — this keeps the rings and the %-positioned labels in agreement.
	function sizeConcentricRings() {
		var offRaw = getComputedStyle( document.body ).getPropertyValue( '--admin-bar-offset' );
		var off = parseInt( offRaw, 10 ) || 0;
		var h = window.innerHeight - off;
		var reach = Math.ceil( Math.sqrt(
			window.innerWidth * window.innerWidth + h * h
		) ) + 2; // +2px safety so the far corner is fully covered, not on the arc edge
		Array.prototype.forEach.call(
			document.querySelectorAll( '.primary-navigation-drawer--concentric .primary-menu' ),
			function ( ul ) { ul.style.setProperty( '--reach', reach + 'px' ); }
		);
	}

	/* ---------- Boot ---------- */
	function init() {
		stripBootstrapDropdownAttrs();
		bindDrawer();
		bindDropdowns();
		bindStickyShadow();
		initRingMenus();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

	// Keep the concentric rings sized to the viewport as it changes.
	window.addEventListener( 'resize', sizeConcentricRings );
})();
