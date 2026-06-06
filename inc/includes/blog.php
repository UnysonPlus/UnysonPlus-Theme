<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * Blog settings runtime (Phase 1 — Blog Index).
 *
 * Reads the `blog_index` option group (framework-customizations/theme/options/
 * blog-index.php) and wires it into the posts listing through the theme's
 * existing loop hooks — no template forks beyond the listing card:
 *
 *   unysonplus_before_loop  → open the .posts-list wrapper (grid/list/masonry)
 *   unysonplus_after_loop   → close it + render pagination per the setting
 *   unysonplus_entry_header → append the post-meta row (listing only)
 *   excerpt_length / excerpt_more filters → length + "Read more" text
 *
 * The card (template-parts/content.php) reads the layout / featured-image /
 * content-type values directly via unysonplus_blog_get().
 *
 * Single-post, archive-header and search settings arrive in later phases under
 * their own option groups (blog_single / blog_archives); this file only knows
 * blog_index for now.
 *
 * @since unysonplus-theme 2.1.44
 */

/* ---------------------------------------------------------------------------
 * Helper
 * ------------------------------------------------------------------------- */
if ( ! function_exists( 'unysonplus_blog_get' ) ) :
/**
 * Read one key from the blog_index option group.
 *
 * @param string $key     Inner-option key (e.g. 'blog_layout').
 * @param mixed  $default Returned when Unyson is inactive or the value is empty.
 * @return mixed
 */
function unysonplus_blog_get( $key, $default = '' ) {
	static $cache = null;
	if ( $cache === null ) {
		$cache = function_exists( 'fw_get_db_settings_option' )
			? (array) fw_get_db_settings_option( 'blog_index', array() )
			: array();
	}
	if ( ! array_key_exists( $key, $cache ) ) { return $default; }
	$val = $cache[ $key ];
	return ( $val === '' || $val === null ) ? $default : $val;
}
endif;

if ( ! function_exists( 'unysonplus_blog_is_listing' ) ) :
/** True on the blog index / archives / search results (a multi-post loop). */
function unysonplus_blog_is_listing() {
	return ( is_home() || is_archive() || is_search() ) && ! is_singular();
}
endif;

if ( ! function_exists( 'unysonplus_reading_time' ) ) :
/** Rough reading-time label from the post body (~200 wpm). */
function unysonplus_reading_time( $post_id = null ) {
	$words = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ?: get_the_ID() ) ) );
	$mins  = max( 1, (int) ceil( $words / 200 ) );
	/* translators: %d: minutes */
	return sprintf( _n( '%d min read', '%d min read', $mins, 'unysonplus' ), $mins );
}
endif;

/* ---------------------------------------------------------------------------
 * Listing wrapper (grid / list / masonry) + pagination
 * ------------------------------------------------------------------------- */
if ( ! function_exists( 'unysonplus_blog_open_list' ) ) :
function unysonplus_blog_open_list() {
	if ( ! unysonplus_blog_is_listing() ) { return; }

	list( $layout, $cols ) = unysonplus_blog_listing_layout();
	$classes = array( 'posts-list', 'posts-list--' . $layout );
	if ( $layout !== 'list' ) { $classes[] = 'posts-cols-' . $cols; }

	echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
}
endif;
add_action( 'unysonplus_before_loop', 'unysonplus_blog_open_list' );

if ( ! function_exists( 'unysonplus_blog_close_list' ) ) :
function unysonplus_blog_close_list() {
	if ( ! unysonplus_blog_is_listing() ) { return; }

	echo '</div><!-- .posts-list -->';

	// Pagination — archive.php's hardcoded nav was removed so this is the single source.
	if ( unysonplus_blog_get( 'blog_pagination', 'numbers' ) === 'prev_next' ) {
		the_posts_navigation();
	} else {
		the_posts_pagination( array(
			'mid_size'  => 2,
			'prev_text' => __( '&larr; Previous', 'unysonplus' ),
			'next_text' => __( 'Next &rarr;', 'unysonplus' ),
		) );
	}
}
endif;
add_action( 'unysonplus_after_loop', 'unysonplus_blog_close_list' );

/* ---------------------------------------------------------------------------
 * Post meta row (listing only — single-post meta is a later phase)
 * ------------------------------------------------------------------------- */
if ( ! function_exists( 'unysonplus_blog_entry_meta' ) ) :
function unysonplus_blog_entry_meta() {
	if ( is_singular() || get_post_type() !== 'post' ) { return; }

	$meta = (array) unysonplus_blog_get( 'blog_meta', array( 'date' => true, 'author' => true, 'category' => true ) );
	$on   = function ( $k ) use ( $meta ) { return ! empty( $meta[ $k ] ); };

	$bits = array();
	if ( $on( 'date' ) ) {
		$bits[] = '<span class="meta-date"><time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time></span>';
	}
	if ( $on( 'author' ) ) {
		$bits[] = '<span class="meta-author">' . esc_html( get_the_author() ) . '</span>';
	}
	if ( $on( 'category' ) ) {
		$cats = get_the_category_list( ', ' );
		if ( $cats ) { $bits[] = '<span class="meta-cat">' . $cats . '</span>'; }
	}
	if ( $on( 'comments' ) && ( comments_open() || get_comments_number() ) ) {
		$bits[] = '<span class="meta-comments">' . esc_html( get_comments_number() ) . '</span>';
	}
	if ( $on( 'reading_time' ) ) {
		$bits[] = '<span class="meta-readtime">' . esc_html( unysonplus_reading_time() ) . '</span>';
	}

	if ( $bits ) {
		echo '<div class="entry-meta">' . implode( '<span class="meta-sep" aria-hidden="true">&middot;</span>', $bits ) . '</div>';
	}
}
endif;
add_action( 'unysonplus_entry_header', 'unysonplus_blog_entry_meta', 20 );

/* ---------------------------------------------------------------------------
 * Excerpt length + "Read more" text (priority 20 overrides the parent defaults)
 * ------------------------------------------------------------------------- */
if ( ! function_exists( 'unysonplus_blog_excerpt_length' ) ) :
function unysonplus_blog_excerpt_length( $length ) {
	if ( is_admin() ) { return $length; }
	$n = (int) unysonplus_blog_get( 'blog_excerpt_length', 30 );
	return $n > 0 ? $n : $length;
}
endif;
add_filter( 'excerpt_length', 'unysonplus_blog_excerpt_length', 20 );

if ( ! function_exists( 'unysonplus_blog_excerpt_more' ) ) :
function unysonplus_blog_excerpt_more( $more ) {
	if ( is_admin() ) { return $more; }
	$text = unysonplus_blog_get( 'blog_read_more', __( 'Read more', 'unysonplus' ) );
	return ' <a class="read-more" href="' . esc_url( get_permalink() ) . '">' . esc_html( $text ) . '</a>';
}
endif;
add_filter( 'excerpt_more', 'unysonplus_blog_excerpt_more', 20 );


/* ===========================================================================
 * PHASE 2 — Single Post (blog_single + per-post post_options overrides)
 *
 * All additive, via the hooks single.php / content-single.php already fire:
 *   unysonplus_entry_top / _before_entry_content → featured image (by position)
 *   unysonplus_entry_header (prio 20)            → single meta row
 *   unysonplus_after_entry_content               → author box
 *   unysonplus_after_entry                       → related posts + prev/next
 *   'wp'                                         → sidebar override
 * ======================================================================== */

if ( ! function_exists( 'unysonplus_single_get' ) ) :
/** Read one key from the blog_single option group. */
function unysonplus_single_get( $key, $default = '' ) {
	static $cache = null;
	if ( $cache === null ) {
		$cache = function_exists( 'fw_get_db_settings_option' )
			? (array) fw_get_db_settings_option( 'blog_single', array() )
			: array();
	}
	if ( ! array_key_exists( $key, $cache ) ) { return $default; }
	$val = $cache[ $key ];
	return ( $val === '' || $val === null ) ? $default : $val;
}
endif;

if ( ! function_exists( 'unysonplus_single_enabled' ) ) :
/**
 * Resolve a single-post show/hide feature: per-post override wins ('show'/'hide'),
 * otherwise the global blog_single switch ('yes'/'no').
 */
function unysonplus_single_enabled( $post_key, $global_key, $global_default = 'yes' ) {
	if ( function_exists( 'fw_get_db_post_option' ) ) {
		$po = fw_get_db_post_option( get_the_ID(), 'post_options' );
		if ( is_array( $po ) && isset( $po[ $post_key ] ) ) {
			if ( $po[ $post_key ] === 'show' ) { return true; }
			if ( $po[ $post_key ] === 'hide' ) { return false; }
		}
	}
	return unysonplus_single_get( $global_key, $global_default ) === 'yes';
}
endif;

/* ---- Featured image (position-aware) ---- */
if ( ! function_exists( 'unysonplus_single_render_featured' ) ) :
function unysonplus_single_render_featured( $slot ) {
	if ( ! is_singular( 'post' ) ) { return; }
	if ( unysonplus_single_get( 'single_featured_position', 'below-title' ) !== $slot ) { return; }
	if ( ! unysonplus_single_enabled( 'post_featured_image', 'single_featured_image', 'yes' ) ) { return; }
	if ( ! has_post_thumbnail() || post_password_required() ) { return; }
	echo '<figure class="single-featured">' . get_the_post_thumbnail( get_the_ID(), 'large' ) . '</figure>';
}
endif;
if ( ! function_exists( 'unysonplus_single_featured_above' ) ) :
function unysonplus_single_featured_above() { unysonplus_single_render_featured( 'above-title' ); }
endif;
if ( ! function_exists( 'unysonplus_single_featured_below' ) ) :
function unysonplus_single_featured_below() { unysonplus_single_render_featured( 'below-title' ); }
endif;
add_action( 'unysonplus_entry_top', 'unysonplus_single_featured_above' );
add_action( 'unysonplus_before_entry_content', 'unysonplus_single_featured_below' );

/* ---- Single meta row ---- */
if ( ! function_exists( 'unysonplus_blog_single_meta' ) ) :
function unysonplus_blog_single_meta() {
	if ( ! is_singular( 'post' ) ) { return; }

	$meta = (array) unysonplus_single_get( 'single_meta', array( 'date' => true, 'author' => true, 'category' => true ) );
	$on   = function ( $k ) use ( $meta ) { return ! empty( $meta[ $k ] ); };

	$bits = array();
	if ( $on( 'date' ) ) {
		$bits[] = '<span class="meta-date"><time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time></span>';
	}
	if ( $on( 'author' ) ) {
		$bits[] = '<span class="meta-author">' . esc_html( get_the_author() ) . '</span>';
	}
	if ( $on( 'category' ) ) {
		$cats = get_the_category_list( ', ' );
		if ( $cats ) { $bits[] = '<span class="meta-cat">' . $cats . '</span>'; }
	}
	if ( $on( 'tags' ) ) {
		$tags = get_the_tag_list( '', ', ' );
		if ( $tags && ! is_wp_error( $tags ) ) { $bits[] = '<span class="meta-tags">' . $tags . '</span>'; }
	}
	if ( $on( 'comments' ) && ( comments_open() || get_comments_number() ) ) {
		$bits[] = '<span class="meta-comments">' . esc_html( get_comments_number() ) . '</span>';
	}
	if ( $on( 'reading_time' ) ) {
		$bits[] = '<span class="meta-readtime">' . esc_html( unysonplus_reading_time() ) . '</span>';
	}

	if ( $bits ) {
		echo '<div class="entry-meta">' . implode( '<span class="meta-sep" aria-hidden="true">&middot;</span>', $bits ) . '</div>';
	}
}
endif;
add_action( 'unysonplus_entry_header', 'unysonplus_blog_single_meta', 20 );

/* ---- Author box ---- */
if ( ! function_exists( 'unysonplus_single_author_box' ) ) :
function unysonplus_single_author_box() {
	if ( ! is_singular( 'post' ) ) { return; }
	if ( ! unysonplus_single_enabled( 'post_author_box', 'single_author_box', 'yes' ) ) { return; }

	$author_id = (int) get_the_author_meta( 'ID' );
	if ( ! $author_id ) { return; }
	$bio = get_the_author_meta( 'description', $author_id );
	?>
	<aside class="author-box">
		<div class="author-box__avatar"><?php echo get_avatar( $author_id, 72 ); ?></div>
		<div class="author-box__body">
			<h2 class="author-box__name"><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></h2>
			<?php if ( $bio ) : ?><p class="author-box__bio"><?php echo esc_html( $bio ); ?></p><?php endif; ?>
			<a class="author-box__link" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"><?php esc_html_e( 'View all posts', 'unysonplus' ); ?> &rarr;</a>
		</div>
	</aside>
	<?php
}
endif;
add_action( 'unysonplus_after_entry_content', 'unysonplus_single_author_box' );

/* ---- Related posts + prev/next (fired once per single via after_entry) ---- */
if ( ! function_exists( 'unysonplus_render_related_posts' ) ) :
function unysonplus_render_related_posts() {
	$count = max( 1, min( 4, (int) unysonplus_single_get( 'single_related_count', 3 ) ) );
	$by    = unysonplus_single_get( 'single_related_by', 'category' );
	$terms = ( $by === 'tag' )
		? wp_get_post_tags( get_the_ID(), array( 'fields' => 'ids' ) )
		: wp_get_post_categories( get_the_ID(), array( 'fields' => 'ids' ) );
	if ( empty( $terms ) || is_wp_error( $terms ) ) { return; }

	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => $count,
		'post__not_in'        => array( get_the_ID() ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		( $by === 'tag' ? 'tag__in' : 'category__in' ) => $terms,
	);
	$q = new WP_Query( $args );
	if ( ! $q->have_posts() ) { wp_reset_postdata(); return; }
	?>
	<section class="related-posts">
		<h2 class="related-posts__title"><?php esc_html_e( 'Related posts', 'unysonplus' ); ?></h2>
		<div class="related-posts__grid posts-cols-<?php echo esc_attr( $count ); ?>">
			<?php while ( $q->have_posts() ) : $q->the_post(); ?>
				<article class="related-post">
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="post-thumb post-thumb--ratio-16-9" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
					<?php endif; ?>
					<h3 class="related-post__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<span class="related-post__date"><?php echo esc_html( get_the_date() ); ?></span>
				</article>
			<?php endwhile; ?>
		</div>
	</section>
	<?php
	wp_reset_postdata();
}
endif;

if ( ! function_exists( 'unysonplus_single_after_entry' ) ) :
function unysonplus_single_after_entry() {
	if ( ! is_singular( 'post' ) ) { return; }
	if ( unysonplus_single_enabled( 'post_related', 'single_related', 'yes' ) ) {
		unysonplus_render_related_posts();
	}
	if ( unysonplus_single_enabled( 'post_nav', 'single_post_nav', 'yes' ) ) {
		the_post_navigation( array(
			'prev_text' => '<span class="nav-label">' . esc_html__( 'Previous', 'unysonplus' ) . '</span> <span class="nav-title">%title</span>',
			'next_text' => '<span class="nav-label">' . esc_html__( 'Next', 'unysonplus' ) . '</span> <span class="nav-title">%title</span>',
		) );
	}
}
endif;
add_action( 'unysonplus_after_entry', 'unysonplus_single_after_entry' );

/* ---- Sidebar override (per-post → global blog_single → inherit) ---- */
if ( ! function_exists( 'unysonplus_single_sidebar_override' ) ) :
function unysonplus_single_sidebar_override() {
	if ( is_admin() || ! is_singular( 'post' ) ) { return; }

	$sb = 'default';
	if ( function_exists( 'fw_get_db_post_option' ) ) {
		$po = fw_get_db_post_option( get_queried_object_id(), 'post_options' );
		if ( is_array( $po ) && ! empty( $po['post_sidebar'] ) ) { $sb = $po['post_sidebar']; }
	}
	if ( $sb === 'default' || $sb === '' ) {
		$sb = unysonplus_single_get( 'single_sidebar', 'inherit' );
	}
	if ( in_array( $sb, array( 'none', 'left', 'right' ), true ) && function_exists( 'unysonplus_set_layout_override' ) ) {
		unysonplus_set_layout_override( array( 'sidebar' => $sb ) );
	}
}
endif;
add_action( 'wp', 'unysonplus_single_sidebar_override', 20 );


/* ===========================================================================
 * PHASE 3 — Archives & Search (blog_archives)
 *
 * archive.php (which category.php delegates to) and search.php route their
 * header through unysonplus_render_archive_header(); the listing wrapper picks
 * its layout per context via unysonplus_blog_listing_layout(); a 'wp' hook
 * applies the archive sidebar override.
 * ======================================================================== */

if ( ! function_exists( 'unysonplus_archive_get' ) ) :
/** Read one key from the blog_archives option group. */
function unysonplus_archive_get( $key, $default = '' ) {
	static $cache = null;
	if ( $cache === null ) {
		$cache = function_exists( 'fw_get_db_settings_option' )
			? (array) fw_get_db_settings_option( 'blog_archives', array() )
			: array();
	}
	if ( ! array_key_exists( $key, $cache ) ) { return $default; }
	$val = $cache[ $key ];
	return ( $val === '' || $val === null ) ? $default : $val;
}
endif;

if ( ! function_exists( 'unysonplus_blog_listing_layout' ) ) :
/**
 * Resolve the listing layout + columns for the current context.
 * Archives / search can override Blog Index, or inherit it.
 *
 * @return array{0:string,1:int} [ layout, columns ]
 */
function unysonplus_blog_listing_layout() {
	$layout = unysonplus_blog_get( 'blog_layout', 'list' );
	$cols   = (int) unysonplus_blog_get( 'blog_columns', 2 );

	if ( is_search() ) {
		$sl = unysonplus_archive_get( 'search_layout', 'inherit' );
		if ( $sl !== 'inherit' && $sl !== '' ) { $layout = $sl; }
	} elseif ( is_archive() ) {
		$al = unysonplus_archive_get( 'archive_layout', 'inherit' );
		if ( $al !== 'inherit' && $al !== '' ) { $layout = $al; }
		$ac = (int) unysonplus_archive_get( 'archive_columns', 0 );
		if ( $ac > 0 ) { $cols = $ac; }
	}

	if ( ! in_array( $layout, array( 'list', 'grid', 'masonry' ), true ) ) { $layout = 'list'; }
	return array( $layout, max( 1, min( 4, $cols ) ) );
}
endif;

if ( ! function_exists( 'unysonplus_blog_current_layout' ) ) :
/** Just the layout string for the current listing context (used by content.php). */
function unysonplus_blog_current_layout() {
	$r = unysonplus_blog_listing_layout();
	return $r[0];
}
endif;

if ( ! function_exists( 'unysonplus_render_archive_header' ) ) :
/**
 * Gated archive / search header. Replaces the hardcoded <header> in
 * archive.php and search.php. No-ops entirely when the header is turned off.
 */
function unysonplus_render_archive_header() {
	if ( unysonplus_archive_get( 'archive_header', 'yes' ) !== 'yes' ) { return; }

	echo '<header class="page-header archive-header">';

	if ( is_search() ) {
		printf(
			'<h1 class="page-title">%s <span>%s</span></h1>',
			esc_html__( 'Search results for:', 'unysonplus' ),
			esc_html( get_search_query() )
		);
	} else {
		the_archive_title( '<h1 class="page-title">', '</h1>' );
		if ( unysonplus_archive_get( 'archive_show_description', 'yes' ) === 'yes' ) {
			the_archive_description( '<div class="archive-description">', '</div>' );
		}
	}

	if ( is_author() && unysonplus_archive_get( 'archive_author_bio', 'yes' ) === 'yes' ) {
		$author_id = (int) get_queried_object_id();
		$bio       = $author_id ? get_the_author_meta( 'description', $author_id ) : '';
		if ( $author_id ) {
			echo '<div class="archive-author">' . get_avatar( $author_id, 64 );
			echo '<div class="archive-author__body">';
			if ( $bio ) { echo '<p class="archive-author__bio">' . esc_html( $bio ) . '</p>'; }
			echo '</div></div>';
		}
	}

	echo '</header><!-- .archive-header -->';
}
endif;

if ( ! function_exists( 'unysonplus_archive_sidebar_override' ) ) :
function unysonplus_archive_sidebar_override() {
	if ( is_admin() || is_singular() ) { return; }
	if ( ! ( is_archive() || is_search() || is_home() ) ) { return; }

	$sb = unysonplus_archive_get( 'archive_sidebar', 'inherit' );
	if ( in_array( $sb, array( 'none', 'left', 'right' ), true ) && function_exists( 'unysonplus_set_layout_override' ) ) {
		unysonplus_set_layout_override( array( 'sidebar' => $sb ) );
	}
}
endif;
add_action( 'wp', 'unysonplus_archive_sidebar_override', 20 );
