<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

/**
 * Shared building blocks for the Header / Footer settings option arrays.
 *
 * These are the reusable LEAF pieces (choice lists, the "Add element" popup, the
 * row-title template, the column field, the column-ratio picker, the custom-styling
 * block). The section option files (header-layout.php, footer-pre/main/post/
 * copyright.php) spell out their own count → columns → custom-styling STRUCTURE
 * explicitly and call these helpers for the repeated leaves — so the files stay
 * readable while nothing is duplicated.
 *
 * Why functions in an auto-loaded include (not a require'd options file): several
 * option files would each require such a file, re-declaring named functions and
 * fataling. inc/init.php auto-loads everything in inc/includes/, once.
 */


/* ============================================================
 * Choice lists (shared by header + footer element pickers)
 * ============================================================ */

if ( ! function_exists( 'unysonplus_hf_choices' ) ) :
/**
 * @return array{sidebar:array,menu:array,menu_location:array,builder:array}
 */
function unysonplus_hf_choices() {
	static $cache = null;
	if ( $cache !== null ) {
		return $cache;
	}

	$sidebar = array(
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
	);
	if ( ! empty( $GLOBALS['wp_registered_sidebars'] ) ) {
		foreach ( $GLOBALS['wp_registered_sidebars'] as $sb ) {
			if ( ! isset( $sidebar[ $sb['id'] ] ) ) {
				$sidebar[ $sb['id'] ] = $sb['name'];
			}
		}
	}

	$menu = array();
	if ( function_exists( 'wp_get_nav_menus' ) ) {
		foreach ( wp_get_nav_menus() as $menu_obj ) {
			$menu[ $menu_obj->term_id ] = $menu_obj->name;
		}
	}

	$menu_location = array(
		'primary'   => __( 'Primary menu', 'unysonplus' ),
		'secondary' => __( 'Secondary menu', 'unysonplus' ),
		'footer'    => __( 'Footer menu', 'unysonplus' ),
	);
	if ( function_exists( 'get_registered_nav_menus' ) ) {
		foreach ( get_registered_nav_menus() as $loc => $label ) {
			if ( ! isset( $menu_location[ $loc ] ) ) {
				$menu_location[ $loc ] = $label;
			}
		}
	}

	$builder = function_exists( 'unysonplus_builder_post_choices' )
		? unysonplus_builder_post_choices()
		: array( '' => __( '— Select a layout —', 'unysonplus' ) );

	$cache = array(
		'sidebar'       => $sidebar,
		'menu'          => $menu,
		'menu_location' => $menu_location,
		'builder'       => $builder,
	);
	return $cache;
}
endif;


/* ============================================================
 * Element pickers (the popup shown when you click "Add" in a column)
 * ============================================================ */

if ( ! function_exists( 'unysonplus_footer_element_popup' ) ) :
function unysonplus_footer_element_popup() {
	static $popup = null;
	if ( $popup !== null ) {
		return $popup;
	}
	$ch = unysonplus_hf_choices();

	$popup = array(
		'element_type' => array(
			'type'   => 'multi-picker',
			'label'  => false,
			'desc'   => false,
			'picker' => array(
				'element' => array(
					'label'   => __( 'Element', 'unysonplus' ),
					'type'    => 'select',
					'value'   => 'custom_html',
					'choices' => array(
						'logo'            => __( 'Logo', 'unysonplus' ),
						'footer_logo'     => __( 'Footer Logo', 'unysonplus' ),
						'menu'            => __( 'Menu', 'unysonplus' ),
						'menu_area'       => __( 'Menu Area', 'unysonplus' ),
						'cta_button'      => __( 'CTA Button', 'unysonplus' ),
						'phone'           => __( 'Phone Number', 'unysonplus' ),
						'search'          => __( 'Search', 'unysonplus' ),
						'social_icons'    => __( 'Social Icons', 'unysonplus' ),
						'custom_html'     => __( 'Custom HTML', 'unysonplus' ),
						'text'            => __( 'Text', 'unysonplus' ),
						'widget_area'     => __( 'Widget Area', 'unysonplus' ),
						'back_to_top'     => __( 'Back to Top', 'unysonplus' ),
						'builder_section' => __( 'Builder Section', 'unysonplus' ),
					),
					'desc'    => __( 'Select footer element.', 'unysonplus' ),
				),
			),
			'choices' => array(
				'cta_button' => array(
					'cta_text'       => array( 'label' => __( 'Button Text', 'unysonplus' ),       'type' => 'text',         'value' => 'Get Started' ),
					'cta_link'       => array( 'label' => __( 'Button Link', 'unysonplus' ),       'type' => 'text',         'value' => '#' ),
					'cta_bg_color'   => array( 'label' => __( 'Button Background', 'unysonplus' ),  'type' => 'color-picker', 'value' => '#0d6efd' ),
					'cta_text_color' => array( 'label' => __( 'Button Text Color', 'unysonplus' ), 'type' => 'color-picker', 'value' => '#ffffff' ),
					'cta_style'      => array(
						'label'   => __( 'Button Style', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'filled',
						'choices' => array(
							'filled'  => __( 'Filled', 'unysonplus' ),
							'outline' => __( 'Outline', 'unysonplus' ),
							'pill'    => __( 'Pill (Rounded)', 'unysonplus' ),
						),
					),
				),
				'phone' => array(
					'phone_number' => array( 'label' => __( 'Phone Number', 'unysonplus' ), 'type' => 'text', 'value' => '' ),
				),
				'custom_html' => array(
					'custom_html_content' => array( 'label' => __( 'Custom HTML', 'unysonplus' ), 'type' => 'textarea', 'value' => '' ),
				),
				'menu' => array(
					'menu_id' => array(
						'label'   => __( 'Select Menu', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '',
						'choices' => $ch['menu'],
						'desc'    => __( 'Choose a menu created in Appearance > Menus.', 'unysonplus' ),
					),
				),
				'menu_area' => array(
					'menu_location' => array(
						'label'   => __( 'Menu Location', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'primary',
						'choices' => $ch['menu_location'],
						'desc'    => __( 'Select a theme menu location.', 'unysonplus' ),
					),
				),
				'text' => array(
					'text_content' => array(
						'label'         => __( 'Text', 'unysonplus' ),
						'type'          => 'wp-editor',
						'value'         => '',
						'desc'          => __( 'Add rich text content. Use the {{current_year}} dynamic tag for the current year.', 'unysonplus' ),
						'tinymce'       => true,
						'shortcodes'    => true,
						'size'          => 'large',
						'editor_height' => 200,
						'reinit'        => true,
						'wpautop'       => true,
					),
				),
				'widget_area' => array(
					'sidebar_id' => array(
						'label'   => __( 'Widget Area', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'sidebar-right',
						'choices' => $ch['sidebar'],
						'desc'    => __( 'Select a registered widget area.', 'unysonplus' ),
					),
				),
				'footer_logo' => array(
					'footer_logo_image' => array(
						'label' => __( 'Footer Logo', 'unysonplus' ),
						'type'  => 'upload',
						'desc'  => __( 'Upload a logo for the footer (can differ from header logo).', 'unysonplus' ),
					),
					'footer_logo_width' => array(
						'label' => __( 'Logo Max Width', 'unysonplus' ),
						'desc'  => __( 'Max width of the footer logo.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => array( 'rem', 'px', 'em' ),
						'value' => array( 'value' => '12.5', 'unit' => 'rem' ),
						'min'   => 0,
					),
				),
				'back_to_top' => array(
					'back_to_top_text' => array(
						'label' => __( 'Button Text', 'unysonplus' ),
						'type'  => 'text',
						'value' => 'Back to Top',
						'desc'  => __( 'Leave empty to show only an arrow icon.', 'unysonplus' ),
					),
				),
				'builder_section' => array(
					'builder_post_id' => array(
						'label'   => __( 'Saved Layout', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '',
						'choices' => $ch['builder'],
						'desc'    => __( 'Render a page-builder layout inside this footer column — for bespoke designs the standard elements can\'t express.', 'unysonplus' ),
					),
				),
			),
			'show_borders' => false,
		),
		'visibility' => array(
			'type'    => 'checkboxes',
			'label'   => __( 'Hide On', 'unysonplus' ),
			'desc'    => __( 'Hide this element on the selected screen sizes.', 'unysonplus' ),
			'value'   => array(),
			'choices' => array(
				'hide-xs' => __( 'Mobile (< 768px)', 'unysonplus' ),
				'hide-sm' => __( 'Tablet (768–991px)', 'unysonplus' ),
				'hide-md' => __( 'Desktop (≥ 992px)', 'unysonplus' ),
			),
		),
	);
	return $popup;
}
endif;

if ( ! function_exists( 'unysonplus_header_element_popup' ) ) :
function unysonplus_header_element_popup() {
	static $popup = null;
	if ( $popup !== null ) {
		return $popup;
	}
	$ch = unysonplus_hf_choices();

	$popup = array(
		'element_type' => array(
			'type'   => 'multi-picker',
			'label'  => false,
			'desc'   => false,
			'picker' => array(
				'element' => array(
					'label'   => __( 'Element', 'unysonplus' ),
					'type'    => 'select',
					'value'   => 'logo',
					'choices' => array(
						'logo'            => __( 'Logo', 'unysonplus' ),
						'menu'            => __( 'Menu', 'unysonplus' ),
						'menu_area'       => __( 'Menu Area', 'unysonplus' ),
						'cta_button'      => __( 'CTA Button', 'unysonplus' ),
						'phone'           => __( 'Phone Number', 'unysonplus' ),
						'search'          => __( 'Search', 'unysonplus' ),
						'social_icons'    => __( 'Social Icons', 'unysonplus' ),
						'custom_html'     => __( 'Custom HTML', 'unysonplus' ),
						'text'            => __( 'Text', 'unysonplus' ),
						'widget_area'     => __( 'Widget Area', 'unysonplus' ),
						'builder_section' => __( 'Builder Section', 'unysonplus' ),
						'spacer'          => __( 'Spacer', 'unysonplus' ),
						'divider'         => __( 'Divider', 'unysonplus' ),
					),
					'desc'    => __( 'Select header element.', 'unysonplus' ),
				),
			),
			'choices' => array(
				'cta_button' => array(
					'cta_text'       => array( 'label' => __( 'Button Text', 'unysonplus' ),       'type' => 'text',         'value' => 'Get Started' ),
					'cta_link'       => array( 'label' => __( 'Button Link', 'unysonplus' ),       'type' => 'text',         'value' => '#' ),
					'cta_bg_color'   => array( 'label' => __( 'Button Background', 'unysonplus' ),  'type' => 'color-picker', 'value' => '#0d6efd' ),
					'cta_text_color' => array( 'label' => __( 'Button Text Color', 'unysonplus' ), 'type' => 'color-picker', 'value' => '#ffffff' ),
					'cta_style'      => array(
						'label'   => __( 'Button Style', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'filled',
						'choices' => array(
							'filled'  => __( 'Filled', 'unysonplus' ),
							'outline' => __( 'Outline', 'unysonplus' ),
							'pill'    => __( 'Pill (Rounded)', 'unysonplus' ),
						),
					),
				),
				'phone' => array(
					'phone_number' => array( 'label' => __( 'Phone Number', 'unysonplus' ), 'type' => 'text', 'value' => '' ),
				),
				'custom_html' => array(
					'custom_html_content' => array( 'label' => __( 'Custom HTML', 'unysonplus' ), 'type' => 'textarea', 'value' => '' ),
				),
				'menu' => array(
					'menu_id' => array(
						'label'   => __( 'Select Menu', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '',
						'choices' => $ch['menu'],
						'desc'    => __( 'Choose a menu created in Appearance > Menus.', 'unysonplus' ),
					),
				),
				'menu_area' => array(
					'menu_location' => array(
						'label'   => __( 'Menu Location', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'primary',
						'choices' => $ch['menu_location'],
						'desc'    => __( 'Select a theme menu location.', 'unysonplus' ),
					),
				),
				'text' => array(
					'text_content' => array(
						'label'         => __( 'Text', 'unysonplus' ),
						'type'          => 'wp-editor',
						'value'         => '',
						'desc'          => __( 'Add rich text content. Use the {{current_year}} dynamic tag for the current year.', 'unysonplus' ),
						'tinymce'       => true,
						'shortcodes'    => true,
						'size'          => 'large',
						'editor_height' => 200,
						'reinit'        => true,
						'wpautop'       => true,
					),
				),
				'widget_area' => array(
					'sidebar_id' => array(
						'label'   => __( 'Widget Area', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'sidebar-right',
						'choices' => $ch['sidebar'],
						'desc'    => __( 'Select a registered widget area.', 'unysonplus' ),
					),
				),
				'builder_section' => array(
					'builder_post_id' => array(
						'label'   => __( 'Saved Layout', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '',
						'choices' => $ch['builder'],
						'desc'    => __( 'Render a page-builder layout inside this slot — for bespoke header designs the standard elements can\'t express.', 'unysonplus' ),
					),
				),
			),
			'show_borders' => false,
		),
		'visibility' => array(
			'type'    => 'checkboxes',
			'label'   => __( 'Hide On', 'unysonplus' ),
			'desc'    => __( 'Hide this element on the selected screen sizes.', 'unysonplus' ),
			'value'   => array(),
			'choices' => array(
				'hide-xs' => __( 'Mobile (< 768px)', 'unysonplus' ),
				'hide-sm' => __( 'Tablet (768–991px)', 'unysonplus' ),
				'hide-md' => __( 'Desktop (≥ 992px)', 'unysonplus' ),
			),
		),
	);
	return $popup;
}
endif;


/* ============================================================
 * Row-title templates (the underscore.js label per added element)
 * ============================================================ */

if ( ! function_exists( 'unysonplus_footer_row_template' ) ) :
function unysonplus_footer_row_template() {
	static $tpl = null;
	if ( $tpl !== null ) {
		return $tpl;
	}
	$ch                      = unysonplus_hf_choices();
	$element_label_map       = '{"logo":"Logo","footer_logo":"Footer Logo","menu":"Menu","menu_area":"Menu Area","cta_button":"CTA Button","phone":"Phone Number","search":"Search","social_icons":"Social Icons","custom_html":"Custom HTML","text":"Text","widget_area":"Widget Area","copyright_text":"Copyright Text","back_to_top":"Back to Top","builder_section":"Builder Section"}';
	$sidebar_label_map       = json_encode( $ch['sidebar'], JSON_UNESCAPED_UNICODE );
	$menu_label_map          = json_encode( array_map( 'strval', $ch['menu'] ), JSON_UNESCAPED_UNICODE );
	$menu_location_label_map = json_encode( $ch['menu_location'], JSON_UNESCAPED_UNICODE );
	$builder_label_map       = json_encode( array_map( 'strval', $ch['builder'] ), JSON_UNESCAPED_UNICODE );

	$tpl = '{{= (function(){ var el = element_type["element"]; var lbl = (' . $element_label_map . ')[el] || el; if (el === "widget_area" && element_type["widget_area"] && element_type["widget_area"]["sidebar_id"]) { var smap = ' . $sidebar_label_map . '; var sid = element_type["widget_area"]["sidebar_id"]; lbl = (smap[sid] || sid); } if (el === "menu" && element_type["menu"] && element_type["menu"]["menu_id"]) { var mmap = ' . $menu_label_map . '; var mid = element_type["menu"]["menu_id"]; lbl = (mmap[mid] || mid); } if (el === "menu_area" && element_type["menu_area"] && element_type["menu_area"]["menu_location"]) { var amap = ' . $menu_location_label_map . '; var loc = element_type["menu_area"]["menu_location"]; lbl = (amap[loc] || loc); } if (el === "builder_section" && element_type["builder_section"] && element_type["builder_section"]["builder_post_id"]) { var bmap = ' . $builder_label_map . '; var bid = element_type["builder_section"]["builder_post_id"]; lbl = (bmap[bid] || bid); } if (el === "text" && element_type["text"] && element_type["text"]["text_content"]) { var _t = String(element_type["text"]["text_content"]).replace(/<[^>]*>/g," ").replace(/&[a-z#0-9]+;/gi," ").replace(/\s+/g," ").trim(); if (_t) lbl = (_t.length > 45 ? _t.substring(0,45) + "…" : _t); } if (el === "custom_html" && element_type["custom_html"] && element_type["custom_html"]["custom_html_content"]) { var _h = String(element_type["custom_html"]["custom_html_content"]).replace(/<[^>]*>/g," ").replace(/\s+/g," ").trim(); if (_h) lbl = (_h.length > 45 ? _h.substring(0,45) + "…" : _h); } if (el === "copyright_text" && element_type["copyright_text"] && element_type["copyright_text"]["copyright_content"]) { var _c = String(element_type["copyright_text"]["copyright_content"]).replace(/<[^>]*>/g," ").replace(/&[a-z#0-9]+;/gi," ").replace(/\s+/g," ").trim(); if (_c) lbl = (_c.length > 45 ? _c.substring(0,45) + "…" : _c); } return lbl; })() }}';
	return $tpl;
}
endif;

if ( ! function_exists( 'unysonplus_header_row_template' ) ) :
function unysonplus_header_row_template() {
	static $tpl = null;
	if ( $tpl !== null ) {
		return $tpl;
	}
	$ch                      = unysonplus_hf_choices();
	$sidebar_label_map       = json_encode( $ch['sidebar'], JSON_UNESCAPED_UNICODE );
	$menu_label_map          = json_encode( array_map( 'strval', $ch['menu'] ), JSON_UNESCAPED_UNICODE );
	$menu_location_label_map = json_encode( $ch['menu_location'], JSON_UNESCAPED_UNICODE );
	$builder_label_map       = json_encode( array_map( 'strval', $ch['builder'] ), JSON_UNESCAPED_UNICODE );

	$tpl = '{{= (function(){ var el = element_type["element"]; var lbl = ({"logo":"Logo","cta_button":"CTA Button","phone":"Phone Number","search":"Search","social_icons":"Social Icons","custom_html":"Custom HTML","text":"Text","menu":"Menu","menu_area":"Menu Area","widget_area":"Widget Area","builder_section":"Builder Section","spacer":"Spacer","divider":"Divider"})[el] || el; if (el === "widget_area" && element_type["widget_area"] && element_type["widget_area"]["sidebar_id"]) { var smap = ' . $sidebar_label_map . '; var sid = element_type["widget_area"]["sidebar_id"]; lbl = (smap[sid] || sid); } if (el === "menu" && element_type["menu"] && element_type["menu"]["menu_id"]) { var mmap = ' . $menu_label_map . '; var mid = element_type["menu"]["menu_id"]; lbl = (mmap[mid] || mid); } if (el === "menu_area" && element_type["menu_area"] && element_type["menu_area"]["menu_location"]) { var amap = ' . $menu_location_label_map . '; var loc = element_type["menu_area"]["menu_location"]; lbl = (amap[loc] || loc); } if (el === "builder_section" && element_type["builder_section"] && element_type["builder_section"]["builder_post_id"]) { var bmap = ' . $builder_label_map . '; var bid = element_type["builder_section"]["builder_post_id"]; lbl = (bmap[bid] || bid); } if (el === "text" && element_type["text"] && element_type["text"]["text_content"]) { var _t = String(element_type["text"]["text_content"]).replace(/<[^>]*>/g," ").replace(/&[a-z#0-9]+;/gi," ").replace(/\s+/g," ").trim(); if (_t) lbl = (_t.length > 45 ? _t.substring(0,45) + "…" : _t); } if (el === "custom_html" && element_type["custom_html"] && element_type["custom_html"]["custom_html_content"]) { var _h = String(element_type["custom_html"]["custom_html_content"]).replace(/<[^>]*>/g," ").replace(/\s+/g," ").trim(); if (_h) lbl = (_h.length > 45 ? _h.substring(0,45) + "…" : _h); } return lbl; })() }}';
	return $tpl;
}
endif;


/* ============================================================
 * One column field (addable-popup) + the column-ratio picker
 * ============================================================ */

if ( ! function_exists( 'unysonplus_footer_column' ) ) :
function unysonplus_footer_column( $label, $defaults = array() ) {
	return array(
		'label'         => $label,
		'type'          => 'addable-popup',
		'value'         => $defaults,
		'desc'          => false,
		'template'      => unysonplus_footer_row_template(),
		'popup-options' => unysonplus_footer_element_popup(),
	);
}
endif;

if ( ! function_exists( 'unysonplus_header_column' ) ) :
function unysonplus_header_column( $label, $defaults = array() ) {
	return array(
		'label'         => $label,
		'type'          => 'addable-popup',
		'value'         => $defaults,
		'desc'          => false,
		'template'      => unysonplus_header_row_template(),
		'popup-options' => unysonplus_header_element_popup(),
	);
}
endif;

if ( ! function_exists( 'unysonplus_footer_ratio_picker' ) ) :
/**
 * Column-Ratio image-picker for a given column count. Returns null for 1 column
 * (no ratio choice) — the caller simply omits it.
 *
 * @param string $prefix unused in the option array but kept for call-site clarity
 * @param int    $count  2..5
 * @return array|null
 */
function unysonplus_footer_ratio_picker( $prefix, $count ) {
	$ratios = array(
		'2' => array(
			'2-equal'   => __( '1/2 + 1/2', 'unysonplus' ),
			'2-1-3-2-3' => __( '1/3 + 2/3', 'unysonplus' ),
			'2-2-3-1-3' => __( '2/3 + 1/3', 'unysonplus' ),
			'2-1-4-3-4' => __( '1/4 + 3/4', 'unysonplus' ),
			'2-3-4-1-4' => __( '3/4 + 1/4', 'unysonplus' ),
		),
		'3' => array(
			'3-equal'       => __( '1/3 + 1/3 + 1/3', 'unysonplus' ),
			'3-1-2-1-4-1-4' => __( '1/2 + 1/4 + 1/4', 'unysonplus' ),
			'3-1-4-1-4-1-2' => __( '1/4 + 1/4 + 1/2', 'unysonplus' ),
			'3-1-4-1-2-1-4' => __( '1/4 + 1/2 + 1/4', 'unysonplus' ),
			'3-5-2-5'       => __( '5/12 + 2/12 + 5/12', 'unysonplus' ),
			'3-5-3-4'       => __( '5/12 + 3/12 + 4/12', 'unysonplus' ),
		),
		'4' => array(
			'4-equal'           => __( '1/4 + 1/4 + 1/4 + 1/4', 'unysonplus' ),
			'4-1-3-1-6-1-4-1-4' => __( '1/3 + 1/6 + 1/4 + 1/4', 'unysonplus' ),
			'4-1-3-1-4-1-4-1-6' => __( '1/3 + 1/4 + 1/4 + 1/6', 'unysonplus' ),
			'4-1-3-1-3-1-6-1-6' => __( '1/3 + 1/3 + 1/6 + 1/6', 'unysonplus' ),
			'4-5-2-3-2-2'       => __( '5/12 + 3/12 + 2/12 + 2/12', 'unysonplus' ),
			'4-2-2-3-5'         => __( '2/12 + 2/12 + 3/12 + 5/12', 'unysonplus' ),
			'4-1-2-1-6-1-6-1-6' => __( '1/2 + 1/6 + 1/6 + 1/6', 'unysonplus' ),
			'4-1-6-1-6-1-6-1-2' => __( '1/6 + 1/6 + 1/6 + 1/2', 'unysonplus' ),
		),
		'5' => array(
			'5-equal'               => __( '1/5 + 1/5 + 1/5 + 1/5 + 1/5', 'unysonplus' ),
			'5-1-3-1-6-1-6-1-6-1-6' => __( '1/3 + 1/6 + 1/6 + 1/6 + 1/6', 'unysonplus' ),
			'5-1-6-1-6-1-6-1-6-1-3' => __( '1/6 + 1/6 + 1/6 + 1/6 + 1/3', 'unysonplus' ),
		),
	);

	$count = (string) $count;
	if ( empty( $ratios[ $count ] ) ) {
		return null;
	}

	$col_img     = get_template_directory_uri() . '/images/image-picker/columns/';
	$img_choices = array();
	foreach ( $ratios[ $count ] as $rk => $rlabel ) {
		$img_choices[ $rk ] = array(
			'small' => array( 'src' => $col_img . $rk . '.svg', 'height' => 40, 'title' => $rlabel ),
		);
	}

	return array(
		'label'   => __( 'Column Ratio', 'unysonplus' ),
		'type'    => 'image-picker',
		'value'   => (string) array_key_first( $ratios[ $count ] ),
		'choices' => $img_choices,
	);
}
endif;


/* ============================================================
 * Per-section "Custom Styling" override block
 * ============================================================ */

if ( ! function_exists( 'unysonplus_hf_custom_styling' ) ) :
/**
 * Shared per-section "Custom Styling" override block for BOTH header and footer
 * sections. All output is class- or stylesheet-based (no inline element styles):
 *   - Padding → the `spacing` option type (responsive Bootstrap utility classes).
 *   - Container + Custom CSS Class → wrapper classes.
 *   - Background / Text typography / Link color / Borders → emitted as scoped
 *     rules into the generated CSS file by inc/includes/hf-custom-css.php.
 *
 * @param string $prefix e.g. 'pre_footer', 'main_footer', 'post_footer',
 *                        'copyright', 'topbar', 'main', 'bottombar'
 * @return array the {prefix}_custom_styling multi-picker definition
 */
function unysonplus_hf_custom_styling( $prefix ) {
	return array(
		'type'   => 'multi-picker',
		'label'  => false,
		'desc'   => false,
		'picker' => array(
			'enabled' => array(
				'label'        => __( 'Custom Styling', 'unysonplus' ),
				'type'         => 'switch',
				'right-choice' => array( 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ),
				'left-choice'  => array( 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ),
				'value'        => 'no',
				'desc'         => __( 'Enable to override the global footer styling for this section.', 'unysonplus' ),
			),
		),
		'choices' => array(
			'yes' => array(
				$prefix . '_container' => array(
					'label'   => __( 'Container', 'unysonplus' ),
					'type'    => 'select',
					'value'   => 'container',
					'choices' => array(
						'container'       => __( 'Fixed Width', 'unysonplus' ),
						'container-fluid' => __( 'Full Width', 'unysonplus' ),
					),
				),
				$prefix . '_bg_color' => array(
					'label' => __( 'Background Color', 'unysonplus' ),
					'type'  => 'rgba-color-picker',
					'value' => '',
					'desc'  => __( 'e.g. rgba(33, 37, 41, 1)', 'unysonplus' ),
				),
				$prefix . '_bg_image' => array(
					'label' => __( 'Background Image', 'unysonplus' ),
					'type'  => 'upload',
					'desc'  => __( 'Optional background image for this section.', 'unysonplus' ),
				),
				$prefix . '_bg_overlay' => array(
					'label'      => __( 'Background Overlay Opacity', 'unysonplus' ),
					'type'       => 'slider',
					'value'      => 80,
					'properties' => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
					'desc'       => __( 'Overlay opacity over the background image (0 = transparent, 100 = solid). Only used when a background image is set.', 'unysonplus' ),
				),
				$prefix . '_typography' => array(
					'label' => __( 'Text', 'unysonplus' ),
					'type'  => 'typography-v2',
					'desc'  => __( 'Font family, size, weight, line-height, letter-spacing and color for this section\'s text.', 'unysonplus' ),
				),
				$prefix . '_link_color' => array(
					'label' => __( 'Link Color', 'unysonplus' ),
					'type'  => 'color-picker',
					'value' => '',
					'desc'  => __( 'e.g. #adb5bd', 'unysonplus' ),
				),
				$prefix . '_padding' => array(
					'type'  => 'spacing',
					'mode'  => 'padding',
					'label' => __( 'Padding', 'unysonplus' ),
					'desc'  => __( 'Inner spacing for this section (responsive). Applied as utility classes.', 'unysonplus' ),
				),
				$prefix . '_border_top_color' => array(
					'label' => __( 'Top Border Color', 'unysonplus' ),
					'type'  => 'color-picker',
					'value' => '',
					'desc'  => __( 'Leave empty for no border.', 'unysonplus' ),
				),
				$prefix . '_border_top_width' => array(
					'label' => __( 'Top Border Width', 'unysonplus' ),
					'type'  => 'text',
					'value' => '',
					'desc'  => __( 'e.g. 1px', 'unysonplus' ),
				),
				$prefix . '_border_bottom_color' => array(
					'label' => __( 'Bottom Border Color', 'unysonplus' ),
					'type'  => 'color-picker',
					'value' => '',
					'desc'  => __( 'Leave empty for no border.', 'unysonplus' ),
				),
				$prefix . '_border_bottom_width' => array(
					'label' => __( 'Bottom Border Width', 'unysonplus' ),
					'type'  => 'text',
					'value' => '',
					'desc'  => __( 'e.g. 1px', 'unysonplus' ),
				),
				$prefix . '_css_class' => array(
					'label' => __( 'Custom CSS Class', 'unysonplus' ),
					'type'  => 'text',
					'value' => '',
					'desc'  => __( 'Add custom CSS class(es) to this section.', 'unysonplus' ),
				),
			),
		),
		'show_borders' => false,
	);
}
endif;

if ( ! function_exists( 'unysonplus_footer_custom_styling' ) ) :
/**
 * Back-compat alias — footer section files call this; delegates to the shared
 * header/footer styling block.
 *
 * @param string $prefix e.g. 'pre_footer', 'main_footer', 'post_footer', 'copyright'
 * @return array
 */
function unysonplus_footer_custom_styling( $prefix ) {
	return unysonplus_hf_custom_styling( $prefix );
}
endif;
