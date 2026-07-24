<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Preset Library — browse + install downloadable presets from UnysonPlus-Library.
 *
 * This augments the existing `preset-loader` system (settings-presets.php): a
 * remote catalog of presets is fetched from the shared content repo, each preset
 * is downloaded on demand into wp-content/uploads/unysonplus-presets/<group>/, and
 * installed presets are injected into the `unysonplus_settings_preset_groups`
 * registry via its filter — so they render as ordinary cards and are applied by the
 * EXISTING apply flow (`unysonplus_apply_settings_preset`). No new apply code.
 *
 * A distributable preset file is just the values map a group's "Export current"
 * produces: { <leaf_key>: <value>, … }. The catalog tags each entry with its
 * `group` (e.g. header_layout) so a group's browse modal shows only its own presets.
 *
 * Mirrors the Template Library installer pattern (catalog.json → wp_remote_get →
 * 12h transient → atomic install into uploads).
 */

/* -----------------------------------------------------------------------------
 * Paths + catalog
 * -------------------------------------------------------------------------- */

if ( ! function_exists( 'unysonplus_preset_library_install_dir' ) ) :
	/** Absolute path of the uploads install dir (no trailing slash). Filterable. */
	function unysonplus_preset_library_install_dir() {
		$up = wp_upload_dir();
		return apply_filters( 'unysonplus_preset_library_install_dir', trailingslashit( $up['basedir'] ) . 'unysonplus-presets' );
	}
endif;

if ( ! function_exists( 'unysonplus_preset_library_catalog_url' ) ) :
	/** URL of the presets catalog.json in the shared library repo. Filterable. */
	function unysonplus_preset_library_catalog_url() {
		return apply_filters(
			'unysonplus_preset_library_catalog_url',
			'https://raw.githubusercontent.com/UnysonPlus/UnysonPlus-Library/master/presets/catalog.json'
		);
	}
endif;

if ( ! function_exists( 'unysonplus_preset_library_catalog' ) ) :
	/**
	 * Fetch + decode the remote presets catalog, cached in a 12h transient (5-min
	 * negative cache on failure). Returns { version, base_url, presets:{ slug => {…} } }.
	 *
	 * @param bool $force Bypass the transient.
	 */
	function unysonplus_preset_library_catalog( $force = false ) {
		$key = 'unysonplus_preset_library_catalog';

		if ( ! $force ) {
			$cached = get_transient( $key );
			if ( is_array( $cached ) ) { return $cached; }
		}

		$empty = array( 'version' => 0, 'base_url' => '', 'presets' => array() );

		$res = wp_remote_get( unysonplus_preset_library_catalog_url(), array( 'timeout' => 15 ) );
		if ( is_wp_error( $res ) || (int) wp_remote_retrieve_response_code( $res ) !== 200 ) {
			set_transient( $key, $empty, 5 * MINUTE_IN_SECONDS );
			return $empty;
		}

		$json = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( ! is_array( $json ) || empty( $json['presets'] ) || ! is_array( $json['presets'] ) ) {
			set_transient( $key, $empty, 5 * MINUTE_IN_SECONDS );
			return $empty;
		}

		$catalog = array(
			'version'  => isset( $json['version'] ) ? (int) $json['version'] : 1,
			'base_url' => isset( $json['base_url'] ) ? trailingslashit( (string) $json['base_url'] ) : '',
			'presets'  => array(),
		);

		foreach ( $json['presets'] as $slug => $p ) {
			$slug  = sanitize_key( $slug );
			$group = isset( $p['group'] ) ? sanitize_key( $p['group'] ) : '';
			if ( $slug === '' || $group === '' || ! is_array( $p ) ) { continue; }
			$catalog['presets'][ $slug ] = array(
				'slug'  => $slug,
				'group' => $group,
				'label' => isset( $p['label'] ) ? (string) $p['label'] : ucwords( str_replace( '-', ' ', $slug ) ),
				'desc'  => isset( $p['desc'] ) ? (string) $p['desc'] : '',
			);
		}

		set_transient( $key, $catalog, 12 * HOUR_IN_SECONDS );
		return $catalog;
	}
endif;

/* -----------------------------------------------------------------------------
 * Installed presets (local scan — no network)
 * -------------------------------------------------------------------------- */

if ( ! function_exists( 'unysonplus_preset_library_installed_for_group' ) ) :
	/**
	 * Installed presets for one group, keyed `lib_<slug>` => { label, desc, values }.
	 * Cached per request (the registry filter calls this for every group on each load).
	 */
	function unysonplus_preset_library_installed_for_group( $group ) {
		static $cache = array();
		$group = sanitize_key( $group );
		if ( isset( $cache[ $group ] ) ) { return $cache[ $group ]; }

		$out = array();
		$dir = trailingslashit( unysonplus_preset_library_install_dir() ) . $group;
		if ( is_dir( $dir ) ) {
			foreach ( (array) glob( trailingslashit( $dir ) . '*.json' ) as $file ) {
				$data = json_decode( (string) file_get_contents( $file ), true );
				if ( ! is_array( $data ) || empty( $data['values'] ) || ! is_array( $data['values'] ) ) { continue; }
				$slug = isset( $data['slug'] ) ? sanitize_key( $data['slug'] ) : sanitize_key( basename( $file, '.json' ) );
				if ( $slug === '' ) { continue; }
				$out[ 'lib_' . $slug ] = array(
					'label'  => isset( $data['label'] ) ? (string) $data['label'] : ucwords( str_replace( '-', ' ', $slug ) ),
					'desc'   => isset( $data['desc'] ) ? (string) $data['desc'] : '',
					'values' => $data['values'],
				);
			}
		}

		$cache[ $group ] = $out;
		return $out;
	}
endif;

if ( ! function_exists( 'unysonplus_preset_library_installed_slugs' ) ) :
	/** Bare installed slugs for a group (for the browse modal's "Installed" state). */
	function unysonplus_preset_library_installed_slugs( $group ) {
		$slugs = array();
		foreach ( array_keys( unysonplus_preset_library_installed_for_group( $group ) ) as $k ) {
			$slugs[] = preg_replace( '/^lib_/', '', $k );
		}
		return $slugs;
	}
endif;

/**
 * Inject installed library presets into the preset registry, so they render as
 * ordinary cards and are applied by the existing handler. Priority 20 so the
 * built-in presets are already in place.
 */
add_filter( 'unysonplus_settings_preset_groups', function ( $groups ) {
	if ( ! is_array( $groups ) ) { return $groups; }
	foreach ( $groups as $group => &$conf ) {
		if ( empty( $conf['presets'] ) || ! is_array( $conf['presets'] ) ) { continue; }
		foreach ( unysonplus_preset_library_installed_for_group( $group ) as $key => $p ) {
			$conf['presets'][ $key ] = array(
				'label'  => $p['label'],
				'desc'   => $p['desc'],
				'values' => $p['values'],
			);
		}
	}
	unset( $conf );
	return $groups;
}, 20 );

/* -----------------------------------------------------------------------------
 * Install / uninstall
 * -------------------------------------------------------------------------- */

if ( ! function_exists( 'unysonplus_preset_library_install' ) ) :
	/**
	 * Download one preset from the catalog into uploads/unysonplus-presets/<group>/.
	 * The remote preset file is the plain values map; we wrap it with label/desc/group.
	 *
	 * @param string $slug
	 * @return true|WP_Error
	 */
	function unysonplus_preset_library_install( $slug ) {
		$slug = sanitize_key( $slug );
		if ( $slug === '' ) { return new WP_Error( 'bad_slug', __( 'Invalid preset.', 'unysonplus' ) ); }

		$catalog = unysonplus_preset_library_catalog();
		if ( empty( $catalog['presets'][ $slug ] ) ) {
			return new WP_Error( 'not_in_catalog', __( 'That preset is not in the library.', 'unysonplus' ) );
		}
		if ( $catalog['base_url'] === '' ) {
			return new WP_Error( 'no_base_url', __( 'Library catalog is missing a base URL.', 'unysonplus' ) );
		}

		$entry  = $catalog['presets'][ $slug ];
		$group  = $entry['group'];
		$values = unysonplus_preset_library__fetch_json( $catalog['base_url'] . $slug . '.json' );
		if ( is_wp_error( $values ) ) { return $values; }
		if ( empty( $values ) || ! is_array( $values ) || array_values( $values ) === $values ) {
			// Must be a non-empty associative values map, not a list.
			return new WP_Error( 'bad_preset', __( 'The preset file is not a valid values map.', 'unysonplus' ) );
		}

		$root = unysonplus_preset_library_install_dir();
		$dir  = trailingslashit( $root ) . $group;
		if ( ! wp_mkdir_p( $dir ) ) {
			return new WP_Error( 'mkdir_failed', __( 'Could not create the presets folder.', 'unysonplus' ) );
		}

		$payload = array(
			'slug'      => $slug,
			'group'     => $group,
			'label'     => $entry['label'],
			'desc'      => $entry['desc'],
			'values'    => $values,
			'installed' => gmdate( 'c' ),
		);

		// Atomic: write to temp then rename.
		$dest = trailingslashit( $dir ) . $slug . '.json';
		$tmp  = $dest . '.tmp-' . wp_generate_password( 6, false );
		if ( false === file_put_contents( $tmp, wp_json_encode( $payload ) ) ) {
			@unlink( $tmp );
			return new WP_Error( 'write_failed', __( 'Could not write the preset file.', 'unysonplus' ) );
		}
		if ( ! @rename( $tmp, $dest ) ) {
			@unlink( $tmp );
			return new WP_Error( 'install_failed', __( 'Could not finalize the install.', 'unysonplus' ) );
		}

		return true;
	}
endif;

if ( ! function_exists( 'unysonplus_preset_library_uninstall' ) ) :
	/**
	 * Remove an installed library preset.
	 *
	 * @param string $group
	 * @param string $slug
	 * @return true|WP_Error
	 */
	function unysonplus_preset_library_uninstall( $group, $slug ) {
		$group = sanitize_key( $group );
		$slug  = sanitize_key( $slug );
		$file  = trailingslashit( unysonplus_preset_library_install_dir() ) . $group . '/' . $slug . '.json';
		if ( $group === '' || $slug === '' || ! is_readable( $file ) ) {
			return new WP_Error( 'not_installed', __( 'That preset is not installed.', 'unysonplus' ) );
		}
		@unlink( $file );
		return true;
	}
endif;

if ( ! function_exists( 'unysonplus_preset_library__fetch_json' ) ) :
	/** GET a URL and json_decode it. Returns array|WP_Error. */
	function unysonplus_preset_library__fetch_json( $url ) {
		$res = wp_remote_get( $url, array( 'timeout' => 20 ) );
		if ( is_wp_error( $res ) ) { return $res; }
		if ( (int) wp_remote_retrieve_response_code( $res ) !== 200 ) {
			return new WP_Error( 'http_' . wp_remote_retrieve_response_code( $res ), __( 'Download failed.', 'unysonplus' ) );
		}
		$json = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( ! is_array( $json ) ) { return new WP_Error( 'bad_json', __( 'Downloaded file was not valid JSON.', 'unysonplus' ) ); }
		return $json;
	}
endif;

/* -----------------------------------------------------------------------------
 * Browse data + AJAX (admin only, manage_options + nonce)
 * -------------------------------------------------------------------------- */

if ( ! function_exists( 'unysonplus_preset_library_browse_items' ) ) :
	/**
	 * Catalog entries for one group + which are installed, for the browse modal.
	 * Each item: { slug, label, desc, installed:bool }.
	 */
	function unysonplus_preset_library_browse_items( $group ) {
		$group     = sanitize_key( $group );
		$catalog   = unysonplus_preset_library_catalog();
		$installed = unysonplus_preset_library_installed_slugs( $group );
		$items     = array();
		foreach ( ( isset( $catalog['presets'] ) ? $catalog['presets'] : array() ) as $slug => $p ) {
			if ( $p['group'] !== $group ) { continue; }
			$items[] = array(
				'slug'      => $slug,
				'label'     => $p['label'],
				'desc'      => $p['desc'],
				'installed' => in_array( $slug, $installed, true ),
			);
		}
		usort( $items, function ( $a, $b ) { return strcasecmp( $a['label'], $b['label'] ); } );
		return array(
			'items'     => $items,
			'catalogOk' => ! empty( $catalog['presets'] ),
		);
	}
endif;

add_action( 'wp_ajax_unysonplus_preset_library', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'unysonplus' ) ), 403 );
	}
	check_ajax_referer( 'unysonplus_preset_library', 'nonce' );

	$action = isset( $_POST['lib_action'] ) ? sanitize_key( $_POST['lib_action'] ) : '';
	$group  = isset( $_POST['group'] ) ? sanitize_key( $_POST['group'] ) : '';
	$slug   = isset( $_POST['slug'] ) ? sanitize_key( $_POST['slug'] ) : '';

	if ( $action === 'browse' ) {
		$r = unysonplus_preset_library_browse_items( $group );
		wp_send_json_success( $r );
	} elseif ( $action === 'install' ) {
		$r = unysonplus_preset_library_install( $slug );
	} elseif ( $action === 'uninstall' ) {
		$r = unysonplus_preset_library_uninstall( $group, $slug );
	} elseif ( $action === 'refresh' ) {
		unysonplus_preset_library_catalog( true );
		$r = true;
	} else {
		$r = new WP_Error( 'bad_action', __( 'Unknown action.', 'unysonplus' ) );
	}

	if ( is_wp_error( $r ) ) {
		wp_send_json_error( array( 'message' => $r->get_error_message() ) );
	}

	// After install/uninstall/refresh: return the fresh browse list for the group.
	wp_send_json_success( unysonplus_preset_library_browse_items( $group ) );
} );

if ( ! function_exists( 'unysonplus_preset_library_localize' ) ) :
	/**
	 * Hand the preset-loader script the config the browse modal needs. Called from
	 * the option type's _enqueue_static (so it runs exactly when the script loads).
	 */
	function unysonplus_preset_library_localize( $handle ) {
		wp_localize_script( $handle, 'upwPresetLibrary', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'unysonplus_preset_library' ),
			'i18n'    => array(
				'browse'      => __( 'Browse Library', 'unysonplus' ),
				'title'       => __( 'Preset Library', 'unysonplus' ),
				'close'       => __( 'Close', 'unysonplus' ),
				'install'     => __( 'Install', 'unysonplus' ),
				'installed'   => __( 'Installed', 'unysonplus' ),
				'remove'      => __( 'Remove', 'unysonplus' ),
				'installing'  => __( 'Installing…', 'unysonplus' ),
				'removing'    => __( 'Removing…', 'unysonplus' ),
				'refresh'     => __( 'Refresh', 'unysonplus' ),
				'empty'       => __( 'No library presets for this section yet.', 'unysonplus' ),
				'unreachable' => __( 'The preset library is unreachable right now. Try Refresh later.', 'unysonplus' ),
				'installedHint' => __( 'Installed — it now appears as a card above, ready to Apply.', 'unysonplus' ),
				'error'       => __( 'Something went wrong. Please try again.', 'unysonplus' ),
			),
		) );
	}
endif;
