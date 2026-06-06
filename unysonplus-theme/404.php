<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * When the Misc > 404 Page option is set, render that page's content
 * instead of the default markup.
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 * @package Unysonplus
 */

$replacement_id = function_exists( 'unysonplus_misc_404_page_id' )
	? unysonplus_misc_404_page_id()
	: 0;

$show_search       = ( ! function_exists( 'unysonplus_misc_get' ) ) || ( unysonplus_misc_get( '404_show_search', 'yes' ) === 'yes' );
$show_recent_posts = function_exists( 'unysonplus_misc_get' ) && ( unysonplus_misc_get( '404_show_recent_posts', 'no' ) === 'yes' );

get_header(); ?>

<div class="container">
	<div class="row">
		<main id="main" class="site-main content-area col-md" role="main">

			<?php if ( $replacement_id ) :
				$replacement = get_post( $replacement_id );
				if ( $replacement ) :
					global $post;
					$post = $replacement;
					setup_postdata( $post );
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'error-404 error-404--page' ); ?>>
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>
						</header>
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
					</article>
					<?php
					wp_reset_postdata();
				endif;
			else : ?>

				<section class="error-404 not-found">
					<header class="page-header">
						<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'unysonplus' ); ?></h1>
					</header><!-- .page-header -->

					<div class="page-content">
						<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'unysonplus' ); ?></p>

						<?php if ( $show_search ) get_search_form(); ?>

						<?php if ( $show_recent_posts ) :
							$recent = new WP_Query( array(
								'post_type'           => 'post',
								'posts_per_page'      => 5,
								'ignore_sticky_posts' => true,
							) );
							if ( $recent->have_posts() ) : ?>
								<h2 class="recent-posts-title"><?php esc_html_e( 'Recent posts', 'unysonplus' ); ?></h2>
								<ul class="recent-posts">
									<?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
										<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
									<?php endwhile; ?>
								</ul>
							<?php endif;
							wp_reset_postdata();
						endif; ?>

					</div><!-- .page-content -->
				</section><!-- .error-404 -->

			<?php endif; ?>

		</main><!-- #main -->
		<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer();
