<?php
/**
 * Native <head> metadata fallback (description, canonical, Open Graph, Twitter cards).
 *
 * WordPress core only emits charset, viewport, a title, and a singular-only canonical.
 * When NO dedicated SEO plugin is active this fills the gap so pages are self-describing
 * to social scrapers and AI agents. Skips itself when Yoast / Rank Math / SEOPress /
 * AIOSEO is running (they own this surface) — reusing unysonplus_seo_plugin_active()
 * from schema-jsonld.php. Override with the `unysonplus_emit_meta` filter.
 *
 * Auto-loaded from inc/includes/ (see inc/init.php).
 *
 * @package UnysonPlus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'unysonplus_should_emit_meta' ) ) {
	/**
	 * Whether to emit native head metadata on this request.
	 *
	 * @return bool
	 */
	function unysonplus_should_emit_meta() {
		$default = function_exists( 'unysonplus_seo_plugin_active' ) ? ! unysonplus_seo_plugin_active() : true;

		/**
		 * Filter the native head-metadata fallback. Defaults on only when no dedicated
		 * SEO plugin is handling meta tags.
		 *
		 * @param bool $emit
		 */
		return (bool) apply_filters( 'unysonplus_emit_meta', $default );
	}
}

if ( ! function_exists( 'unysonplus_head_meta' ) ) {
	/**
	 * Print description + canonical + Open Graph + Twitter tags for the current context.
	 *
	 * @return void
	 */
	function unysonplus_head_meta() {
		if ( ! unysonplus_should_emit_meta() ) {
			return;
		}

		$type  = 'website';
		$title = '';
		$desc  = '';
		$url   = '';
		$image = '';

		if ( is_singular() ) {
			$post  = get_queried_object();
			if ( ! $post instanceof WP_Post ) {
				return;
			}
			$title = wp_strip_all_tags( get_the_title( $post ) );
			$url   = get_permalink( $post );
			$type  = is_singular( 'post' ) ? 'article' : 'website';
			$desc  = has_excerpt( $post )
				? get_the_excerpt( $post )
				: wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '…' );
			if ( has_post_thumbnail( $post ) ) {
				$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'large' );
				if ( $img ) {
					$image = $img[0];
				}
			}
		} elseif ( is_front_page() || is_home() ) {
			$title = wp_strip_all_tags( get_bloginfo( 'name' ) );
			$desc  = wp_strip_all_tags( get_bloginfo( 'description' ) );
			$url   = home_url( '/' );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term  = get_queried_object();
			$title = wp_strip_all_tags( get_the_archive_title() );
			$desc  = wp_strip_all_tags( get_the_archive_description() );
			if ( $term && ! is_wp_error( $term ) ) {
				$link = get_term_link( $term );
				if ( ! is_wp_error( $link ) ) {
					$url = $link;
				}
			}
		} elseif ( is_post_type_archive() ) {
			$title = wp_strip_all_tags( get_the_archive_title() );
			$desc  = wp_strip_all_tags( get_the_archive_description() );
			$link  = get_post_type_archive_link( get_query_var( 'post_type' ) );
			if ( $link ) {
				$url = $link;
			}
		} elseif ( is_search() ) {
			/* translators: %s: search query. */
			$title = sprintf( __( 'Search results for “%s”', 'unysonplus' ), wp_strip_all_tags( get_search_query() ) );
		} else {
			return; // Date/author/other archives — leave to core.
		}

		$site = wp_strip_all_tags( get_bloginfo( 'name' ) );
		if ( '' === $desc ) {
			$desc = wp_strip_all_tags( get_bloginfo( 'description' ) );
		}
		if ( '' === $image ) {
			$logo_id = (int) get_theme_mod( 'custom_logo' );
			if ( $logo_id ) {
				$logo = wp_get_attachment_image_src( $logo_id, 'full' );
				if ( $logo ) {
					$image = $logo[0];
				}
			}
		}

		$out = '';
		if ( '' !== $desc ) {
			$out .= sprintf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
		}
		// Canonical: core already prints one on singular; only add it where core does not.
		if ( ! is_singular() && '' !== $url ) {
			$out .= sprintf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
		}

		// Open Graph.
		$out .= sprintf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
		if ( '' !== $title ) {
			$out .= sprintf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		}
		if ( '' !== $desc ) {
			$out .= sprintf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
		}
		if ( '' !== $url ) {
			$out .= sprintf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		}
		if ( '' !== $site ) {
			$out .= sprintf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $site ) );
		}
		if ( '' !== $image ) {
			$out .= sprintf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		}

		// Twitter card.
		$out .= sprintf( '<meta name="twitter:card" content="%s">' . "\n", '' !== $image ? 'summary_large_image' : 'summary' );
		if ( '' !== $title ) {
			$out .= sprintf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
		}
		if ( '' !== $desc ) {
			$out .= sprintf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
		}
		if ( '' !== $image ) {
			$out .= sprintf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
		}

		echo "\n" . $out; // phpcs:ignore WordPress.Security.EscapeOutput -- each value escaped above.
	}
	add_action( 'wp_head', 'unysonplus_head_meta', 2 );
}
