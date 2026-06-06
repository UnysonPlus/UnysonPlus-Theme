<?php
/**
 * The default page template.
 *
 * Default = no sidebar. Editors who want sidebars assign one of the named
 * templates (Right Sidebar, Left Sidebar, Boxed Narrow, etc.) instead.
 *
 * Page-builder pages skip the wrapper entirely — the builder owns layout.
 *
 * @package Unysonplus
 */

unysonplus_set_layout_override( array( 'sidebar' => 'none' ) );

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
