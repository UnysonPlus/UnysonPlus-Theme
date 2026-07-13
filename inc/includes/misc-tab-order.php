<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }

/**
 * Miscellaneous sub-tab ordering.
 *
 * The Miscellaneous tab mixes theme-defined sub-tabs (misc.php: Developer Tools) with
 * plugin-injected ones (Custom CSS, Custom Scripts, Analytics, Performance, Media, 404,
 * Maintenance, Export/Import, Reset). The plugin merges its sub-tabs AFTER the theme's
 * (helpers.php `upw_ts_merge_into_misc`), so without this pass the theme's Developer
 * Tools would sit at the very top and Custom CSS would trail the theme sub-tabs.
 *
 * This late `fw_settings_options` filter (priority 20 — after the plugin's default-priority
 * merge) reorders the merged set:
 *   1. Custom CSS is pulled to the FRONT (the most-reached-for tab).
 *   2. Developer Tools is dropped right AFTER Export/Import (it's a rarely-touched,
 *      developer-only tab, so it sits near the end by the other admin utilities).
 *
 * Purely positional; each step is an independent, graceful no-op if its tab is absent
 * (e.g. shortcodes extension inactive → no Custom CSS / Export-Import to reorder).
 */

if ( ! function_exists( 'unysonplus_misc_reorder_subtabs' ) ) :
function unysonplus_misc_reorder_subtabs( $options ) {
	if ( ! is_array( $options ) ) { return $options; }

	foreach ( $options as $i => $section ) {
		if ( ! is_array( $section ) || ! isset( $section['misc_container']['options'] ) || ! is_array( $section['misc_container']['options'] ) ) {
			continue;
		}
		foreach ( $section['misc_container']['options'] as $bk => $box ) {
			if ( ! isset( $box['type'], $box['options'] ) || $box['type'] !== 'box' || ! is_array( $box['options'] ) ) {
				continue;
			}
			$tabs = $box['options'];

			// 1) Custom CSS first.
			if ( isset( $tabs['tab_custom_css'] ) ) {
				$css = $tabs['tab_custom_css'];
				unset( $tabs['tab_custom_css'] );
				$tabs = array( 'tab_custom_css' => $css ) + $tabs;
			}

			// 2) Developer Tools right after Export/Import (only when both are present).
			if ( isset( $tabs['tab_dev_tools'], $tabs['tab_export_import'] ) ) {
				$dev = $tabs['tab_dev_tools'];
				unset( $tabs['tab_dev_tools'] );
				$rebuilt = array();
				foreach ( $tabs as $k => $v ) {
					$rebuilt[ $k ] = $v;
					if ( $k === 'tab_export_import' ) {
						$rebuilt['tab_dev_tools'] = $dev; // drop it right after Export/Import
					}
				}
				$tabs = $rebuilt;
			}

			$options[ $i ]['misc_container']['options'][ $bk ]['options'] = $tabs;
			return $options;
		}
	}
	return $options;
}
endif;
add_filter( 'fw_settings_options', 'unysonplus_misc_reorder_subtabs', 20 );
