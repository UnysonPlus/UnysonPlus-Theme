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

/**
 * Menu item sub-labels from the Description field.
 *
 * If a header menu item has a Description set (Appearance → Menus → item →
 * Description; enable the field via Screen Options), render it as a small
 * sub-label beneath the link text — e.g. "Home" / "Start Here". Only affects
 * top-level items of the header nav locations, and only when a description is
 * actually set, so existing menus are unchanged.
 *
 * Locations are filterable via `unysonplus_menu_sublabel_locations`.
 */
if ( ! function_exists( 'unysonplus_nav_menu_item_sublabel' ) ) :
function unysonplus_nav_menu_item_sublabel( $title, $item, $args, $depth ) {
	$location  = ( is_object( $args ) && isset( $args->theme_location ) ) ? $args->theme_location : '';
	$locations = apply_filters( 'unysonplus_menu_sublabel_locations', array( 'primary', 'secondary' ) );

	if ( 0 === (int) $depth
		&& in_array( $location, $locations, true )
		&& isset( $item->description )
		&& '' !== trim( (string) $item->description )
	) {
		$title = '<span class="menu-label">' . $title . '</span>'
			. '<small class="menu-sublabel">' . esc_html( wp_specialchars_decode( (string) $item->description ) ) . '</small>';
	}

	return $title;
}
add_filter( 'nav_menu_item_title', 'unysonplus_nav_menu_item_sublabel', 10, 4 );
endif;

/**
 * Per-item menu icons.
 *
 * Adds an "Icon" picker to every nav menu item (Appearance → Menus → expand an
 * item) using the framework's `icon-v2` option type — the same control the
 * MegaMenu extension uses. That means icon fonts, emoji, inline SVG *and custom
 * image upload*, not a hand-typed class.
 *
 * Why a field and not markup in the label: a menu label is TEXT. Pasting
 * `<span class="…">` into it leaks raw HTML into every context that escapes or
 * strips titles (feeds, breadcrumbs, admin lists, <title>, share cards).
 *
 * MEGAMENU: the MegaMenu extension already ships a per-item icon-v2 picker
 * (FW_Extension_MegaMenu::get_icon_option(), stored in `_fw_ext_mega_menu`) and
 * renders it through its own walker. When that extension is ACTIVE it owns the
 * icon completely — this field hides itself and stops rendering, so you never
 * get two pickers or two icons on one item. This theme field is the fallback
 * that keeps menu icons working when MegaMenu is switched off.
 *
 * Stored per item in `_unysonplus_menu_icon` as an icon-v2 value.
 */
if ( ! function_exists( 'unysonplus_menu_icon_meta_key' ) ) :
function unysonplus_menu_icon_meta_key() {
	return '_unysonplus_menu_icon';
}
endif;

if ( ! function_exists( 'unysonplus_menu_icon_megamenu_owns_it' ) ) :
/**
 * Is the MegaMenu extension active (and therefore the owner of item icons)?
 *
 * @return bool
 */
function unysonplus_menu_icon_megamenu_owns_it() {
	return function_exists( 'fw' ) && (bool) fw()->extensions->get( 'megamenu' );
}
endif;

if ( ! function_exists( 'unysonplus_menu_icon_option' ) ) :
/**
 * The icon option definition (mirrors MegaMenu's, so both agree on the type).
 *
 * @return array
 */
function unysonplus_menu_icon_option() {
	return apply_filters( 'unysonplus_menu_icon_option', array(
		'type'  => 'icon-v2',
		'label' => __( 'Icon', 'unysonplus' ),
		'desc'  => __( 'Shown before the menu label. Pick an icon font, emoji, SVG, or upload an image.', 'unysonplus' ),
	) );
}
endif;

if ( ! function_exists( 'unysonplus_menu_icon_admin_assets' ) ) :
/**
 * Bootstrap the icon picker on Appearance → Menus.
 *
 * The picker is an FW option type and this is a CORE WP screen, so two things
 * that normally happen for free have to be done by hand:
 *
 *   1. Enqueue the options RUNTIME, via enqueue_options_static(). It is not enough
 *      to enqueue the icon type's own statics: the picker is an fw.OptionsModal,
 *      and the modal shell (fw / fw.Modal / fw.OptionsModal + its CSS) lives in
 *      the `fw-backend-options` handles. enqueue_options_static() is the canonical
 *      entry point — it runs register_static(), wp_enqueue_media() and enqueues
 *      `fw-backend-options` *and* every option type's statics. Enqueueing only the
 *      icon statics gives you a working button with an unstyled modal.
 *   2. Fire `fw:options:init`. FW option types do all their wiring inside that
 *      event (render-icon-previews.js paints the preview there; the legacy
 *      'icon' type binds its clicks there). Nothing on nav-menus.php fires it,
 *      which is why the picker previously rendered dead. Firing it is exactly what
 *      FW's own containers do (addable-box: `fwEvents.trigger('fw:options:init',
 *      { $elements: $clone })`).
 *
 * @param string $hook
 */
function unysonplus_menu_icon_admin_assets( $hook ) {
	if ( 'nav-menus.php' !== $hook || unysonplus_menu_icon_megamenu_owns_it() || ! function_exists( 'fw' ) ) {
		return;
	}

	// Registers the handles + enqueues the icon type's statics (same call MegaMenu makes).
	fw()->backend->enqueue_options_static( array( 'icon' => unysonplus_menu_icon_option() ) );

	// ...then ask for the modal runtime EXPLICITLY. enqueue_options_static() only
	// enqueues `fw-backend-options` behind a one-shot `static $static_enqueue`, so
	// if anything called it earlier in the request that flag is already spent and
	// our call silently skips the runtime — which is exactly the "modal opens but
	// is unstyled" symptom. wp_enqueue_*() is idempotent, so asking again is free.
	wp_enqueue_media();
	wp_enqueue_style( 'fw-backend-options' );
	wp_enqueue_script( 'fw-backend-options' );
}
add_action( 'admin_enqueue_scripts', 'unysonplus_menu_icon_admin_assets' );
endif;

if ( ! function_exists( 'unysonplus_menu_icon_admin_init_js' ) ) :
/**
 * Fire fw:options:init for the menu panels (see above). Printed in the footer so
 * every FW script is already on the page, and re-fired when WP injects a new item
 * via ajax — the option types guard with :not(.initialized), so it's idempotent.
 */
function unysonplus_menu_icon_admin_init_js() {
	if ( unysonplus_menu_icon_megamenu_owns_it() || ! function_exists( 'fw' ) ) {
		return;
	}
	?>
	<script>
	jQuery(function ($) {
		if (typeof fwEvents === 'undefined') { return; }

		function upwInitMenuIcons($scope) {
			if ($scope && $scope.length) {
				fwEvents.trigger('fw:options:init', { $elements: $scope });
			}
		}

		upwInitMenuIcons($('#menu-to-edit'));

		// A newly added item arrives after the initial init.
		$(document).on('menu-item-added', function (e, $item) {
			upwInitMenuIcons($item && $item.length ? $item : $('#menu-to-edit'));
		});
	});
	</script>
	<?php
}
add_action( 'admin_footer-nav-menus.php', 'unysonplus_menu_icon_admin_init_js' );
endif;

if ( ! function_exists( 'unysonplus_nav_menu_item_icon_field' ) ) :
/**
 * Render the Icon field inside the nav-menu item panel.
 *
 * @param int    $item_id
 * @param object $item
 * @param int    $depth
 * @param array  $args
 */
function unysonplus_nav_menu_item_icon_field( $item_id, $item, $depth, $args ) {
	// MegaMenu ships its own icon-v2 picker for menu items — let it own them.
	if ( unysonplus_menu_icon_megamenu_owns_it() ) {
		return;
	}

	$raw = get_post_meta( $item_id, unysonplus_menu_icon_meta_key(), true );

	// Preferred: the framework's real picker (icon fonts / emoji / SVG / upload).
	if ( function_exists( 'fw' ) ) {
		echo '<div class="field-unysonplus-menu-icon description description-wide">';
		echo fw()->backend->render_option( // phpcs:ignore WordPress.Security.EscapeOutput — option types return escaped markup
			'icon',
			unysonplus_menu_icon_option(),
			array(
				'value'       => $raw,
				'id_prefix'   => 'unysonplus-menu-icon-' . (int) $item_id . '-',
				'name_prefix' => 'unysonplus-menu-icon[' . (int) $item_id . ']',
			)
		);
		echo '</div>';
		return;
	}

	// Fallback: plugin inactive → no option types available, so a plain class field.
	$value = unysonplus_menu_icon_to_class( $raw );
	?>
	<p class="field-unysonplus-menu-icon description description-wide">
		<label for="unysonplus-menu-icon-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'Icon (CSS class)', 'unysonplus' ); ?><br>
			<input type="text"
			       id="unysonplus-menu-icon-<?php echo esc_attr( $item_id ); ?>"
			       class="widefat code"
			       name="unysonplus-menu-icon[<?php echo esc_attr( $item_id ); ?>]"
			       value="<?php echo esc_attr( $value ); ?>"
			       placeholder="bi bi-dice-5" />
		</label>
	</p>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'unysonplus_nav_menu_item_icon_field', 10, 4 );
endif;

if ( ! function_exists( 'unysonplus_menu_icon_to_class' ) ) :
/**
 * Reduce a stored icon value to an editable class string for the simple field.
 * Non-font icons (emoji / svg / upload, set via MegaMenu) have no class — return
 * '' so the field stays blank rather than printing an array.
 *
 * @param mixed $value
 * @return string
 */
function unysonplus_menu_icon_to_class( $value ) {
	if ( is_string( $value ) ) {
		return trim( $value );
	}
	if ( is_array( $value ) && isset( $value['type'] ) && 'icon-font' === $value['type'] ) {
		return isset( $value['icon-class'] ) ? trim( (string) $value['icon-class'] ) : '';
	}
	return '';
}
endif;

if ( ! function_exists( 'unysonplus_nav_menu_item_icon_save' ) ) :
/**
 * Persist the Icon field.
 *
 * @param int $menu_id
 * @param int $menu_item_db_id
 */
function unysonplus_nav_menu_item_icon_save( $menu_id, $menu_item_db_id ) {
	if ( unysonplus_menu_icon_megamenu_owns_it() || ! function_exists( 'fw' ) ) {
		return; // MegaMenu saves its own icon into _fw_ext_mega_menu.
	}

	$key = unysonplus_menu_icon_meta_key();

	// Nav-menu saves are already nonce-checked by wp-admin/nav-menus.php.
	if ( ! isset( $_POST['unysonplus-menu-icon'][ $menu_item_db_id ] ) ) {
		return; // panel not submitted (e.g. a partial save) — don't wipe a stored icon
	}

	$raw = wp_unslash( $_POST['unysonplus-menu-icon'][ $menu_item_db_id ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

	// Picker posted a structured value → let the option type sanitize it; it's the
	// only thing that understands every shape (icon-font / emoji / svg / upload).
	if ( is_array( $raw ) && function_exists( 'fw' ) ) {
		$parsed = fw_get_options_values_from_input( array( 'icon' => unysonplus_menu_icon_option() ), $raw );
		$value  = isset( $parsed['icon'] ) ? $parsed['icon'] : null;
		$is_set = function_exists( 'fw_ext_mega_menu_icon_is_set' )
			? fw_ext_mega_menu_icon_is_set( $value )
			: ! empty( $value );

		if ( $is_set ) {
			update_post_meta( $menu_item_db_id, $key, $value );
		} else {
			delete_post_meta( $menu_item_db_id, $key );
		}
		return;
	}

	// Fallback field: a class list only — never markup.
	$class = trim( preg_replace( '/[^A-Za-z0-9_\- ]/', '', (string) $raw ) );

	if ( '' === $class ) {
		delete_post_meta( $menu_item_db_id, $key );
		return;
	}

	// Always store the icon-v2 shape, so MegaMenu's picker reads and edits the very
	// same value the moment that extension is switched on.
	update_post_meta( $menu_item_db_id, $key, array(
		'type'       => 'icon-font',
		'icon-class' => $class,
	) );
}
add_action( 'wp_update_nav_menu_item', 'unysonplus_nav_menu_item_icon_save', 10, 2 );
endif;

if ( ! function_exists( 'unysonplus_nav_menu_item_icon' ) ) :
/**
 * Prepend the icon to the item label.
 *
 * @param string $title
 * @param object $item
 * @param array  $args
 * @param int    $depth
 * @return string
 */
function unysonplus_nav_menu_item_icon( $title, $item, $args, $depth ) {
	// MegaMenu renders its own icon through its walker — bail so an item never
	// ends up with two.
	if ( unysonplus_menu_icon_megamenu_owns_it() || ! is_object( $item ) || empty( $item->ID ) ) {
		return $title;
	}

	$icon = get_post_meta( $item->ID, unysonplus_menu_icon_meta_key(), true );

	$is_set = function_exists( 'fw_ext_mega_menu_icon_is_set' )
		? fw_ext_mega_menu_icon_is_set( $icon )
		: ! empty( $icon );

	if ( ! $is_set ) {
		return $title;
	}

	// sc_icon_render() understands every icon-v2 shape (font / emoji / svg /
	// uploaded image) AND a legacy class string, so pre-icon-v2 values still work.
	if ( function_exists( 'sc_icon_render' ) ) {
		return sc_icon_render( $icon, array( 'class' => 'menu-icon' ) ) . $title;
	}

	// Fallback: plain class string, no shortcodes extension loaded.
	if ( is_string( $icon ) && '' !== trim( $icon ) ) {
		$cls = trim( $icon );
		if ( false === strpos( $cls, ' ' ) && 0 === strpos( $cls, 'bi-' ) ) {
			$cls = 'bi ' . $cls;
		}
		return '<i class="menu-icon ' . esc_attr( $cls ) . '" aria-hidden="true"></i>' . $title;
	}

	return $title;
}
// Priority 9 — BEFORE the sub-label filter (10), so the icon ends up inside the
// .menu-label span: inline with the text, with the sub-label beneath it. Running
// after 10 would make the icon a sibling of the label/sub-label stack and the
// flex-column would push it onto its own line.
add_filter( 'nav_menu_item_title', 'unysonplus_nav_menu_item_icon', 9, 4 );
endif;
