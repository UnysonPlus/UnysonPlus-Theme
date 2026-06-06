<?php
/**
 * Default post-format content template — the listing card (used by the loop).
 *
 * Driven by Theme Settings → Blog → Blog Index (read via unysonplus_blog_get()).
 * The .posts-list wrapper around the loop, the meta row, pagination, excerpt
 * length and "Read more" text are wired in inc/includes/blog.php through the
 * loop hooks; this card handles featured image, header and the content body.
 *
 * @package Unysonplus
 */

$bp_get = function_exists( 'unysonplus_blog_get' )
	? 'unysonplus_blog_get'
	: function ( $k, $d = '' ) { return $d; };

$layout       = function_exists( 'unysonplus_blog_current_layout' ) ? unysonplus_blog_current_layout() : $bp_get( 'blog_layout', 'list' );
$show_image   = $bp_get( 'blog_featured_image', 'yes' ) !== 'no';
$ratio        = $bp_get( 'blog_image_ratio', '16-9' );
$content_type = $bp_get( 'blog_content', 'excerpt' );

$card_class = ( $layout === 'list' ) ? 'post-entry post-entry--list' : 'post-entry post-entry--card';
$is_builder = function_exists( 'fw_ext_page_builder_is_builder_post' ) && fw_ext_page_builder_is_builder_post( get_the_ID() );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $card_class ); ?>>
	<?php
	/** Fires at the top of <article>, before any entry content renders. */
	do_action( 'unysonplus_entry_top' );
	?>

	<?php if ( $show_image && has_post_thumbnail() && ! post_password_required() ) : ?>
		<a class="post-thumb post-thumb--ratio-<?php echo esc_attr( $ratio ); ?>" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'large', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
		</a>
	<?php endif; ?>

	<div class="post-entry__body">
		<?php if ( ! $is_builder ) : ?>
			<header class="entry-header">
				<?php do_action( 'unysonplus_entry_header' ); ?>
			</header><!-- .entry-header -->
		<?php endif; ?>

		<?php
		/** Fires just before the entry content/summary wrapper. */
		do_action( 'unysonplus_before_entry_content' );

		if ( is_search() || $content_type === 'excerpt' ) :
			?>
			<div class="entry-summary"><?php the_excerpt(); ?></div><!-- .entry-summary -->
			<?php
		else :
			?>
			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages( array(
					'before'    => '<div class="page-links">' . esc_html__( 'Pages:', 'unysonplus' ) . ' <ul class="pagination">',
					'after'     => '</ul></div>',
					'separator' => '',
				) );
				?>
			</div><!-- .entry-content -->
			<?php
		endif;

		/** Fires just after the entry content/summary wrapper. */
		do_action( 'unysonplus_after_entry_content' );
		?>
	</div><!-- .post-entry__body -->

	<?php
	/** Fires at the bottom of <article>, just before it closes. */
	do_action( 'unysonplus_entry_bottom' );
	?>
</article><!-- #post-## -->
