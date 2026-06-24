<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package Unysonplus
 */

$is_builder = function_exists( 'fw_ext_page_builder_is_builder_post' )
	&& fw_ext_page_builder_is_builder_post( get_the_ID() );

$hide_title          = function_exists( 'unysonplus_should_hide_page_title' ) && unysonplus_should_hide_page_title();
$hide_featured       = function_exists( 'fw_get_db_post_option' )
	&& fw_get_db_post_option( get_the_ID(), 'hide_featured_image' );
// Global "Show Featured Image on Pages" toggle (Pages → Defaults, default yes);
// a per-page hide_featured_image meta still wins.
$pages_featured_on   = ! function_exists( 'unysonplus_pages_get' )
	|| unysonplus_pages_get( 'pages_show_featured_image', 'yes' ) !== 'no';
$has_thumbnail       = $pages_featured_on && ! $hide_featured && has_post_thumbnail();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'col-12' ); ?>>
	<?php
	/** Fires at the top of <article>. */
	do_action( 'unysonplus_entry_top' );
	?>

	<?php if ( ! $is_builder ) : ?>
		<?php
		// Prefer the new hero header (per-page meta or global default).
		// Returns false when no hero image is configured — fall back to the
		// classic title-only header in that case.
		$hero_rendered = function_exists( 'unysonplus_render_page_hero' )
			? unysonplus_render_page_hero()
			: false;
		?>

		<?php if ( ! $hero_rendered ) : ?>
			<?php
			$page_header_attr = array( 'class' => 'entry-header' );
			if ( $has_thumbnail ) {
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				if ( $thumbnail ) {
					$page_header_attr['style'] = 'background-image: url("' . esc_url( $thumbnail[0] ) . '"); background-size: cover; background-position: center;';
				}
			}
			?>
			<header <?php echo unysonplus_attr_to_html( $page_header_attr ); ?>>
				<?php do_action( 'unysonplus_entry_header' ); ?>
			</header><!-- .entry-header -->
		<?php endif; ?>
	<?php endif; ?>

	<?php
	/** Fires before .entry-content opens. */
	do_action( 'unysonplus_before_entry_content' );
	?>
	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'unysonplus' ),
			'after'  => '</div>',
		) );
		?>
	</div><!-- .entry-content -->
	<?php
	/** Fires after .entry-content closes. */
	do_action( 'unysonplus_after_entry_content' );
	?>

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
					/* translators: %s: Name of current post */
					esc_html__( 'Edit %s', 'unysonplus' ),
					the_title( '<span class="screen-reader-text">"', '"</span>', false )
				),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>

	<?php
	/** Fires at the bottom of <article>. */
	do_action( 'unysonplus_entry_bottom' );
	?>
</article><!-- #post-## -->
