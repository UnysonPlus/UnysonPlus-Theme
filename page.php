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

// Builder pages carry no <article> (content-page.php drops it), so <main> IS the page's
// own content element — hand it the page's post_class() hooks (post-N / type-page / hentry
// / …) so plugins and CSS that key off .post-<id> etc. keep working. Classic pages keep
// their <article>, so <main> stays without them. (Resolved from the queried object, since
// the loop's the_post() has not run yet at this point.)
$upw_main_extra = unysonplus_is_page_builder_post()
	? implode( ' ', get_post_class( '', get_queried_object_id() ) )
	: '';
unysonplus_main_wrapper_open( $upw_main_extra );
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
