<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

/**
 * Theme Menu Registration
 */
if ( ! function_exists( 'unysonplus_register_menus' ) ) :
function unysonplus_register_menus() {

    // Load Bootstrap Navwalker if not already loaded
    if ( ! class_exists( 'WP_Bootstrap_Navwalker' ) ) {
        require_once get_template_directory() . '/inc/class-wp-bootstrap-navwalker.php';
    }

    $menus = array();

    $menus['primary']    = __( 'Primary menu', 'unysonplus' );
    $menus['secondary']  = __( 'Secondary menu', 'unysonplus' );
    $menus['footer']     = __( 'Footer menu', 'unysonplus' );
    // Dedicated menus for the fullscreen/off-canvas header modes — so their menu
    // can differ from the header's Primary nav. Each falls back to Primary when
    // no menu is assigned (see unysonplus_drawer_nav_menu()).
    $menus['overlay']    = __( 'Overlay menu (Overlay header mode)', 'unysonplus' );
    $menus['off-canvas'] = __( 'Off-Canvas menu (Off-Canvas header mode)', 'unysonplus' );

    if ( function_exists( 'fw_get_db_settings_option' ) ) {
        $header_topbar = fw_get_db_settings_option( 'header_topbar' );

        if ( ! empty( $header_topbar['yes'] ) && is_array( $header_topbar['yes'] ) ) {
            if ( ! empty( $header_topbar['yes']['topbar_left_menu']['display'] ) && $header_topbar['yes']['topbar_left_menu']['display'] === 'yes' ) {
                $menus['top-left'] = __( 'Top left menu', 'unysonplus' );
            }
            if ( ! empty( $header_topbar['yes']['topbar_right_menu']['display'] ) && $header_topbar['yes']['topbar_right_menu']['display'] === 'yes' ) {
                $menus['top-right'] = __( 'Top right menu', 'unysonplus' );
            }
        }
    }

    register_nav_menus( $menus );

    // --- Menu Arguments for wp_nav_menu ---
    global $unysonplus_menus;
    $unysonplus_menus = array(
        'primary'   => array(
            'theme_location'       => 'primary',
            'depth'                => 4,
            'container'            => 'nav',
            'container_class'      => 'primary-navigation',
            'container_aria_label' => __( 'Primary', 'unysonplus' ),
            'menu_class'           => 'primary-menu',
            'item_spacing'         => 'discard',
            'fallback_cb'          => false,
        ),
        'secondary' => array(
            'theme_location'  => 'secondary',
            'depth'           => 4,
            'container'       => 'nav',
            'container_id'    => 'secondary-navigation',
            'container_class' => 'site-navigation secondary-navigation',
            'menu_class'      => 'nav navbar-nav me-auto',
            'link_class'      => 'nav-link',
            'link_before'     => '<span>',
            'link_after'      => '</span>',
            'item_spacing'    => 'discard',
            'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
            'walker'          => new WP_Bootstrap_Navwalker(),
        ),
        'footer'    => array(
            'theme_location'  => 'footer',
            'depth'           => 1,
            'container'       => 'nav',
            'container_id'    => 'footer-menu',
            'container_class' => 'footer-menu me-auto',
            'menu_class'      => '',
            'link_before'     => '<span>',
            'link_after'      => '</span>',
            'item_spacing'    => 'discard',
        ),
        // Overlay / Off-Canvas drawers: same markup contract as Primary
        // (container .primary-navigation + ul.primary-menu) so the drawer CSS/JS
        // — including the radial layout — target them unchanged.
        'overlay'   => array(
            'theme_location'       => 'overlay',
            'depth'                => 4,
            'container'            => 'nav',
            'container_class'      => 'primary-navigation',
            'container_aria_label' => __( 'Overlay', 'unysonplus' ),
            'menu_class'           => 'primary-menu',
            'item_spacing'         => 'discard',
            'fallback_cb'          => false,
        ),
        'off-canvas' => array(
            'theme_location'       => 'off-canvas',
            'depth'                => 4,
            'container'            => 'nav',
            'container_class'      => 'primary-navigation',
            'container_aria_label' => __( 'Off-canvas', 'unysonplus' ),
            'menu_class'           => 'primary-menu',
            'item_spacing'         => 'discard',
            'fallback_cb'          => false,
        ),
    );
}
add_action( 'after_setup_theme', 'unysonplus_register_menus' );
endif;


/**
 * Display a registered menu. If no menu is assigned to the location,
 * show an admin-only setup notice (visitors see nothing).
 */
if ( ! function_exists( 'unysonplus_nav_menu' ) ) :
function unysonplus_nav_menu( $menu_type ) {
    global $unysonplus_menus;

    if ( empty( $unysonplus_menus[ $menu_type ] ) ) {
        return;
    }

    if ( has_nav_menu( $menu_type ) ) {
        wp_nav_menu( $unysonplus_menus[ $menu_type ] );
        return;
    }

    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $location_label = $unysonplus_menus[ $menu_type ]['theme_location'];
    printf(
        '<div class="primary-menu-notice" role="note"><p>%s</p></div>',
        sprintf(
            wp_kses(
                /* translators: 1: menu location slug, 2: URL to the WordPress nav-menus screen */
                __( 'No menu assigned to the <strong>%1$s</strong> location. <a href="%2$s">Set one up &rarr;</a> <span class="primary-menu-notice__hint">(admins only)</span>', 'unysonplus' ),
                array(
                    'a'      => array( 'href' => array() ),
                    'strong' => array(),
                    'span'   => array( 'class' => array() ),
                )
            ),
            esc_html( $location_label ),
            esc_url( admin_url( 'nav-menus.php?action=locations' ) )
        )
    );
}
endif;


/**
 * Render a header drawer/overlay menu that has its own dedicated location
 * ('overlay' / 'off-canvas'). Uses that location when a menu is assigned to it;
 * otherwise falls back to the Primary menu so existing sites keep working
 * without configuring a second menu.
 */
if ( ! function_exists( 'unysonplus_drawer_nav_menu' ) ) :
function unysonplus_drawer_nav_menu( $location ) {
    if ( $location && has_nav_menu( $location ) ) {
        unysonplus_nav_menu( $location );
    } else {
        unysonplus_nav_menu( 'primary' );
    }
}
endif;