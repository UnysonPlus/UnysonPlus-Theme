/**
 * Single-post enhancers: reading progress bar, table of contents, copy-link.
 * Enqueued only when at least one of those is enabled (see inc/includes/blog.php).
 */
( function () {
	'use strict';

	var content = document.querySelector( '.single .entry-content, .single-post .entry-content, article .entry-content' );

	/* ---- Reading progress bar ------------------------------------------ */
	function initProgress() {
		var bar = document.querySelector( '.reading-progress__fill' );
		if ( ! bar || ! content ) { return; }

		var ticking = false;
		function update() {
			ticking = false;
			var rect = content.getBoundingClientRect();
			var total = content.offsetHeight - window.innerHeight;
			var scrolled = -rect.top;
			var pct = total > 0 ? Math.min( 1, Math.max( 0, scrolled / total ) ) : 0;
			bar.style.width = ( pct * 100 ).toFixed( 2 ) + '%';
		}
		function onScroll() {
			if ( ! ticking ) { ticking = true; window.requestAnimationFrame( update ); }
		}
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', onScroll, { passive: true } );
		update();
	}

	/* ---- Table of contents --------------------------------------------- */
	function slugify( text, used ) {
		var base = text.toLowerCase().trim().replace( /[^\w\s-]/g, '' ).replace( /\s+/g, '-' ).replace( /-+/g, '-' ) || 'section';
		var slug = base, i = 2;
		while ( used[ slug ] ) { slug = base + '-' + i++; }
		used[ slug ] = true;
		return slug;
	}
	function initToc() {
		var toc = document.querySelector( '.post-toc' );
		if ( ! toc || ! content ) { return; }
		var list = toc.querySelector( '.post-toc__list' );
		var heads = content.querySelectorAll( 'h2, h3' );
		if ( ! list || heads.length < 2 ) { return; } // not worth a TOC

		var used = {};
		var frag = document.createDocumentFragment();
		heads.forEach( function ( h ) {
			if ( ! h.id ) { h.id = slugify( h.textContent, used ); } else { used[ h.id ] = true; }
			var li = document.createElement( 'li' );
			li.className = 'post-toc__item post-toc__item--' + h.tagName.toLowerCase();
			var a = document.createElement( 'a' );
			a.href = '#' + h.id;
			a.textContent = h.textContent;
			li.appendChild( a );
			frag.appendChild( li );
		} );
		list.appendChild( frag );
		toc.hidden = false;
	}

	/* ---- Copy link ----------------------------------------------------- */
	function initCopy() {
		document.querySelectorAll( '.post-share__btn--copy' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var url = btn.getAttribute( 'data-url' ) || window.location.href;
				var done = function () {
					var prev = btn.textContent;
					btn.textContent = btn.getAttribute( 'data-done' ) || 'Copied!';
					btn.classList.add( 'is-copied' );
					setTimeout( function () { btn.textContent = prev; btn.classList.remove( 'is-copied' ); }, 1800 );
				};
				if ( navigator.clipboard && navigator.clipboard.writeText ) {
					navigator.clipboard.writeText( url ).then( done ).catch( done );
				} else {
					var t = document.createElement( 'textarea' );
					t.value = url; document.body.appendChild( t ); t.select();
					try { document.execCommand( 'copy' ); } catch ( e ) {}
					document.body.removeChild( t ); done();
				}
			} );
		} );
	}

	function boot() { initProgress(); initToc(); initCopy(); }

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
