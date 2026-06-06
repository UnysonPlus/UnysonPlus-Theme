<?php if ( ! defined( 'FW' ) ) {
        die( 'Forbidden' );
}

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

$sidebar_label_map = json_encode( $sidebar_choices, JSON_UNESCAPED_UNICODE );
$menu_label_map = json_encode( array_map( 'strval', $menu_choices ), JSON_UNESCAPED_UNICODE );
$menu_location_label_map = json_encode( $menu_location_choices, JSON_UNESCAPED_UNICODE );

$element_popup_options = [
        'element_type' => [
                'type'         => 'multi-picker',
                'label'        => false,
                'desc'         => false,
                'picker'       => [
                        'element' => [
                                'label'   => __( 'Element', 'unysonplus' ),
                                'type'    => 'select',
                                'value'   => 'logo',
                                'choices' => [
                                        'logo'          => __( 'Logo', 'unysonplus' ),
                                        'menu'          => __( 'Menu', 'unysonplus' ),
                                        'menu_area'     => __( 'Menu Area', 'unysonplus' ),
                                        'cta_button'    => __( 'CTA Button', 'unysonplus' ),
                                        'phone'         => __( 'Phone Number', 'unysonplus' ),
                                        'search'        => __( 'Search', 'unysonplus' ),
                                        'social_icons'  => __( 'Social Icons', 'unysonplus' ),
                                        'custom_html'   => __( 'Custom HTML', 'unysonplus' ),
                                        'text'          => __( 'Text', 'unysonplus' ),
                                        'widget_area'   => __( 'Widget Area', 'unysonplus' ),
                                ],
                                'desc'    => __( 'Select header element.', 'unysonplus' ),
                        ]
                ],
                'choices'      => [
                        'cta_button'  => [
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
                ],
                'show_borders' => false,
        ],
];

$column_options = function( $label, $defaults = [] ) use ( $element_popup_options, $sidebar_label_map, $menu_label_map, $menu_location_label_map ) {
        return [
                'label'         => __( $label, 'unysonplus' ),
                'type'          => 'addable-popup',
                'value'         => $defaults,
                'desc'          => __( 'Add and reorder header elements. Drag to sort.', 'unysonplus' ),
                'template'      => '{{= (function(){ var el = element_type["element"]; var lbl = ({"logo":"Logo","cta_button":"CTA Button","phone":"Phone Number","search":"Search","social_icons":"Social Icons","custom_html":"Custom HTML","text":"Text","menu":"Menu","menu_area":"Menu Area","widget_area":"Widget Area"})[el] || el; if (el === "widget_area" && element_type["widget_area"] && element_type["widget_area"]["sidebar_id"]) { var smap = ' . $sidebar_label_map . '; var sid = element_type["widget_area"]["sidebar_id"]; lbl += " - " + (smap[sid] || sid); } if (el === "menu" && element_type["menu"] && element_type["menu"]["menu_id"]) { var mmap = ' . $menu_label_map . '; var mid = element_type["menu"]["menu_id"]; lbl += " - " + (mmap[mid] || mid); } if (el === "menu_area" && element_type["menu_area"] && element_type["menu_area"]["menu_location"]) { var amap = ' . $menu_location_label_map . '; var loc = element_type["menu_area"]["menu_location"]; lbl += " - " + (amap[loc] || loc); } return lbl; })() }}',
                'popup-options' => $element_popup_options,
        ];
};

$options = [
        'header_layout' => [
                'type'          => 'multi',
                'label'         => false,
                'inner-options' => [
                        'container' => [
                                'label'   => __( 'Container', 'unysonplus' ),
                                'type'    => 'select',
                                'value'   => 'container',
                                'choices' => [
                                        'container'       => __( 'Fixed Width', 'unysonplus' ),
                                        'container-fluid' => __( 'Full Width', 'unysonplus' ),
                                ],
                        ],
                        'min_height' => [
                                'label' => __( 'Main Header Height', 'unysonplus' ),
                                'desc'  => __( 'Minimum height of the main header row.', 'unysonplus' ),
                                'type'  => 'unit-input',
                                'units' => [ 'rem', 'px', 'em' ],
                                'value' => [ 'value' => '5', 'unit' => 'rem' ],
                                'min'   => 0,
                        ],
                        'bg_color' => [
                                'label' => __( 'Main Header Background', 'unysonplus' ),
                                'type'  => 'rgba-color-picker',
                                'value' => 'rgba(255, 255, 255, 1)',
                        ],
                        'sticky_header' => [
                                'label'        => __( 'Sticky Header', 'unysonplus' ),
                                'type'         => 'switch',
                                'value'        => 'no',
                                'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
                                'left-choice'  => [ 'value' => 'no', 'label' => __( 'No', 'unysonplus' ) ],
                                'desc'         => __( 'Make the main header stick to the top on scroll', 'unysonplus' ),
                        ],

                        'topbar_settings' => [
                                'type'         => 'multi-picker',
                                'label'        => false,
                                'desc'         => false,
                                'picker'       => [
                                        'enabled' => [
                                                'label'        => __( 'Enable Top Bar', 'unysonplus' ),
                                                'type'         => 'switch',
                                                'right-choice' => [
                                                        'value' => 'yes',
                                                        'label' => __( 'Yes', 'unysonplus' ),
                                                ],
                                                'left-choice'  => [
                                                        'value' => 'no',
                                                        'label' => __( 'No', 'unysonplus' ),
                                                ],
                                                'value'        => 'no',
                                        ],
                                ],
                                'choices'      => [
                                        'yes' => [
                                                'topbar_bg_color' => [
                                                        'label' => __( 'Top Bar Background', 'unysonplus' ),
                                                        'type'  => 'rgba-color-picker',
                                                        'value' => 'rgba(33, 37, 41, 1)',
                                                ],
                                                'topbar_text_color' => [
                                                        'label' => __( 'Top Bar Text Color', 'unysonplus' ),
                                                        'type'  => 'color-picker',
                                                        'value' => '#ffffff',
                                                ],
                                                'topbar_left'   => $column_options( 'Top Bar — Left Column' ),
                                                'topbar_center' => $column_options( 'Top Bar — Center Column' ),
                                                'topbar_right'  => $column_options( 'Top Bar — Right Column' ),
                                        ],
                                ],
                                'show_borders' => false,
                        ],

                        'main_header_group' => [
                                'type'    => 'group',
                                'options' => [
                                        'main_left' => $column_options(
                                                'Main Header — Left Column',
                                                [ [ 'element_type' => [ 'element' => 'logo' ] ] ]
                                        ),
                                        'main_center' => $column_options( 'Main Header — Center Column' ),
                                        'main_right' => $column_options(
                                                'Main Header — Right Column',
                                                [ [ 'element_type' => [ 'element' => 'menu_area', 'menu_area' => [ 'menu_location' => 'primary' ] ] ] ]
                                        ),
                                ],
                        ],
                ],
        ],
];
