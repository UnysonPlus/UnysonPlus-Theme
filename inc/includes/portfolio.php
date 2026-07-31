<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Theme Settings → Portfolio bridge.
 *
 * The Portfolio extension reads every display setting through
 * FW_Extension_Portfolio::get_setting(), which runs the value through the
 * fw:ext:portfolio:setting filter. This file supplies the theme's values from
 * the Theme Settings → Portfolio tab (portfolio-archive.php /
 * portfolio-single.php option files): a saved value overrides the extension's
 * own Settings page; 'inherit' / empty leaves the extension value in charge.
 * Theme-only display knobs with no extension-side field (archive_gap,
 * archive_ratio, archive_hover, card_show_category, card_show_summary) reach
 * the extension the same way — get_setting()'s code default applies until the
 * theme supplies a value here.
 */

if ( ! function_exists( 'unysonplus_portfolio_get' ) ) :
/**
 * Read one Theme Settings → Portfolio value (both sub-tab multis merged).
 * Returns $default when unset, empty, or set to 'inherit'.
 *
 * @param string $key
 * @param mixed  $default
 *
 * @return mixed
 */
function unysonplus_portfolio_get( $key, $default = null ) {
	static $cache = null;

	if ( null === $cache ) {
		$cache = array();
		if ( function_exists( 'fw_get_db_settings_option' ) ) {
			foreach ( array( 'portfolio_archive', 'portfolio_single' ) as $group ) {
				$cache = array_merge( $cache, (array) fw_get_db_settings_option( $group, array() ) );
			}
		}
	}

	if ( ! array_key_exists( $key, $cache ) ) {
		return $default;
	}

	$val = $cache[ $key ];

	return ( '' === $val || null === $val || 'inherit' === $val ) ? $default : $val;
}
endif;

if ( ! function_exists( 'unysonplus_portfolio_setting_override' ) ) :
/**
 * fw:ext:portfolio:setting bridge — see the file docblock.
 *
 * @param mixed  $value   The extension-resolved value.
 * @param string $key     Setting key.
 * @param mixed  $default The caller's default.
 *
 * @return mixed
 */
function unysonplus_portfolio_setting_override( $value, $key, $default ) {
	$theme_value = unysonplus_portfolio_get( $key, null );

	return ( null === $theme_value ) ? $value : $theme_value;
}
endif;

add_filter( 'fw:ext:portfolio:setting', 'unysonplus_portfolio_setting_override', 10, 3 );

if ( ! function_exists( 'unysonplus_portfolio_preset_group' ) ) :
/**
 * Persona presets for the Theme Settings → Portfolio → Presets loader. One
 * click applies a whole archive/cards look tuned to a portfolio persona; the
 * user then fine-tunes under Archive & Cards. Registered into the shared
 * settings-preset registry (inc/includes/settings-presets.php).
 *
 * @param array $groups
 *
 * @return array
 */
function unysonplus_portfolio_preset_group( $groups ) {
	$keys = array(
		'archive_columns', 'archive_per_page', 'orderby', 'order', 'featured_first',
		'archive_filter_bar', 'archive_gap', 'archive_ratio', 'archive_hover',
		'card_show_category', 'card_show_summary',
	);

	$p = function ( $over = array() ) {
		return array_merge( array(
			'archive_columns'    => '3',
			'archive_per_page'   => '12',
			'orderby'            => 'date',
			'order'              => 'DESC',
			'featured_first'     => 'yes',
			'archive_filter_bar' => 'yes',
			'archive_gap'        => '24',
			'archive_ratio'      => '4-3',
			'archive_hover'      => 'zoom',
			'card_show_category' => 'no',
			'card_show_summary'  => 'yes',
		), $over );
	};

	$groups['portfolio_archive'] = array(
		'label'        => __( 'Portfolio', 'unysonplus' ),
		'allowed_keys' => $keys,
		'presets'      => array(
			'case_study' => array(
				'label'  => __( 'Case Study (Designer)', 'unysonplus' ),
				'desc'   => __( 'Two roomy columns with category + summary on the cards — a curated, story-led work index.', 'unysonplus' ),
				'values' => $p( array(
					'archive_columns'    => '2',
					'archive_gap'        => '32',
					'card_show_category' => 'yes',
				) ),
			),
			'photographer' => array(
				'label'  => __( 'Gallery (Photographer)', 'unysonplus' ),
				'desc'   => __( 'Three tight columns, original image proportions, no text noise — the images carry the page.', 'unysonplus' ),
				'values' => $p( array(
					'archive_gap'        => '12',
					'archive_ratio'      => 'auto',
					'archive_hover'      => 'none',
					'archive_per_page'   => '24',
					'card_show_summary'  => 'no',
				) ),
			),
			'developer' => array(
				'label'  => __( 'Cards (Developer)', 'unysonplus' ),
				'desc'   => __( 'Wide 16:9 cards with category + summary — project cards that read like release notes.', 'unysonplus' ),
				'values' => $p( array(
					'archive_ratio'      => '16-9',
					'card_show_category' => 'yes',
				) ),
			),
			'agency' => array(
				'label'  => __( 'Showcase (Agency)', 'unysonplus' ),
				'desc'   => __( 'Overlay captions sliding over the imagery, featured work floated first — a polished client-facing showcase.', 'unysonplus' ),
				'values' => $p( array(
					'archive_hover'      => 'overlay',
					'card_show_category' => 'yes',
					'card_show_summary'  => 'no',
				) ),
			),
		),
	);

	return $groups;
}
endif;

add_filter( 'unysonplus_settings_preset_groups', 'unysonplus_portfolio_preset_group' );
