<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

/**
 * Settings presets — the data + apply pipeline behind the `preset-loader` option
 * type (inc/includes/option-types/preset-loader).
 *
 * A "preset group" maps a Theme-Settings storage key (e.g. `header_menu`) to a set
 * of named presets. Each preset is a flat map of that group's LEAF option ids →
 * values in their exact saved shapes (compact color { predefined, custom },
 * unit-input { value, unit }, image-picker scalar, …). Applying a preset merges its
 * values over the group's current saved value and writes it back with
 * fw_set_db_settings_option(); the option-type JS then reloads the settings page so
 * every widget re-renders from the DB (see the AJAX handler below). This "apply →
 * save → reload" model is reliable across every option type — no fragile per-widget
 * JS value-setting.
 *
 * `allowed_keys` whitelists which leaf ids a preset (or an uploaded custom JSON) may
 * touch, so an upload can't pollute the option with foreign keys.
 *
 * Groups so far: Menu (`header_menu`), Top Bar (`header_topbar`), Typography
 * (`typography`), Header (`header_layout`), Pages (`general_pages`), Blog
 * (`blog_index`) and Card Design (`blog_card`). The registry + option type are
 * generic, so extending to another tab is just authoring one more group here —
 * no new code.
 */

if ( ! function_exists( 'unysonplus_settings_preset_groups' ) ) :
/**
 * The preset registry. Filterable so other areas (or a child theme) can register
 * their own groups: add_filter( 'unysonplus_settings_preset_groups', … ).
 *
 * @return array<string,array{label:string,allowed_keys:string[],presets:array}>
 */
function unysonplus_settings_preset_groups() {
	// Value-shape helpers keep the preset table readable.
	$c = function ( $predefined = '', $custom = '' ) {           // compact preset color
		return array( 'predefined' => $predefined, 'custom' => $custom );
	};
	$u = function ( $value = '', $unit = 'rem' ) {               // unit-input
		return array( 'value' => (string) $value, 'unit' => $unit );
	};

	// Header → Menu leaf ids (the whitelist + the full key set every preset defines,
	// so applying is deterministic rather than a partial merge).
	$menu_keys = array(
		'menu_item_style', 'menu_link_color', 'menu_link_hover_color', 'menu_item_bg',
		'menu_item_hover_bg', 'menu_link_padding_x', 'menu_link_padding_y',
		'menu_dropdown_style', 'menu_dropdown_bg', 'menu_dropdown_link',
		'menu_dropdown_link_hover', 'menu_dropdown_item_hover_bg',
		'menu_dropdown_width', 'menu_dropdown_radius',
	);

	$menu_presets = array(
		'classic' => array(
			'label' => __( 'Classic', 'unysonplus' ),
			'desc'  => __( 'Theme defaults — a clean bar with a soft-shadowed dropdown. Also resets the Menu tab.', 'unysonplus' ),
			'values' => array(
				'menu_item_style'             => 'none',
				'menu_link_color'             => $c(),
				'menu_link_hover_color'       => $c(),
				'menu_item_bg'                => $c(),
				'menu_item_hover_bg'          => $c(),
				'menu_link_padding_x'         => $u(),
				'menu_link_padding_y'         => $u(),
				'menu_dropdown_style'         => 'classic',
				'menu_dropdown_bg'            => $c(),
				'menu_dropdown_link'          => $c(),
				'menu_dropdown_link_hover'    => $c(),
				'menu_dropdown_item_hover_bg' => $c(),
				'menu_dropdown_width'         => $u( '', 'px' ),
				'menu_dropdown_radius'        => $u( '', 'px' ),
			),
		),
		'minimal' => array(
			'label' => __( 'Minimal', 'unysonplus' ),
			'desc'  => __( 'Understated: plain links, a primary-colored hover, and a flat borderless dropdown.', 'unysonplus' ),
			'values' => array(
				'menu_item_style'             => 'none',
				'menu_link_color'             => $c(),
				'menu_link_hover_color'       => $c( 'text-primary' ),
				'menu_item_bg'                => $c(),
				'menu_item_hover_bg'          => $c(),
				'menu_link_padding_x'         => $u( '0.75', 'rem' ),
				'menu_link_padding_y'         => $u( '0.4', 'rem' ),
				'menu_dropdown_style'         => 'minimal',
				'menu_dropdown_bg'            => $c(),
				'menu_dropdown_link'          => $c(),
				'menu_dropdown_link_hover'    => $c( 'text-primary' ),
				'menu_dropdown_item_hover_bg' => $c(),
				'menu_dropdown_width'         => $u( '200', 'px' ),
				'menu_dropdown_radius'        => $u( '6', 'px' ),
			),
		),
		'underline' => array(
			'label' => __( 'Underline', 'unysonplus' ),
			'desc'  => __( 'An animated underline on each item, paired with a top-accent dropdown.', 'unysonplus' ),
			'values' => array(
				'menu_item_style'             => 'underline-grow',
				'menu_link_color'             => $c(),
				'menu_link_hover_color'       => $c( 'text-primary' ),
				'menu_item_bg'                => $c(),
				'menu_item_hover_bg'          => $c(),
				'menu_link_padding_x'         => $u( '0.9', 'rem' ),
				'menu_link_padding_y'         => $u( '0.5', 'rem' ),
				'menu_dropdown_style'         => 'top-accent',
				'menu_dropdown_bg'            => $c(),
				'menu_dropdown_link'          => $c(),
				'menu_dropdown_link_hover'    => $c( 'text-primary' ),
				'menu_dropdown_item_hover_bg' => $c(),
				'menu_dropdown_width'         => $u( '220', 'px' ),
				'menu_dropdown_radius'        => $u( '8', 'px' ),
			),
		),
		'pill' => array(
			'label' => __( 'Pill Nav', 'unysonplus' ),
			'desc'  => __( 'Rounded pill items that fill with the primary color on hover, over an elevated dropdown.', 'unysonplus' ),
			'values' => array(
				'menu_item_style'             => 'pill',
				'menu_link_color'             => $c(),
				'menu_link_hover_color'       => $c( '', '#ffffff' ), // white text on the filled pill
				'menu_item_bg'                => $c(),
				'menu_item_hover_bg'          => $c( 'bg-primary' ),
				'menu_link_padding_x'         => $u( '1.1', 'rem' ),
				'menu_link_padding_y'         => $u( '0.55', 'rem' ),
				'menu_dropdown_style'         => 'elevated',
				'menu_dropdown_bg'            => $c(),
				'menu_dropdown_link'          => $c(),
				'menu_dropdown_link_hover'    => $c( 'text-primary' ),
				'menu_dropdown_item_hover_bg' => $c(),
				'menu_dropdown_width'         => $u( '240', 'px' ),
				'menu_dropdown_radius'        => $u( '12', 'px' ),
			),
		),
		'bold' => array(
			'label' => __( 'Bold', 'unysonplus' ),
			'desc'  => __( 'Solid box items in the primary color, over a bordered dropdown.', 'unysonplus' ),
			'values' => array(
				'menu_item_style'             => 'box',
				'menu_link_color'             => $c(),
				'menu_link_hover_color'       => $c( '', '#ffffff' ),
				'menu_item_bg'                => $c(),
				'menu_item_hover_bg'          => $c( 'bg-primary' ),
				'menu_link_padding_x'         => $u( '1', 'rem' ),
				'menu_link_padding_y'         => $u( '0.6', 'rem' ),
				'menu_dropdown_style'         => 'bordered',
				'menu_dropdown_bg'            => $c(),
				'menu_dropdown_link'          => $c(),
				'menu_dropdown_link_hover'    => $c( 'text-primary' ),
				'menu_dropdown_item_hover_bg' => $c(),
				'menu_dropdown_width'         => $u( '220', 'px' ),
				'menu_dropdown_radius'        => $u( '4', 'px' ),
			),
		),
	);

	// Typography → the `typography` group leaf ids. A preset fills the Heading Font +
	// Body + per-heading size scale so the existing css-tokens pipeline renders it.
	$typo_keys = array( 'heading_font', 'body', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'body_link', 'body_link_hover' );
	$typo_presets = array();
	if ( function_exists( 'unysonplus_typography_presets' ) ) {
		foreach ( unysonplus_typography_presets() as $slug => $p ) {
			$lh   = (string) $p['heading_lh'];
			$vals = array(
				'heading_font'    => array( 'family' => $p['heading'] ),
				'body'            => array( 'family' => $p['body'], 'variation' => 'regular', 'size' => 16, 'line-height' => 1.6, 'letter-spacing' => 0, 'color' => '' ),
				'body_link'       => '',
				'body_link_hover' => '',
			);
			foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $i => $t ) {
				// Font + size scale + line-height drive the look; weight/letter-spacing are
				// left at the theme default for robustness (tune per-heading in Advanced).
				$vals[ $t ] = array( 'family' => '', 'variation' => 'regular', 'size' => $p['sizes'][ $i ], 'line-height' => $lh, 'letter-spacing' => 0, 'color' => '' );
			}
			$typo_presets[ $slug ] = array(
				'label'  => $p['label'],
				'desc'   => ( $p['heading'] !== '' ? $p['heading'] : __( 'System', 'unysonplus' ) ) . ' + ' . ( $p['body'] !== '' ? $p['body'] : __( 'System', 'unysonplus' ) ),
				'values' => $vals,
			);
		}
	}

	// Header (Layout / chrome) → whole-header looks. One pick sets the layout mode +
	// design + behaviour + chrome toggles, showcasing the theme's layout modes. The
	// `header_mode` value is the full nested multi-picker shape (mode + its reveal).
	$hl_keys = array(
		'header_mode', 'container', 'header_behavior', 'header_border', 'header_shadow',
		'header_glass', 'header_uppercase_nav', 'bg_color', 'mobile_breakpoint',
	);
	$topd = function ( $design, $sub = array() ) {   // header_mode for a Top design
		return array( 'mode' => 'top', 'top' => array( 'header_design' => array_merge( array( 'design' => $design ), $sub ) ) );
	};
	$hl = function ( $mode, $behavior, $toggles = array(), $container = 'container' ) use ( $c ) {
		return array(
			'header_mode'          => $mode,
			'container'            => $container,
			'header_behavior'      => $behavior,
			'header_border'        => ! empty( $toggles['border'] )    ? 'yes' : 'no',
			'header_shadow'        => ! empty( $toggles['shadow'] )    ? 'yes' : 'no',
			'header_glass'         => ! empty( $toggles['glass'] )     ? 'yes' : 'no',
			'header_uppercase_nav' => ! empty( $toggles['uppercase'] ) ? 'yes' : 'no',
			'bg_color'             => $c(),
			'mobile_breakpoint'    => 'lg',
		);
	};
	$header_presets = array(
		'classic' => array(
			'label'  => __( 'Classic Bar', 'unysonplus' ),
			'desc'   => __( 'Clean full-width bar. Static. (Also resets the chrome.)', 'unysonplus' ),
			'values' => $hl( $topd( 'classic' ), 'static' ),
		),
		'sticky' => array(
			'label'  => __( 'Sticky Minimal', 'unysonplus' ),
			'desc'   => __( 'Slim bar that sticks on scroll, hairline border, uppercase nav.', 'unysonplus' ),
			'values' => $hl( $topd( 'classic' ), 'sticky', array( 'border' => 1, 'uppercase' => 1 ) ),
		),
		'pill' => array(
			'label'  => __( 'Floating Pill', 'unysonplus' ),
			'desc'   => __( 'Rounded floating nav, sticky with a soft shadow.', 'unysonplus' ),
			'values' => $hl( $topd( 'pill', array( 'pill' => array( 'pill_radius' => 'full', 'pill_inset' => 'none', 'pill_shadow' => 'medium' ) ) ), 'sticky', array( 'shadow' => 1 ) ),
		),
		'card' => array(
			'label'  => __( 'Elevated Card', 'unysonplus' ),
			'desc'   => __( 'Header floats as a raised card, sticky.', 'unysonplus' ),
			'values' => $hl( $topd( 'card', array( 'card' => array( 'card_radius' => 'medium', 'card_shadow' => 'medium' ) ) ), 'sticky', array( 'shadow' => 1 ) ),
		),
		'centered' => array(
			'label'  => __( 'Centered', 'unysonplus' ),
			'desc'   => __( 'Centered logo stacked above centered navigation.', 'unysonplus' ),
			'values' => $hl( $topd( 'centered', array( 'centered' => array( 'centered_gap' => 'normal' ) ) ), 'static' ),
		),
		'transparent' => array(
			'label'  => __( 'Transparent Hero', 'unysonplus' ),
			'desc'   => __( 'Frosted, transparent header that overlays the hero.', 'unysonplus' ),
			'values' => $hl( $topd( 'classic' ), 'transparent-overlay', array( 'glass' => 1 ), 'container-fluid' ),
		),
		'vertical' => array(
			'label'  => __( 'Vertical Rail', 'unysonplus' ),
			'desc'   => __( 'Fixed side rail with the logo + menu.', 'unysonplus' ),
			'values' => $hl( array( 'mode' => 'vertical', 'vertical' => array( 'vertical_side' => array( 'side' => 'left' ), 'vertical_width' => $u( '16.25', 'rem' ) ) ), 'static' ),
		),
		'overlay' => array(
			'label'  => __( 'Fullscreen Overlay', 'unysonplus' ),
			'desc'   => __( 'Hamburger opens a fullscreen Concentric menu.', 'unysonplus' ),
			'values' => $hl( array( 'mode' => 'overlay', 'overlay' => array( 'overlay_style' => array( 'style' => 'concentric', 'concentric' => array( 'overlay_corner' => 'tr' ) ), 'overlay_color_mode' => 'shade', 'overlay_bg_opacity' => 100 ) ), 'static' ),
		),
	);

	// Pages (site-wide page DEFAULTS) → whole-page-default looks. Uses the existing
	// `general_pages` keys, so applying one is fully wired through unysonplus_pages_get().
	$pages_keys = array(
		'default_page_layout', 'pages_show_breadcrumbs',
		'pages_show_featured_image', 'default_header_preset', 'default_footer_preset',
	);
	$pg = function ( $layout, $crumbs, $featured ) {
		return array(
			'default_page_layout'       => $layout,
			'pages_show_breadcrumbs'    => $crumbs ? 'yes' : 'no',
			'pages_show_featured_image' => $featured ? 'yes' : 'no',
			// Presets don't force a header/footer preset — leave those to the Header/Footer tabs.
			'default_header_preset'     => '',
			'default_footer_preset'     => '',
		);
	};
	$pages_presets = array(
		'standard'  => array( 'label' => __( 'Standard', 'unysonplus' ),          'desc' => __( 'No sidebar, featured image on.', 'unysonplus' ),                'values' => $pg( 'default', false, true ) ),
		'sidebar_r' => array( 'label' => __( 'Right Sidebar', 'unysonplus' ),      'desc' => __( 'Content + a right sidebar. Breadcrumbs on.', 'unysonplus' ),     'values' => $pg( 'sidebar-right', true, true ) ),
		'sidebar_l' => array( 'label' => __( 'Left Sidebar', 'unysonplus' ),       'desc' => __( 'Content + a left sidebar. Breadcrumbs on.', 'unysonplus' ),      'values' => $pg( 'sidebar-left', true, true ) ),
		'landing'   => array( 'label' => __( 'Full-Width Landing', 'unysonplus' ), 'desc' => __( 'No sidebar, no breadcrumbs, no featured image.', 'unysonplus' ), 'values' => $pg( 'full-width', false, false ) ),
		'docs'      => array( 'label' => __( 'Docs', 'unysonplus' ),               'desc' => __( 'Left sidebar for navigation, breadcrumbs on.', 'unysonplus' ),   'values' => $pg( 'sidebar-left', true, false ) ),
		'editorial' => array( 'label' => __( 'Boxed Editorial', 'unysonplus' ),    'desc' => __( 'Narrow boxed reading column, featured image on.', 'unysonplus' ), 'values' => $pg( 'boxed-narrow', false, true ) ),
	);

	// Top Bar (the row above the main header) → ready-made column content. A preset
	// fills the topbar_left/center/right columns with element items in the exact shape
	// the addable-popup stores: a list of { element_type: { element:'<type>', '<type>':
	// {…settings} } }. `$el()` builds one item; a minimal item (selected element only)
	// renders on the front end AND opens cleanly in the element modal (missing sibling
	// choices just fall back to their defaults). Applying replaces the three columns
	// only — topbar_custom_styling (the bar's look) is left to the user.
	$topbar_keys = array( 'topbar_left', 'topbar_center', 'topbar_right' );
	$el = function ( $type, $settings = null ) {
		$et = array( 'element' => $type );
		if ( is_array( $settings ) ) { $et[ $type ] = $settings; }
		return array( 'element_type' => $et );
	};
	$topbar_presets = array(
		'contact_social' => array(
			'label' => __( 'Contact & Social', 'unysonplus' ),
			'desc'  => __( 'Phone + email on the left, social icons on the right — the classic business top bar. Edit each element after applying; social icons use Theme Settings → Social.', 'unysonplus' ),
			'values' => array(
				'topbar_left' => array(
					$el( 'phone', array( 'phone_number' => '+1 (555) 123-4567' ) ),
					$el( 'text', array( 'text_content' => 'info@example.com' ) ),
				),
				'topbar_center' => array(),
				'topbar_right'  => array(
					$el( 'social_icons' ),
				),
			),
		),
	);

	// Blog → the whole posts-listing look. The group key IS the stored option
	// group (`blog_index`), so applying writes straight into Blog → Blog Index.
	// Every preset defines all listing keys for a deterministic apply.
	$blog_keys = array(
		'blog_layout', 'blog_columns', 'blog_card_style', 'blog_featured_image',
		'blog_image_ratio', 'blog_image_hover', 'blog_category_badge', 'blog_content',
		'blog_excerpt_length', 'blog_meta', 'blog_meta_position', 'blog_read_more',
		'blog_sticky_highlight', 'blog_first_featured', 'blog_pagination',
	);
	$bl = function ( $over = array() ) {
		return array_merge( array(
			'blog_layout'           => 'list',
			'blog_columns'          => '2',
			'blog_card_style'       => 'plain',
			'blog_featured_image'   => 'yes',
			'blog_image_ratio'      => '16-9',
			'blog_image_hover'      => 'zoom',
			'blog_category_badge'   => 'no',
			'blog_content'          => 'excerpt',
			'blog_excerpt_length'   => '30',
			'blog_meta'             => array( 'date' => true, 'author' => true, 'category' => true, 'comments' => false, 'reading_time' => false ),
			'blog_meta_position'    => 'below-title',
			'blog_read_more'        => __( 'Read more', 'unysonplus' ),
			'blog_sticky_highlight' => 'yes',
			'blog_first_featured'   => 'no',
			'blog_pagination'       => 'numbers',
		), $over );
	};
	$blog_presets = array(
		'classic_list' => array(
			'label' => __( 'Classic List', 'unysonplus' ),
			'desc'  => __( 'Stacked full-width posts with an excerpt — the timeless blog. Meta below the title, numbered pages.', 'unysonplus' ),
			'values' => $bl(),
		),
		'grid_cards' => array(
			'label' => __( 'Grid Cards', 'unysonplus' ),
			'desc'  => __( 'Three boxed cards per row with a zoom-on-hover image and a category badge. Load-More pagination.', 'unysonplus' ),
			'values' => $bl( array(
				'blog_layout'         => 'grid',
				'blog_columns'        => '3',
				'blog_card_style'     => 'boxed',
				'blog_image_ratio'    => '4-3',
				'blog_category_badge' => 'yes',
				'blog_meta'           => array( 'date' => true, 'author' => false, 'category' => false, 'comments' => false, 'reading_time' => true ),
				'blog_pagination'     => 'load_more',
			) ),
		),
		'magazine' => array(
			'label' => __( 'Magazine', 'unysonplus' ),
			'desc'  => __( 'A large featured first post over a two-column grid of bordered cards, category badges, meta above the title.', 'unysonplus' ),
			'values' => $bl( array(
				'blog_layout'         => 'grid',
				'blog_columns'        => '2',
				'blog_card_style'     => 'bordered',
				'blog_image_ratio'    => '16-9',
				'blog_category_badge' => 'yes',
				'blog_first_featured' => 'yes',
				'blog_meta_position'  => 'above-title',
				'blog_meta'           => array( 'date' => true, 'author' => true, 'category' => true, 'comments' => false, 'reading_time' => false ),
			) ),
		),
		'minimal' => array(
			'label' => __( 'Minimal', 'unysonplus' ),
			'desc'  => __( 'Text-first list: no images, short excerpts, just date + reading time. Quiet and fast.', 'unysonplus' ),
			'values' => $bl( array(
				'blog_layout'         => 'list',
				'blog_card_style'     => 'plain',
				'blog_featured_image' => 'no',
				'blog_image_hover'    => 'none',
				'blog_excerpt_length' => '18',
				'blog_meta'           => array( 'date' => true, 'author' => false, 'category' => false, 'comments' => false, 'reading_time' => true ),
				'blog_pagination'     => 'prev_next',
			) ),
		),
		'editorial' => array(
			'label' => __( 'Editorial', 'unysonplus' ),
			'desc'  => __( 'Big 16:9 imagery in a stacked list with generous excerpts — a long-form, story-led feel.', 'unysonplus' ),
			'values' => $bl( array(
				'blog_layout'         => 'list',
				'blog_card_style'     => 'plain',
				'blog_image_ratio'    => '16-9',
				'blog_image_hover'    => 'zoom',
				'blog_excerpt_length' => '45',
				'blog_meta_position'  => 'above-title',
				'blog_meta'           => array( 'date' => true, 'author' => true, 'category' => true, 'comments' => false, 'reading_time' => false ),
			) ),
		),
	);

	// Blog → Card Design. A focused visual layer (radius / shadow / padding /
	// hover accent) stored in its own `blog_card` group so it composes with the
	// whole-blog Blog presets rather than colliding with them.
	$card_keys = array( 'blog_card_radius', 'blog_card_shadow', 'blog_card_padding', 'blog_card_hover_accent' );
	$cd = function ( $radius, $shadow, $padding, $accent ) {
		return array(
			'blog_card_radius'       => $radius,
			'blog_card_shadow'       => $shadow,
			'blog_card_padding'      => $padding,
			'blog_card_hover_accent' => $accent ? 'yes' : 'no',
		);
	};
	$card_presets = array(
		'soft'     => array( 'label' => __( 'Soft', 'unysonplus' ),     'desc' => __( 'Large radius, medium shadow, roomy padding — friendly and modern.', 'unysonplus' ), 'values' => $cd( 'lg', 'md', 'roomy', false ) ),
		'sharp'    => array( 'label' => __( 'Sharp', 'unysonplus' ),    'desc' => __( 'Square corners, subtle shadow — crisp and editorial.', 'unysonplus' ),            'values' => $cd( 'none', 'sm', 'normal', false ) ),
		'floating' => array( 'label' => __( 'Floating', 'unysonplus' ), 'desc' => __( 'Extra-large radius and a deep shadow with a hover accent — cards lift off the page.', 'unysonplus' ), 'values' => $cd( 'xl', 'lg', 'normal', true ) ),
		'flat'     => array( 'label' => __( 'Flat', 'unysonplus' ),     'desc' => __( 'Medium radius, no shadow — quiet and lightweight.', 'unysonplus' ),                'values' => $cd( 'md', 'none', 'normal', false ) ),
		'framed'   => array( 'label' => __( 'Framed', 'unysonplus' ),   'desc' => __( 'Medium radius, no shadow, compact — pairs with the Bordered card style.', 'unysonplus' ), 'values' => $cd( 'md', 'none', 'compact', true ) ),
	);

	// Social → icon style. A preset sets the whole look (shape / size / gap / colors /
	// brand / hover) of the site-wide social icons (Social tab).
	$social_keys = array(
		'social_icon_style', 'social_icon_size', 'social_icon_gap', 'social_icon_brand',
		'social_icon_color', 'social_icon_bg', 'social_icon_hover_color', 'social_icon_hover_bg',
		'social_icon_hover_fx',
	);
	$social_presets = array(
		'minimal' => array(
			'label' => __( 'Minimal', 'unysonplus' ),
			'desc'  => __( 'Bare glyphs, a touch larger, that shift to the primary color on hover.', 'unysonplus' ),
			'values' => array(
				'social_icon_style' => 'bare', 'social_icon_size' => $u( '1.25', 'rem' ), 'social_icon_gap' => $u( '0.85', 'rem' ),
				'social_icon_brand' => 'no', 'social_icon_color' => $c(), 'social_icon_bg' => $c(),
				'social_icon_hover_color' => $c( 'text-primary' ), 'social_icon_hover_bg' => $c(), 'social_icon_hover_fx' => 'none',
			),
		),
		'circle' => array(
			'label' => __( 'Circle', 'unysonplus' ),
			'desc'  => __( 'Filled primary-color circles with white glyphs that lift on hover.', 'unysonplus' ),
			'values' => array(
				'social_icon_style' => 'circle', 'social_icon_size' => $u( '2.25', 'rem' ), 'social_icon_gap' => $u( '0.5', 'rem' ),
				'social_icon_brand' => 'no', 'social_icon_color' => $c( '', '#ffffff' ), 'social_icon_bg' => $c( 'bg-primary' ),
				'social_icon_hover_color' => $c( '', '#ffffff' ), 'social_icon_hover_bg' => $c(), 'social_icon_hover_fx' => 'lift',
			),
		),
		'outline' => array(
			'label' => __( 'Outline', 'unysonplus' ),
			'desc'  => __( 'Outlined circles in the primary color that fill in on hover.', 'unysonplus' ),
			'values' => array(
				'social_icon_style' => 'circle-outline', 'social_icon_size' => $u( '2.25', 'rem' ), 'social_icon_gap' => $u( '0.5', 'rem' ),
				'social_icon_brand' => 'no', 'social_icon_color' => $c( 'text-primary' ), 'social_icon_bg' => $c(),
				'social_icon_hover_color' => $c( '', '#ffffff' ), 'social_icon_hover_bg' => $c( 'bg-primary' ), 'social_icon_hover_fx' => 'fill',
			),
		),
		'square' => array(
			'label' => __( 'Square', 'unysonplus' ),
			'desc'  => __( 'Dark rounded squares with white glyphs; hover turns them primary.', 'unysonplus' ),
			'values' => array(
				'social_icon_style' => 'square', 'social_icon_size' => $u( '2.25', 'rem' ), 'social_icon_gap' => $u( '0.4', 'rem' ),
				'social_icon_brand' => 'no', 'social_icon_color' => $c( '', '#ffffff' ), 'social_icon_bg' => $c( '', '#222222' ),
				'social_icon_hover_color' => $c( '', '#ffffff' ), 'social_icon_hover_bg' => $c( 'bg-primary' ), 'social_icon_hover_fx' => 'none',
			),
		),
		'brand' => array(
			'label' => __( 'Brand Colors', 'unysonplus' ),
			'desc'  => __( 'Filled circles in each network\'s real brand color, lifting on hover.', 'unysonplus' ),
			'values' => array(
				'social_icon_style' => 'circle', 'social_icon_size' => $u( '2.25', 'rem' ), 'social_icon_gap' => $u( '0.5', 'rem' ),
				'social_icon_brand' => 'yes', 'social_icon_color' => $c(), 'social_icon_bg' => $c(),
				'social_icon_hover_color' => $c(), 'social_icon_hover_bg' => $c(), 'social_icon_hover_fx' => 'lift',
			),
		),
	);

	$groups = array(
		'header_menu' => array(
			'label'        => __( 'Menu', 'unysonplus' ),
			'allowed_keys' => $menu_keys,
			'presets'      => $menu_presets,
		),
		'social_style' => array(
			'label'        => __( 'Social', 'unysonplus' ),
			'allowed_keys' => $social_keys,
			'presets'      => $social_presets,
		),
		'header_topbar' => array(
			'label'        => __( 'Top Bar', 'unysonplus' ),
			'allowed_keys' => $topbar_keys,
			'presets'      => $topbar_presets,
		),
		'typography' => array(
			'label'        => __( 'Typography', 'unysonplus' ),
			'allowed_keys' => $typo_keys,
			'presets'      => $typo_presets,
		),
		'header_layout' => array(
			'label'        => __( 'Header', 'unysonplus' ),
			'allowed_keys' => $hl_keys,
			'presets'      => $header_presets,
		),
		'general_pages' => array(
			'label'        => __( 'Pages', 'unysonplus' ),
			'allowed_keys' => $pages_keys,
			'presets'      => $pages_presets,
		),
		'blog_index' => array(
			'label'        => __( 'Blog', 'unysonplus' ),
			'allowed_keys' => $blog_keys,
			'presets'      => $blog_presets,
		),
		'blog_card' => array(
			'label'        => __( 'Card Design', 'unysonplus' ),
			'allowed_keys' => $card_keys,
			'presets'      => $card_presets,
		),
	);

	return apply_filters( 'unysonplus_settings_preset_groups', $groups );
}
endif;

if ( ! function_exists( 'unysonplus_settings_preset_current_json' ) ) :
/**
 * Current saved values for a group, JSON-encoded — the payload the option type's
 * "Export" button downloads. Restricted to the group's allowed keys.
 *
 * @param string $group
 * @return string JSON object (‘{}’ when unavailable).
 */
function unysonplus_settings_preset_current_json( $group ) {
	if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return '{}'; }
	$groups = unysonplus_settings_preset_groups();
	if ( empty( $groups[ $group ] ) ) { return '{}'; }
	$allowed = isset( $groups[ $group ]['allowed_keys'] ) ? (array) $groups[ $group ]['allowed_keys'] : array();
	$current = fw_get_db_settings_option( $group, array() );
	if ( ! is_array( $current ) ) { $current = array(); }
	if ( $allowed ) { $current = array_intersect_key( $current, array_flip( $allowed ) ); }
	$json = wp_json_encode( $current );
	return is_string( $json ) ? $json : '{}';
}
endif;

if ( ! function_exists( 'unysonplus_ajax_apply_settings_preset' ) ) :
/**
 * AJAX: apply a preset (or an uploaded custom JSON) to a settings group, then save.
 * The option-type JS reloads the page on success so the widgets re-render.
 */
function unysonplus_ajax_apply_settings_preset() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'You are not allowed to do this.', 'unysonplus' ) ), 403 );
	}
	check_ajax_referer( 'unysonplus_settings_preset', 'nonce' );

	$group  = isset( $_POST['group'] ) ? sanitize_key( wp_unslash( $_POST['group'] ) ) : '';
	$groups = unysonplus_settings_preset_groups();
	if ( $group === '' || empty( $groups[ $group ] ) ) {
		wp_send_json_error( array( 'message' => __( 'Unknown preset group.', 'unysonplus' ) ) );
	}
	$conf    = $groups[ $group ];
	$allowed = isset( $conf['allowed_keys'] ) ? (array) $conf['allowed_keys'] : array();

	// Resolve the values to apply: a named preset (trusted registry) or uploaded JSON.
	$values = null;
	if ( ! empty( $_POST['preset'] ) ) {
		$key = sanitize_key( wp_unslash( $_POST['preset'] ) );
		if ( empty( $conf['presets'][ $key ]['values'] ) || ! is_array( $conf['presets'][ $key ]['values'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Unknown preset.', 'unysonplus' ) ) );
		}
		$values = $conf['presets'][ $key ]['values'];
	} elseif ( isset( $_POST['custom'] ) ) {
		$decoded = json_decode( (string) wp_unslash( $_POST['custom'] ), true );
		if ( ! is_array( $decoded ) ) {
			wp_send_json_error( array( 'message' => __( 'The uploaded file is not valid preset JSON.', 'unysonplus' ) ) );
		}
		$values = $decoded;
	} else {
		wp_send_json_error( array( 'message' => __( 'Nothing to apply.', 'unysonplus' ) ) );
	}

	// Whitelist to the group's known keys (drops any foreign keys from an upload).
	if ( $allowed ) {
		$values = array_intersect_key( $values, array_flip( $allowed ) );
	}
	if ( empty( $values ) ) {
		wp_send_json_error( array( 'message' => __( 'No applicable settings in that preset.', 'unysonplus' ) ) );
	}

	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		wp_send_json_error( array( 'message' => __( 'The framework is unavailable.', 'unysonplus' ) ) );
	}

	$current = fw_get_db_settings_option( $group, array() );
	if ( ! is_array( $current ) ) { $current = array(); }
	fw_set_db_settings_option( $group, array_merge( $current, $values ) );

	// Refresh the cached front-end CSS so the change shows without a manual Save;
	// typography presets also need the Google-fonts <link> rebuilt for the new
	// families. (The form Save path does both too — this just makes Apply instant.)
	if ( function_exists( 'unysonplus_hf_regenerate_css' ) ) { unysonplus_hf_regenerate_css(); }
	if ( $group === 'typography' && function_exists( '_action_theme_process_google_fonts' ) ) {
		_action_theme_process_google_fonts();
	}
	do_action( 'unysonplus_settings_preset_applied', $group, $values );

	wp_send_json_success( array( 'applied' => true ) );
}
add_action( 'wp_ajax_unysonplus_apply_settings_preset', 'unysonplus_ajax_apply_settings_preset' );
endif;
