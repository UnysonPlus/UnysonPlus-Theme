<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }

/**
 * Theme settings schema migrations — single versioned runner.
 *
 * Replaces the previously scattered per-migration `admin_init` hooks. Each
 * migration is a callback keyed by the schema version it brings the install UP
 * TO. On admin load, every migration whose version is newer than the stored
 * `unysonplus_schema_version` runs once, in ascending order, then the stored
 * version is advanced to UNYSONPLUS_SCHEMA_VERSION.
 *
 * The migration callbacks live next to the code they migrate
 * (`unysonplus_migrate_layout_settings` in inc/includes/layout.php,
 * `unysonplus_migrate_header_layout` in inc/includes/header-footer-presets.php)
 * and are each individually idempotent — so re-running them (or a brand-new
 * install that never had the old data shape) is a safe no-op. The version gate is
 * the fast path; the per-migration guards are the correctness backstop.
 *
 * Adding a migration: write an idempotent callback, register it as
 * `<n> => 'callback'` in unysonplus_schema_migrations(), and bump
 * UNYSONPLUS_SCHEMA_VERSION to <n>.
 */

if ( ! defined( 'UNYSONPLUS_SCHEMA_VERSION' ) ) {
	define( 'UNYSONPLUS_SCHEMA_VERSION', 3 );
}

if ( ! function_exists( 'unysonplus_schema_migrations' ) ) :
/**
 * Map of schema version => migration callback. Each callback upgrades stored
 * settings/preset data TO that version.
 *
 * @return array<int,string>
 */
function unysonplus_schema_migrations() {
	return array(
		1 => 'unysonplus_migrate_layout_settings',  // General → Layout split (sidebar / preloader / scroll) + header_mode / vertical_width move
		2 => 'unysonplus_migrate_header_layout',    // header_layout blob → header_topbar / header_main / header_bottombar
		3 => 'unysonplus_migrate_width_mode',       // Site Width Mode flat → multi-picker; drop layout_container_max_width
	);
}
endif;

if ( ! function_exists( 'unysonplus_run_schema_migrations' ) ) :
/**
 * Run any pending migrations once, then record the new schema version.
 */
function unysonplus_run_schema_migrations() {
	$current = (int) get_option( 'unysonplus_schema_version', 0 );
	$target  = (int) UNYSONPLUS_SCHEMA_VERSION;
	if ( $current >= $target ) {
		return; // up to date — fast path, no work.
	}

	$migrations = unysonplus_schema_migrations();
	ksort( $migrations );
	foreach ( $migrations as $version => $callback ) {
		if ( $current < (int) $version && function_exists( $callback ) ) {
			call_user_func( $callback );
		}
	}

	update_option( 'unysonplus_schema_version', $target, false );
}
endif;
add_action( 'admin_init', 'unysonplus_run_schema_migrations' );
