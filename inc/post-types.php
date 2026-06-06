<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * Define custom posts and taxonomies
 */

/**
 * Register a book post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */

/*$labels = array(
	'name'               => __( 'Books', 'unysonplus' ),
	'singular_name'      => __( 'Book', 'unysonplus' ),
	'menu_name'          => __( 'Books', 'unysonplus' ),
	'name_admin_bar'     => __( 'Book', 'unysonplus' ),
	'add_new'            => __( 'Add New', 'unysonplus' ),
	'add_new_item'       => __( 'Add New Book', 'unysonplus' ),
	'new_item'           => __( 'New Book', 'unysonplus' ),
	'edit_item'          => __( 'Edit Book', 'unysonplus' ),
	'view_item'          => __( 'View Book', 'unysonplus' ),
	'all_items'          => __( 'All Books', 'unysonplus' ),
	'search_items'       => __( 'Search Books', 'unysonplus' ),
	'parent_item_colon'  => __( 'Parent Books:', 'unysonplus' ),
	'not_found'          => __( 'No books found.', 'unysonplus' ),
	'not_found_in_trash' => __( 'No books found in Trash.', 'unysonplus' )
);

$args = array(
	'labels'             => $labels,
	'public'             => true,
	'publicly_queryable' => true,
	'show_ui'            => true,
	'show_in_menu'       => true,
	'query_var'          => true,
	'rewrite'            => array( 'slug' => 'book' ),
	'capability_type'    => 'post',
	'has_archive'        => true,
	'hierarchical'       => false,
	'menu_position'      => null,
	//'menu_icon'					 => 'dashicons-tablet';
	'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
);

register_post_type( 'book', $args );*/

/**
 * Register a genre taxonomy.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
 */

/*$labels = array(
	'name'              => __( 'Genres', 'unysonplus' ),
	'singular_name'     => __( 'Genre', 'unysonplus' ),
	'search_items'      => __( 'Search Genres', 'unysonplus' ),
	'all_items'         => __( 'All Genres', 'unysonplus' ),
	'parent_item'       => __( 'Parent Genre', 'unysonplus' ),
	'parent_item_colon' => __( 'Parent Genre', 'unysonplus' ) . ':',
	'edit_item'         => __( 'Edit Genre', 'unysonplus' ),
	'update_item'       => __( 'Update Genre', 'unysonplus' ),
	'add_new_item'      => __( 'Add New Genre', 'unysonplus' ),
	'new_item_name'     => __( 'New Genre Name', 'unysonplus' ),
	'menu_name'         => __( 'Genre', 'unysonplus' ),
);

$args = array(
	'hierarchical'      => true,
	'labels'            => $labels,
	'show_ui'           => true,
	'show_admin_column' => true,
	'query_var'         => true,
	'rewrite'           => array( 'slug' => 'genre' ),
);

register_taxonomy( 'genre', array( 'book' ), $args );*/