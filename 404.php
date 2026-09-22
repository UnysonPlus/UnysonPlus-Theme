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

$has_misc          = function_exists( 'unysonplus_misc_get' );
$show_search       = ( ! $has_misc ) || ( unysonplus_misc_get( '404_show_search', 'yes' ) === 'yes' );
$show_recent_posts = $has_misc && ( unysonplus_misc_get( '404_show_recent_posts', 'no' ) === 'yes' );
$show_button       = ( ! $has_misc ) || ( unysonplus_misc_get( '404_show_button', 'yes' ) === 'yes' );

// Copy overrides — a blank setting falls back to the built-in default.
$heading      = $has_misc ? trim( (string) unysonplus_misc_get( '404_heading', '' ) ) : '';
$message      = $has_misc ? trim( (string) unysonplus_misc_get( '404_text', '' ) ) : '';
$button_label = $has_misc ? trim( (string) unysonplus_misc_get( '404_button_label', '' ) ) : '';
if ( '' === $heading )      { $heading      = __( 'This page wandered off.', 'unysonplus' ); }
if ( '' === $message )      { $message      = __( 'The page you were looking for isn\'t here. It may have moved, or it never existed. Let\'s get you back on track.', 'unysonplus' ); }
if ( '' === $button_label ) { $button_label = __( 'Back to home', 'unysonplus' ); }

get_header(); ?>

<?php
// <main> is the flex-grow region (.site-content) and the Vertical-grid content cell.
// No sidebar on a 404 — an error page shouldn't carry the blog's widgets.
?>
<main id="main" class="site-content site-main error-404-page" role="main">

	<?php if ( $replacement_id ) :
		// Escape hatch: render a user-built page (Misc → 404 Page) instead of the
		// default state. A page-builder page brings its own sections/containers.
		$replacement = get_post( $replacement_id );
		if ( $replacement ) :
			global $post;
			$post = $replacement;
			setup_postdata( $post );
			?>
			<div class="entry-content"><?php the_content(); ?></div>
			<?php
			wp_reset_postdata();
		endif;
	else : ?>

		<section class="error-404 not-found">
			<div class="error-404__inner">
				<p class="error-404__code" aria-hidden="true">404</p>
				<h1 class="error-404__title"><?php echo esc_html( $heading ); ?></h1>
				<div class="error-404__text"><?php echo wp_kses_post( wpautop( $message ) ); ?></div>

				<?php if ( $show_button ) : ?>
					<div class="error-404__actions">
						<a class="error-404__btn error-404__btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<?php echo esc_html( $button_label ); ?>
						</a>
					</div>
				<?php endif; ?>

				<?php if ( $show_search ) : ?>
					<?php // Self-contained search (inline SVG icon) — the theme's global search form
					// uses a Font Awesome glyph that isn't guaranteed to be enqueued on a 404. ?>
					<div class="error-404__search">
						<form role="search" method="get" class="error-404__search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<label class="screen-reader-text" for="error-404-search-field"><?php esc_html_e( 'Search for:', 'unysonplus' ); ?></label>
							<input type="search" id="error-404-search-field" name="s" class="error-404__search-input" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search the site&hellip;', 'unysonplus' ); ?>" />
							<button type="submit" class="error-404__search-submit">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
								<span><?php esc_html_e( 'Search', 'unysonplus' ); ?></span>
							</button>
						</form>
					</div>
				<?php endif; ?>

				<?php if ( $show_recent_posts ) :
					$recent = new WP_Query( array(
						'post_type'           => 'post',
						'posts_per_page'      => 4,
						'ignore_sticky_posts' => true,
					) );
					if ( $recent->have_posts() ) : ?>
						<nav class="error-404__recent" aria-label="<?php esc_attr_e( 'Recent posts', 'unysonplus' ); ?>">
							<p class="error-404__recent-label"><?php esc_html_e( 'Recent posts', 'unysonplus' ); ?></p>
							<ul class="error-404__recent-list">
								<?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
									<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
								<?php endwhile; ?>
							</ul>
						</nav>
					<?php endif;
					wp_reset_postdata();
				endif; ?>
			</div>
		</section><!-- .error-404 -->

	<?php endif; ?>

</main><!-- #main -->
<?php get_footer();
