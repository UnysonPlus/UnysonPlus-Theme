<?php
/**
 * Machine-readable structured data (schema.org JSON-LD).
 *
 * Emits WebSite + SearchAction, Organization, and Article/BlogPosting graphs so that
 * AI browsing agents and search engines can understand the site without scraping the
 * DOM. Auto-loaded from inc/includes/ (see inc/init.php).
 *
 * It deliberately SKIPS itself when a dedicated SEO plugin (Yoast, Rank Math, SEOPress,
 * All in One SEO) is active, because those already output their own schema graph and a
 * second one would create duplicate/conflicting nodes. Override with the
 * `unysonplus_emit_schema` filter (return true to force, false to disable).
 *
 * @package UnysonPlus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'unysonplus_seo_plugin_active' ) ) {
	/**
	 * True when a schema-emitting SEO plugin is running, so the theme steps aside.
	 *
	 * @return bool
	 */
	function unysonplus_seo_plugin_active() {
		return defined( 'WPSEO_VERSION' )      // Yoast SEO.
			|| class_exists( 'RankMath' )      // Rank Math.
			|| defined( 'SEOPRESS_VERSION' )   // SEOPress.
			|| defined( 'AIOSEO_VERSION' )     // All in One SEO.
			|| function_exists( 'aioseo' );
	}
}

if ( ! function_exists( 'unysonplus_should_emit_schema' ) ) {
	/**
	 * Whether the theme should emit its native JSON-LD on this request.
	 *
	 * @return bool
	 */
	function unysonplus_should_emit_schema() {
		/**
		 * Filter the theme's native schema.org output. Defaults to on only when no
		 * dedicated SEO plugin is already handling structured data.
		 *
		 * @param bool $emit
		 */
		return (bool) apply_filters( 'unysonplus_emit_schema', ! unysonplus_seo_plugin_active() );
	}
}

if ( ! function_exists( 'unysonplus_print_jsonld' ) ) {
	/**
	 * Print one JSON-LD <script> from an associative array.
	 *
	 * @param array $data Schema graph.
	 * @return void
	 */
	function unysonplus_print_jsonld( $data ) {
		if ( empty( $data ) || ! is_array( $data ) ) {
			return;
		}
		echo "\n" . '<script type="application/ld+json">'
			. wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
			. '</script>' . "\n";
	}
}

if ( ! function_exists( 'unysonplus_schema_site' ) ) {
	/**
	 * Sitewide Organization node + (front page only) WebSite + SearchAction.
	 *
	 * @return void
	 */
	function unysonplus_schema_site() {
		if ( ! unysonplus_should_emit_schema() ) {
			return;
		}

		$site_url  = home_url( '/' );
		$site_name = get_bloginfo( 'name' );

		// --- Organization (logo pulled from the custom logo when configured). ---
		$org = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Organization',
			'@id'      => $site_url . '#organization',
			'name'     => $site_name,
			'url'      => $site_url,
		);

		$logo_id = (int) get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			$logo = wp_get_attachment_image_src( $logo_id, 'full' );
			if ( $logo ) {
				$org['logo'] = array(
					'@type'  => 'ImageObject',
					'url'    => $logo[0],
					'width'  => (int) $logo[1],
					'height' => (int) $logo[2],
				);
			}
		}

		/**
		 * Filter the Organization `sameAs` social-profile URLs. Integrators (or the
		 * social settings) can populate this so agents can cross-reference the brand.
		 *
		 * @param string[] $urls
		 */
		$same_as = apply_filters( 'unysonplus_schema_same_as', array() );
		if ( ! empty( $same_as ) && is_array( $same_as ) ) {
			$org['sameAs'] = array_values( array_unique( array_filter( $same_as ) ) );
		}

		unysonplus_print_jsonld( $org );

		// --- WebSite + SearchAction — only on the canonical front / posts page. ---
		if ( is_front_page() || is_home() ) {
			unysonplus_print_jsonld(
				array(
					'@context'        => 'https://schema.org',
					'@type'           => 'WebSite',
					'@id'             => $site_url . '#website',
					'url'             => $site_url,
					'name'            => $site_name,
					'publisher'       => array( '@id' => $site_url . '#organization' ),
					'potentialAction' => array(
						'@type'       => 'SearchAction',
						'target'      => array(
							'@type'       => 'EntryPoint',
							'urlTemplate' => $site_url . '?s={search_term_string}',
						),
						'query-input' => 'required name=search_term_string',
					),
				)
			);
		}
	}
	add_action( 'wp_head', 'unysonplus_schema_site', 5 );
}

if ( ! function_exists( 'unysonplus_schema_article' ) ) {
	/**
	 * Article / BlogPosting node on singular blog posts.
	 *
	 * @return void
	 */
	function unysonplus_schema_article() {
		if ( ! unysonplus_should_emit_schema() || ! is_singular( 'post' ) ) {
			return;
		}

		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$site_url = home_url( '/' );
		$permalink = get_permalink( $post );

		$article = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'BlogPosting',
			'@id'              => $permalink . '#article',
			'mainEntityOfPage' => $permalink,
			'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'author'           => array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', $post->post_author ),
				'url'   => get_author_posts_url( $post->post_author ),
			),
			'publisher'        => array( '@id' => $site_url . '#organization' ),
		);

		$excerpt = has_excerpt( $post )
			? get_the_excerpt( $post )
			: wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 40, '…' );
		if ( '' !== trim( (string) $excerpt ) ) {
			$article['description'] = $excerpt;
		}

		if ( has_post_thumbnail( $post ) ) {
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'full' );
			if ( $img ) {
				$article['image'] = array(
					'@type'  => 'ImageObject',
					'url'    => $img[0],
					'width'  => (int) $img[1],
					'height' => (int) $img[2],
				);
			}
		}

		unysonplus_print_jsonld( $article );
	}
	add_action( 'wp_head', 'unysonplus_schema_article', 6 );
}
