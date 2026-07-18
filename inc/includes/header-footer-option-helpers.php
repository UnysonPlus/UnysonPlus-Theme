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

	// Published Snippets (the plugin's `snippet` post type), for the Snippet element.
	// Empty (just the placeholder) when the Snippets extension is inactive.
	$snippet = array( '' => __( '— Select a snippet —', 'unysonplus' ) );
	if ( function_exists( 'fw_ext_snippets_render' ) && post_type_exists( 'snippet' ) ) {
		foreach ( get_posts( array(
			'post_type'        => 'snippet',
			'post_status'      => 'publish',
			'numberposts'      => -1,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'suppress_filters' => false,
		) ) as $sp ) {
			$snippet[ $sp->ID ] = ( $sp->post_title !== '' )
				? $sp->post_title
				/* translators: %d: snippet post ID */
				: sprintf( __( 'Snippet #%d', 'unysonplus' ), $sp->ID );
		}
	}

	$cache = array(
		'sidebar'       => $sidebar,
		'menu'          => $menu,
		'menu_location' => $menu_location,
		'builder'       => $builder,
		'snippet'       => $snippet,
	);
	return $cache;
}
endif;

if ( ! function_exists( 'unysonplus_hf_snippet_options' ) ) :
/**
 * Leaf options for the Snippet element, shared by the header + footer element popups
 * (so the two stay in sync). Renders a Snippet (page-builder content) anywhere an
 * element can go — a header column, a footer column, or the off-canvas drawer — which
 * is how shortcodes / bespoke markup get into the chrome.
 *
 * @return array
 */
function unysonplus_hf_snippet_options() {
	$ch = unysonplus_hf_choices();
	return array(
		'snippet_id' => array(
			'label'   => __( 'Snippet', 'unysonplus' ),
			'type'    => 'select',
			'value'   => '',
			'choices' => $ch['snippet'],
			'desc'    => __( 'Render a published Snippet here — build it with the page builder (Snippets &rarr; Add New) and it can hold any shortcode or custom markup.', 'unysonplus' ),
		),
	);
}
endif;


if ( ! function_exists( 'unysonplus_hf_cta_button_options' ) ) :
/**
 * Leaf options for the CTA Button element, shared by the header + footer element
 * popups (so the two stay in sync).
 *
 * Button Style + Button Size reuse the Button shortcode's `button-style-picker`
 * choices (sc_get_button_style_choices() / sc_get_button_size_choices()), which are
 * sourced from Theme Settings → General → Buttons. A header/footer CTA therefore
 * rides the theme's button design system — rendered as `btn {style} {size}` (e.g.
 * `btn btn-primary btn-lg`) with live previews in the picker — instead of one-off
 * background/text colors. Mirrors the flip-box shortcode's Style/Size pickers.
 *
 * The `button-style-picker` option type is part of the core framework (always
 * available with the plugin active); only the choice helpers belong to the
 * shortcodes extension, so they're function_exists-guarded with a plain-select
 * fallback for Style when that extension isn't active.
 *
 * @return array
 */
function unysonplus_hf_cta_button_options() {
	$opts = array(
		'cta_text' => array( 'label' => __( 'Button Text', 'unysonplus' ), 'type' => 'text', 'value' => 'Get Started' ),
		'cta_link' => array( 'label' => __( 'Button Link', 'unysonplus' ), 'type' => 'text', 'value' => '#' ),
	);

	if ( function_exists( 'sc_get_button_style_choices' ) ) {
		$style_choices = sc_get_button_style_choices();
		$opts['cta_style'] = array(
			'label'   => __( 'Button Style', 'unysonplus' ),
			'desc'    => __( 'Sourced from Theme Settings → General → Buttons (colors + outline presets). Each option previews the real button.', 'unysonplus' ),
			'type'    => 'button-style-picker',
			'choices' => $style_choices,
			'value'   => ( is_array( $style_choices ) && $style_choices ) ? (string) key( $style_choices ) : '',
		);
	} else {
		// Shortcodes styling helper unavailable — basic style select fallback.
		$opts['cta_style'] = array(
			'label'   => __( 'Button Style', 'unysonplus' ),
			'type'    => 'select',
			'value'   => 'filled',
			'choices' => array(
				'filled'  => __( 'Filled', 'unysonplus' ),
				'outline' => __( 'Outline', 'unysonplus' ),
				'pill'    => __( 'Pill (Rounded)', 'unysonplus' ),
			),
		);
	}

	if ( function_exists( 'sc_get_button_size_choices' ) ) {
		$size_choices = sc_get_button_size_choices();
		$opts['cta_size'] = array(
			'label'        => __( 'Button Size', 'unysonplus' ),
			'desc'         => __( 'Sourced from Theme Settings → General → Buttons → Sizes.', 'unysonplus' ),
			'type'         => 'button-style-picker',
			'choices'      => $size_choices,
			'value'        => ( is_array( $size_choices ) && $size_choices ) ? (string) key( $size_choices ) : '',
			// Size classes carry no color, so ride them on a primary button —
			// otherwise the preview is an unstyled, background-less `.btn`.
			'preview_base' => 'btn btn-primary',
		);
	}

	return $opts;
}
endif;


/* ============================================================
 * Element pickers (the popup shown when you click "Add" in a column)
 * ============================================================ */

if ( ! function_exists( 'unysonplus_hf_registered_elements' ) ) :
/**
 * Addon-registered header / footer elements. Plugins or a child theme add a new
 * draggable element to the Add-element popup via the `unysonplus_hf_elements`
 * filter — the USER then places it in any column and orders it like any built-in
 * element (no forced position). Example (e.g. a WooCommerce cart):
 *
 *   add_filter( 'unysonplus_hf_elements', function ( $els ) {
 *       $els['cart'] = array(
 *           'label'   => __( 'Cart', 'my-addon' ),
 *           'context' => 'both',   // 'header' | 'footer' | 'both'
 *           'options' => array(    // option schema shown in the element modal
 *               'cart_show_count' => array( 'type' => 'switch', 'label' => 'Show count', 'value' => 'yes' ),
 *           ),
 *       );
 *       return $els;
 *   } );
 *
 * Render it by hooking (fires from unysonplus_render_{header,footer}_element):
 *
 *   add_action( 'unysonplus_render_hf_element_cart', function ( $settings, $element, $where ) {
 *       echo my_addon_cart_html( $settings );   // $where = 'header' | 'footer'
 *   }, 10, 3 );
 *
 * @param string $context 'header' | 'footer' — only elements for this context are returned.
 * @return array<string,array{label:string,options:array}>
 */
function unysonplus_hf_registered_elements( $context = 'header' ) {
	$els = apply_filters( 'unysonplus_hf_elements', array() );
	if ( ! is_array( $els ) ) { return array(); }

	$out = array();
	foreach ( $els as $slug => $el ) {
		if ( ! is_string( $slug ) || $slug === '' || ! is_array( $el ) || empty( $el['label'] ) ) { continue; }
		$ctx = isset( $el['context'] ) ? $el['context'] : 'both';
		if ( $ctx === 'both' || $ctx === $context ) {
			$out[ sanitize_key( $slug ) ] = array(
				'label'   => $el['label'],
				'options' => ( isset( $el['options'] ) && is_array( $el['options'] ) ) ? $el['options'] : array(),
			);
		}
	}
	return $out;
}
endif;

if ( ! function_exists( 'unysonplus_hf_merge_registered_elements' ) ) :
/**
 * Fold addon-registered elements into a built popup array: their labels join the
 * element dropdown, their option schemas become the element's reveal.
 *
 * @param array  $popup   The popup array (element_type multi-picker + siblings).
 * @param string $context 'header' | 'footer'.
 * @return array
 */
function unysonplus_hf_merge_registered_elements( $popup, $context ) {
	foreach ( unysonplus_hf_registered_elements( $context ) as $slug => $el ) {
		$popup['element_type']['picker']['element']['choices'][ $slug ] = $el['label'];
		if ( ! empty( $el['options'] ) ) {
			$popup['element_type']['choices'][ $slug ] = $el['options'];
		}
	}
	return $popup;
}
endif;

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
						'icon_text'       => __( 'Icon Text', 'unysonplus' ),
						'search'          => __( 'Search', 'unysonplus' ),
						'social_icons'    => __( 'Social Icons', 'unysonplus' ),
						'custom_html'     => __( 'Custom HTML', 'unysonplus' ),
						'text'            => __( 'Text', 'unysonplus' ),
						'widget_area'     => __( 'Widget Area', 'unysonplus' ),
						'back_to_top'     => __( 'Back to Top', 'unysonplus' ),
						'builder_section' => __( 'Builder Section', 'unysonplus' ),
						'snippet'         => __( 'Snippet', 'unysonplus' ),
					),
					'desc'    => __( 'Select footer element.', 'unysonplus' ),
				),
			),
			'choices' => array(
				'cta_button' => unysonplus_hf_cta_button_options(),
				'phone' => array(
					'phone_number' => array( 'label' => __( 'Phone Number', 'unysonplus' ), 'type' => 'text', 'value' => '' ),
				),
				'icon_text' => array(
					'icontext_icon' => array(
						'type'         => 'icon-v2',
						'label'        => __( 'Icon', 'unysonplus' ),
						'desc'         => __( 'Icon shown before the text (e.g. an envelope for email, a phone, a pin for an address).', 'unysonplus' ),
						'preview_size' => 'small',
						'modal_size'   => 'medium',
					),
					'icontext_text' => array(
						'label' => __( 'Text', 'unysonplus' ),
						'desc'  => __( 'The text to display — e.g. info@example.com, +1 (555) 123-4567, or 123 Main St.', 'unysonplus' ),
						'type'  => 'text',
						'value' => '',
					),
					'icontext_link_type' => array(
						'label'   => __( 'Link Type', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'none',
						'choices' => array(
							'none'  => __( 'No link', 'unysonplus' ),
							'url'   => __( 'Website URL', 'unysonplus' ),
							'email' => __( 'Email (mailto:)', 'unysonplus' ),
							'phone' => __( 'Phone (tel:)', 'unysonplus' ),
						),
					),
					'icontext_link' => array(
						'label' => __( 'Link', 'unysonplus' ),
						'desc'  => __( 'The target — a URL, email address, or phone number. Leave empty for Email / Phone to reuse the Text above.', 'unysonplus' ),
						'type'  => 'text',
						'value' => '',
					),
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
				'snippet' => unysonplus_hf_snippet_options(),
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
		'element_css_class' => array(
			'type'  => 'text',
			'label' => __( 'CSS Class', 'unysonplus' ),
			'desc'  => __( 'Extra class(es) added to this element wrapper, for custom CSS targeting.', 'unysonplus' ),
			'value' => '',
		),
	);
	$popup = unysonplus_hf_merge_registered_elements( $popup, 'footer' );
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
						'icon_text'       => __( 'Icon Text', 'unysonplus' ),
						'search'          => __( 'Search', 'unysonplus' ),
						'social_icons'    => __( 'Social Icons', 'unysonplus' ),
						'custom_html'     => __( 'Custom HTML', 'unysonplus' ),
						'text'            => __( 'Text', 'unysonplus' ),
						'widget_area'     => __( 'Widget Area', 'unysonplus' ),
						'builder_section' => __( 'Builder Section', 'unysonplus' ),
						'snippet'         => __( 'Snippet', 'unysonplus' ),
						'spacer'          => __( 'Spacer', 'unysonplus' ),
						'divider'         => __( 'Divider', 'unysonplus' ),
					),
					'desc'    => __( 'Select header element.', 'unysonplus' ),
				),
			),
			'choices' => array(
				'cta_button' => unysonplus_hf_cta_button_options(),
				'phone' => array(
					'phone_number' => array( 'label' => __( 'Phone Number', 'unysonplus' ), 'type' => 'text', 'value' => '' ),
				),
				'icon_text' => array(
					'icontext_icon' => array(
						'type'         => 'icon-v2',
						'label'        => __( 'Icon', 'unysonplus' ),
						'desc'         => __( 'Icon shown before the text (e.g. an envelope for email, a phone, a pin for an address).', 'unysonplus' ),
						'preview_size' => 'small',
						'modal_size'   => 'medium',
					),
					'icontext_text' => array(
						'label' => __( 'Text', 'unysonplus' ),
						'desc'  => __( 'The text to display — e.g. info@example.com, +1 (555) 123-4567, or 123 Main St.', 'unysonplus' ),
						'type'  => 'text',
						'value' => '',
					),
					'icontext_link_type' => array(
						'label'   => __( 'Link Type', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'none',
						'choices' => array(
							'none'  => __( 'No link', 'unysonplus' ),
							'url'   => __( 'Website URL', 'unysonplus' ),
							'email' => __( 'Email (mailto:)', 'unysonplus' ),
							'phone' => __( 'Phone (tel:)', 'unysonplus' ),
						),
					),
					'icontext_link' => array(
						'label' => __( 'Link', 'unysonplus' ),
						'desc'  => __( 'The target — a URL, email address, or phone number. Leave empty for Email / Phone to reuse the Text above.', 'unysonplus' ),
						'type'  => 'text',
						'value' => '',
					),
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
				'snippet' => unysonplus_hf_snippet_options(),
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
		'element_css_class' => array(
			'type'  => 'text',
			'label' => __( 'CSS Class', 'unysonplus' ),
			'desc'  => __( 'Extra class(es) added to this element wrapper, for custom CSS targeting.', 'unysonplus' ),
			'value' => '',
		),
	);
	$popup = unysonplus_hf_merge_registered_elements( $popup, 'header' );
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
	$element_label_map       = '{"logo":"Logo","footer_logo":"Footer Logo","menu":"Menu","menu_area":"Menu Area","cta_button":"CTA Button","phone":"Phone Number","icon_text":"Icon Text","search":"Search","social_icons":"Social Icons","custom_html":"Custom HTML","text":"Text","widget_area":"Widget Area","copyright_text":"Copyright Text","back_to_top":"Back to Top","builder_section":"Builder Section"}';
	$sidebar_label_map       = json_encode( $ch['sidebar'], JSON_UNESCAPED_UNICODE );
	$menu_label_map          = json_encode( array_map( 'strval', $ch['menu'] ), JSON_UNESCAPED_UNICODE );
	$menu_location_label_map = json_encode( $ch['menu_location'], JSON_UNESCAPED_UNICODE );
	$builder_label_map       = json_encode( array_map( 'strval', $ch['builder'] ), JSON_UNESCAPED_UNICODE );

	$tpl = '{{= (function(){ var el = element_type["element"]; var lbl = (' . $element_label_map . ')[el] || el; if (el === "widget_area" && element_type["widget_area"] && element_type["widget_area"]["sidebar_id"]) { var smap = ' . $sidebar_label_map . '; var sid = element_type["widget_area"]["sidebar_id"]; lbl = (smap[sid] || sid); } if (el === "menu" && element_type["menu"] && element_type["menu"]["menu_id"]) { var mmap = ' . $menu_label_map . '; var mid = element_type["menu"]["menu_id"]; lbl = (mmap[mid] || mid); } if (el === "menu_area" && element_type["menu_area"] && element_type["menu_area"]["menu_location"]) { var amap = ' . $menu_location_label_map . '; var loc = element_type["menu_area"]["menu_location"]; lbl = (amap[loc] || loc); } if (el === "builder_section" && element_type["builder_section"] && element_type["builder_section"]["builder_post_id"]) { var bmap = ' . $builder_label_map . '; var bid = element_type["builder_section"]["builder_post_id"]; lbl = (bmap[bid] || bid); } if (el === "text" && element_type["text"] && element_type["text"]["text_content"]) { var _t = String(element_type["text"]["text_content"]).replace(/<[^>]*>/g," ").replace(/&[a-z#0-9]+;/gi," ").replace(/\s+/g," ").trim(); if (_t) lbl = (_t.length > 45 ? _t.substring(0,45) + "…" : _t); } if (el === "custom_html" && element_type["custom_html"] && element_type["custom_html"]["custom_html_content"]) { var _h = String(element_type["custom_html"]["custom_html_content"]).replace(/<[^>]*>/g," ").replace(/\s+/g," ").trim(); if (_h) lbl = (_h.length > 45 ? _h.substring(0,45) + "…" : _h); } if (el === "copyright_text" && element_type["copyright_text"] && element_type["copyright_text"]["copyright_content"]) { var _c = String(element_type["copyright_text"]["copyright_content"]).replace(/<[^>]*>/g," ").replace(/&[a-z#0-9]+;/gi," ").replace(/\s+/g," ").trim(); if (_c) lbl = (_c.length > 45 ? _c.substring(0,45) + "…" : _c); } if (el === "icon_text" && element_type["icon_text"] && element_type["icon_text"]["icontext_text"]) { var _i = String(element_type["icon_text"]["icontext_text"]).replace(/\s+/g," ").trim(); if (_i) lbl = (_i.length > 45 ? _i.substring(0,45) + "…" : _i); } if (el === "cta_button" && element_type["cta_button"] && element_type["cta_button"]["cta_text"]) { var _cta = String(element_type["cta_button"]["cta_text"]).replace(/\s+/g," ").trim(); if (_cta) lbl = (_cta.length > 45 ? _cta.substring(0,45) + "…" : _cta); } return lbl; })() }}';
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

	$tpl = '{{= (function(){ var el = element_type["element"]; var lbl = ({"logo":"Logo","cta_button":"CTA Button","phone":"Phone Number","icon_text":"Icon Text","search":"Search","social_icons":"Social Icons","custom_html":"Custom HTML","text":"Text","menu":"Menu","menu_area":"Menu Area","widget_area":"Widget Area","builder_section":"Builder Section","spacer":"Spacer","divider":"Divider"})[el] || el; if (el === "widget_area" && element_type["widget_area"] && element_type["widget_area"]["sidebar_id"]) { var smap = ' . $sidebar_label_map . '; var sid = element_type["widget_area"]["sidebar_id"]; lbl = (smap[sid] || sid); } if (el === "menu" && element_type["menu"] && element_type["menu"]["menu_id"]) { var mmap = ' . $menu_label_map . '; var mid = element_type["menu"]["menu_id"]; lbl = (mmap[mid] || mid); } if (el === "menu_area" && element_type["menu_area"] && element_type["menu_area"]["menu_location"]) { var amap = ' . $menu_location_label_map . '; var loc = element_type["menu_area"]["menu_location"]; lbl = (amap[loc] || loc); } if (el === "builder_section" && element_type["builder_section"] && element_type["builder_section"]["builder_post_id"]) { var bmap = ' . $builder_label_map . '; var bid = element_type["builder_section"]["builder_post_id"]; lbl = (bmap[bid] || bid); } if (el === "text" && element_type["text"] && element_type["text"]["text_content"]) { var _t = String(element_type["text"]["text_content"]).replace(/<[^>]*>/g," ").replace(/&[a-z#0-9]+;/gi," ").replace(/\s+/g," ").trim(); if (_t) lbl = (_t.length > 45 ? _t.substring(0,45) + "…" : _t); } if (el === "custom_html" && element_type["custom_html"] && element_type["custom_html"]["custom_html_content"]) { var _h = String(element_type["custom_html"]["custom_html_content"]).replace(/<[^>]*>/g," ").replace(/\s+/g," ").trim(); if (_h) lbl = (_h.length > 45 ? _h.substring(0,45) + "…" : _h); } if (el === "icon_text" && element_type["icon_text"] && element_type["icon_text"]["icontext_text"]) { var _i = String(element_type["icon_text"]["icontext_text"]).replace(/\s+/g," ").trim(); if (_i) lbl = (_i.length > 45 ? _i.substring(0,45) + "…" : _i); } if (el === "cta_button" && element_type["cta_button"] && element_type["cta_button"]["cta_text"]) { var _cta = String(element_type["cta_button"]["cta_text"]).replace(/\s+/g," ").trim(); if (_cta) lbl = (_cta.length > 45 ? _cta.substring(0,45) + "…" : _cta); } return lbl; })() }}';
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

if ( ! function_exists( 'unysonplus_footer_equal_split' ) ) :
/**
 * A default Split-Slider value: $n equal-width named-blank segments summing to 100.
 * Used as the split-slider's default `value` so a section always has a definite
 * column count (no AUTO ambiguity when resolving how many columns to render).
 *
 * @param int $n
 * @return array list of array( 'w' => int, 'name' => '' )
 */
function unysonplus_footer_equal_split( $n ) {
	$n    = max( 1, (int) $n );
	$each = (int) floor( 100 / $n );
	$segs = array();
	for ( $i = 0; $i < $n; $i++ ) { $segs[] = array( 'w' => $each, 'name' => '' ); }
	$segs[0]['w'] += 100 - ( $each * $n );
	return $segs;
}
endif;

if ( ! function_exists( 'unysonplus_footer_fifth_ratio_field' ) ) :
/**
 * Column-Ratio IMAGE-PICKER for the 5-unit (fifths) grid — used for the 5-column footer choice.
 * The twelfths split-slider can't express fifths, so this offers curated compositions of the five
 * grid units among 2..5 physical columns (e.g. 2/5 + 1/5 + 1/5 + 1/5 = a wide first column). Each
 * tile is keyed `f5-*` (or `5-equal`) → unysonplus_get_footer_col_classes emits the fifth grid
 * classes (fw-col-sm-15/25/35/45); the footer render sets the real column count from the chosen
 * composition's part-count. Thumbnails are generated inline (no image files). Default = five equal
 * fifths (the expected 5-column grid; the others are additive spanning layouts).
 *
 * @return array image-picker option definition
 */
function unysonplus_footer_fifth_ratio_field() {
	// key => unit widths (each 1..4, summing to 5). '5-equal' reuses the existing 5x1/5 map entry.
	$layouts = array(
		'5-equal'    => array( 1, 1, 1, 1, 1 ),
		'f5-2-1-1-1' => array( 2, 1, 1, 1 ),
		'f5-1-2-1-1' => array( 1, 2, 1, 1 ),
		'f5-1-1-2-1' => array( 1, 1, 2, 1 ),
		'f5-1-1-1-2' => array( 1, 1, 1, 2 ),
		'f5-3-1-1'   => array( 3, 1, 1 ),
		'f5-1-3-1'   => array( 1, 3, 1 ),
		'f5-1-1-3'   => array( 1, 1, 3 ),
		'f5-2-2-1'   => array( 2, 2, 1 ),
		'f5-2-1-2'   => array( 2, 1, 2 ),
		'f5-1-2-2'   => array( 1, 2, 2 ),
		'f5-4-1'     => array( 4, 1 ),
		'f5-1-4'     => array( 1, 4 ),
		'f5-3-2'     => array( 3, 2 ),
		'f5-2-3'     => array( 2, 3 ),
	);
	// Inline SVG bar thumbnail (data URI): bars proportional to the fifth units.
	$thumb = function ( $parts ) {
		$w = 104; $h = 40; $pad = 3; $gap = 3;
		$avail = $w - 2 * $pad - ( count( $parts ) - 1 ) * $gap;
		$x = $pad; $bars = '';
		foreach ( $parts as $u ) {
			$bw    = $avail * $u / 5;
			$bars .= '<rect x="' . round( $x, 1 ) . '" y="' . $pad . '" width="' . round( $bw, 1 ) . '" height="' . ( $h - 2 * $pad ) . '" rx="2" fill="#9aa2ad"/>';
			$x    += $bw + $gap;
		}
		return 'data:image/svg+xml,' . rawurlencode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $w . ' ' . $h . '" width="' . $w . '" height="' . $h . '">' . $bars . '</svg>' );
	};
	$choices = array();
	foreach ( $layouts as $key => $parts ) {
		$label           = implode( ' + ', array_map( function ( $u ) { return $u . '/5'; }, $parts ) );
		$choices[ $key ] = array( 'small' => array( 'src' => $thumb( $parts ), 'height' => 40, 'title' => $label ) );
	}
	return array(
		'label'   => __( 'Column Ratio', 'unysonplus' ),
		'type'    => 'image-picker',
		'value'   => '5-equal',
		'desc'    => __( 'Distribute the 5-unit grid: five equal fifths, or span a column (e.g. 2/5 + 1/5 + 1/5 + 1/5 for a wide first column). A composition with fewer parts renders fewer columns.', 'unysonplus' ),
		'choices' => $choices,
	);
}
endif;

if ( ! function_exists( 'unysonplus_footer_columns_field' ) ) :
/**
 * A footer section's Columns control: a `multi-picker` whose "Number of Columns"
 * select (1..$max) REVEALS exactly that many addable-popup content columns — so the
 * admin only ever shows the columns in use. For 2+ columns the revealed group also
 * carries a `<prefix>_split` Split-Slider (locked to that count) that sets the column
 * RATIO. Widths snap to the Bootstrap 12-column grid at render time
 * (inc/includes/footer-builder.php → col-md-N).
 *
 * Stored (multi-picker): {
 *   count => 'N',
 *   'N'   => { <prefix>_split:[{w,name}..], <prefix>_col_1..N:[items] },
 * }
 * Content is nested under the chosen count, so footers saved before the slider keep
 * their content (only the old ratio image-picker is superseded).
 *
 * @param string $prefix        'pre_footer' | 'main_footer' | 'post_footer' | 'copyright'
 * @param int    $max           most columns (6 for footer rows, 3 for copyright)
 * @param int    $default_count default selected column count
 * @param array  $col1_default  default element items for Column 1 (e.g. the copyright line)
 * @return array the `multi-picker` option definition
 */
function unysonplus_footer_columns_field( $prefix, $max = 6, $default_count = 1, $col1_default = array() ) {
	$max           = max( 1, (int) $max );
	$default_count = max( 1, min( $max, (int) $default_count ) );

	$count_choices = array();
	for ( $i = 1; $i <= $max; $i++ ) {
		$count_choices[ (string) $i ] = sprintf( _n( '%d Column', '%d Columns', $i, 'unysonplus' ), $i );
	}

	$choices = array();
	for ( $n = 1; $n <= $max; $n++ ) {
		$reveal = array();
		// Ratio control. 2, 3, 4 and 6 columns → the Split-Slider on the Bootstrap 12-grid
		// (1/2, 1/3, 1/4, 1/6). 5 columns → the fifths IMAGE-PICKER (`_layout` = f5-* / 5-equal):
		// the 12-grid can't express fifths, so the picker offers curated compositions of the
		// 5 grid units (equal, or span a column like 2/5 + 1/5 + 1/5 + 1/5). (1 col = full width.)
		if ( 5 === $n ) {
			$reveal[ $prefix . '_layout' ] = unysonplus_footer_fifth_ratio_field();
		} elseif ( $n >= 2 ) {
			$reveal[ $prefix . '_split' ] = array(
				'type'        => 'split-slider',
				'label'       => __( 'Column Ratio', 'unysonplus' ),
				'desc'        => __( 'Drag the dividers to set each column\'s width, shown as fractions (e.g. 1/2, 1/3, 1/4). Optionally name each column.', 'unysonplus' ),
				'min'         => $n,
				'max'         => $n,
				'step'        => 1,
				'min_width'   => 8,
				'allow_names' => true,
				'auto_count'  => $n,
				'denominator' => 12, // clean twelfths (1/2, 1/3, 1/4, 1/6)
				'locked'      => true, // count is fixed by the "Number of Columns" select above
				'value'       => unysonplus_footer_equal_split( $n ),
			);
		}
		for ( $i = 1; $i <= $n; $i++ ) {
			$reveal[ $prefix . '_col_' . $i ] = unysonplus_footer_column(
				sprintf( __( 'Column %d', 'unysonplus' ), $i ),
				( 1 === $i ) ? $col1_default : array()
			);
		}
		$choices[ (string) $n ] = $reveal;
	}

	return array(
		'type'   => 'multi-picker',
		'label'  => false,
		'desc'   => false,
		'picker' => array(
			'count' => array(
				'type'    => 'select',
				'label'   => __( 'Number of Columns', 'unysonplus' ),
				'value'   => (string) $default_count,
				'choices' => $count_choices,
				'desc'    => __( 'How many columns this footer section has. Set their widths with the Column Ratio slider that appears below.', 'unysonplus' ),
			),
		),
		'choices'      => $choices,
		'show_borders' => false,
	);
}
endif;

if ( ! function_exists( 'unysonplus_hf_columns_note' ) ) :
/**
 * A short hint explaining the zone-based alignment of a header/footer row's
 * Left / Center / Right columns. Shared by the Top Bar, Main Header and Bottom Bar
 * so the behaviour is discoverable (content aligns to the column it sits in; empty
 * columns collapse). Rendered as an `html-full` note above the columns.
 *
 * @return array
 */
function unysonplus_hf_columns_note() {
	$html = '<div style="box-sizing:border-box;max-width:100%;padding:10px 12px;background:#f6f7f7;border-left:3px solid #2271b1;border-radius:4px;font-size:12.5px;line-height:1.55;color:#50575e;overflow-wrap:break-word;">'
		. '<strong>' . esc_html__( 'How the columns align', 'unysonplus' ) . '</strong> — '
		. esc_html__( 'elements align to the column they sit in: Left → left, Center → centered, Right → right. Empty columns collapse, so filling only one column aligns everything to that side (e.g. put everything in the Center column to center the whole row).', 'unysonplus' )
		. '</div>';
	return array(
		'type'  => 'html-full',
		'label' => false,
		'html'  => $html,
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

if ( ! function_exists( 'unysonplus_hf_border_row_field' ) ) :
/**
 * One border side (Top / Bottom) as a single `multi-inline` row —
 * Width (unit-input) · Style (select) · Color (palette-linked compact preset),
 * laid out left-to-right like the CSS shorthand `1px solid #000`.
 *
 * Replaces the three separate {prefix}_border_{side}_{width,style,color} leaves.
 * Saved value shape:
 *   array(
 *     'width' => array( 'value' => '1', 'unit' => 'px' ),
 *     'style' => 'solid',
 *     'color' => array( 'predefined' => 'text-red', 'custom' => '' ),
 *   )
 * The color child reuses sc_color_field_compact()'s palette choices so it stays
 * tied to Theme Settings → Colors. Consumed by inc/includes/hf-custom-css.php
 * (which also tolerates the legacy flat ids for pre-combine saves).
 *
 * @param string $label e.g. 'Top Border'
 * @param string $desc
 * @return array multi-inline option definition
 */
function unysonplus_hf_border_row_field( $label, $desc ) {
	// Palette-linked color choices — derive from the compact color helper so the
	// border color offers the same presets as every other color control.
	$color_choices = array();
	$color_picker  = 'color-picker';
	if ( function_exists( 'sc_color_field_compact' ) ) {
		$cf            = sc_color_field_compact( array( 'kind' => 'text' ) );
		$color_choices = isset( $cf['choices'] ) ? $cf['choices'] : array();
		$color_picker  = isset( $cf['picker'] )  ? $cf['picker']  : 'color-picker';
	}

	return array(
		'type'  => 'multi-inline',
		'label' => $label,
		'desc'  => $desc,
		'value' => array(
			'width' => array( 'value' => '', 'unit' => 'px' ),
			'style' => 'solid',
			'color' => array( 'predefined' => '', 'custom' => '' ),
		),
		'fw_multi_options' => array(
			'width' => array(
				'type'  => 'unit-input',
				'title' => __( 'Width', 'unysonplus' ),
				'units' => array( 'px', 'em', 'rem' ),
				'min'   => 0,
			),
			'style' => array(
				'type'    => 'select',
				'title'   => __( 'Style', 'unysonplus' ),
				'choices' => array(
					'solid'  => __( 'Solid', 'unysonplus' ),
					'dashed' => __( 'Dashed', 'unysonplus' ),
					'dotted' => __( 'Dotted', 'unysonplus' ),
					'double' => __( 'Double', 'unysonplus' ),
				),
			),
			'color' => array(
				'type'    => 'predefined-colors-color-picker-compact',
				'title'   => __( 'Color', 'unysonplus' ),
				'picker'  => $color_picker,
				'choices' => $color_choices,
			),
		),
	);
}
endif;

if ( ! function_exists( 'unysonplus_hf_border_sides_field' ) ) :
/**
 * Multi-select image-picker: which edge(s) the border applies to — any combination of
 * Top / Right / Bottom / Left. Value is an ARRAY of the checked side keys (default
 * ['top']). Shared by the Footer Layout border and the per-section Custom Styling border
 * so both offer the same control. Consumers map the array to the four CSS edges.
 *
 * @param array $default default checked sides (defaults to array( 'top' )).
 * @return array image-picker (multiple) option definition (caller supplies the option id).
 */
function unysonplus_hf_border_sides_field( $default = array( 'top' ) ) {
	// Inline data-URI SVG tiles: a mini box with an accent line on the relevant edge
	// (horizontal for top/bottom, vertical for left/right), caption baked in — same
	// approach as the Container tiles.
	$svg = function ( $side, $lbl ) {
		$accent = '#2271b1'; $line = '#c3c4c7';
		$box    = '<rect x="30" y="8" width="44" height="26" rx="3" fill="none" stroke="' . $line . '" stroke-width="1.5"/>';
		$edges  = array(
			'top'    => '<rect x="30" y="7"  width="44" height="3" rx="1.5" fill="' . $accent . '"/>',
			'bottom' => '<rect x="30" y="32" width="44" height="3" rx="1.5" fill="' . $accent . '"/>',
			'left'   => '<rect x="29" y="8"  width="3" height="26" rx="1.5" fill="' . $accent . '"/>',
			'right'  => '<rect x="72" y="8"  width="3" height="26" rx="1.5" fill="' . $accent . '"/>',
		);
		$edge = isset( $edges[ $side ] ) ? $edges[ $side ] : '';
		$text = '<text x="52" y="47" text-anchor="middle" font-family="-apple-system,Segoe UI,Roboto,sans-serif" font-size="10" fill="#50575e">' . $lbl . '</text>';
		return 'data:image/svg+xml,' . rawurlencode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 104 52" width="104" height="52">' . $box . $edge . $text . '</svg>' );
	};
	$tile = function ( $v, $l ) use ( $svg ) {
		$u = $svg( $v, $l );
		return array( 'small' => array( 'height' => 52, 'src' => $u ), 'large' => array( 'height' => 74, 'src' => $u ) );
	};
	return array(
		'type'     => 'image-picker',
		'multiple' => true,
		'label'    => __( 'Border Sides', 'unysonplus' ),
		'desc'     => __( 'Check any combination of edges the border applies to — top, right, bottom, left.', 'unysonplus' ),
		'value'    => is_array( $default ) ? array_values( $default ) : array( 'top' ),
		'choices'  => array(
			'top'    => $tile( 'top',    __( 'Top', 'unysonplus' ) ),
			'right'  => $tile( 'right',  __( 'Right', 'unysonplus' ) ),
			'bottom' => $tile( 'bottom', __( 'Bottom', 'unysonplus' ) ),
			'left'   => $tile( 'left',   __( 'Left', 'unysonplus' ) ),
		),
	);
}
endif;

if ( ! function_exists( 'unysonplus_hf_normalize_sides' ) ) :
/**
 * Normalize a Border Sides value to a clean array of edge keys (top/right/bottom/left).
 * Tolerates the legacy single-select strings ('top'|'bottom'|'both') and JSON strings,
 * so consumers can read old + new saves uniformly.
 *
 * @param mixed $value saved sides value (array | 'top'|'bottom'|'both'|'left'|'right' | JSON).
 * @return array subset of array( 'top', 'right', 'bottom', 'left' ), in that order.
 */
function unysonplus_hf_normalize_sides( $value ) {
	$valid = array( 'top', 'right', 'bottom', 'left' );

	if ( is_string( $value ) ) {
		$trimmed = trim( $value );
		if ( $trimmed === '' ) {
			$value = array();
		} elseif ( $trimmed[0] === '[' ) {
			$decoded = json_decode( $trimmed, true );
			$value   = is_array( $decoded ) ? $decoded : array();
		} elseif ( $trimmed === 'both' ) {
			$value = array( 'top', 'bottom' );
		} else {
			$value = array( $trimmed );
		}
	}

	if ( ! is_array( $value ) ) {
		$value = array();
	}

	// Preserve canonical order, drop dupes/invalids.
	$out = array();
	foreach ( $valid as $side ) {
		if ( in_array( $side, $value, true ) ) {
			$out[] = $side;
		}
	}
	return $out;
}
endif;

if ( ! function_exists( 'unysonplus_hf_border_extent_field' ) ) :
/**
 * Inline multi-picker: how far the border runs horizontally — Full Width (edge to
 * edge, default), Container Width (aligned with the site content), or Custom (an exact
 * centered max-width via a revealed unit-input). Canonical inline multi-picker shape
 * (label/desc on the picker, top-level false, default in `value`, only Custom reveals
 * a sub-option). Shared by the footer + Custom Styling borders.
 *
 * @param string $custom_key the revealed unit-input's option id (unique in its scope).
 * @return array multi-picker option definition.
 */
function unysonplus_hf_border_extent_field( $custom_key ) {
	return array(
		'type'   => 'multi-picker',
		'label'  => false,
		'desc'   => false,
		'picker' => array(
			'mode' => array(
				'label'   => __( 'Border Extent', 'unysonplus' ),
				'desc'    => __( 'How far the border runs across the page. Full Width spans edge to edge; Container aligns it with the site content; Custom sets an exact centered width.', 'unysonplus' ),
				'type'    => 'select',
				'choices' => array(
					'full'      => __( 'Full Width', 'unysonplus' ),
					'container' => __( 'Container Width', 'unysonplus' ),
					'custom'    => __( 'Custom Width', 'unysonplus' ),
				),
			),
		),
		'value'   => array( 'mode' => 'full' ),
		'choices' => array(
			'custom' => array(
				$custom_key => array(
					'label' => __( 'Custom Border Width', 'unysonplus' ),
					'desc'  => __( 'Maximum width of the centered border line, e.g. 800px or 60%.', 'unysonplus' ),
					'type'  => 'unit-input',
					'units' => array( 'px', 'rem', 'em', '%' ),
					'value' => array( 'value' => '', 'unit' => 'px' ),
					'min'   => 0,
				),
			),
		),
		'show_borders' => false,
	);
}
endif;

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
	// Preset-linked colour control (tracks Theme Settings → Colors), house style.
	// Falls back to a plain picker if the shortcodes helper isn't loaded. Resolved
	// to CSS by inc/includes/hf-custom-css.php (which tolerates the legacy string).
	$cfield = function ( $label, $desc, $kind = 'text', $picker = 'color-picker' ) {
		if ( function_exists( 'sc_color_field_compact' ) ) {
			return sc_color_field_compact( array( 'label' => $label, 'desc' => $desc, 'kind' => $kind, 'picker' => $picker ) );
		}
		return array( 'label' => $label, 'desc' => $desc, 'type' => ( $picker === 'rgba-color-picker' ? 'rgba-color-picker' : 'color-picker' ), 'value' => '' );
	};

	// Container preview tiles — a mini viewport frame with the content bar drawn either
	// inset (Fixed Width) or edge-to-edge (Full Width), caption baked in (inline data-URI
	// SVG, matching the Social Icon Style / menu-style tiles).
	$container_svg = function ( $variant, $label ) {
		$accent = '#2271b1'; $line = '#c3c4c7';
		$frame  = '<rect x="1" y="2" width="102" height="42" rx="3" fill="none" stroke="' . $line . '" stroke-width="1.5"/>';
		$bar    = ( 'container' === $variant )
			? '<rect x="24" y="13" width="56" height="20" rx="2" fill="' . $accent . '"/>'
			: '<rect x="7" y="13" width="90" height="20" rx="2" fill="' . $accent . '"/>';
		$text   = '<text x="52" y="59" text-anchor="middle" font-family="-apple-system,Segoe UI,Roboto,sans-serif" font-size="10" fill="#50575e">' . $label . '</text>';
		$svg    = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 104 66" width="104" height="66">' . $frame . $bar . $text . '</svg>';
		return 'data:image/svg+xml,' . rawurlencode( $svg );
	};
	$container_choice = function ( $variant, $label ) use ( $container_svg ) {
		$uri = $container_svg( $variant, $label );
		return array(
			'small' => array( 'height' => 66, 'src' => $uri ),
			'large' => array( 'height' => 92, 'src' => $uri ),
		);
	};

	// This block styles the whole bar/section CONTAINER (its background band, width,
	// borders, padding and overall text). The navigation menu's own styling — link
	// colors, item hover/active treatments and dropdown panels — lives in a SEPARATE
	// tab (Header → Menu). In the header contexts we point users there so the two
	// aren't mistaken for duplicates (a footer has no such menu tab, so we don't).
	$is_header    = in_array( $prefix, array( 'main', 'topbar', 'bottombar' ), true );
	$menu_pointer = $is_header
		? __( ' For the navigation menu itself — its link colors, hover/active styles and dropdowns — use Header → Menu instead.', 'unysonplus' )
		: '';

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
				'desc'         => __( 'Enable to set a custom background, text, borders and spacing for this whole bar/section (overrides the global styling).', 'unysonplus' ) . $menu_pointer,
			),
		),
		'choices' => array(
			'yes' => array(
				// Custom Styling grouped into 4 borderless `group` containers (Layout · Background &
				// Text · Borders · Advanced). Group keys are container-only (not stored), so the
				// leaf field ids below are unchanged — saved values need no migration.
				$prefix . '_grp_layout' => array(
					'type'    => 'group',
					'options' => array(
						$prefix . '_container' => array(
							'label'   => __( 'Container', 'unysonplus' ),
							'desc'    => __( 'Fixed Width aligns the content with the site container; Full Width spans edge to edge (with a comfortable gutter).', 'unysonplus' ),
							'type'    => 'image-picker',
							'value'   => 'container',
							'choices' => array(
								'container'       => $container_choice( 'container',       __( 'Fixed Width', 'unysonplus' ) ),
								'container-fluid' => $container_choice( 'container-fluid', __( 'Full Width', 'unysonplus' ) ),
							),
						),
						$prefix . '_padding' => array(
							'type'  => 'spacing',
							'mode'  => 'padding',
							'label' => __( 'Padding', 'unysonplus' ),
							'desc'  => __( 'Inner spacing for this section (responsive). Applied as utility classes.', 'unysonplus' ),
						),
					),
				),
				$prefix . '_grp_appearance' => array(
					'type'    => 'group',
					'options' => array(
						$prefix . '_background' => array(
							'label'   => __( 'Background', 'unysonplus' ),
							'type'    => 'background-pro',
							'disable' => 'video',
							'desc'    => __( 'Background for this section — color, gradient and/or image, layered in one control. (Video is not available for section backgrounds.)', 'unysonplus' ),
						),
						$prefix . '_typography' => array(
							'label' => __( 'Text', 'unysonplus' ),
							'type'  => 'typography',
							'desc'  => __( 'Font family, size, weight, line-height, letter-spacing and color for this whole bar/section\'s text.', 'unysonplus' )
								. ( $is_header ? __( ' (The nav menu\'s own link typography is styled under Header → Menu.)', 'unysonplus' ) : '' ),
						),
						$prefix . '_link_color' => $cfield(
							__( 'Link Color', 'unysonplus' ),
							__( 'Colour of links in this section.', 'unysonplus' )
								. ( $is_header ? __( ' Affects every link in this bar (e.g. a text CTA or phone link); the nav menu has its own link colors under Header → Menu.', 'unysonplus' ) : '' ),
							'text'
						),
					),
				),
				$prefix . '_grp_borders' => array(
					'type'    => 'group',
					'options' => array(
						// ONE shared border (Width · Style · Color) applied to the edges chosen
						// below — mirrors the Footer Layout border. Users won't set different
						// widths per edge; a one-off can override with CSS. Shows only when both
						// a width and a color are set. Consumed by hf-custom-css.php (which also
						// tolerates the legacy per-side _border_top / _border_bottom rows).
						$prefix . '_border' => unysonplus_hf_border_row_field(
							__( 'Border', 'unysonplus' ),
							__( 'Width · style · colour — like the CSS shorthand 1px solid #000. Choose which edges below. Shows only when both a width and a colour are set.', 'unysonplus' )
						),
						$prefix . '_border_sides'  => unysonplus_hf_border_sides_field(),
						$prefix . '_border_extent' => unysonplus_hf_border_extent_field( $prefix . '_border_extent_width' ),
					),
				),
				$prefix . '_grp_advanced' => array(
					'type'    => 'group',
					'options' => array(
						$prefix . '_css_class' => array(
							'label' => __( 'Custom CSS Class', 'unysonplus' ),
							'type'  => 'text',
							'value' => '',
							'desc'  => __( 'Add custom CSS class(es) to this section.', 'unysonplus' ),
							'dynamic_content' => false,
						),
					),
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
