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

// Font Awesome icons. The kit URL is overridable so the theme isn't hard-wired
// to one external account (a liability for a distributable theme + an external
// request per page view). Override priority: the UNYSONPLUS_FONTAWESOME_KIT
// constant (wp-config) → the 'unysonplus_fontawesome_kit_url' filter. Return an
// empty value from either to DISABLE Font Awesome entirely (e.g. when
// self-hosting icons or relying on an icon font shipped by a plugin).
$unysonplus_fa_kit = defined( 'UNYSONPLUS_FONTAWESOME_KIT' )
        ? UNYSONPLUS_FONTAWESOME_KIT
        : 'https://kit.fontawesome.com/9b0e88a93e.js';
$unysonplus_fa_kit = apply_filters( 'unysonplus_fontawesome_kit_url', $unysonplus_fa_kit );

if ( ! empty( $unysonplus_fa_kit ) ) {
        wp_enqueue_script(
                'font-awesome',
                $unysonplus_fa_kit,
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
}

// Bootstrap 5 CSS. The plugin may ship it (enqueued at priority 5 as handle
// 'bootstrap'); prefer that to avoid a double load. But the theme MUST NOT
// depend on the plugin for its grid/container/spacing — some plugin builds
// don't bundle Bootstrap, which left the front end with no `.container`
// (content flush to the viewport edges). So the theme ships its own copy and
// enqueues it only when nothing else already provided the 'bootstrap' handle.
if ( ! wp_style_is( 'bootstrap', 'enqueued' ) && ! wp_style_is( 'bootstrap', 'registered' ) ) {
        wp_enqueue_style(
                'bootstrap',
                get_template_directory_uri() . '/assets/css/bootstrap.min.css',
                array(),
                '5.3.3',
                'all'
        );
}

wp_enqueue_script(
        'bootstrap',
        get_template_directory_uri() . '/assets/js/bootstrap.min.js',
        array(),
        '5.3.3',
        true
);

// parent-style depends on 'bootstrap' so the theme's tokens/overrides always
// cascade AFTER Bootstrap, whether Bootstrap came from the plugin or the theme.
wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        array( 'bootstrap' ),
        $unysonplus_theme_version,
        'all'
);

// RTL overlay — only on RTL locales. Mirrors the theme's physical directional
// rules; Bootstrap's logical utilities + dir="rtl" handle everything else.
// Loads AFTER parent-style (it's in the "after theme" cascade layer below).
if ( is_rtl() ) {
        wp_enqueue_style(
                'unysonplus-rtl',
                get_template_directory_uri() . '/assets/css/rtl.css',
                array( 'parent-style' ),
                $unysonplus_theme_version,
                'all'
        );
}

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

// Header & Footer Builder — element styles (nav/logo/search/social shortcodes)
// + builder-header type/behavior chrome, and the hide-on-scroll behavior JS.
// Loaded globally because the nav shortcodes can appear in normal page content.
wp_enqueue_style(
        'unysonplus-hf-builder',
        get_template_directory_uri() . '/assets/css/header-footer-builder.css',
        array(),
        $unysonplus_theme_version,
        'all'
);

wp_enqueue_script(
        'unysonplus-header-behaviors',
        get_template_directory_uri() . '/assets/js/header-behaviors.js',
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

if ( ! function_exists( 'unysonplus_style_handle_resolvable' ) ) {
        /**
         * True only when a style handle AND its entire dependency subtree are
         * registered, with no cycle back to the theme stylesheet.
         *
         * The cascade orderer below makes `parent-style` depend on the other queued
         * stylesheets. If any of those carries a missing or cyclic dependency, that
         * unresolvable link propagates up into `parent-style`, and WordPress then
         * silently refuses to print it (and `child-style` with it) — a blank,
         * unstyled site with no error. This guard lets the orderer skip such a
         * handle instead of folding it in.
         *
         * @param WP_Styles $styles
         * @param string    $handle
         * @param array     $stack  Recursion / cycle guard.
         * @return bool
         */
        function unysonplus_style_handle_resolvable( $styles, $handle, $stack = array() ) {
                if ( ! isset( $styles->registered[ $handle ] ) ) { return false; }   // unregistered dep
                if ( in_array( $handle, $stack, true ) ) { return false; }           // cycle
                $stack[] = $handle;
                foreach ( (array) $styles->registered[ $handle ]->deps as $dep ) {
                        if ( 'parent-style' === $dep ) { return false; }            // would cycle through the theme stylesheet
                        if ( ! unysonplus_style_handle_resolvable( $styles, $dep, $stack ) ) { return false; }
                }
                return true;
        }
}

if ( ! function_exists( 'unysonplus_order_theme_stylesheets' ) ) {
        function unysonplus_order_theme_stylesheets() {
                $styles = wp_styles();
                if ( ! isset( $styles->registered['parent-style'] ) ) { return; }

                // Layers that must load AFTER the theme stylesheet.
                $after_theme = array( 'unysonplus-rtl', 'unysonplus-presets', 'unysonplus-dynamic', 'unysonplus-hf-custom', 'unysonplus-hf-custom-inline', 'child-style' );

                // parent-style depends on every other enqueued stylesheet except the
                // "after theme" layer, so it prints right after the framework CSS.
                // Only RESOLVABLE handles are folded in — a handle with a missing or
                // cyclic dependency is skipped (and reported) so it can never silently
                // break the whole cascade.
                $deps    = array();
                $skipped = array();
                foreach ( $styles->queue as $handle ) {
                        if ( 'parent-style' === $handle || in_array( $handle, $after_theme, true ) ) { continue; }
                        if ( unysonplus_style_handle_resolvable( $styles, $handle ) ) {
                                $deps[] = $handle;
                        } else {
                                $skipped[] = $handle;
                        }
                }
                $styles->registered['parent-style']->deps = array_values( array_unique(
                        array_merge( (array) $styles->registered['parent-style']->deps, $deps )
                ) );

                // Last-resort guarantee: if parent-style is somehow still unresolvable
                // (e.g. a broken pre-existing dep), drop the extra deps so the theme
                // stylesheet ALWAYS prints. A slightly-off cascade order beats an
                // unstyled site.
                if ( ! unysonplus_style_handle_resolvable( $styles, 'parent-style' ) ) {
                        $styles->registered['parent-style']->deps = array();
                }

                // Child theme loads dead last — after the presets + per-page dynamic CSS
                // AND the header/footer generated CSS (so the order is
                // parent-style → presets → dynamic → hf-custom → child-style).
                //
                // Do NOT gate these with unysonplus_style_handle_resolvable(): that helper
                // reports any handle depending on `parent-style` as unresolvable (a cycle),
                // which is the right guard only when building parent-style's OWN deps. The
                // after-theme layers (hf-custom / hf-custom-inline) legitimately depend on
                // parent-style, and child-style depending on THEM is not a cycle — so the
                // guard would wrongly drop hf-custom and let child-style print before it. A
                // plain registered check is correct and safe here (parent-style is always
                // registered by the time this runs, per the early return above).
                if ( isset( $styles->registered['child-style'] ) ) {
                        $child_deps = array( 'parent-style' );
                        foreach ( array( 'unysonplus-rtl', 'unysonplus-presets', 'unysonplus-dynamic', 'unysonplus-hf-custom', 'unysonplus-hf-custom-inline' ) as $handle ) {
                                if ( isset( $styles->registered[ $handle ] ) ) { $child_deps[] = $handle; }
                        }
                        $styles->registered['child-style']->deps = array_values( array_unique(
                                array_merge( (array) $styles->registered['child-style']->deps, $child_deps )
                        ) );
                }

                // Surface skipped handles loudly instead of failing silently: log for
                // developers (WP_DEBUG) and stash for an admin notice.
                if ( ! empty( $skipped ) ) {
                        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                                error_log( 'UnysonPlus: stylesheet ordering skipped unresolvable handle(s): ' . implode( ', ', $skipped ) . ' — missing or cyclic dependency. Check the wp_enqueue_style() deps for each.' );
                        }
                        set_transient( 'unysonplus_style_order_skipped', $skipped, HOUR_IN_SECONDS );
                } elseif ( false !== get_transient( 'unysonplus_style_order_skipped' ) ) {
                        delete_transient( 'unysonplus_style_order_skipped' );
                }
        }
}
add_action( 'wp_enqueue_scripts', 'unysonplus_order_theme_stylesheets', 9999 );

if ( ! function_exists( 'unysonplus_style_order_admin_notice' ) ) {
        /**
         * Admin warning when the cascade orderer had to skip a stylesheet handle —
         * so a mis-slotted enqueue is visible instead of being discovered via a
         * broken front end.
         */
        function unysonplus_style_order_admin_notice() {
                if ( ! current_user_can( 'edit_theme_options' ) ) { return; }
                $skipped = get_transient( 'unysonplus_style_order_skipped' );
                if ( empty( $skipped ) || ! is_array( $skipped ) ) { return; }
                echo '<div class="notice notice-warning"><p><strong>'
                        . esc_html__( 'UnysonPlus theme', 'unysonplus' ) . ':</strong> '
                        . esc_html__( 'these stylesheet handles could not be folded into the theme cascade (missing or cyclic dependency):', 'unysonplus' )
                        . ' <code>' . esc_html( implode( ', ', $skipped ) ) . '</code>. '
                        . esc_html__( 'They still print on their own, but check their wp_enqueue_style() dependencies.', 'unysonplus' )
                        . '</p></div>';
        }
}
add_action( 'admin_notices', 'unysonplus_style_order_admin_notice' );

if ( ! function_exists( 'unysonplus_assert_theme_styles_printed' ) ) {
        /**
         * Dev smoke check (WP_DEBUG, front end only): the theme stylesheet — and the
         * child stylesheet when a child theme is active — MUST print on every
         * request. If the cascade orderer or a bad dependency ever drops them again
         * (the exact failure that blanked the site once), this logs loudly at
         * wp_footer instead of letting a silently-unstyled page ship. Inert in
         * production (WP_DEBUG off) and never prints output — log only.
         */
        function unysonplus_assert_theme_styles_printed() {
                if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || is_admin() ) { return; }
                $required = array( 'parent-style' );
                if ( is_child_theme() ) { $required[] = 'child-style'; }
                foreach ( $required as $handle ) {
                        if ( ! wp_style_is( $handle, 'done' ) ) {
                                error_log( 'UnysonPlus SMOKE CHECK FAILED: required stylesheet "' . $handle . '" did not print — the theme CSS cascade is broken (check stylesheet dependencies / ordering in inc/static.php).' );
                        }
                }
        }
}
add_action( 'wp_footer', 'unysonplus_assert_theme_styles_printed', 9999 );
