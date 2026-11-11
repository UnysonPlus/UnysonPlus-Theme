<?php
/**
 * Template part for displaying single post content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package Unysonplus
 */

$page_header_attr = array( 'class' => 'entry-header' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	/** Fires at the top of <article>. */
	do_action( 'unysonplus_entry_top' );
	?>

	<?php if ( ! function_exists( 'fw_ext_page_builder_is_builder_post' ) || ! fw_ext_page_builder_is_builder_post( get_the_ID() ) ) : ?>
		<header <?php echo unysonplus_attr_to_html( $page_header_attr ); ?>>
			<?php do_action( 'unysonplus_entry_header' ); ?>
		</header><!-- .entry-header -->
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

	<?php
	/** Fires at the bottom of <article>. */
	do_action( 'unysonplus_entry_bottom' );
	?>
</article><!-- #post-## -->
