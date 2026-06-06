<?php
/**
 * Template Name: No Footer
 *
 * Hides the site footer on this page. Header still renders.
 *
 * @package Unysonplus
 */

unysonplus_set_layout_override( array(
	'sidebar'     => 'none',
	'hide_footer' => true,
) );

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
