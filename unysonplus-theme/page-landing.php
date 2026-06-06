<?php
/**
 * Template Name: Landing Page
 *
 * Strips header, footer, page title, and sidebar. Full-width edge-to-edge —
 * intended for campaign landing pages built with the page builder.
 *
 * @package Unysonplus
 */

unysonplus_set_layout_override( array(
	'sidebar'     => 'none',
	'width'       => 'full',
	'hide_header' => true,
	'hide_footer' => true,
	'hide_title'  => true,
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
