<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }
/**
 * WooCommerce compatibility layer.
 *
 * Auto-loaded by Theme_Includes for every request (this `includes` folder is in
 * the auto-include list in inc/init.php). Everything past the guard below is
 * gated behind a single class_exists( 'WooCommerce' ) check, so the file is
 * inert — and the theme behaves exactly as before — when WooCommerce isn't
 * installed / active.
 *
 * What it does:
 *   - Renders shop / product / cart / checkout / account pages inside the
 *     theme's own content wrapper (unysonplus_main_wrapper_open/close) instead
 *     of WooCommerce's default <div id="primary"><main> markup, so shop pages
 *     inherit the theme's .container width, sidebar system and layout overrides.
 *   - Routes the shop sidebar through the theme's single sidebar system (one
 *     sidebar, not WooCommerce's separate get_sidebar('shop')) and defaults
 *     shop pages to no sidebar (filterable).
 *   - Sensible, filterable product-grid / related / per-page counts.
 *   - Enqueues a small woocommerce.css compat sheet, only on WC pages.
 *
 * The add_theme_support('woocommerce') + gallery-feature declarations live in
 * inc/hooks.php's after_setup_theme callback.
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/* ============================================================
 * Content wrapper — use the theme's wrapper, not WooCommerce's.
 * ============================================================ */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'unysonplus_woocommerce_wrapper_open' ) ) :
function unysonplus_woocommerce_wrapper_open() {
	if ( function_exists( 'unysonplus_main_wrapper_open' ) ) {
		unysonplus_main_wrapper_open( 'woocommerce-content' );
	}
}
endif;
add_action( 'woocommerce_before_main_content', 'unysonplus_woocommerce_wrapper_open', 10 );

if ( ! function_exists( 'unysonplus_woocommerce_wrapper_close' ) ) :
function unysonplus_woocommerce_wrapper_close() {
	if ( function_exists( 'unysonplus_main_wrapper_close' ) ) {
		unysonplus_main_wrapper_close();
	}
}
endif;
add_action( 'woocommerce_after_main_content', 'unysonplus_woocommerce_wrapper_close', 10 );

// The theme renders its own sidebar inside the wrapper close, so suppress
// WooCommerce's separate get_sidebar('shop') to avoid a double sidebar.
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );


/* ============================================================
 * Layout — route WC pages through the theme's override system.
 *
 * Default: no sidebar (clean full-width shop). Override per the
 * `unysonplus_woocommerce_sidebar` filter ('none' | 'left' | 'right').
 * The WooCommerce *extension* (Unyson+ → Extensions → WooCommerce) feeds these
 * `unysonplus_woocommerce_*` filters from its own Shop settings; the theme just
 * defines the defaults below.
 * ============================================================ */

if ( ! function_exists( 'unysonplus_woocommerce_is_page' ) ) :
function unysonplus_woocommerce_is_page() {
	return function_exists( 'is_woocommerce' ) && (
		is_woocommerce() || is_cart() || is_checkout() || is_account_page()
	);
}
endif;

if ( ! function_exists( 'unysonplus_woocommerce_set_layout' ) ) :
function unysonplus_woocommerce_set_layout() {
	if ( ! unysonplus_woocommerce_is_page() ) {
		return;
	}

	/**
	 * Sidebar position for WooCommerce pages.
	 *
	 * @param string $position 'none' | 'left' | 'right'. Default 'none'.
	 */
	$sidebar = apply_filters( 'unysonplus_woocommerce_sidebar', 'none' );

	if ( function_exists( 'unysonplus_set_layout_override' ) ) {
		unysonplus_set_layout_override( array( 'sidebar' => $sidebar ) );
	}
}
endif;
add_action( 'template_redirect', 'unysonplus_woocommerce_set_layout' );


/* ============================================================
 * Product-grid / related / per-page counts (all filterable).
 * ============================================================ */

add_filter( 'loop_shop_columns', function () {
	return (int) apply_filters( 'unysonplus_woocommerce_loop_columns', 3 );
} );

add_filter( 'loop_shop_per_page', function () {
	return (int) apply_filters( 'unysonplus_woocommerce_products_per_page', 12 );
}, 20 );

add_filter( 'woocommerce_product_thumbnails_columns', function () {
	return (int) apply_filters( 'unysonplus_woocommerce_thumbnail_columns', 4 );
} );

add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = (int) apply_filters( 'unysonplus_woocommerce_related_count', 3 );
	$args['columns']        = (int) apply_filters( 'unysonplus_woocommerce_loop_columns', 3 );
	return $args;
} );


/* ============================================================
 * Styles — small compat sheet, enqueued only on WC pages.
 * ============================================================ */

if ( ! function_exists( 'unysonplus_woocommerce_styles' ) ) :
function unysonplus_woocommerce_styles() {
	if ( ! unysonplus_woocommerce_is_page() ) {
		return;
	}

	$ver = wp_get_theme( get_template() )->get( 'Version' );
	if ( empty( $ver ) ) { $ver = '1.0'; }

	// No dependency on parent-style: inc/static.php's late reorder makes
	// parent-style depend on every other queued sheet, so a parent-style dep
	// here would form a cycle. Empty deps → this loads before parent-style,
	// which is fine (it only targets WC-specific selectors the theme ignores).
	wp_enqueue_style(
		'unysonplus-woocommerce',
		get_template_directory_uri() . '/inc/css/woocommerce.css',
		array(),
		$ver,
		'all'
	);
}
endif;
add_action( 'wp_enqueue_scripts', 'unysonplus_woocommerce_styles', 20 );
