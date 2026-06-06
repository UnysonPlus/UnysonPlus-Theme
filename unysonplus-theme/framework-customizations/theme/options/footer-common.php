<?php if ( ! defined( 'FW' ) ) {
        die( 'Forbidden' );
}

$layout_choices_by_count = [
        '1' => [
                '1-col' => __( 'Full Width', 'unysonplus' ),
        ],
        '2' => [
                '2-equal'   => __( '1/2 + 1/2', 'unysonplus' ),
                '2-1-3-2-3' => __( '1/3 + 2/3', 'unysonplus' ),
                '2-2-3-1-3' => __( '2/3 + 1/3', 'unysonplus' ),
                '2-1-4-3-4' => __( '1/4 + 3/4', 'unysonplus' ),
                '2-3-4-1-4' => __( '3/4 + 1/4', 'unysonplus' ),
        ],
        '3' => [
                '3-equal'       => __( '1/3 + 1/3 + 1/3', 'unysonplus' ),
                '3-1-2-1-4-1-4' => __( '1/2 + 1/4 + 1/4', 'unysonplus' ),
                '3-1-4-1-4-1-2' => __( '1/4 + 1/4 + 1/2', 'unysonplus' ),
                '3-1-4-1-2-1-4' => __( '1/4 + 1/2 + 1/4', 'unysonplus' ),
                '3-5-2-5'       => __( '5/12 + 2/12 + 5/12', 'unysonplus' ),
                '3-5-3-4'       => __( '5/12 + 3/12 + 4/12', 'unysonplus' ),
        ],
        '4' => [
                '4-equal'             => __( '1/4 + 1/4 + 1/4 + 1/4', 'unysonplus' ),
                '4-1-3-1-6-1-4-1-4'  => __( '1/3 + 1/6 + 1/4 + 1/4', 'unysonplus' ),
                '4-1-3-1-4-1-4-1-6'  => __( '1/3 + 1/4 + 1/4 + 1/6', 'unysonplus' ),
                '4-1-3-1-3-1-6-1-6'  => __( '1/3 + 1/3 + 1/6 + 1/6', 'unysonplus' ),
                '4-5-2-3-2-2'        => __( '5/12 + 3/12 + 2/12 + 2/12', 'unysonplus' ),
                '4-2-2-3-5'          => __( '2/12 + 2/12 + 3/12 + 5/12', 'unysonplus' ),
                '4-1-2-1-6-1-6-1-6'  => __( '1/2 + 1/6 + 1/6 + 1/6', 'unysonplus' ),
                '4-1-6-1-6-1-6-1-2'  => __( '1/6 + 1/6 + 1/6 + 1/2', 'unysonplus' ),
        ],
        '5' => [
                '5-equal'                   => __( '1/5 + 1/5 + 1/5 + 1/5 + 1/5', 'unysonplus' ),
                '5-1-3-1-6-1-6-1-6-1-6'    => __( '1/3 + 1/6 + 1/6 + 1/6 + 1/6', 'unysonplus' ),
                '5-1-6-1-6-1-6-1-6-1-3'    => __( '1/6 + 1/6 + 1/6 + 1/6 + 1/3', 'unysonplus' ),
        ],
];

$sidebar_choices = [
        'sidebar-right' => __( 'Right Sidebar Area', 'unysonplus' ),
        'sidebar-left'  => __( 'Left Sidebar Area', 'unysonplus' ),
        'header-1'      => __( 'Header Widget Area 1', 'unysonplus' ),
        'header-2'      => __( 'Header Widget Area 2', 'unysonplus' ),
        'header-3'      => __( 'Header Widget Area 3', 'unysonplus' ),
        'footer-1'      => __( 'Footer Column 1', 'unysonplus' ),
        'footer-2'      => __( 'Footer Column 2', 'unysonplus' ),
        'footer-3'      => __( 'Footer Column 3', 'unysonplus' ),
        'footer-4'      => __( 'Footer Column 4', 'unysonplus' ),
        'footer-5'      => __( 'Footer Column 5', 'unysonplus' ),
];
if ( ! empty( $GLOBALS['wp_registered_sidebars'] ) ) {
        foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
                if ( ! isset( $sidebar_choices[ $sidebar['id'] ] ) ) {
                        $sidebar_choices[ $sidebar['id'] ] = $sidebar['name'];
                }
        }
}

$menu_choices = [];
if ( function_exists( 'wp_get_nav_menus' ) ) {
        foreach ( wp_get_nav_menus() as $menu_obj ) {
                $menu_choices[ $menu_obj->term_id ] = $menu_obj->name;
        }
}

$menu_location_choices = [
        'primary'   => __( 'Primary menu', 'unysonplus' ),
        'secondary' => __( 'Secondary menu', 'unysonplus' ),
        'footer'    => __( 'Footer menu', 'unysonplus' ),
];
if ( function_exists( 'get_registered_nav_menus' ) ) {
        foreach ( get_registered_nav_menus() as $loc => $label ) {
                if ( ! isset( $menu_location_choices[ $loc ] ) ) {
                        $menu_location_choices[ $loc ] = $label;
                }
        }
}

$footer_element_popup_options = [
        'element_type' => [
                'type'         => 'multi-picker',
                'label'        => false,
                'desc'         => false,
                'picker'       => [
                        'element' => [
                                'label'   => __( 'Element', 'unysonplus' ),
                                'type'    => 'select',
                                'value'   => 'custom_html',
                                'choices' => [
                                        'logo'           => __( 'Logo', 'unysonplus' ),
                                        'footer_logo'    => __( 'Footer Logo', 'unysonplus' ),
                                        'menu'           => __( 'Menu', 'unysonplus' ),
                                        'menu_area'      => __( 'Menu Area', 'unysonplus' ),
                                        'cta_button'     => __( 'CTA Button', 'unysonplus' ),
                                        'phone'          => __( 'Phone Number', 'unysonplus' ),
                                        'search'         => __( 'Search', 'unysonplus' ),
                                        'social_icons'   => __( 'Social Icons', 'unysonplus' ),
                                        'custom_html'    => __( 'Custom HTML', 'unysonplus' ),
                                        'text'           => __( 'Text', 'unysonplus' ),
                                        'widget_area'    => __( 'Widget Area', 'unysonplus' ),
                                        'copyright_text' => __( 'Copyright Text', 'unysonplus' ),
                                        'back_to_top'    => __( 'Back to Top', 'unysonplus' ),
                                ],
                                'desc'    => __( 'Select footer element.', 'unysonplus' ),
                        ],
                ],
                'choices'      => [
                        'cta_button' => [
                                'cta_text' => [
                                        'label' => __( 'Button Text', 'unysonplus' ),
                                        'type'  => 'text',
                                        'value' => 'Get Started',
                                ],
                                'cta_link' => [
                                        'label' => __( 'Button Link', 'unysonplus' ),
                                        'type'  => 'text',
                                        'value' => '#',
                                ],
                                'cta_bg_color' => [
                                        'label' => __( 'Button Background', 'unysonplus' ),
                                        'type'  => 'color-picker',
                                        'value' => '#0d6efd',
                                ],
                                'cta_text_color' => [
                                        'label' => __( 'Button Text Color', 'unysonplus' ),
                                        'type'  => 'color-picker',
                                        'value' => '#ffffff',
                                ],
                                'cta_style' => [
                                        'label'   => __( 'Button Style', 'unysonplus' ),
                                        'type'    => 'select',
                                        'value'   => 'filled',
                                        'choices' => [
                                                'filled'  => __( 'Filled', 'unysonplus' ),
                                                'outline' => __( 'Outline', 'unysonplus' ),
                                                'pill'    => __( 'Pill (Rounded)', 'unysonplus' ),
                                        ],
                                ],
                        ],
                        'phone' => [
                                'phone_number' => [
                                        'label' => __( 'Phone Number', 'unysonplus' ),
                                        'type'  => 'text',
                                        'value' => '',
                                ],
                        ],
                        'custom_html' => [
                                'custom_html_content' => [
                                        'label' => __( 'Custom HTML', 'unysonplus' ),
                                        'type'  => 'textarea',
                                        'value' => '',
                                ],
                        ],
                        'menu' => [
                                'menu_id' => [
                                        'label'   => __( 'Select Menu', 'unysonplus' ),
                                        'type'    => 'select',
                                        'value'   => '',
                                        'choices' => $menu_choices,
                                        'desc'    => __( 'Choose a menu created in Appearance > Menus.', 'unysonplus' ),
                                ],
                        ],
                        'menu_area' => [
                                'menu_location' => [
                                        'label'   => __( 'Menu Location', 'unysonplus' ),
                                        'type'    => 'select',
                                        'value'   => 'primary',
                                        'choices' => $menu_location_choices,
                                        'desc'    => __( 'Select a theme menu location.', 'unysonplus' ),
                                ],
                        ],
                        'text' => [
                                'text_content' => [
                                        'label'         => __( 'Text', 'unysonplus' ),
                                        'type'          => 'wp-editor',
                                        'value'         => '',
                                        'desc'          => __( 'Add rich text content.', 'unysonplus' ),
                                        'tinymce'       => true,
                                        'size'          => 'large',
                                        'editor_height' => 200,
                                        'reinit'        => true,
                                        'wpautop'       => true,
                                ],
                        ],
                        'widget_area' => [
                                'sidebar_id' => [
                                        'label'   => __( 'Widget Area', 'unysonplus' ),
                                        'type'    => 'select',
                                        'value'   => 'sidebar-right',
                                        'choices' => $sidebar_choices,
                                        'desc'    => __( 'Select a registered widget area.', 'unysonplus' ),
                                ],
                        ],
                        'footer_logo' => [
                                'footer_logo_image' => [
                                        'label' => __( 'Footer Logo', 'unysonplus' ),
                                        'type'  => 'upload',
                                        'desc'  => __( 'Upload a logo for the footer (can differ from header logo).', 'unysonplus' ),
                                ],
                                'footer_logo_width' => [
                                        'label' => __( 'Logo Max Width', 'unysonplus' ),
                                        'desc'  => __( 'Max width of the footer logo.', 'unysonplus' ),
                                        'type'  => 'unit-input',
                                        'units' => [ 'rem', 'px', 'em' ],
                                        'value' => [ 'value' => '12.5', 'unit' => 'rem' ],
                                        'min'   => 0,
                                ],
                        ],
                        'copyright_text' => [
                                'copyright_content' => [
                                        'label' => __( 'Copyright Text', 'unysonplus' ),
                                        'type'  => 'wp-editor',
                                        'value' => '&copy; {year} ' . get_bloginfo( 'name' ) . '. All rights reserved.',
                                        'desc'  => __( 'Use {year} for the current year.', 'unysonplus' ),
                                        'size'  => 'small',
                                        'editor_height' => 100,
                                ],
                        ],
                        'back_to_top' => [
                                'back_to_top_text' => [
                                        'label' => __( 'Button Text', 'unysonplus' ),
                                        'type'  => 'text',
                                        'value' => 'Back to Top',
                                        'desc'  => __( 'Leave empty to show only an arrow icon.', 'unysonplus' ),
                                ],
                        ],
                ],
                'show_borders' => false,
        ],
];

$element_label_map = '{"logo":"Logo","footer_logo":"Footer Logo","menu":"Menu","menu_area":"Menu Area","cta_button":"CTA Button","phone":"Phone Number","search":"Search","social_icons":"Social Icons","custom_html":"Custom HTML","text":"Text","widget_area":"Widget Area","copyright_text":"Copyright Text","back_to_top":"Back to Top"}';

$sidebar_label_map = json_encode( $sidebar_choices, JSON_UNESCAPED_UNICODE );
$menu_label_map = json_encode( array_map( 'strval', $menu_choices ), JSON_UNESCAPED_UNICODE );
$menu_location_label_map = json_encode( $menu_location_choices, JSON_UNESCAPED_UNICODE );

$footer_column_options = function( $label, $defaults = [] ) use ( $footer_element_popup_options, $element_label_map, $sidebar_label_map, $menu_label_map, $menu_location_label_map ) {
        return [
                'label'         => $label,
                'type'          => 'addable-popup',
                'value'         => $defaults,
                'desc'          => __( 'Add and reorder footer elements. Drag to sort.', 'unysonplus' ),
                'template'      => '{{= (function(){ var el = element_type["element"]; var lbl = (' . $element_label_map . ')[el] || el; if (el === "widget_area" && element_type["widget_area"] && element_type["widget_area"]["sidebar_id"]) { var smap = ' . $sidebar_label_map . '; var sid = element_type["widget_area"]["sidebar_id"]; lbl += " - " + (smap[sid] || sid); } if (el === "menu" && element_type["menu"] && element_type["menu"]["menu_id"]) { var mmap = ' . $menu_label_map . '; var mid = element_type["menu"]["menu_id"]; lbl += " - " + (mmap[mid] || mid); } if (el === "menu_area" && element_type["menu_area"] && element_type["menu_area"]["menu_location"]) { var amap = ' . $menu_location_label_map . '; var loc = element_type["menu_area"]["menu_location"]; lbl += " - " + (amap[loc] || loc); } return lbl; })() }}',
                'popup-options' => $footer_element_popup_options,
        ];
};

$section_settings = function( $prefix ) {
        return [
                $prefix . '_custom_styling' => [
                        'type'         => 'multi-picker',
                        'label'        => false,
                        'desc'         => false,
                        'picker'       => [
                                'enabled' => [
                                        'label'        => __( 'Custom Styling', 'unysonplus' ),
                                        'type'         => 'switch',
                                        'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
                                        'left-choice'  => [ 'value' => 'no', 'label' => __( 'No', 'unysonplus' ) ],
                                        'value'        => 'no',
                                        'desc'         => __( 'Enable to override the global footer styling for this section.', 'unysonplus' ),
                                ],
                        ],
                        'choices' => [
                                'yes' => [
                                        $prefix . '_container' => [
                                                'label'   => __( 'Container', 'unysonplus' ),
                                                'type'    => 'select',
                                                'value'   => 'container',
                                                'choices' => [
                                                        'container'       => __( 'Fixed Width', 'unysonplus' ),
                                                        'container-fluid' => __( 'Full Width', 'unysonplus' ),
                                                ],
                                        ],
                                        $prefix . '_bg_color' => [
                                                'label' => __( 'Background Color', 'unysonplus' ),
                                                'type'  => 'rgba-color-picker',
                                                'value' => '',
                                                'desc'  => __( 'e.g. rgba(33, 37, 41, 1)', 'unysonplus' ),
                                        ],
                                        $prefix . '_bg_image' => [
                                                'label' => __( 'Background Image', 'unysonplus' ),
                                                'type'  => 'upload',
                                                'desc'  => __( 'Optional background image for this section.', 'unysonplus' ),
                                        ],
                                        $prefix . '_bg_overlay' => [
                                                'label' => __( 'Background Overlay Opacity', 'unysonplus' ),
                                                'type'  => 'slider',
                                                'value' => 80,
                                                'properties' => [
                                                        'min' => 0,
                                                        'max' => 100,
                                                        'step' => 5,
                                                ],
                                                'desc' => __( 'Overlay opacity over the background image (0 = transparent, 100 = solid). Only used when a background image is set.', 'unysonplus' ),
                                        ],
                                        $prefix . '_text_color' => [
                                                'label' => __( 'Text Color', 'unysonplus' ),
                                                'type'  => 'color-picker',
                                                'value' => '',
                                                'desc'  => __( 'e.g. #ffffff', 'unysonplus' ),
                                        ],
                                        $prefix . '_link_color' => [
                                                'label' => __( 'Link Color', 'unysonplus' ),
                                                'type'  => 'color-picker',
                                                'value' => '',
                                                'desc'  => __( 'e.g. #adb5bd', 'unysonplus' ),
                                        ],
                                        $prefix . '_padding_top' => [
                                                'label' => __( 'Padding Top', 'unysonplus' ),
                                                'type'  => 'text',
                                                'value' => '',
                                                'desc'  => __( 'e.g. 40px', 'unysonplus' ),
                                        ],
                                        $prefix . '_padding_bottom' => [
                                                'label' => __( 'Padding Bottom', 'unysonplus' ),
                                                'type'  => 'text',
                                                'value' => '',
                                                'desc'  => __( 'e.g. 40px', 'unysonplus' ),
                                        ],
                                        $prefix . '_border_top_color' => [
                                                'label' => __( 'Top Border Color', 'unysonplus' ),
                                                'type'  => 'color-picker',
                                                'value' => '',
                                                'desc'  => __( 'Leave empty for no border.', 'unysonplus' ),
                                        ],
                                        $prefix . '_border_top_width' => [
                                                'label' => __( 'Top Border Width', 'unysonplus' ),
                                                'type'  => 'text',
                                                'value' => '',
                                                'desc'  => __( 'e.g. 1px', 'unysonplus' ),
                                        ],
                                        $prefix . '_css_class' => [
                                                'label' => __( 'Custom CSS Class', 'unysonplus' ),
                                                'type'  => 'text',
                                                'value' => '',
                                                'desc'  => __( 'Add custom CSS class(es) to this section.', 'unysonplus' ),
                                        ],
                                ],
                        ],
                        'show_borders' => false,
                ],
        ];
};

/**
 * Build a "Number of Columns" picker + per-column addable-popup picker.
 *
 * @param string        $prefix         Option-key prefix (e.g. 'main_footer', 'copyright').
 * @param int           $max_cols       How many column choices to expose (1..N).
 * @param string        $default_count  Initially-selected column count ('1'..(string)$max_cols).
 * @param callable|null $col_default_fn function( int $c, int $total ): array — returns the default
 *                                      element list for column $c (1-indexed). When null, each
 *                                      column defaults to a widget_area element bound to
 *                                      `footer-<c>` (legacy behavior).
 */
$footer_columns_picker = function( $prefix, $max_cols = 5, $default_count = '3', $col_default_fn = null )
        use ( $layout_choices_by_count, $footer_column_options ) {
        $count_choices = [];
        for ( $n = 1; $n <= $max_cols; $n++ ) {
                $count_choices[ (string) $n ] = sprintf( _n( '%d Column', '%d Columns', $n, 'unysonplus' ), $n );
        }

        $choices = [];

        for ( $n = 1; $n <= $max_cols; $n++ ) {
                $key  = (string) $n;
                $opts = [];

                if ( $n > 1 && isset( $layout_choices_by_count[ $key ] ) ) {
                        $ratios      = $layout_choices_by_count[ $key ];
                        $first_key   = array_key_first( $ratios );
                        $col_img_uri = get_template_directory_uri() . '/images/image-picker/columns/';
                        $img_choices = [];
                        foreach ( $ratios as $rk => $rlabel ) {
                                $img_choices[ $rk ] = [
                                        'small' => [
                                                'src'    => $col_img_uri . $rk . '.svg',
                                                'height' => 40,
                                                'title'  => $rlabel,
                                        ],
                                ];
                        }
                        $opts[ $prefix . '_layout' ] = [
                                'label'   => __( 'Column Ratio', 'unysonplus' ),
                                'type'    => 'image-picker',
                                'choices' => $img_choices,
                                'value'   => $first_key,
                        ];
                }

                for ( $c = 1; $c <= $n; $c++ ) {
                        if ( is_callable( $col_default_fn ) ) {
                                $col_default = call_user_func( $col_default_fn, $c, $n );
                                if ( ! is_array( $col_default ) ) { $col_default = []; }
                        } else {
                                $col_default = [ [
                                        'element_type' => [
                                                'element'     => 'widget_area',
                                                'widget_area' => [ 'sidebar_id' => 'footer-' . $c ],
                                        ],
                                ] ];
                        }
                        $opts[ $prefix . '_col_' . $c ] = $footer_column_options( sprintf( __( 'Column %d', 'unysonplus' ), $c ), $col_default );
                }

                $choices[ $key ] = $opts;
        }

        return [
                $prefix . '_columns' => [
                        'type'         => 'multi-picker',
                        'label'        => false,
                        'desc'         => false,
                        'picker'       => [
                                'count' => [
                                        'type'    => 'select',
                                        'label'   => __( 'Number of Columns', 'unysonplus' ),
                                        'choices' => $count_choices,
                                        'value'   => $default_count,
                                ],
                        ],
                        'choices'      => $choices,
                        'show_borders' => false,
                ],
        ];
};
