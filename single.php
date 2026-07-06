<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 * @package Unysonplus
 */

get_header();
unysonplus_main_wrapper_open( 'content-area' );

while ( have_posts() ) :
	the_post();

	do_action( 'unysonplus_before_entry' );

	get_template_part( 'template-parts/content', 'single' );

	do_action( 'unysonplus_after_entry' );

	if ( ( comments_open() || get_comments_number() )
		&& ( ! function_exists( 'unysonplus_single_comments_enabled' ) || unysonplus_single_comments_enabled() ) ) {
		comments_template();
	}

endwhile;

unysonplus_main_wrapper_close();
get_footer();
