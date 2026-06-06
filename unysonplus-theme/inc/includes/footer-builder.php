<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

if ( ! function_exists( 'unysonplus_get_footer_col_classes' ) ) :
function unysonplus_get_footer_col_classes( $layout ) {
        $map = array(
                '1-col'         => array( 'col-md-12' ),
                '2-equal'       => array( 'col-md-6', 'col-md-6' ),
                '2-1-3-2-3'     => array( 'col-md-4', 'col-md-8' ),
                '2-2-3-1-3'     => array( 'col-md-8', 'col-md-4' ),
                '2-1-4-3-4'     => array( 'col-md-3', 'col-md-9' ),
                '2-3-4-1-4'     => array( 'col-md-9', 'col-md-3' ),
                '3-equal'       => array( 'col-md-4', 'col-md-4', 'col-md-4' ),
                '3-1-2-1-4-1-4' => array( 'col-md-6', 'col-md-3', 'col-md-3' ),
                '3-1-4-1-4-1-2' => array( 'col-md-3', 'col-md-3', 'col-md-6' ),
                '3-1-4-1-2-1-4' => array( 'col-md-3', 'col-md-6', 'col-md-3' ),
                '3-5-2-5'       => array( 'col-md-5', 'col-md-2', 'col-md-5' ),
                '3-5-3-4'       => array( 'col-md-5', 'col-md-3', 'col-md-4' ),
                '4-equal'             => array( 'col-md-3', 'col-md-3', 'col-md-3', 'col-md-3' ),
                '4-1-3-1-6-1-4-1-4'  => array( 'col-md-4', 'col-md-2', 'col-md-3', 'col-md-3' ),
                '4-1-3-1-4-1-4-1-6'  => array( 'col-md-4', 'col-md-3', 'col-md-3', 'col-md-2' ),
                '4-1-3-1-3-1-6-1-6'  => array( 'col-md-4', 'col-md-4', 'col-md-2', 'col-md-2' ),
                '4-5-2-3-2-2'        => array( 'col-md-5', 'col-md-3', 'col-md-2', 'col-md-2' ),
                '4-2-2-3-5'          => array( 'col-md-2', 'col-md-2', 'col-md-3', 'col-md-5' ),
                '4-1-2-1-6-1-6-1-6'  => array( 'col-md-6', 'col-md-2', 'col-md-2', 'col-md-2' ),
                '4-1-6-1-6-1-6-1-2'  => array( 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-6' ),
                '5-equal'                => array( 'col', 'col', 'col', 'col', 'col' ),
                '5-1-3-1-6-1-6-1-6-1-6' => array( 'col-md-4', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2' ),
                '5-1-6-1-6-1-6-1-6-1-3' => array( 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-4' ),
        );
        return isset( $map[ $layout ] ) ? $map[ $layout ] : array( 'col-md-12' );
}
endif;


if ( ! function_exists( 'unysonplus_render_footer_element' ) ) :
function unysonplus_render_footer_element( $element ) {
        if ( empty( $element['element_type']['element'] ) ) return;

        $type     = $element['element_type']['element'];
        $settings = ! empty( $element['element_type'][ $type ] ) ? $element['element_type'][ $type ] : array();

        switch ( $type ) {
                case 'logo':
                        unysonplus_logo();
                        break;

                case 'footer_logo':
                        unysonplus_render_footer_logo( $settings );
                        break;

                case 'primary_menu':
                        unysonplus_nav_menu( 'primary' );
                        break;

                case 'secondary_menu':
                        unysonplus_nav_menu( 'secondary' );
                        break;

                case 'menu':
                        unysonplus_render_menu( $settings );
                        break;

                case 'menu_area':
                        unysonplus_render_menu_area( $settings );
                        break;

                case 'footer_menu':
                        unysonplus_render_footer_menu();
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

                case 'custom_html':
                        unysonplus_render_custom_html( $settings );
                        break;

                case 'text':
                        unysonplus_render_text_element( $settings );
                        break;

                case 'widget_area':
                        unysonplus_render_widget_area( $settings );
                        break;

                case 'copyright_text':
                        unysonplus_render_copyright_text( $settings );
                        break;

                case 'back_to_top':
                        unysonplus_render_back_to_top( $settings );
                        break;
        }
}
endif;


if ( ! function_exists( 'unysonplus_render_footer_column' ) ) :
function unysonplus_render_footer_column( $column_data, $col_class ) {
        if ( empty( $column_data ) || ! is_array( $column_data ) ) return;

        if ( isset( $column_data['elements'] ) ) {
                $column_data = $column_data['elements'];
        }

        if ( empty( $column_data ) ) return;

        echo '<div class="' . esc_attr( $col_class ) . '">';
        echo '<div class="footer-column">';
        foreach ( $column_data as $element ) {
                if ( empty( $element['element_type']['element'] ) ) continue;
                $type = $element['element_type']['element'];
                echo '<div class="footer-element footer-element--' . esc_attr( $type ) . '">';
                unysonplus_render_footer_element( $element );
                echo '</div>';
        }
        echo '</div>';
        echo '</div>';
}
endif;


if ( ! function_exists( 'unysonplus_extract_footer_columns_data' ) ) :
function unysonplus_extract_footer_columns_data( $section_data, $prefix ) {
        $columns_data = ! empty( $section_data[ $prefix . '_columns' ] ) ? $section_data[ $prefix . '_columns' ] : array();

        if ( empty( $columns_data ) ) {
                return array( 'layout' => '1-col', 'col_count' => 1, 'columns' => array() );
        }

        $count      = ! empty( $columns_data['count'] ) ? $columns_data['count'] : '1';
        $choice     = ! empty( $columns_data[ $count ] ) ? $columns_data[ $count ] : array();

        if ( $count === '1' ) {
                $layout = '1-col';
        } else {
                $layout = ! empty( $choice[ $prefix . '_layout' ] ) ? $choice[ $prefix . '_layout' ] : '1-col';
        }

        $cols = array();
        for ( $i = 1; $i <= (int) $count; $i++ ) {
                $col_key = $prefix . '_col_' . $i;
                $cols[ $i ] = ! empty( $choice[ $col_key ] ) ? $choice[ $col_key ] : array();
        }

        return array( 'layout' => $layout, 'col_count' => (int) $count, 'columns' => $cols );
}
endif;


if ( ! function_exists( 'unysonplus_render_footer_section' ) ) :
function unysonplus_render_footer_section( $section_data, $prefix, $section_class = '' ) {
        $extracted   = unysonplus_extract_footer_columns_data( $section_data, $prefix );
        $layout      = $extracted['layout'];
        $col_count   = $extracted['col_count'];
        $columns     = $extracted['columns'];
        $col_classes = unysonplus_get_footer_col_classes( $layout );

        $has_content = false;
        foreach ( $columns as $col_data ) {
                if ( ! empty( $col_data ) && is_array( $col_data ) ) {
                        $has_content = true;
                        break;
                }
        }
        if ( ! $has_content ) return;

        $custom_styling = ! empty( $section_data[ $prefix . '_custom_styling' ] ) ? $section_data[ $prefix . '_custom_styling' ] : array();
        $has_custom     = ! empty( $custom_styling['enabled'] ) && $custom_styling['enabled'] === 'yes';
        $cs             = $has_custom && ! empty( $custom_styling['yes'] ) ? $custom_styling['yes'] : array();

        $container = ! empty( $cs[ $prefix . '_container' ] ) ? $cs[ $prefix . '_container' ] : 'container';

        $classes = array( 'footer-section', 'footer-section--' . str_replace( '_', '-', $prefix ) );
        if ( ! empty( $cs[ $prefix . '_css_class' ] ) ) $classes[] = $cs[ $prefix . '_css_class' ];
        if ( ! empty( $section_class ) ) $classes[] = $section_class;

        $styles    = array();
        $link_css  = '';
        $overlay   = '';

        if ( $has_custom ) {
                $bg_color       = ! empty( $cs[ $prefix . '_bg_color' ] ) ? $cs[ $prefix . '_bg_color' ] : '';
                $bg_image       = ! empty( $cs[ $prefix . '_bg_image' ]['url'] ) ? $cs[ $prefix . '_bg_image' ]['url'] : '';
                $text_color     = ! empty( $cs[ $prefix . '_text_color' ] ) ? $cs[ $prefix . '_text_color' ] : '';
                $link_color     = ! empty( $cs[ $prefix . '_link_color' ] ) ? $cs[ $prefix . '_link_color' ] : '';
                $padding_top    = ! empty( $cs[ $prefix . '_padding_top' ] ) ? $cs[ $prefix . '_padding_top' ] : '';
                $padding_bottom = ! empty( $cs[ $prefix . '_padding_bottom' ] ) ? $cs[ $prefix . '_padding_bottom' ] : '';
                $border_color   = ! empty( $cs[ $prefix . '_border_top_color' ] ) ? $cs[ $prefix . '_border_top_color' ] : '';
                $border_width   = ! empty( $cs[ $prefix . '_border_top_width' ] ) ? $cs[ $prefix . '_border_top_width' ] : '';

                if ( ! empty( $text_color ) )     $styles[] = 'color: ' . $text_color;
                if ( ! empty( $padding_top ) )    $styles[] = 'padding-top: ' . $padding_top;
                if ( ! empty( $padding_bottom ) ) $styles[] = 'padding-bottom: ' . $padding_bottom;
                if ( ! empty( $link_color ) )     $link_css = '--footer-link-color: ' . $link_color;

                if ( ! empty( $bg_image ) ) {
                        $styles[] = 'background-image: url(' . esc_url( $bg_image ) . ')';
                        $styles[] = 'background-size: cover';
                        $styles[] = 'background-position: center';
                        $bg_overlay  = isset( $cs[ $prefix . '_bg_overlay' ] ) ? intval( $cs[ $prefix . '_bg_overlay' ] ) : 80;
                        $overlay_bg  = ! empty( $bg_color ) ? $bg_color : 'rgba(0,0,0,0.8)';
                        $overlay     = '<div class="footer-section__overlay" style="background: ' . esc_attr( $overlay_bg ) . '; opacity: ' . esc_attr( $bg_overlay / 100 ) . ';"></div>';
                } elseif ( ! empty( $bg_color ) ) {
                        $styles[] = 'background-color: ' . $bg_color;
                }

                if ( ! empty( $border_color ) && ! empty( $border_width ) ) {
                        $styles[] = 'border-top: ' . $border_width . ' solid ' . $border_color;
                }
        }

        $style_attr = '';
        if ( ! empty( $styles ) || ! empty( $link_css ) ) {
                $parts = array();
                if ( ! empty( $styles ) ) $parts[] = implode( '; ', $styles );
                if ( ! empty( $link_css ) ) $parts[] = $link_css;
                $style_attr = ' style="' . esc_attr( implode( '; ', $parts ) ) . '"';
        }
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $style_attr; ?>>
                <?php echo $overlay; ?>
                <div class="<?php echo esc_attr( $container ); ?> footer-section__inner">
                        <div class="row footer-row">
                                <?php
                                for ( $i = 1; $i <= $col_count; $i++ ) {
                                        $col_data  = isset( $columns[ $i ] ) ? $columns[ $i ] : array();
                                        $col_class = isset( $col_classes[ $i - 1 ] ) ? $col_classes[ $i - 1 ] : 'col';
                                        unysonplus_render_footer_column( $col_data, $col_class );
                                }
                                ?>
                        </div>
                </div>
        </div>
        <?php
}
endif;


if ( ! function_exists( 'unysonplus_render_footer_logo' ) ) :
function unysonplus_render_footer_logo( $settings ) {
        $image     = ! empty( $settings['footer_logo_image']['url'] ) ? $settings['footer_logo_image']['url'] : '';
        $max_width = function_exists( 'unysonplus_css_length' )
                ? unysonplus_css_length( ! empty( $settings['footer_logo_width'] ) ? $settings['footer_logo_width'] : '' )
                : '';
        if ( $max_width === '' ) { $max_width = '12.5rem'; }

        if ( empty( $image ) ) {
                unysonplus_logo();
                return;
        }

        echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="footer-logo-link">';
        echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="footer-logo-img" style="max-width: ' . esc_attr( $max_width ) . ';">';
        echo '</a>';
}
endif;


if ( ! function_exists( 'unysonplus_render_footer_menu' ) ) :
function unysonplus_render_footer_menu() {
        if ( ! has_nav_menu( 'footer' ) ) return;

        wp_nav_menu( array(
                'theme_location' => 'footer',
                'menu_class'     => 'footer-menu list-unstyled',
                'container'      => 'nav',
                'container_class' => 'footer-menu-nav',
                'depth'          => 1,
                'item_spacing'   => 'discard',
        ) );
}
endif;


if ( ! function_exists( 'unysonplus_render_copyright_text' ) ) :
function unysonplus_render_copyright_text( $settings ) {
        // Defensive fallback — if copyright_content is empty (user cleared it,
        // or the stored element-list never persisted it), use a sensible
        // default so the footer is never silently blank.
        $text = ! empty( $settings['copyright_content'] )
                ? $settings['copyright_content']
                : sprintf( '&copy; {year} %s. All rights reserved.', get_bloginfo( 'name' ) );

        $text = str_replace( '{year}', date( 'Y' ), $text );
        echo '<div class="footer-copyright-text">' . do_shortcode( wp_kses_post( $text ) ) . '</div>';
}
endif;


if ( ! function_exists( 'unysonplus_render_back_to_top' ) ) :
function unysonplus_render_back_to_top( $settings ) {
        $text  = ! empty( $settings['back_to_top_text'] ) ? $settings['back_to_top_text'] : '';
        $label = ( $text !== '' ) ? $text : __( 'Back to top', 'unysonplus' );
        echo '<a href="#top" class="footer-back-to-top" aria-label="' . esc_attr( $label ) . '">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/></svg>';
        if ( ! empty( $text ) ) {
                echo ' <span>' . esc_html( $text ) . '</span>';
        }
        echo '</a>';
}
endif;


if ( ! function_exists( 'unysonplus_render_widget_area' ) ) :
function unysonplus_render_widget_area( $settings ) {
        $sidebar_id = ! empty( $settings['sidebar_id'] ) ? $settings['sidebar_id'] : '';
        if ( empty( $sidebar_id ) ) return;

        if ( is_active_sidebar( $sidebar_id ) ) {
                echo '<div class="footer-widget-area">';
                dynamic_sidebar( $sidebar_id );
                echo '</div>';
        }
}
endif;


if ( ! function_exists( 'unysonplus_render_text_element' ) ) :
function unysonplus_render_text_element( $settings ) {
        $content = ! empty( $settings['text_content'] ) ? $settings['text_content'] : '';
        if ( empty( $content ) ) return;

        echo '<div class="builder-text-element">' . do_shortcode( wpautop( wp_kses_post( $content ) ) ) . '</div>';
}
endif;


if ( ! function_exists( 'unysonplus_render_menu' ) ) :
function unysonplus_render_menu( $settings ) {
        $menu_id = ! empty( $settings['menu_id'] ) ? $settings['menu_id'] : '';
        if ( empty( $menu_id ) ) return;

        $menu = wp_get_nav_menu_object( $menu_id );
        if ( ! $menu ) return;

        wp_nav_menu( array(
                'menu'            => $menu_id,
                'depth'           => 2,
                'container'       => 'nav',
                'container_class' => 'builder-menu',
                'menu_class'      => 'builder-menu-list list-unstyled',
                'item_spacing'    => 'discard',
        ) );
}
endif;


if ( ! function_exists( 'unysonplus_render_menu_area' ) ) :
function unysonplus_render_menu_area( $settings ) {
        $location = ! empty( $settings['menu_location'] ) ? $settings['menu_location'] : '';
        if ( empty( $location ) ) return;

        if ( function_exists( 'unysonplus_nav_menu' ) ) {
                unysonplus_nav_menu( $location );
        }
}
endif;


if ( ! function_exists( 'unysonplus_render_footer_fallback' ) ) :
/**
 * Render a sensible default footer when Unyson isn't active or the user
 * hasn't configured any footer sections. Renders any populated footer
 * widget areas (footer-1..footer-5) first, then a copyright line.
 */
function unysonplus_render_footer_fallback() {
        $widget_ids = array( 'footer-1', 'footer-2', 'footer-3', 'footer-4', 'footer-5' );
        $active     = array_values( array_filter( $widget_ids, 'is_active_sidebar' ) );

        if ( ! empty( $active ) ) {
                $count     = count( $active );
                $col_class = 'col-md-' . max( 2, intval( 12 / $count ) );
                ?>
                <div class="footer-section footer-section--widgets">
                        <div class="container">
                                <div class="row">
                                        <?php foreach ( $active as $id ) : ?>
                                                <div class="<?php echo esc_attr( $col_class ); ?>">
                                                        <?php dynamic_sidebar( $id ); ?>
                                                </div>
                                        <?php endforeach; ?>
                                </div>
                        </div>
                </div>
                <?php
        }

        ?>
        <div class="footer-section footer-section--copyright footer-fallback">
                <div class="container">
                        <p>
                                <?php
                                printf(
                                        /* translators: 1: current year, 2: site name */
                                        wp_kses_post( __( '&copy; %1$s %2$s. All rights reserved.', 'unysonplus' ) ),
                                        esc_html( gmdate( 'Y' ) ),
                                        esc_html( get_bloginfo( 'name' ) )
                                );
                                ?>
                        </p>
                </div>
        </div>
        <?php
}
endif;

