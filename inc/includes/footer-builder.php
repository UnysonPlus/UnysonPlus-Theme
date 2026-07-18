<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

if ( ! function_exists( 'unysonplus_get_footer_col_classes' ) ) :
function unysonplus_get_footer_col_classes( $layout ) {
        $map = array(
                '1-col'         => array( 'fw-col-md-12' ),
                '2-equal'       => array( 'fw-col-md-6', 'fw-col-md-6' ),
                '2-1-3-2-3'     => array( 'fw-col-md-4', 'fw-col-md-8' ),
                '2-2-3-1-3'     => array( 'fw-col-md-8', 'fw-col-md-4' ),
                '2-1-4-3-4'     => array( 'fw-col-md-3', 'fw-col-md-9' ),
                '2-3-4-1-4'     => array( 'fw-col-md-9', 'fw-col-md-3' ),
                '3-equal'       => array( 'fw-col-md-4', 'fw-col-md-4', 'fw-col-md-4' ),
                '3-1-2-1-4-1-4' => array( 'fw-col-md-6', 'fw-col-md-3', 'fw-col-md-3' ),
                '3-1-4-1-4-1-2' => array( 'fw-col-md-3', 'fw-col-md-3', 'fw-col-md-6' ),
                '3-1-4-1-2-1-4' => array( 'fw-col-md-3', 'fw-col-md-6', 'fw-col-md-3' ),
                '3-5-2-5'       => array( 'fw-col-md-5', 'fw-col-md-2', 'fw-col-md-5' ),
                '3-5-3-4'       => array( 'fw-col-md-5', 'fw-col-md-3', 'fw-col-md-4' ),
                '4-equal'             => array( 'fw-col-md-3', 'fw-col-md-3', 'fw-col-md-3', 'fw-col-md-3' ),
                '4-1-3-1-6-1-4-1-4'  => array( 'fw-col-md-4', 'fw-col-md-2', 'fw-col-md-3', 'fw-col-md-3' ),
                '4-1-3-1-4-1-4-1-6'  => array( 'fw-col-md-4', 'fw-col-md-3', 'fw-col-md-3', 'fw-col-md-2' ),
                '4-1-3-1-3-1-6-1-6'  => array( 'fw-col-md-4', 'fw-col-md-4', 'fw-col-md-2', 'fw-col-md-2' ),
                '4-5-2-3-2-2'        => array( 'fw-col-md-5', 'fw-col-md-3', 'fw-col-md-2', 'fw-col-md-2' ),
                '4-2-2-3-5'          => array( 'fw-col-md-2', 'fw-col-md-2', 'fw-col-md-3', 'fw-col-md-5' ),
                '4-1-2-1-6-1-6-1-6'  => array( 'fw-col-md-6', 'fw-col-md-2', 'fw-col-md-2', 'fw-col-md-2' ),
                '4-1-6-1-6-1-6-1-2'  => array( 'fw-col-md-2', 'fw-col-md-2', 'fw-col-md-2', 'fw-col-md-6' ),
                '5-equal'                => array( 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15' ),
                '5-1-3-1-6-1-6-1-6-1-6' => array( 'fw-col-md-4', 'fw-col-md-2', 'fw-col-md-2', 'fw-col-md-2', 'fw-col-md-2' ),
                '5-1-6-1-6-1-6-1-6-1-3' => array( 'fw-col-md-2', 'fw-col-md-2', 'fw-col-md-2', 'fw-col-md-2', 'fw-col-md-4' ),
                // UnysonPlus fifths (2/5..4/5 span the fifth grid): 2-col, 3-col and 4-col compositions of the 5-unit grid.
                'f5-2-1-1-1' => array( 'fw-col-12 fw-col-sm-25', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15' ),
                'f5-1-2-1-1' => array( 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-25', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15' ),
                'f5-1-1-2-1' => array( 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-25', 'fw-col-12 fw-col-sm-15' ),
                'f5-1-1-1-2' => array( 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-25' ),
                'f5-3-1-1'   => array( 'fw-col-12 fw-col-sm-35', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15' ),
                'f5-1-3-1'   => array( 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-35', 'fw-col-12 fw-col-sm-15' ),
                'f5-1-1-3'   => array( 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-35' ),
                'f5-2-2-1'   => array( 'fw-col-12 fw-col-sm-25', 'fw-col-12 fw-col-sm-25', 'fw-col-12 fw-col-sm-15' ),
                'f5-2-1-2'   => array( 'fw-col-12 fw-col-sm-25', 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-25' ),
                'f5-1-2-2'   => array( 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-25', 'fw-col-12 fw-col-sm-25' ),
                'f5-4-1'     => array( 'fw-col-12 fw-col-sm-45', 'fw-col-12 fw-col-sm-15' ),
                'f5-1-4'     => array( 'fw-col-12 fw-col-sm-15', 'fw-col-12 fw-col-sm-45' ),
                'f5-3-2'     => array( 'fw-col-12 fw-col-sm-35', 'fw-col-12 fw-col-sm-25' ),
                'f5-2-3'     => array( 'fw-col-12 fw-col-sm-25', 'fw-col-12 fw-col-sm-35' ),
        );
        return isset( $map[ $layout ] ) ? $map[ $layout ] : array( 'fw-col-md-12' );
}
endif;


if ( ! function_exists( 'unysonplus_footer_widths_to_grid' ) ) :
/**
 * Snap a list of column widths (percentages, any scale) to a grid and return the
 * matching column classes. Normally the page-builder 12-column grid (`fw-col-md-N`, each
 * 1..12, summing to 12). SPECIAL CASE: five EQUAL columns use the plugin's one-fifth
 * grid class (`fw-col-12 fw-col-sm-15`, 20% each) — five equal columns can't be
 * expressed on a 12-grid, and 1/5 is the only supported fifth width (no 2/5..4/5).
 *
 * @param array $widths one width per column
 * @param int   $count  expected column count (defensive fallback)
 * @return string[] e.g. array( 'fw-col-md-6', 'fw-col-md-3', 'fw-col-md-3' )
 */
function unysonplus_footer_widths_to_grid( $widths, $count ) {
        $count = max( 1, (int) $count );
        if ( ! is_array( $widths ) || count( $widths ) !== $count ) {
                $widths = array_fill( 0, $count, 100 / $count );
        }
        $sum = array_sum( $widths );
        if ( $sum <= 0 ) { $widths = array_fill( 0, $count, 100 / $count ); $sum = 100; }

        // Fifths: if every column width is a clean multiple of 20% (1/5..4/5) and they sum to
        // five fifths, use the UnysonPlus fifth grid (fw-col-sm-15/25/35/45) — the 12-grid can't
        // express fifths. Covers 5-equal (1/5x5) AND spanning layouts like 2/5 + 1/5 + 1/5 + 1/5.
        $fifth_units = array();
        $fifth_ok    = true;
        $fifth_total = 0;
        foreach ( $widths as $w ) {
                $pct = $w / $sum * 100;
                $u   = (int) round( $pct / 20 );
                if ( $u < 1 || $u > 4 || abs( $pct - $u * 20 ) > 3 ) { $fifth_ok = false; break; }
                $fifth_units[] = $u;
                $fifth_total  += $u;
        }
        if ( $fifth_ok && 5 === $fifth_total ) {
                $fifth_class = array( 1 => 'fw-col-12 fw-col-sm-15', 2 => 'fw-col-12 fw-col-sm-25', 3 => 'fw-col-12 fw-col-sm-35', 4 => 'fw-col-12 fw-col-sm-45' );
                $classes = array();
                foreach ( $fifth_units as $u ) { $classes[] = $fifth_class[ $u ]; }
                return $classes;
        }

        // Each width → nearest twelfth (at least 1), then reconcile the total to 12.
        $units = array();
        foreach ( $widths as $w ) { $units[] = max( 1, (int) round( $w / $sum * 12 ) ); }
        $total = array_sum( $units );
        $guard = 0;
        while ( $total !== 12 && $guard++ < 48 ) {
                $idx = 0; $best = -1;
                foreach ( $units as $i => $u ) { if ( $u > $best ) { $best = $u; $idx = $i; } } // largest column absorbs the diff
                if ( $total > 12 ) { if ( $units[ $idx ] > 1 ) { $units[ $idx ]--; $total--; } else { break; } }
                else { $units[ $idx ]++; $total++; }
        }

        $classes = array();
        foreach ( $units as $u ) { $classes[] = 'fw-col-md-' . max( 1, min( 12, $u ) ); }
        return $classes;
}
endif;


if ( ! function_exists( 'unysonplus_footer_resolve_tokens' ) ) :
/**
 * Resolve dynamic-content tokens inside footer text (copyright / Text element).
 *
 * The legacy proprietary {year} token is retired in favor of the unified
 * {{current_year}} Dynamic Content tag. Any old saved value is bridged to the
 * new token so existing footers keep working, then handed to the plugin's
 * resolver. If the Unyson+ plugin is inactive, {{current_year}} still degrades
 * to the current year so the footer is never left with a literal token.
 */
function unysonplus_footer_resolve_tokens( $text ) {
        if ( ! is_string( $text ) || '' === $text ) return $text;

        // Bridge the retired {year} token to the unified {{current_year}} tag.
        $text = str_replace( '{year}', '{{current_year}}', $text );

        if ( function_exists( 'fw_dynamic_content' ) ) {
                return fw_dynamic_content()->resolve( $text );
        }

        // Plugin inactive — resolve a small set of common tokens standalone so the
        // footer never shows a literal {{token}}. These IDs MUST match the plugin's
        // Dynamic Content tag ids (framework/includes/dynamic-content/tags/core.php)
        // so the SAME token works whether the plugin is active (full engine, many
        // more tags) or not (this fallback).
        return strtr( $text, array(
                '{{current_year}}'   => date_i18n( 'Y' ),
                '{{copyright_year}}' => date_i18n( 'Y' ),
                '{{site_name}}'      => get_bloginfo( 'name' ),
                '{{site_tagline}}'   => get_bloginfo( 'description' ),
        ) );
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

                case 'icon_text':
                        unysonplus_render_icon_text( $settings );
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

                case 'builder_section':
                        if ( function_exists( 'unysonplus_render_builder_section' ) ) {
                                unysonplus_render_builder_section( $settings );
                        }
                        break;

                case 'snippet':
                        if ( function_exists( 'unysonplus_render_hf_snippet' ) ) {
                                unysonplus_render_hf_snippet( $settings );
                        }
                        break;

                default:
                        // Addon-registered element types (see the unysonplus_hf_elements filter).
                        do_action( 'unysonplus_render_hf_element_' . $type, $settings, $element, 'footer' );
                        do_action( 'unysonplus_render_hf_element', $type, $settings, $element, 'footer' );
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

        // Let addons inject / reorder footer elements at render time (optional).
        $column_data = apply_filters( 'unysonplus_hf_column_elements', $column_data, 'footer', '' );
        if ( empty( $column_data ) || ! is_array( $column_data ) ) return;

        echo '<div class="' . esc_attr( $col_class ) . '">';
        echo '<div class="footer-column">';
        foreach ( $column_data as $element ) {
                if ( empty( $element['element_type']['element'] ) ) continue;
                $type = $element['element_type']['element'];

                // Per-element CSS Class (addable-popup field) — sanitized tokens on the wrapper.
                $extra_class = '';
                if ( ! empty( $element['element_css_class'] ) ) {
                        $safe = array();
                        foreach ( preg_split( '/\s+/', trim( (string) $element['element_css_class'] ) ) as $cls ) {
                                $cls = sanitize_html_class( $cls );
                                if ( $cls !== '' ) { $safe[] = $cls; }
                        }
                        if ( $safe ) { $extra_class = ' ' . implode( ' ', $safe ); }
                }

                echo '<div class="footer-element footer-element--' . esc_attr( $type ) . esc_attr( function_exists( 'unysonplus_element_visibility_classes' ) ? unysonplus_element_visibility_classes( $element ) : '' ) . esc_attr( $extra_class ) . '">';
                unysonplus_render_footer_element( $element );
                echo '</div>';
        }
        echo '</div>';
        echo '</div>';
}
endif;


if ( ! function_exists( 'unysonplus_extract_footer_columns_data' ) ) :
/**
 * Resolve a footer section's columns to { col_count, classes (fw-col-md-N), columns }.
 * Shape (multi-picker): { count, '<n>':{ <prefix>_split:[{w,name}..], <prefix>_col_i } }.
 * The count select gives the column count; the Split-Slider's segment widths snap to
 * the page-builder 12-grid (→ fw-col-md-N). Falls back to a footer saved before the slider
 * (the old <prefix>_layout ratio picker) and to equal columns when neither is set.
 */
function unysonplus_extract_footer_columns_data( $section_data, $prefix ) {
        $columns_data = ! empty( $section_data[ $prefix . '_columns' ] ) ? $section_data[ $prefix . '_columns' ] : array();

        if ( empty( $columns_data ) || ! is_array( $columns_data ) ) {
                return array( 'col_count' => 0, 'classes' => array(), 'columns' => array() );
        }

        $count  = ! empty( $columns_data['count'] ) ? max( 1, (int) $columns_data['count'] ) : 1;
        $choice = ! empty( $columns_data[ (string) $count ] ) ? $columns_data[ (string) $count ]
                : ( ! empty( $columns_data[ $count ] ) ? $columns_data[ $count ] : array() );

        // Column widths → grid classes. A FIFTH composition (image-picker `f5-*` key) wins over
        // everything — the split-slider snaps to twelfths and can't carry fifths; its part-count
        // also sets the real column count (e.g. `f5-2-1-1-1` = 4 columns). Otherwise: the
        // Split-Slider ratio, then the legacy ratio image-picker, then equal columns.
        $layout_key = ! empty( $choice[ $prefix . '_layout' ] ) ? (string) $choice[ $prefix . '_layout' ] : '';
        $is_fifth   = ( strpos( $layout_key, 'f5-' ) === 0 );
        $segments   = isset( $choice[ $prefix . '_split' ] ) ? $choice[ $prefix . '_split' ] : null;
        if ( is_string( $segments ) ) {
                $decoded  = json_decode( $segments, true );
                $segments = is_array( $decoded ) ? $decoded : null;
        }
        if ( $count > 1 && $is_fifth ) {
                $classes = unysonplus_get_footer_col_classes( $layout_key );
                $count   = count( $classes ); // a spanning composition renders fewer physical columns
        } elseif ( $count > 1 && is_array( $segments ) && $segments ) {
                $widths = array();
                foreach ( $segments as $seg ) {
                        $widths[] = is_array( $seg ) ? max( 0, (float) ( isset( $seg['w'] ) ? $seg['w'] : 0 ) ) : max( 0, (float) $seg );
                }
                $classes = unysonplus_footer_widths_to_grid( $widths, $count );
        } elseif ( $count > 1 && $layout_key !== '' ) {
                $classes = unysonplus_get_footer_col_classes( $layout_key ); // legacy ratio
        } else {
                $classes = unysonplus_footer_widths_to_grid( array_fill( 0, $count, 100 / $count ), $count );
        }

        // Defensive: the resolved classes MUST match the column count. An unknown / legacy /
        // mismatched layout key returns a single 'fw-col-md-12' (see unysonplus_get_footer_col_classes),
        // which would leave the remaining columns class-less and silently break the grid. When the
        // count doesn't line up, fall back to equal columns so the grid always renders.
        if ( ! is_array( $classes ) || count( $classes ) !== $count ) {
                $classes = unysonplus_footer_widths_to_grid( array_fill( 0, $count, 100 / $count ), $count );
        }

        $columns = array();
        for ( $i = 1; $i <= $count; $i++ ) {
                $col_key       = $prefix . '_col_' . $i;
                $columns[ $i ] = ! empty( $choice[ $col_key ] ) ? $choice[ $col_key ] : array();
        }

        return array( 'col_count' => $count, 'classes' => $classes, 'columns' => $columns );
}
endif;


if ( ! function_exists( 'unysonplus_render_footer_section' ) ) :
function unysonplus_render_footer_section( $section_data, $prefix, $section_class = '' ) {
        $extracted   = unysonplus_extract_footer_columns_data( $section_data, $prefix );
        $col_count   = $extracted['col_count'];
        $columns     = $extracted['columns'];
        $col_classes = isset( $extracted['classes'] ) ? $extracted['classes'] : array();

        $has_content = false;
        foreach ( $columns as $col_data ) {
                if ( ! empty( $col_data ) && is_array( $col_data ) ) {
                        $has_content = true;
                        break;
                }
        }
        if ( ! $has_content ) return;

        // Visual styling (bg / typography / link / borders) is compiled into the
        // generated CSS file (inc/includes/hf-custom-css.php), scoped to
        // .footer-section--{prefix}. Only container + css-class + padding are
        // class-based here — no inline element styles or overlay element.
        $custom_styling = ! empty( $section_data[ $prefix . '_custom_styling' ] ) ? $section_data[ $prefix . '_custom_styling' ] : array();
        $attr = function_exists( 'unysonplus_hf_section_render_attrs' )
                ? unysonplus_hf_section_render_attrs( $custom_styling, $prefix, 'container' )
                : array( 'container' => 'container', 'class' => '' );

        $classes = array( 'footer-section', 'footer-section--' . str_replace( '_', '-', $prefix ) );
        if ( ! empty( $section_class ) ) $classes[] = $section_class;
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $classes ) ) . $attr['class']; // phpcs:ignore — $attr['class'] is pre-escaped ?>">
                <div class="<?php echo esc_attr( unysonplus_fw_container_class( $attr['container'] ) ); ?> footer-section__inner">
                        <div class="fw-row footer-row">
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

        // max-width is emitted to the generated CSS file via a per-instance hash
        // class (see inc/includes/hf-custom-css.php) — no inline style here.
        $logo_class = 'footer-logo-img';
        if ( function_exists( 'unysonplus_footer_logo_class' ) ) {
                $w = unysonplus_footer_logo_class( $max_width );
                if ( $w !== '' ) { $logo_class .= ' ' . $w; }
        }
        echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="footer-logo-link">';
        echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="' . esc_attr( $logo_class ) . '">';
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
                : sprintf( '&copy; {{current_year}} %s. All rights reserved.', get_bloginfo( 'name' ) );

        $text = unysonplus_footer_resolve_tokens( $text );
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
        $content = unysonplus_footer_resolve_tokens( $content );

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
                $col_class = 'fw-col-md-' . max( 2, intval( 12 / $count ) );
                ?>
                <div class="footer-section footer-section--widgets">
                        <div class="fw-container">
                                <div class="fw-row">
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
                <div class="fw-container">
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

