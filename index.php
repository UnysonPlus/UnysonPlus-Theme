<?php
/**
 * The main template file (blog index fallback).
 *
 * @package Unysonplus
 */

get_header();
unysonplus_main_wrapper_open();

if ( have_posts() ) :

	/**
	 * Fires before the post loop starts.
	 */
	do_action( 'unysonplus_before_loop' );

	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', get_post_format() );
	endwhile;

	/**
	 * Fires after the post loop ends.
	 */
	do_action( 'unysonplus_after_loop' );

else :

	get_template_part( 'template-parts/content', 'none' );

endif;

unysonplus_main_wrapper_close();
get_footer();
