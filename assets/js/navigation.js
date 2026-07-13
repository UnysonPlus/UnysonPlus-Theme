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
		drawer.classList.remove( 'is-closing' ); // in case we re-open mid-close
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

		var isConcentric = drawer.classList.contains( 'primary-navigation-drawer--concentric' );
		var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		drawer.classList.remove( DRAWER_OPEN );
		// Concentric: mark the drawer as closing so the rings collapse with a
		// reversed stagger (outermost first) and the labels fade — see
		// overlay-concentric.css. Removing .is-open is what starts the collapse.
		if ( isConcentric && ! reduceMotion ) { drawer.classList.add( 'is-closing' ); }
		drawer.setAttribute( 'aria-hidden', 'true' );
		document.body.classList.remove( BODY_LOCK );

		Array.prototype.forEach.call(
			document.querySelectorAll( '[aria-controls="' + DRAWER_ID + '"]' ),
			function ( btn ) { btn.setAttribute( 'aria-expanded', 'false' ); }
		);

		document.removeEventListener( 'keydown', onDrawerKeydown );

		// Keep the drawer mounted until the close animation finishes. Concentric
		// rings collapse with a per-ring stagger: total ≈ (count-1)*60ms + 550ms;
		// other modes are a short slide (~250ms).
		var hideDelay = 250;
		if ( isConcentric && ! reduceMotion ) {
			var count = drawer.querySelectorAll( '.primary-menu > li' ).length || 5;
			hideDelay = ( count - 1 ) * 60 + 550 + 120;
		}

		window.setTimeout( function () {
			if ( ! drawer.classList.contains( DRAWER_OPEN ) ) {
				drawer.hidden = true;
				drawer.classList.remove( 'is-closing' );
				if ( lastTrigger && typeof lastTrigger.focus === 'function' ) {
					lastTrigger.focus();
				}
			}
		}, hideDelay );
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

	/* ---------- Scroll Spy (Header → Layout → Scroll Spy) ---------- */
	// Mode-agnostic: every header mode renders .primary-menu > li.menu-item > a, so
	// ONE observer lights up the active section's item in the Top bar, the Vertical
	// rail and the Overlay / Off-canvas drawers alike. It moves the native
	// .current-menu-item class (already styled for every menu preset in style.css)
	// onto the item whose #section sits at the top of the viewport, and smooth-
	// scrolls on click — deferring to Lenis (Animation Engine → Scroll Loop) when it
	// owns scrolling (Lenis adds .lenis to <html> and exposes window.__upwLenis).
	function initScrollSpy() {
		if ( ! document.body.classList.contains( 'nav-scrollspy' ) ) { return; }
		if ( ! ( 'IntersectionObserver' in window ) ) { return; }

		var reduce   = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		var byId     = {};   // id -> { el, lis:[] }
		var sections = [];
		var spyLis   = [];   // only these <li> ever get .current-menu-item toggled

		function headerOffset() {
			var off = 0;
			var h   = document.querySelector( '.site-header' );
			if ( h ) {
				var pos = getComputedStyle( h ).position;
				if ( h.classList.contains( 'header-sticky' ) || h.classList.contains( 'is-stuck' ) || pos === 'fixed' || pos === 'sticky' ) {
					off += h.offsetHeight;
				}
			}
			var bar = document.getElementById( 'wpadminbar' );
			if ( bar ) { off += bar.offsetHeight; }
			return off;
		}

		Array.prototype.forEach.call( document.querySelectorAll( '.primary-menu a[href*="#"]' ), function ( a ) {
			if ( ! a.hash || a.hash === '#' ) { return; }
			// Same-page anchors only — leave links to another page's #anchor to the browser.
			if ( a.pathname && a.pathname.replace( /^\/?/, '/' ) !== location.pathname.replace( /^\/?/, '/' ) ) { return; }
			var id;
			try { id = decodeURIComponent( a.hash.slice( 1 ) ); } catch ( e ) { id = a.hash.slice( 1 ); }
			var el = document.getElementById( id );
			if ( ! el ) { return; }

			if ( ! byId[ id ] ) { byId[ id ] = { el: el, lis: [] }; sections.push( byId[ id ] ); }
			var li = a.closest( 'li' );
			if ( li ) {
				byId[ id ].lis.push( li );
				if ( spyLis.indexOf( li ) === -1 ) { spyLis.push( li ); }
			}

			a.addEventListener( 'click', function ( ev ) {
				ev.preventDefault();
				var off = headerOffset();
				if ( window.__upwLenis ) {
					window.__upwLenis.scrollTo( el, { offset: -off } );
				} else {
					window.scrollTo( { top: el.getBoundingClientRect().top + window.pageYOffset - off, behavior: reduce ? 'auto' : 'smooth' } );
				}
				if ( typeof closeDrawer === 'function' && drawer && drawer.classList.contains( DRAWER_OPEN ) ) { closeDrawer(); }
				if ( history.replaceState ) { history.replaceState( null, '', a.hash ); }
			} );
		} );
		if ( ! sections.length ) { return; }

		// Enables the gated native smooth-scroll in style.css (html.nav-scrollspy).
		document.documentElement.classList.add( 'nav-scrollspy' );

		// Document order, so "the last section whose top has crossed the line" is right.
		sections.sort( function ( a, b ) {
			return ( a.el.compareDocumentPosition( b.el ) & Node.DOCUMENT_POSITION_FOLLOWING ) ? -1 : 1;
		} );

		function setActive( el ) {
			spyLis.forEach( function ( li ) { li.classList.remove( 'current-menu-item' ); } );
			var target = null;
			sections.forEach( function ( s ) { if ( s.el === el ) { target = s; } } );
			if ( ! target ) { return; }
			target.lis.forEach( function ( li ) {
				li.classList.add( 'current-menu-item' );
				// Sub-menu item → light its parent top-level item too.
				var parent = li.parentElement ? li.parentElement.closest( 'li' ) : null;
				if ( parent ) { parent.classList.add( 'current-menu-item' ); }
			} );
		}

		// Active = the LAST section whose top has passed an activation line just below
		// the sticky header (headerOffset() is read live, so sticky/shrink + resize need
		// no rebuild). Falls back to the first section above the line (very top of page).
		function pick() {
			var line   = headerOffset() + Math.round( window.innerHeight * 0.28 );
			var active = sections[0].el;
			for ( var i = 0; i < sections.length; i++ ) {
				if ( sections[ i ].el.getBoundingClientRect().top <= line ) { active = sections[ i ].el; }
			}
			setActive( active );
		}

		var ticking = false;
		function onScroll() {
			if ( ticking ) { return; }
			ticking = true;
			window.requestAnimationFrame( function () { ticking = false; pick(); } );
		}
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', onScroll, { passive: true } );
		pick();
	}

	/* ---------- Boot ---------- */
	function init() {
		stripBootstrapDropdownAttrs();
		bindDrawer();
		bindDropdowns();
		bindStickyShadow();
		initRingMenus();
		initScrollSpy();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

	// Keep the concentric rings sized to the viewport as it changes.
	window.addEventListener( 'resize', sizeConcentricRings );
})();
