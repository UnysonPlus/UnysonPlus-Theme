<?php
/**
 * Template part for displaying standard post content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package Unysonplus
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	/** Fires at the top of <article>. */
	do_action( 'unysonplus_entry_top' );
	?>

	<header class="entry-header">
		<?php do_action( 'unysonplus_entry_header' ); ?>
	</header><!-- .entry-header -->

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
