<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * This is for enqueing static files: css and javascript
 * wp_enqueue_style() and wp_enqueue_script()
 *
 * Theme-owned assets share a single version sourced from style.css's
 * Version: header so bumping that one value busts the browser cache
 * for every asset the theme ships.
 */

// Parent (template) version — wp_get_theme() with no args returns the CHILD
// when one is active, so pin to get_template() to keep the theme's own assets
// busting on the parent's Version: header regardless of any child theme.
$unysonplus_theme_version = wp_get_theme( get_template() )->get( 'Version' );
if ( empty( $unysonplus_theme_version ) ) {
        $unysonplus_theme_version = '1.0';
}

wp_enqueue_script(
        'font-awesome',
        'https://kit.fontawesome.com/9b0e88a93e.js',
        array(),
        null,
        array( 'strategy' => 'defer', 'in_footer' => false )
);

add_filter( 'script_loader_tag', function ( $tag, $handle ) {
        if ( 'font-awesome' === $handle && false === strpos( $tag, 'crossorigin' ) ) {
                $tag = str_replace( ' src=', ' crossorigin="anonymous" src=', $tag );
        }
        return $tag;
}, 10, 2 );

// Bootstrap 5 CSS is now shipped by the unysonplus plugin
// (`framework/static/css/bootstrap.min.css`, enqueued at priority 5 by
// `framework/includes/bootstrap.php`). The plugin's enqueue also includes
// a `wp_style_is( 'bootstrap', 'registered' )` dedup check — if a future
// scenario needs the theme to ship its own Bootstrap CSS again, we can
// add the enqueue back here and the plugin will step aside.

wp_enqueue_script(
        'bootstrap',
        get_template_directory_uri() . '/assets/js/bootstrap.min.js',
        array(),
        '5.3.3',
        true
);

wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        $unysonplus_theme_version,
        'all'
);

wp_enqueue_script(
        'theme',
        get_template_directory_uri() . '/assets/js/theme.js',
        array( 'jquery' ),
        $unysonplus_theme_version,
        true
);

wp_enqueue_script(
        'unysonplus-navigation',
        get_template_directory_uri() . '/assets/js/navigation.js',
        array(),
        $unysonplus_theme_version,
        true
);

wp_enqueue_script(
        'unysonplus-scroll-top',
        get_template_directory_uri() . '/assets/js/scroll-top.js',
        array(),
        $unysonplus_theme_version,
        true
);

if ( function_exists( 'unysonplus_misc_get' ) && unysonplus_misc_get( 'dark_mode_enable' ) === 'yes' ) {
        wp_enqueue_script(
                'unysonplus-theme-toggle',
                get_template_directory_uri() . '/assets/js/theme-toggle.js',
                array(),
                $unysonplus_theme_version,
                true
        );
}

// General Layout runtime — preloader, scroll progress bar, and
// auto-stacking of floating buttons. Enqueue when any floating-UI
// feature is active so the stack classifier always runs.
$unysonplus_needs_layout_js =
        ( function_exists( 'unysonplus_layout_get' ) && (
                unysonplus_layout_get( 'layout_preloader_style', 'none' ) !== 'none' ||
                unysonplus_layout_get( 'layout_scroll_progress', 'no' ) === 'yes'
        ) ) ||
        ( function_exists( 'unysonplus_misc_get' ) && (
                unysonplus_misc_get( 'scroll_top_enable' ) === 'yes' ||
                unysonplus_misc_get( 'dark_mode_enable' ) === 'yes'
        ) );

if ( $unysonplus_needs_layout_js ) {
        wp_enqueue_script(
                'unysonplus-layout',
                get_template_directory_uri() . '/assets/js/layout.js',
                array(),
                $unysonplus_theme_version,
                true
        );
}

/**
 * Stylesheet cascade order.
 *
 * The theme's style.css must load AFTER the framework / shortcode CSS so the
 * theme's design defaults win over the plugin's component styles. The user's
 * generated CSS (button presets + per-page dynamic) then loads after the theme
 * so site / page customizations still override it, and a child theme's
 * stylesheet loads dead last so it can override everything:
 *
 *   framework / shortcode CSS → parent-style → presets + dynamic → child-style
 *
 * We wire the dependency graph late on wp_enqueue_scripts (after every handle
 * is queued) so WordPress prints them in this order regardless of the priority
 * each one enqueued at.
 */
if ( is_child_theme() ) {
        wp_enqueue_style(
                'child-style',
                get_stylesheet_uri(),
                array( 'parent-style' ),
                wp_get_theme()->get( 'Version' ),
                'all'
        );
}

if ( ! function_exists( 'unysonplus_order_theme_stylesheets' ) ) {
        function unysonplus_order_theme_stylesheets() {
                $styles = wp_styles();
                if ( ! isset( $styles->registered['parent-style'] ) ) { return; }

                // Layers that must load AFTER the theme stylesheet.
                $after_theme = array( 'unysonplus-presets', 'unysonplus-dynamic', 'child-style' );

                // parent-style depends on every other enqueued stylesheet except the
                // "after theme" layer, so it prints right after the framework CSS.
                $deps = array();
                foreach ( $styles->queue as $handle ) {
                        if ( 'parent-style' === $handle || in_array( $handle, $after_theme, true ) ) { continue; }
                        $deps[] = $handle;
                }
                $styles->registered['parent-style']->deps = array_values( array_unique(
                        array_merge( (array) $styles->registered['parent-style']->deps, $deps )
                ) );

                // Child theme loads dead last — after the presets + per-page dynamic CSS.
                if ( isset( $styles->registered['child-style'] ) ) {
                        $child_deps = array( 'parent-style' );
                        foreach ( array( 'unysonplus-presets', 'unysonplus-dynamic' ) as $handle ) {
                                if ( isset( $styles->registered[ $handle ] ) ) { $child_deps[] = $handle; }
                        }
                        $styles->registered['child-style']->deps = array_values( array_unique(
                                array_merge( (array) $styles->registered['child-style']->deps, $child_deps )
                        ) );
                }
        }
}
add_action( 'wp_enqueue_scripts', 'unysonplus_order_theme_stylesheets', 9999 );
