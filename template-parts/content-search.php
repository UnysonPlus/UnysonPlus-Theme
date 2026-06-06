<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package Unysonplus
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?>>
	<?php
	/** Fires at the top of <article>. */
	do_action( 'unysonplus_entry_top' );
	?>

	<header class="entry-header">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="entry-thumbnail">
				<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'alignleft' ) ); ?>
			</div>
		<?php endif; ?>
		<?php
		/** Fires before the entry title. */
		do_action( 'unysonplus_before_entry_title' );

		the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );

		/** Fires after the entry title. */
		do_action( 'unysonplus_after_entry_title' );
		?>

		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta"></div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php
	/** Fires before .entry-summary opens. */
	do_action( 'unysonplus_before_entry_content' );
	?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php
	/** Fires after .entry-summary closes. */
	do_action( 'unysonplus_after_entry_content' );
	?>

	<footer class="entry-footer"></footer><!-- .entry-footer -->

	<?php
	/** Fires at the bottom of <article>. */
	do_action( 'unysonplus_entry_bottom' );
	?>
</article><!-- #post-## -->
