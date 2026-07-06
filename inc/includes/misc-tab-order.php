<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }

/**
 * Miscellaneous sub-tab ordering.
 *
 * The Miscellaneous tab mixes theme-defined sub-tabs (misc.php: Dark Mode, Developer
 * Tools) with plugin-injected ones (Custom CSS, Performance, Media, Export/Import,
 * Reset, …). The plugin merges its sub-tabs AFTER the theme's, so the theme's
 * Developer Tools would otherwise sit near the top. This late `fw_settings_options`
 * filter (priority 20 — after the plugin's default-priority merge) repositions the
 * theme's `tab_dev_tools` to sit right AFTER the plugin's `tab_export_import`.
 *
 * Purely positional; graceful no-op if either tab is absent (e.g. shortcodes
 * extension inactive → no Export/Import to anchor to).
 */

if ( ! function_exists( 'unysonplus_misc_reorder_dev_tools' ) ) :
function unysonplus_misc_reorder_dev_tools( $options ) {
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
			if ( ! isset( $tabs['tab_dev_tools'] ) || ! isset( $tabs['tab_export_import'] ) ) {
				return $options; // nothing to reorder (or anchor missing)
			}

			$dev = $tabs['tab_dev_tools'];
			unset( $tabs['tab_dev_tools'] );

			$rebuilt = array();
			foreach ( $tabs as $k => $v ) {
				$rebuilt[ $k ] = $v;
				if ( $k === 'tab_export_import' ) {
					$rebuilt['tab_dev_tools'] = $dev; // drop it right after Export/Import
				}
			}

			$options[ $i ]['misc_container']['options'][ $bk ]['options'] = $rebuilt;
			return $options;
		}
	}
	return $options;
}
endif;
add_filter( 'fw_settings_options', 'unysonplus_misc_reorder_dev_tools', 20 );
