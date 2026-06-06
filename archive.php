<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package Unysonplus
 */

get_header();
unysonplus_main_wrapper_open( 'content-area' );

if ( have_posts() ) :

	/**
	 * Fires before the archive page title (page-header) renders.
	 */
	do_action( 'unysonplus_before_archive_title' );
	unysonplus_render_archive_header();
	/**
	 * Fires after the archive page title (page-header) renders.
	 */
	do_action( 'unysonplus_after_archive_title' );

	/**
	 * Fires before the post loop starts.
	 */
	do_action( 'unysonplus_before_loop' );

	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', get_post_format() );
	endwhile;

	/**
	 * Fires after the post loop ends (before pagination).
	 */
	do_action( 'unysonplus_after_loop' );

else :

	get_template_part( 'template-parts/content', 'none' );

endif;

unysonplus_main_wrapper_close();
get_footer();
