/* preset-loader option type — card select, JSON upload/export, and apply.
 *
 * Apply mirrors Unyson's own "Reset current tab" behaviour: it writes the preset
 * to the DB via AJAX, then RE-RENDERS THE SETTINGS FORM IN PLACE (no full page
 * reload) — GET the page, swap the form's HTML, re-init the options, restore
 * scroll — and reuses the framework's tab-restore (its
 * sessionStorage['fw_settings_restore_tab'] key + the 'fw:settings-form:reset'
 * event, handled in views/backend-settings-form.php) so you stay on the same tab.
 */
( function ( $ ) {
	'use strict';

	// The framework's own tab-restore key + form selector (see backend-settings-form.php).
	var FW_RESTORE_KEY = 'fw_settings_restore_tab',
		FW_FORM_SELECTOR = 'form.fw-settings-form';

	function rootOf( el ) {
		return $( el ).closest( '[data-preset-group]' );
	}

	function setStatus( $root, text, kind ) {
		var $s = $root.find( '.fw-preset-loader-status' );
		$s.removeClass( 'is-error is-busy' );
		if ( kind ) { $s.addClass( kind ); }
		$s.text( text || '' );
	}

	function selectedKey( $root ) {
		var $sel = $root.find( '.fw-preset-card.is-selected' ).first();
		return $sel.length ? $sel.attr( 'data-preset-key' ) : '';
	}

	/* The bare ids of the currently-open tab chain (outermost→innermost), in the
	   exact shape the framework's restore expects. Mirrors the reset-tab script:
	   only VISIBLE active navs (a nav inside a hidden panel is a stale ui-state-active). */
	function openTabChain( $form ) {
		var ids = [];
		$form.find( '.fw-options-tabs-list li.ui-state-active > a.nav-tab' ).each( function () {
			if ( ! $( this ).is( ':visible' ) ) { return; }
			var href = this.getAttribute( 'href' ) || '';
			if ( href.indexOf( '#fw-options-tab-' ) === 0 ) {
				ids.push( href.replace( /^#fw-options-tab-/, '' ) );
			}
		} );
		return ids;
	}

	/* Re-render the settings form in place from a fresh GET of the page, then let
	   the framework reopen the remembered tab (via the 'fw:settings-form:reset'
	   event it listens for). Falls back to a full reload on any failure — the
	   framework's on-load probe still restores the tab from the same key. */
	function rerenderFormInPlace( $form ) {
		$.ajax( { type: 'GET', dataType: 'text' } ).done( function ( html ) {
			var $new = $( FW_FORM_SELECTOR, html ).first();
			if ( ! $new.length ) { window.location.reload(); return; }

			$form.css( 'transition', 'opacity ease .3s' ).css( 'opacity', '0' );
			$form.trigger( 'fw:settings-form:before-html-reset' );
			if ( typeof fwEvents !== 'undefined' ) { fwEvents.trigger( 'fw:options:teardown', { $elements: $form } ); }

			setTimeout( function () {
				var scrollTop = $( window ).scrollTop();
				$form.css( { 'display': 'block', 'height': $form.height() + 'px' } );
				$form.get( 0 ).innerHTML = $new.get( 0 ).innerHTML;
				$new = undefined;
				$form.css( { 'display': '', 'height': '' } );

				if ( typeof fwEvents !== 'undefined' ) { fwEvents.trigger( 'fw:options:init', { $elements: $form } ); }
				$( window ).scrollTop( scrollTop );

				$form.css( 'opacity', '' );
				setTimeout( function () { $form.css( 'transition', '' ); }, 300 );

				// Fires the framework's activateChain() to reopen the remembered tab.
				$form.trigger( 'fw:settings-form:reset' );
			}, 300 );
		} ).fail( function () {
			window.location.reload();
		} );
	}

	function applyThenRerender( $root ) {
		var $form = $root.closest( FW_FORM_SELECTOR );
		if ( ! $form.length ) { $form = $root.closest( 'form' ); }
		if ( ! $form.length ) { window.location.reload(); return; }

		// Remember the open tab chain in the framework's own key so its restore
		// reopens it (works for both the in-place re-render and the reload fallback).
		try {
			var ids = openTabChain( $form );
			if ( ids.length ) { window.sessionStorage.setItem( FW_RESTORE_KEY, JSON.stringify( ids ) ); }
		} catch ( e ) {}

		rerenderFormInPlace( $form );
	}

	// --- Card selection ---
	$( document ).on( 'click', '.fw-preset-card', function () {
		var $card = $( this );
		if ( $card.is( ':disabled' ) || $card.hasClass( 'is-disabled' ) ) { return; }
		var $root = rootOf( this );
		$root.find( '.fw-preset-card' ).removeClass( 'is-selected' );
		$card.addClass( 'is-selected' );
		$root.find( '.fw-preset-apply' ).prop( 'disabled', false );
		setStatus( $root, '' );
	} );

	// --- Upload custom JSON ---
	$( document ).on( 'change', '.fw-preset-upload', function () {
		var input = this,
			$root = rootOf( input );
		if ( ! input.files || ! input.files.length ) { return; }
		var file = input.files[0],
			reader = new FileReader();

		reader.onload = function ( e ) {
			var obj;
			try {
				obj = JSON.parse( e.target.result );
			} catch ( err ) {
				setStatus( $root, 'That file is not valid JSON.', 'is-error' );
				return;
			}
			if ( ! obj || typeof obj !== 'object' || Array.isArray( obj ) ) {
				setStatus( $root, 'That file is not a valid preset.', 'is-error' );
				return;
			}
			var $custom = $root.find( '.fw-preset-card--custom' );
			$custom.data( 'payload', obj )
				.prop( 'disabled', false )
				.removeClass( 'is-disabled' );
			$custom.find( '.fw-preset-card__custom-hint' ).text( 'Loaded: ' + file.name );
			$root.find( '.fw-preset-card' ).removeClass( 'is-selected' );
			$custom.addClass( 'is-selected' );
			$root.find( '.fw-preset-apply' ).prop( 'disabled', false );
			setStatus( $root, '' );
		};
		reader.readAsText( file );
		input.value = '';
	} );

	// --- Apply ---
	$( document ).on( 'click', '.fw-preset-apply', function () {
		var $root = rootOf( this ),
			$btn = $( this ),
			key = selectedKey( $root ),
			group = $root.attr( 'data-preset-group' );

		if ( ! key ) { return; }
		if ( ! window.confirm( 'Apply this preset? It saves these settings and refreshes this tab. Unsaved changes elsewhere on this page will be lost.' ) ) {
			return;
		}

		var payload = {
			action: 'unysonplus_apply_settings_preset',
			group: group,
			nonce: $root.attr( 'data-preset-nonce' )
		};
		if ( key === '__custom__' ) {
			var custom = $root.find( '.fw-preset-card--custom' ).data( 'payload' );
			if ( ! custom ) { setStatus( $root, 'No uploaded file to apply.', 'is-error' ); return; }
			payload.custom = JSON.stringify( custom );
		} else {
			payload.preset = key;
		}

		$btn.prop( 'disabled', true );
		setStatus( $root, 'Applying…', 'is-busy' );

		$.post( $root.attr( 'data-preset-ajaxurl' ), payload )
			.done( function ( res ) {
				if ( res && res.success ) {
					setStatus( $root, 'Applied. Refreshing…', 'is-busy' );
					applyThenRerender( $root );
				} else {
					var msg = ( res && res.data && res.data.message ) ? res.data.message : 'Could not apply the preset.';
					setStatus( $root, msg, 'is-error' );
					$btn.prop( 'disabled', false );
				}
			} )
			.fail( function () {
				setStatus( $root, 'Request failed. Please try again.', 'is-error' );
				$btn.prop( 'disabled', false );
			} );
	} );

	// --- Export current (download the saved values as JSON) ---
	$( document ).on( 'click', '.fw-preset-export', function () {
		var $root = rootOf( this ),
			group = $root.attr( 'data-preset-group' ),
			raw = $root.attr( 'data-preset-current' ) || '{}',
			pretty;
		try {
			pretty = JSON.stringify( JSON.parse( raw ), null, 2 );
		} catch ( e ) {
			pretty = raw;
		}
		var blob = new Blob( [ pretty ], { type: 'application/json' } ),
			url = URL.createObjectURL( blob ),
			a = document.createElement( 'a' );
		a.href = url;
		a.download = ( group || 'settings' ) + '-preset.json';
		document.body.appendChild( a );
		a.click();
		document.body.removeChild( a );
		setTimeout( function () { URL.revokeObjectURL( url ); }, 1000 );
	} );

	/* ------------------------------------------------------------------ *
	 * Browse Library — a modal of downloadable presets for this group.
	 * Installing one drops it into the group's registry (server-side filter),
	 * so it appears as an ordinary card above and is applied by the flow above.
	 * ------------------------------------------------------------------ */

	var PL = window.upwPresetLibrary || null;
	var $plModal = null, plGroup = '', $plRoot = null, plItems = [];

	function plEsc( s ) {
		return String( s == null ? '' : s ).replace( /[&<>"']/g, function ( c ) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ c ];
		} );
	}

	function plBuild() {
		$plModal = $(
			'<div class="upw-plib-overlay" role="dialog" aria-modal="true" hidden>' +
				'<div class="upw-plib__backdrop"></div>' +
				'<div class="upw-plib">' +
					'<div class="upw-plib__head">' +
						'<span class="upw-plib__title">' + plEsc( PL.i18n.title ) + '</span>' +
						'<span class="upw-plib__head-actions">' +
							'<button type="button" class="button button-small upw-plib__refresh">' + plEsc( PL.i18n.refresh ) + '</button>' +
							'<button type="button" class="upw-plib__close" aria-label="' + plEsc( PL.i18n.close ) + '">&times;</button>' +
						'</span>' +
					'</div>' +
					'<div class="upw-plib__body"></div>' +
				'</div>' +
			'</div>'
		);
		$( document.body ).append( $plModal );

		$plModal.on( 'click', '.upw-plib__close, .upw-plib__backdrop', plClose );
		$plModal.on( 'click', function ( e ) { if ( e.target === $plModal[0] ) { plClose(); } } );
		$plModal.on( 'click', '.upw-plib__refresh', function () { plFetch( 'refresh' ); } );
		$plModal.on( 'click', '[data-plib-act]', function () { plAction( $( this ) ); } );
		$( document ).on( 'keydown.upwPlib', function ( e ) {
			if ( e.key === 'Escape' && $plModal && ! $plModal.prop( 'hidden' ) ) { plClose(); }
		} );
	}

	function plOpen( $root ) {
		if ( ! PL ) { return; }
		if ( ! $plModal ) { plBuild(); }
		$plRoot = $root;
		plGroup = $root.attr( 'data-preset-group' ) || '';
		$plModal.prop( 'hidden', false );
		plRenderBody( '<div class="upw-plib__loading"><span class="spinner is-active"></span></div>' );
		plFetch( 'browse' );
	}

	function plClose() { if ( $plModal ) { $plModal.prop( 'hidden', true ); } }

	function plRenderBody( html ) { $plModal.find( '.upw-plib__body' ).html( html ); }

	function plAjax( libAction, slug ) {
		return $.post( PL.ajaxUrl, {
			action: 'unysonplus_preset_library',
			lib_action: libAction,
			group: plGroup,
			slug: slug || '',
			nonce: PL.nonce
		} );
	}

	function plFetch( libAction ) {
		plAjax( libAction )
			.done( function ( res ) {
				if ( res && res.success && res.data ) {
					plItems = res.data.items || [];
					plRender( res.data.catalogOk );
				} else {
					plRenderBody( '<p class="upw-plib__empty">' + plEsc( PL.i18n.error ) + '</p>' );
				}
			} )
			.fail( function () { plRenderBody( '<p class="upw-plib__empty">' + plEsc( PL.i18n.error ) + '</p>' ); } );
	}

	function plRender( catalogOk ) {
		if ( ! catalogOk ) {
			plRenderBody( '<div class="upw-plib__notice">' + plEsc( PL.i18n.unreachable ) + '</div>' );
			return;
		}
		if ( ! plItems.length ) {
			plRenderBody( '<p class="upw-plib__empty">' + plEsc( PL.i18n.empty ) + '</p>' );
			return;
		}
		var cards = plItems.map( function ( t ) {
			var action = t.installed
				? '<span class="upw-plib__badge">' + plEsc( PL.i18n.installed ) + '</span>' +
				  '<button type="button" class="button button-small upw-plib__remove" data-plib-act="uninstall" data-slug="' + plEsc( t.slug ) + '">' + plEsc( PL.i18n.remove ) + '</button>'
				: '<button type="button" class="button button-primary button-small" data-plib-act="install" data-slug="' + plEsc( t.slug ) + '">' + plEsc( PL.i18n.install ) + '</button>';
			return '' +
				'<div class="upw-plib__card" data-slug="' + plEsc( t.slug ) + '">' +
					'<div class="upw-plib__card-main">' +
						'<span class="upw-plib__card-name">' + plEsc( t.label ) + '</span>' +
						( t.desc ? '<span class="upw-plib__card-desc">' + plEsc( t.desc ) + '</span>' : '' ) +
					'</div>' +
					'<div class="upw-plib__card-action">' + action + '</div>' +
				'</div>';
		} ).join( '' );
		plRenderBody( '<div class="upw-plib__grid">' + cards + '</div>' );
	}

	// Keep the real card grid in sync when a preset is installed/removed, so the
	// user can Apply it immediately without a page reload (the server registry
	// already carries it via the filter).
	function plSyncCard( slug, item, add ) {
		if ( ! $plRoot ) { return; }
		var $cards = $plRoot.find( '.fw-preset-loader-cards' );
		var key = 'lib_' + slug;
		$cards.find( '.fw-preset-card[data-preset-key="' + key + '"]' ).remove();
		if ( add && item ) {
			var $card = $(
				'<button type="button" class="fw-preset-card" data-preset-key="' + plEsc( key ) + '">' +
					'<span class="fw-preset-card__name">' + plEsc( item.label ) + '</span>' +
					( item.desc ? '<span class="fw-preset-card__desc">' + plEsc( item.desc ) + '</span>' : '' ) +
				'</button>'
			);
			var $custom = $cards.find( '.fw-preset-card--custom' );
			if ( $custom.length ) { $card.insertBefore( $custom ); } else { $cards.append( $card ); }
		}
	}

	function plAction( $btn ) {
		var act = $btn.data( 'plib-act' ), slug = $btn.data( 'slug' );
		var item = plItems.filter( function ( t ) { return t.slug === slug; } )[0];
		$btn.prop( 'disabled', true ).text( act === 'install' ? PL.i18n.installing : PL.i18n.removing );
		plAjax( act, slug )
			.done( function ( res ) {
				if ( res && res.success && res.data ) {
					plItems = res.data.items || [];
					plSyncCard( slug, item, act === 'install' );
					plRender( res.data.catalogOk );
				} else {
					$btn.prop( 'disabled', false ).text( act === 'install' ? PL.i18n.install : PL.i18n.remove );
				}
			} )
			.fail( function () { $btn.prop( 'disabled', false ).text( act === 'install' ? PL.i18n.install : PL.i18n.remove ); } );
	}

	$( document ).on( 'click', '.fw-preset-browse', function () { plOpen( rootOf( this ) ); } );

	// Delete an installed library preset directly from its card (the "×" control).
	// stopPropagation keeps the click from also selecting the card.
	$( document ).on( 'click', '.fw-preset-card__del', function ( e ) {
		e.preventDefault();
		e.stopPropagation();
		if ( ! PL ) { return; }
		var $del = $( this ),
			$card = $del.closest( '.fw-preset-card' ),
			$root = rootOf( this ),
			group = $del.attr( 'data-del-group' ),
			slug = $del.attr( 'data-del-slug' );
		if ( ! window.confirm( 'Remove this installed library preset? You can re-install it from Browse Library.' ) ) { return; }
		$del.css( 'opacity', '.4' );
		$.post( PL.ajaxUrl, {
			action: 'unysonplus_preset_library',
			lib_action: 'uninstall',
			group: group,
			slug: slug,
			nonce: PL.nonce
		} ).done( function ( res ) {
			if ( res && res.success ) {
				if ( $card.hasClass( 'is-selected' ) ) {
					$root.find( '.fw-preset-apply' ).prop( 'disabled', true );
				}
				$card.remove();
			} else {
				$del.css( 'opacity', '' );
			}
		} ).fail( function () { $del.css( 'opacity', '' ); } );
	} );

} )( jQuery );
