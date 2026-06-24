<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * Custom post types registered by the theme.
 *
 * Included from inc/init.php during the `init` hook (see
 * Theme_Includes::_action_init), so register_post_type() is called directly here.
 */

if ( ! function_exists( 'unysonplus_register_preset_post_types' ) ) :
/**
 * Header / Footer preset post types.
 *
 * Each preset is one post. Its Unyson option meta box (defined in
 * framework-customizations/theme/options/posts/up_header.php and up_footer.php)
 * reuses the exact same slot UI as the global Theme Settings header/footer, so a
 * preset's stored meta is structurally identical to the global `header_layout` /
 * footer settings options — no migration, and the same render code renders both.
 *
 * Non-public on purpose: a preset must never resolve as a front-end URL or appear
 * in search / sitemaps. Registered with core register_post_type() only, so presets
 * survive when the Unyson+ plugin is inactive (the meta box simply won't render).
 *
 * Phase 1 supports only 'title' (= preset name). 'editor' / REST are intentionally
 * withheld until Phase 2 adds a page-builder build-mode to these same post types.
 * Capabilities map to edit_theme_options so presets are admin-managed.
 */
function unysonplus_register_preset_post_types() {

	// Map only the PRIMITIVE caps to edit_theme_options. With map_meta_cap => true
	// WordPress derives the meta caps (edit_post / read_post / delete_post) from
	// these against a specific post — remapping the meta caps directly triggers a
	// "map_meta_cap was called incorrectly" notice in WP 6.1+.
	$caps = array(
		'edit_posts'             => 'edit_theme_options',
		'edit_others_posts'      => 'edit_theme_options',
		'edit_published_posts'   => 'edit_theme_options',
		'publish_posts'          => 'edit_theme_options',
		'delete_posts'           => 'edit_theme_options',
		'delete_others_posts'    => 'edit_theme_options',
		'delete_published_posts' => 'edit_theme_options',
		'read_private_posts'     => 'edit_theme_options',
		'create_posts'           => 'edit_theme_options',
		'read'                   => 'read',
	);

	$shared = array(
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_in_nav_menus'   => false,
		'show_ui'             => true,
		'show_in_menu'        => 'themes.php',
		'show_in_rest'        => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => false,
		'hierarchical'        => false,
		'map_meta_cap'        => true,
		'capabilities'        => $caps,
		'supports'            => array( 'title' ),
		'menu_icon'           => 'dashicons-layout',
	);

	register_post_type( 'up_header', array_merge( $shared, array(
		'labels' => array(
			'name'               => __( 'Header Presets', 'unysonplus' ),
			'singular_name'      => __( 'Header Preset', 'unysonplus' ),
			'menu_name'          => __( 'Header Presets', 'unysonplus' ),
			'name_admin_bar'     => __( 'Header Preset', 'unysonplus' ),
			'add_new'            => __( 'Add New', 'unysonplus' ),
			'add_new_item'       => __( 'Add New Header Preset', 'unysonplus' ),
			'new_item'           => __( 'New Header Preset', 'unysonplus' ),
			'edit_item'          => __( 'Edit Header Preset', 'unysonplus' ),
			'view_item'          => __( 'View Header Preset', 'unysonplus' ),
			'all_items'          => __( 'Header Presets', 'unysonplus' ),
			'search_items'       => __( 'Search Header Presets', 'unysonplus' ),
			'not_found'          => __( 'No header presets found.', 'unysonplus' ),
			'not_found_in_trash' => __( 'No header presets found in Trash.', 'unysonplus' ),
		),
	) ) );

	register_post_type( 'up_footer', array_merge( $shared, array(
		'labels' => array(
			'name'               => __( 'Footer Presets', 'unysonplus' ),
			'singular_name'      => __( 'Footer Preset', 'unysonplus' ),
			'menu_name'          => __( 'Footer Presets', 'unysonplus' ),
			'name_admin_bar'     => __( 'Footer Preset', 'unysonplus' ),
			'add_new'            => __( 'Add New', 'unysonplus' ),
			'add_new_item'       => __( 'Add New Footer Preset', 'unysonplus' ),
			'new_item'           => __( 'New Footer Preset', 'unysonplus' ),
			'edit_item'          => __( 'Edit Footer Preset', 'unysonplus' ),
			'view_item'          => __( 'View Footer Preset', 'unysonplus' ),
			'all_items'          => __( 'Footer Presets', 'unysonplus' ),
			'search_items'       => __( 'Search Footer Presets', 'unysonplus' ),
			'not_found'          => __( 'No footer presets found.', 'unysonplus' ),
			'not_found_in_trash' => __( 'No footer presets found in Trash.', 'unysonplus' ),
		),
	) ) );
}
endif;

// When the Header & Footer Builder plugin extension is active it OWNS these CPTs
// (registers them with page-builder 'editor' support). The theme only registers
// them as a title-only fallback when that extension is absent, so previously
// assigned presets still resolve and the resolver degrades to the slot Default.
if ( ! defined( 'UP_HFBUILDER_OWNS_CPTS' ) ) {
	unysonplus_register_preset_post_types();
}
