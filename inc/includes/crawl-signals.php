<?php
/**
 * Crawl signals for search engines and AI browsing agents.
 *
 * 1. Appends a Sitemap: reference (and an llms.txt pointer) to the virtual robots.txt.
 * 2. Serves a generated /llms.txt — a plain-text, markdown-flavoured entry point (per the
 *    llmstxt.org convention) listing the site's key pages + recent posts, so an agent gets
 *    a canonical map of the site without crawling. Cached 12h; cleared on content save.
 *    The whole body is filterable via `unysonplus_llms_txt`.
 *
 * Auto-loaded from inc/includes/ (see inc/init.php).
 *
 * @package UnysonPlus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'unysonplus_robots_txt_signals' ) ) {
	/**
	 * Add a Sitemap: line + an llms.txt pointer to the generated robots.txt.
	 *
	 * @param string $output Current robots.txt body.
	 * @param bool   $public Whether the site is set to be indexed.
	 * @return string
	 */
	function unysonplus_robots_txt_signals( $output, $public ) {
		if ( ! $public ) {
			return $output; // Non-public site — do not advertise a sitemap/entry point.
		}

		// Reference the core XML sitemap index when present and not already listed.
		if ( function_exists( 'get_sitemap_url' ) ) {
			$sitemap = get_sitemap_url( 'index' );
			if ( $sitemap && false === stripos( $output, 'sitemap:' ) ) {
				$output .= "\nSitemap: " . esc_url_raw( $sitemap ) . "\n";
			}
		}

		// Point AI agents at the llms.txt entry point.
		if ( false === stripos( $output, 'llms.txt' ) ) {
			$output .= "\n# AI agents: a plain-text site map is available at\n# "
				. esc_url_raw( home_url( '/llms.txt' ) ) . "\n";
		}

		return $output;
	}
	add_filter( 'robots_txt', 'unysonplus_robots_txt_signals', 20, 2 );
}

if ( ! function_exists( 'unysonplus_build_llms_txt' ) ) {
	/**
	 * Build (and cache) the /llms.txt body.
	 *
	 * @return string
	 */
	function unysonplus_build_llms_txt() {
		$cached = get_transient( 'unysonplus_llms_txt' );
		if ( false !== $cached ) {
			return $cached;
		}

		// Plain-text output: strip tags AND decode HTML entities (&amp;, &#8211;, …).
		$clean = static function ( $s ) {
			return trim( html_entity_decode( wp_strip_all_tags( (string) $s ), ENT_QUOTES, 'UTF-8' ) );
		};

		$name = $clean( get_bloginfo( 'name' ) );
		$desc = $clean( get_bloginfo( 'description' ) );

		$lines   = array();
		$lines[] = '# ' . ( '' !== $name ? $name : home_url( '/' ) );
		if ( '' !== $desc ) {
			$lines[] = '';
			$lines[] = '> ' . $desc;
		}
		$lines[] = '';
		$lines[] = 'URL: ' . home_url( '/' );

		// Key pages.
		$pages = get_pages(
			array(
				'sort_column' => 'menu_order,post_title',
				'number'      => 50,
			)
		);
		if ( ! empty( $pages ) ) {
			$lines[] = '';
			$lines[] = '## Pages';
			foreach ( $pages as $pg ) {
				$title = $clean( get_the_title( $pg ) );
				if ( '' === $title ) {
					continue;
				}
				$lines[] = '- [' . $title . '](' . get_permalink( $pg ) . ')';
			}
		}

		// Recent posts.
		$posts = get_posts(
			array(
				'numberposts' => 20,
				'post_status' => 'publish',
			)
		);
		if ( ! empty( $posts ) ) {
			$lines[] = '';
			$lines[] = '## Recent posts';
			foreach ( $posts as $p ) {
				$title = $clean( get_the_title( $p ) );
				if ( '' === $title ) {
					continue;
				}
				$lines[] = '- [' . $title . '](' . get_permalink( $p ) . ')';
			}
		}

		$body = implode( "\n", $lines ) . "\n";

		/**
		 * Filter the full /llms.txt body before it is cached + served.
		 *
		 * @param string $body
		 */
		$body = (string) apply_filters( 'unysonplus_llms_txt', $body );

		set_transient( 'unysonplus_llms_txt', $body, 12 * HOUR_IN_SECONDS );
		return $body;
	}
}

if ( ! function_exists( 'unysonplus_serve_llms_txt' ) ) {
	/**
	 * Serve /llms.txt as text/plain when that path is requested.
	 *
	 * @param WP $wp Current WordPress environment.
	 * @return void
	 */
	function unysonplus_serve_llms_txt( $wp ) {
		$req = isset( $wp->request ) ? trim( (string) $wp->request, '/' ) : '';
		if ( 'llms.txt' !== $req ) {
			return;
		}

		$body = unysonplus_build_llms_txt();
		if ( ! headers_sent() ) {
			header( 'Content-Type: text/plain; charset=utf-8' );
			header( 'X-Robots-Tag: noindex' );
		}
		echo $body; // phpcs:ignore WordPress.Security.EscapeOutput -- plain-text body, titles stripped + URLs escaped during build.
		exit;
	}
	add_action( 'parse_request', 'unysonplus_serve_llms_txt', 0 );
}

if ( ! function_exists( 'unysonplus_flush_llms_txt_cache' ) ) {
	/**
	 * Drop the cached llms.txt when content changes.
	 *
	 * @return void
	 */
	function unysonplus_flush_llms_txt_cache() {
		delete_transient( 'unysonplus_llms_txt' );
	}
	add_action( 'save_post', 'unysonplus_flush_llms_txt_cache' );
	add_action( 'deleted_post', 'unysonplus_flush_llms_txt_cache' );
}
