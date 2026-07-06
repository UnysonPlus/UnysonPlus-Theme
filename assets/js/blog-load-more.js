/**
 * Blog "Load More" — progressive enhancement.
 *
 * The server prints a real "Load more" link to the next page (Blog → Blog Index
 * → Pagination = Load More). With JS on, we intercept the click, fetch that next
 * page, append its `.post-entry` cards into the current `.posts-list`, and swap
 * the button's target to the following page — until there are no more pages.
 *
 * No AJAX endpoint or query passing: we fetch the actual paginated URL and read
 * its markup, so it stays in lockstep with whatever the loop would render.
 */
( function () {
	'use strict';

	function init( wrap ) {
		if ( ! wrap || wrap.dataset.upwBound ) { return; }
		wrap.dataset.upwBound = '1';

		var list = ( function () {
			var prev = wrap.previousElementSibling;
			while ( prev && ! prev.classList.contains( 'posts-list' ) ) {
				prev = prev.previousElementSibling;
			}
			return prev;
		} )();
		var btn = wrap.querySelector( '.posts-loadmore__btn' );
		if ( ! list || ! btn ) { return; }

		btn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			if ( wrap.classList.contains( 'is-loading' ) ) { return; }

			var url = btn.getAttribute( 'href' );
			if ( ! url ) { return; }
			wrap.classList.add( 'is-loading' );

			fetch( url, { credentials: 'same-origin' } )
				.then( function ( r ) {
					if ( ! r.ok ) { throw new Error( 'HTTP ' + r.status ); }
					return r.text();
				} )
				.then( function ( html ) {
					var doc = new DOMParser().parseFromString( html, 'text/html' );
					var nextList = doc.querySelector( '.posts-list' );
					if ( nextList ) {
						var frag = document.createDocumentFragment();
						nextList.querySelectorAll( '.post-entry' ).forEach( function ( node ) {
							frag.appendChild( document.importNode( node, true ) );
						} );
						list.appendChild( frag );
					}

					// The next page's own Load-More link, if any, becomes ours.
					var nextBtn = doc.querySelector( '.posts-loadmore__btn' );
					if ( nextBtn && nextBtn.getAttribute( 'href' ) ) {
						btn.setAttribute( 'href', nextBtn.getAttribute( 'href' ) );
					} else {
						wrap.parentNode.removeChild( wrap );
					}
				} )
				.catch( function () {
					// On failure, fall back to a normal navigation.
					window.location.href = url;
				} )
				.finally( function () {
					wrap.classList.remove( 'is-loading' );
				} );
		} );
	}

	function boot() {
		document.querySelectorAll( '.posts-loadmore' ).forEach( init );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
