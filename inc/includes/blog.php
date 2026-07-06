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
		$cache = array();
		if ( function_exists( 'fw_get_db_settings_option' ) ) {
			// Blog Index listing settings + the Card Design visual layer.
			foreach ( array( 'blog_index', 'blog_card' ) as $group ) {
				$cache = array_merge( $cache, (array) fw_get_db_settings_option( $group, array() ) );
			}
		}
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

	// Card design + behavior modifiers (all CSS-driven; see style.css).
	$card = unysonplus_blog_get( 'blog_card_style', 'plain' );
	if ( in_array( $card, array( 'boxed', 'bordered', 'overlay' ), true ) ) {
		$classes[] = 'posts-list--card-' . $card;
	}
	$hover = unysonplus_blog_get( 'blog_image_hover', 'zoom' );
	if ( in_array( $hover, array( 'zoom', 'lift', 'none' ), true ) ) {
		$classes[] = 'posts-list--hover-' . $hover;
	}
	if ( unysonplus_blog_get( 'blog_meta_position', 'below-title' ) === 'above-title' ) {
		$classes[] = 'posts-list--meta-above';
	}
	if ( unysonplus_blog_get( 'blog_sticky_highlight', 'yes' ) === 'yes' ) {
		$classes[] = 'posts-list--sticky-hl';
	}
	// "Feature first post" only makes sense in a grid/masonry on page 1.
	if ( $layout !== 'list'
		&& unysonplus_blog_get( 'blog_first_featured', 'no' ) === 'yes'
		&& (int) get_query_var( 'paged' ) <= 1 ) {
		$classes[] = 'posts-list--first-featured';
	}

	// Card Design layer → CSS custom properties on the wrapper (radius/shadow/padding).
	$radius_map = array( 'none' => '0', 'sm' => '.5rem', 'md' => 'var(--radius-lg, 14px)', 'lg' => '1.25rem', 'xl' => '1.75rem' );
	$shadow_map = array(
		'none' => 'none',
		'sm'   => '0 .25rem 1.25rem rgba(0,0,0,.07)',
		'md'   => '0 .5rem 1.75rem rgba(0,0,0,.1)',
		'lg'   => '0 1rem 2.5rem rgba(0,0,0,.14)',
	);
	$pad_map = array( 'compact' => '.9rem 1rem 1.1rem', 'normal' => '1.25rem 1.375rem 1.5rem', 'roomy' => '1.75rem 1.875rem 2rem' );
	$radius = $radius_map[ unysonplus_blog_get( 'blog_card_radius', 'md' ) ] ?? $radius_map['md'];
	$shadow = $shadow_map[ unysonplus_blog_get( 'blog_card_shadow', 'sm' ) ] ?? $shadow_map['sm'];
	$pad    = $pad_map[ unysonplus_blog_get( 'blog_card_padding', 'normal' ) ] ?? $pad_map['normal'];
	$style  = sprintf( '--post-card-radius:%s;--post-card-shadow:%s;--post-card-pad:%s;', $radius, $shadow, $pad );

	if ( unysonplus_blog_get( 'blog_card_hover_accent', 'no' ) === 'yes' ) {
		$classes[] = 'posts-list--card-accent';
	}

	echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" style="' . esc_attr( $style ) . '">';
}
endif;
add_action( 'unysonplus_before_loop', 'unysonplus_blog_open_list' );

if ( ! function_exists( 'unysonplus_blog_close_list' ) ) :
function unysonplus_blog_close_list() {
	if ( ! unysonplus_blog_is_listing() ) { return; }

	echo '</div><!-- .posts-list -->';

	// Pagination — archive.php's hardcoded nav was removed so this is the single source.
	$mode = unysonplus_blog_get( 'blog_pagination', 'numbers' );
	if ( $mode === 'prev_next' ) {
		the_posts_navigation();
	} elseif ( $mode === 'load_more' ) {
		unysonplus_blog_load_more_button();
	} else {
		the_posts_pagination( array(
			'mid_size'  => 2,
			'prev_text' => __( '&larr; Previous', 'unysonplus' ),
			'next_text' => __( 'Next &rarr;', 'unysonplus' ),
		) );
	}
}
endif;

if ( ! function_exists( 'unysonplus_blog_load_more_button' ) ) :
/**
 * "Load More" control. Progressive enhancement: it's a real link to the next
 * page (works with JS off); assets/js/blog-load-more.js upgrades the click to
 * fetch the next page and append its `.post-entry` cards in place.
 */
function unysonplus_blog_load_more_button() {
	global $wp_query;
	$paged = max( 1, (int) get_query_var( 'paged' ) );
	$max   = (int) $wp_query->max_num_pages;
	if ( $paged >= $max ) { return; }

	$next = get_next_posts_page_link( $max );
	echo '<div class="posts-loadmore" data-paged="' . esc_attr( $paged ) . '" data-max="' . esc_attr( $max ) . '">';
	echo '<a class="posts-loadmore__btn btn" href="' . esc_url( $next ) . '" rel="next">'
		. esc_html__( 'Load more', 'unysonplus' ) . '</a>';
	echo '</div>';
}
endif;

if ( ! function_exists( 'unysonplus_blog_primary_category' ) ) :
/**
 * The post's primary category link markup (Yoast primary term aware), or ''.
 */
function unysonplus_blog_primary_category() {
	$cats = get_the_category();
	if ( empty( $cats ) ) { return ''; }

	$term = $cats[0];
	$primary_id = (int) get_post_meta( get_the_ID(), '_yoast_wpseo_primary_category', true );
	if ( $primary_id ) {
		foreach ( $cats as $c ) {
			if ( (int) $c->term_id === $primary_id ) { $term = $c; break; }
		}
	}
	return '<a class="post-cat-badge" href="' . esc_url( get_category_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
}
endif;

if ( ! function_exists( 'unysonplus_blog_set_posts_per_page' ) ) :
/**
 * Honor Blog → Blog Index → Posts Per Page on the main blog / archive / search
 * loop (blank = leave Settings → Reading untouched).
 */
function unysonplus_blog_set_posts_per_page( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) { return; }
	if ( ! ( $query->is_home() || $query->is_archive() || $query->is_search() ) ) { return; }

	$ppp = (int) unysonplus_blog_get( 'blog_posts_per_page', 0 );
	if ( $ppp > 0 ) { $query->set( 'posts_per_page', $ppp ); }
}
endif;
add_action( 'pre_get_posts', 'unysonplus_blog_set_posts_per_page' );

if ( ! function_exists( 'unysonplus_blog_enqueue_load_more' ) ) :
/** Enqueue the Load-More enhancer only on listings that use it. */
function unysonplus_blog_enqueue_load_more() {
	if ( ! unysonplus_blog_is_listing() ) { return; }
	if ( unysonplus_blog_get( 'blog_pagination', 'numbers' ) !== 'load_more' ) { return; }

	$theme = wp_get_theme();
	wp_enqueue_script(
		'unysonplus-blog-load-more',
		get_template_directory_uri() . '/assets/js/blog-load-more.js',
		array(),
		$theme->exists() ? $theme->get( 'Version' ) : false,
		true
	);
}
endif;
add_action( 'wp_enqueue_scripts', 'unysonplus_blog_enqueue_load_more' );

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
/**
 * Read one key from the single-post settings. The Single Post tab is split into
 * three stored groups — `blog_single` (Content & Meta), `blog_single_hero`
 * (Header & Hero) and `blog_single_extras` (Elements) — merged here so callers
 * keep using flat leaf keys regardless of which sub-tab owns them.
 */
function unysonplus_single_get( $key, $default = '' ) {
	static $cache = null;
	if ( $cache === null ) {
		$cache = array();
		if ( function_exists( 'fw_get_db_settings_option' ) ) {
			foreach ( array( 'blog_single', 'blog_single_hero', 'blog_single_extras' ) as $group ) {
				$vals = (array) fw_get_db_settings_option( $group, array() );
				$cache = array_merge( $cache, $vals );
			}
		}
	}
	if ( ! array_key_exists( $key, $cache ) ) { return $default; }
	$val = $cache[ $key ];
	return ( $val === '' || $val === null ) ? $default : $val;
}
endif;

if ( ! function_exists( 'unysonplus_single_use_hero' ) ) :
/**
 * True when this single post should render the hero-style header. The per-post
 * "Header Style" (post_header_style: default/standard/hero) overrides the global
 * single_header_style; either way a hero needs a featured image.
 */
function unysonplus_single_use_hero() {
	if ( ! is_singular( 'post' ) || ! has_post_thumbnail() || post_password_required() ) {
		return false;
	}
	$style = unysonplus_single_get( 'single_header_style', 'standard' );
	if ( function_exists( 'fw_get_db_post_option' ) ) {
		$po = fw_get_db_post_option( get_the_ID(), 'post_options' );
		if ( is_array( $po ) && ! empty( $po['post_header_style'] ) && $po['post_header_style'] !== 'default' ) {
			$style = $po['post_header_style'];
		}
	}
	return $style === 'hero';
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
	if ( unysonplus_single_use_hero() ) { return; } // the hero shows the image instead
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

	$style = unysonplus_single_get( 'single_related_style', 'grid' );
	$style = in_array( $style, array( 'grid', 'list', 'carousel' ), true ) ? $style : 'grid';
	$ratio = unysonplus_single_get( 'single_related_ratio', '16-9' );
	$ratio = in_array( $ratio, array( '16-9', '4-3', '1-1' ), true ) ? $ratio : '16-9';
	?>
	<section class="related-posts related-posts--<?php echo esc_attr( $style ); ?>">
		<h2 class="related-posts__title"><?php esc_html_e( 'Related posts', 'unysonplus' ); ?></h2>
		<div class="related-posts__grid posts-cols-<?php echo esc_attr( $count ); ?>">
			<?php while ( $q->have_posts() ) : $q->the_post(); ?>
				<article class="related-post">
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="post-thumb post-thumb--ratio-<?php echo esc_attr( $ratio ); ?>" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
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

/* ---------------------------------------------------------------------------
 * TIER 2 — Single Post: hero header, reading progress, breadcrumbs, table of
 * contents, share buttons, tag row, comments gate.
 * ------------------------------------------------------------------------- */

/* ---- Hero header: title + meta over the featured image ---- */
if ( ! function_exists( 'unysonplus_single_render_hero' ) ) :
function unysonplus_single_render_hero() {
	if ( ! unysonplus_single_use_hero() ) { return; }

	$height  = unysonplus_single_get( 'single_hero_height', 'medium' );
	$height  = in_array( $height, array( 'small', 'medium', 'large', 'fullscreen' ), true ) ? $height : 'medium';
	$align   = unysonplus_single_get( 'single_hero_align', 'bottom' );
	$align   = in_array( $align, array( 'top', 'center', 'bottom' ), true ) ? $align : 'bottom';
	$img     = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	$opacity = max( 0, min( 100, (int) unysonplus_single_get( 'single_hero_overlay_opacity', 45 ) ) );

	$overlay = unysonplus_single_get( 'single_hero_overlay_color', '' );
	if ( function_exists( 'unysonplus_preset_color_to_css' ) ) {
		$overlay = unysonplus_preset_color_to_css( $overlay );
	}
	if ( ! is_string( $overlay ) || $overlay === '' ) { $overlay = '#000000'; }

	$classes = array( 'single-hero', 'single-hero--' . $height, 'single-hero--align-' . $align );
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="background-image:url('<?php echo esc_url( $img ); ?>');">
		<?php if ( $opacity > 0 ) : ?>
			<div class="single-hero__overlay" style="background-color:<?php echo esc_attr( $overlay ); ?>;opacity:<?php echo esc_attr( $opacity / 100 ); ?>;"></div>
		<?php endif; ?>
		<div class="single-hero__inner fw-container">
			<?php if ( unysonplus_single_enabled( 'post_breadcrumbs', 'single_breadcrumbs', 'no' ) ) { unysonplus_single_breadcrumbs(); } ?>
			<h1 class="single-hero__title entry-title"><?php the_title(); ?></h1>
			<?php unysonplus_blog_single_meta(); ?>
		</div>
	</div>
	<?php
}
endif;
add_action( 'unysonplus_before_entry', 'unysonplus_single_render_hero' );

if ( ! function_exists( 'unysonplus_single_hero_suppress_default' ) ) :
/** When the hero renders the title + meta, drop them from the in-article header. */
function unysonplus_single_hero_suppress_default() {
	if ( ! unysonplus_single_use_hero() ) { return; }
	remove_action( 'unysonplus_entry_header', 'unysonplus_entry_title' );
	remove_action( 'unysonplus_entry_header', 'unysonplus_blog_single_meta', 20 );
	add_filter( 'body_class', function ( $c ) { $c[] = 'single-has-hero'; return $c; } );
}
endif;
add_action( 'wp', 'unysonplus_single_hero_suppress_default', 30 );

/* ---- Breadcrumbs (standard header: render above the article) ---- */
if ( ! function_exists( 'unysonplus_single_breadcrumbs' ) ) :
function unysonplus_single_breadcrumbs() {
	$home = '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'unysonplus' ) . '</a>';
	$crumbs = array( $home );
	$cats = get_the_category();
	if ( ! empty( $cats ) ) {
		$crumbs[] = '<a href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
	}
	$crumbs[] = '<span class="is-current">' . esc_html( wp_trim_words( get_the_title(), 8, '…' ) ) . '</span>';
	echo '<nav class="single-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'unysonplus' ) . '">'
		. implode( '<span class="crumb-sep" aria-hidden="true">›</span>', $crumbs ) . '</nav>';
}
endif;
if ( ! function_exists( 'unysonplus_single_breadcrumbs_standard' ) ) :
function unysonplus_single_breadcrumbs_standard() {
	if ( ! is_singular( 'post' ) || unysonplus_single_use_hero() ) { return; } // hero renders its own
	if ( ! unysonplus_single_enabled( 'post_breadcrumbs', 'single_breadcrumbs', 'no' ) ) { return; }
	unysonplus_single_breadcrumbs();
}
endif;
add_action( 'unysonplus_before_entry', 'unysonplus_single_breadcrumbs_standard', 5 );

/* ---- Reading progress bar ---- */
if ( ! function_exists( 'unysonplus_single_progress_bar' ) ) :
function unysonplus_single_progress_bar() {
	if ( ! is_singular( 'post' ) ) { return; }
	if ( ! unysonplus_single_enabled( 'post_progress_bar', 'single_progress_bar', 'no' ) ) { return; }
	echo '<div class="reading-progress" role="progressbar" aria-label="' . esc_attr__( 'Reading progress', 'unysonplus' ) . '"><span class="reading-progress__fill"></span></div>';
}
endif;
add_action( 'wp_body_open', 'unysonplus_single_progress_bar' );

/* ---- Table of contents (placeholder — populated by JS from H2/H3) ---- */
if ( ! function_exists( 'unysonplus_single_toc' ) ) :
function unysonplus_single_toc() {
	if ( ! is_singular( 'post' ) ) { return; }
	if ( ! unysonplus_single_enabled( 'post_toc', 'single_toc', 'no' ) ) { return; }
	$title = unysonplus_single_get( 'single_toc_title', __( 'In this article', 'unysonplus' ) );
	echo '<nav class="post-toc" hidden aria-label="' . esc_attr__( 'Table of contents', 'unysonplus' ) . '">'
		. '<p class="post-toc__title">' . esc_html( $title ) . '</p>'
		. '<ul class="post-toc__list"></ul></nav>';
}
endif;
add_action( 'unysonplus_before_entry_content', 'unysonplus_single_toc', 5 );

/* ---- Share buttons ---- */
if ( ! function_exists( 'unysonplus_single_share_markup' ) ) :
function unysonplus_single_share_markup() {
	$nets = (array) unysonplus_single_get( 'single_share_networks', array( 'x' => true, 'facebook' => true, 'linkedin' => true, 'copy' => true ) );
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( get_the_title() );
	$links = array(
		'x'        => array( __( 'Share on X', 'unysonplus' ),        'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title ),
		'facebook' => array( __( 'Share on Facebook', 'unysonplus' ), 'https://www.facebook.com/sharer/sharer.php?u=' . $url ),
		'linkedin' => array( __( 'Share on LinkedIn', 'unysonplus' ), 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url ),
		'whatsapp' => array( __( 'Share on WhatsApp', 'unysonplus' ), 'https://api.whatsapp.com/send?text=' . $title . '%20' . $url ),
	);
	$out = '<div class="post-share"><span class="post-share__label">' . esc_html__( 'Share', 'unysonplus' ) . '</span>';
	foreach ( $links as $key => $l ) {
		if ( empty( $nets[ $key ] ) ) { continue; }
		$out .= '<a class="post-share__btn post-share__btn--' . esc_attr( $key ) . '" href="' . esc_url( $l[1] ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $l[0] ) . '">' . esc_html( ucfirst( $key === 'x' ? 'X' : $key ) ) . '</a>';
	}
	if ( ! empty( $nets['copy'] ) ) {
		$out .= '<button type="button" class="post-share__btn post-share__btn--copy" data-url="' . esc_url( get_permalink() ) . '" aria-label="' . esc_attr__( 'Copy link', 'unysonplus' ) . '">' . esc_html__( 'Copy link', 'unysonplus' ) . '</button>';
	}
	$out .= '</div>';
	return $out;
}
endif;
if ( ! function_exists( 'unysonplus_single_share_top' ) ) :
function unysonplus_single_share_top() {
	if ( ! is_singular( 'post' ) ) { return; }
	if ( ! unysonplus_single_enabled( 'post_share', 'single_share', 'no' ) ) { return; }
	$pos = unysonplus_single_get( 'single_share_position', 'bottom' );
	if ( $pos === 'top' || $pos === 'both' ) { echo unysonplus_single_share_markup(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ }
}
endif;
if ( ! function_exists( 'unysonplus_single_share_bottom' ) ) :
function unysonplus_single_share_bottom() {
	if ( ! is_singular( 'post' ) ) { return; }
	if ( ! unysonplus_single_enabled( 'post_share', 'single_share', 'no' ) ) { return; }
	$pos = unysonplus_single_get( 'single_share_position', 'bottom' );
	if ( $pos === 'bottom' || $pos === 'both' ) { echo unysonplus_single_share_markup(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ }
}
endif;
add_action( 'unysonplus_before_entry_content', 'unysonplus_single_share_top', 20 );
add_action( 'unysonplus_after_entry_content', 'unysonplus_single_share_bottom', 5 );

/* ---- Tag row (below content, before the author box) ---- */
if ( ! function_exists( 'unysonplus_single_tag_row' ) ) :
function unysonplus_single_tag_row() {
	if ( ! is_singular( 'post' ) ) { return; }
	if ( ! unysonplus_single_enabled( 'post_tags', 'single_tags', 'yes' ) ) { return; }
	$tags = get_the_tags();
	if ( empty( $tags ) || is_wp_error( $tags ) ) { return; }
	echo '<div class="post-tags">';
	foreach ( $tags as $tag ) {
		echo '<a class="post-tag" href="' . esc_url( get_tag_link( $tag ) ) . '">#' . esc_html( $tag->name ) . '</a>';
	}
	echo '</div>';
}
endif;
add_action( 'unysonplus_after_entry_content', 'unysonplus_single_tag_row', 3 );

/* ---- Comments gate (read by single.php) ---- */
if ( ! function_exists( 'unysonplus_single_comments_enabled' ) ) :
function unysonplus_single_comments_enabled() {
	return unysonplus_single_enabled( 'post_comments', 'single_comments', 'yes' );
}
endif;

/* ---- Enqueue the single-post enhancer (progress bar + TOC + copy link) ---- */
if ( ! function_exists( 'unysonplus_single_enqueue_js' ) ) :
function unysonplus_single_enqueue_js() {
	if ( ! is_singular( 'post' ) ) { return; }
	$needs = unysonplus_single_enabled( 'post_progress_bar', 'single_progress_bar', 'no' )
		|| unysonplus_single_enabled( 'post_toc', 'single_toc', 'no' )
		|| ( unysonplus_single_enabled( 'post_share', 'single_share', 'no' ) && ! empty( ( (array) unysonplus_single_get( 'single_share_networks', array() ) )['copy'] ) );
	if ( ! $needs ) { return; }

	$theme = wp_get_theme();
	wp_enqueue_script(
		'unysonplus-blog-single',
		get_template_directory_uri() . '/assets/js/blog-single.js',
		array(),
		$theme->exists() ? $theme->get( 'Version' ) : false,
		true
	);
}
endif;
add_action( 'wp_enqueue_scripts', 'unysonplus_single_enqueue_js' );

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
