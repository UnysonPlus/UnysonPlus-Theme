<?php
/**
 * Template Name: Right Sidebar
 *
 * Forces a right sidebar on this page (overrides the General → Layout default).
 * Per-page meta "Sidebar Position" can still override this back to none/left.
 *
 * @package Unysonplus
 */

unysonplus_set_layout_override( array( 'sidebar' => 'right' ) );

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
