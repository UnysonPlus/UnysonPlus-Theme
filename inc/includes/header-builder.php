<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

if ( ! function_exists( 'unysonplus_element_visibility_classes' ) ) :
/**
 * Map an element's per-device "Hide On" checkboxes to hide-xs/sm/md classes
 * (shared by header + footer element renderers). Handles both Unyson checkbox
 * shapes: associative (key => bool) and a plain list of selected keys.
 */
function unysonplus_element_visibility_classes( $element ) {
	$vis = ! empty( $element['visibility'] ) ? $element['visibility'] : array();
	if ( ! is_array( $vis ) ) { return ''; }
	$out = '';
	foreach ( array( 'hide-xs', 'hide-sm', 'hide-md' ) as $hc ) {
		$on = isset( $vis[ $hc ] ) ? ! empty( $vis[ $hc ] ) : in_array( $hc, $vis, true );
		if ( $on ) { $out .= ' ' . $hc; }
	}
	return $out;
}
endif;

if ( ! function_exists( 'unysonplus_render_primary_toggler' ) ) :
function unysonplus_render_primary_toggler() {
        static $rendered = false;
        if ( $rendered ) return;
        $rendered = true;
        echo '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primary-navigation" aria-controls="primary-navigation" aria-expanded="false" aria-label="' . esc_attr__( 'Toggle navigation', 'unysonplus' ) . '">';
        echo '<span class="navbar-toggler-icon"></span>';
        echo '</button>';
}
endif;


if ( ! function_exists( 'unysonplus_render_header_element' ) ) :
function unysonplus_render_header_element( $element ) {
        if ( empty( $element['element_type']['element'] ) ) return;

        $type     = $element['element_type']['element'];
        $settings = ! empty( $element['element_type'][ $type ] ) ? $element['element_type'][ $type ] : array();

        switch ( $type ) {
                case 'logo':
                        unysonplus_logo();
                        break;

                case 'primary_menu':
                        unysonplus_render_primary_menu_inline();
                        break;

                case 'secondary_menu':
                        unysonplus_nav_menu( 'secondary' );
                        break;

                case 'cta_button':
                        unysonplus_render_cta_button( $settings );
                        break;

                case 'phone':
                        unysonplus_render_phone( $settings );
                        break;

                case 'search':
                        unysonplus_render_search();
                        break;

                case 'social_icons':
                        unysonplus_render_social_icons();
                        break;

                case 'menu':
                        unysonplus_render_menu( $settings );
                        break;

                case 'menu_area':
                        if ( ! empty( $settings['menu_location'] ) && $settings['menu_location'] === 'primary' ) {
                                unysonplus_render_primary_menu_inline();
                        } else {
                                unysonplus_render_menu_area( $settings );
                        }
                        break;

                case 'text':
                        unysonplus_render_text_element( $settings );
                        break;

                case 'custom_html':
                        unysonplus_render_custom_html( $settings );
                        break;

                case 'widget_area':
                        if ( function_exists( 'unysonplus_render_widget_area' ) ) {
                                unysonplus_render_widget_area( $settings );
                        }
                        break;

                case 'builder_section':
                        if ( function_exists( 'unysonplus_render_builder_section' ) ) {
                                unysonplus_render_builder_section( $settings );
                        }
                        break;

                case 'spacer':
                        echo '<span class="header-spacer" aria-hidden="true"></span>';
                        break;

                case 'divider':
                        echo '<span class="header-divider" role="separator" aria-orientation="vertical"></span>';
                        break;
        }
}
endif;


if ( ! function_exists( 'unysonplus_render_header_column' ) ) :
/**
 * Render the inner elements for a header slot. The caller is responsible
 * for wrapping with the outer .header-col container (see
 * template-parts/header-builder.php), so this helper only emits the
 * per-element .header-element wrappers.
 *
 * @param array  $elements Element configs from header_layout.
 * @param string $align    Retained for backward compatibility; unused now
 *                         that alignment lives on the outer wrapper.
 */
function unysonplus_render_header_column( $elements, $align = 'start' ) {
        if ( empty( $elements ) || ! is_array( $elements ) ) return;

        foreach ( $elements as $element ) {
                if ( empty( $element['element_type']['element'] ) ) continue;
                $type = $element['element_type']['element'];
                echo '<div class="header-element header-element--' . esc_attr( $type ) . esc_attr( unysonplus_element_visibility_classes( $element ) ) . '">';
                unysonplus_render_header_element( $element );
                echo '</div>';
        }
}
endif;


if ( ! function_exists( 'unysonplus_render_primary_menu_inline' ) ) :
/**
 * Render the primary navigation inline inside a header slot. Shows the
 * registered primary menu when assigned; shows an admin-only setup
 * notice otherwise so site editors know to assign one.
 */
function unysonplus_render_primary_menu_inline() {
        if ( has_nav_menu( 'primary' ) ) {
                if ( function_exists( 'unysonplus_nav_menu' ) ) {
                        unysonplus_nav_menu( 'primary' );
                }
                return;
        }

        if ( ! current_user_can( 'edit_theme_options' ) ) {
                return;
        }

        echo '<div class="primary-menu-notice" role="note">';
        printf(
                '<p>%s</p>',
                sprintf(
                        wp_kses(
                                /* translators: %s: URL to the WordPress nav-menus screen */
                                __( 'No menu assigned to the <strong>Primary</strong> location. <a href="%s">Set one up &rarr;</a> <span class="primary-menu-notice__hint">(admins only)</span>', 'unysonplus' ),
                                array(
                                        'a'      => array( 'href' => array() ),
                                        'strong' => array(),
                                        'span'   => array( 'class' => array() ),
                                )
                        ),
                        esc_url( admin_url( 'nav-menus.php?action=locations' ) )
                )
        );
        echo '</div>';
}
endif;


if ( ! function_exists( 'unysonplus_header_has_primary_nav' ) ) :
function unysonplus_header_has_primary_nav( $columns ) {
        foreach ( $columns as $col ) {
                if ( ! is_array( $col ) ) continue;
                foreach ( $col as $element ) {
                        if ( empty( $element['element_type']['element'] ) ) continue;
                        $type = $element['element_type']['element'];
                        if ( $type === 'primary_menu' ) return true;
                        if ( $type === 'menu_area' && ! empty( $element['element_type']['menu_area']['menu_location'] )
                             && $element['element_type']['menu_area']['menu_location'] === 'primary' ) {
                                return true;
                        }
                }
        }
        return false;
}
endif;


if ( ! function_exists( 'unysonplus_render_cta_button' ) ) :
function unysonplus_render_cta_button( $settings ) {
        $text = ! empty( $settings['cta_text'] ) ? $settings['cta_text'] : 'Get Started';
        $link = ! empty( $settings['cta_link'] ) ? $settings['cta_link'] : '#';

        // Colors are emitted to the generated CSS file via a per-instance hash
        // class (see inc/includes/hf-custom-css.php) — no inline styles here.
        $classes = function_exists( 'unysonplus_cta_button_classes' )
                ? unysonplus_cta_button_classes( $settings )
                : 'header-cta-btn';

        echo '<a href="' . esc_url( $link ) . '" class="' . esc_attr( $classes ) . '">' . esc_html( $text ) . '</a>';
}
endif;


if ( ! function_exists( 'unysonplus_render_phone' ) ) :
function unysonplus_render_phone( $settings ) {
        $phone = ! empty( $settings['phone_number'] ) ? $settings['phone_number'] : '';
        if ( empty( $phone ) ) return;

        $phone_link = preg_replace( '/[^0-9+]/', '', $phone );
        echo '<a href="tel:' . esc_attr( $phone_link ) . '" class="header-phone">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/></svg>';
        echo ' <span>' . esc_html( $phone ) . '</span>';
        echo '</a>';
}
endif;


if ( ! function_exists( 'unysonplus_render_search' ) ) :
function unysonplus_render_search() {
        echo '<form role="search" method="get" class="header-search-form" action="' . esc_url( home_url( '/' ) ) . '">';
        echo '<input type="search" class="header-search-input" placeholder="' . esc_attr__( 'Search...', 'unysonplus' ) . '" value="' . get_search_query() . '" name="s" />';
        echo '<button type="submit" class="header-search-btn" aria-label="' . esc_attr__( 'Search', 'unysonplus' ) . '">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>';
        echo '</button>';
        echo '</form>';
}
endif;


if ( ! function_exists( 'unysonplus_render_social_icons' ) ) :
function unysonplus_render_social_icons() {
        if ( ! function_exists( 'fw_get_db_settings_option' ) ) return;
        $social_profiles = fw_get_db_settings_option( 'social_profiles' );
        if ( empty( $social_profiles ) || ! is_array( $social_profiles ) ) return;

        echo '<div class="header-social-icons">';
        foreach ( $social_profiles as $profile ) {
                if ( empty( $profile['link'] ) ) continue;
                $name = ! empty( $profile['name'] ) ? $profile['name'] : '';
                echo '<a href="' . esc_url( $profile['link'] ) . '" class="header-social-icon" target="_blank" rel="noopener noreferrer" title="' . esc_attr( $name ) . '">';
                if ( ! empty( $profile['icon']['icon-class'] ) ) {
                        echo '<i class="' . esc_attr( $profile['icon']['icon-class'] ) . '"></i>';
                } else {
                        echo esc_html( $name );
                }
                echo '</a>';
        }
        echo '</div>';
}
endif;


if ( ! function_exists( 'unysonplus_render_custom_html' ) ) :
function unysonplus_render_custom_html( $settings ) {
        if ( ! empty( $settings['custom_html_content'] ) ) {
                echo '<div class="header-custom-html">' . do_shortcode(  $settings['custom_html_content']  ) . '</div>';
        }
}
endif;
