<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 * @package Unysonplus
 */

get_header();
unysonplus_main_wrapper_open( 'content-area' );

if ( have_posts() ) :

	/**
	 * Fires before the search results title (page-header) renders.
	 */
	do_action( 'unysonplus_before_archive_title' );
	unysonplus_render_archive_header();
	/**
	 * Fires after the search results title.
	 */
	do_action( 'unysonplus_after_archive_title' );

	/**
	 * Fires before the search results loop starts.
	 */
	do_action( 'unysonplus_before_loop' );

	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'search' );
	endwhile;

	/**
	 * Fires after the search results loop ends.
	 */
	do_action( 'unysonplus_after_loop' );

else :

	get_template_part( 'template-parts/content', 'none' );

endif;

unysonplus_main_wrapper_close();
get_footer();
