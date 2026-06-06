<?php
/**
 * Template Name: Left Sidebar
 *
 * Forces a left sidebar on this page (overrides the General → Layout default).
 *
 * @package Unysonplus
 */

unysonplus_set_layout_override( array( 'sidebar' => 'left' ) );

get_header();
unysonplus_main_wrapper_open();
?>
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content', 'page' );
		}
	} else {
		get_template_part( 'template-parts/content', 'none' );
	}
	?>
<?php
unysonplus_main_wrapper_close();
get_footer();
