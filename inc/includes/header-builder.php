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

                case 'icon_text':
                        unysonplus_render_icon_text( $settings );
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

                default:
                        // Addon-registered element types (see the unysonplus_hf_elements
                        // filter). Render via a per-type action, plus a generic catch-all.
                        do_action( 'unysonplus_render_hf_element_' . $type, $settings, $element, 'header' );
                        do_action( 'unysonplus_render_hf_element', $type, $settings, $element, 'header' );
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
        // Let addons inject / reorder elements at render time (optional). $align is
        // the zone: 'start' | 'center' | 'end' (= left / center / right).
        $elements = apply_filters( 'unysonplus_hf_column_elements', $elements, 'header', $align );

        if ( empty( $elements ) || ! is_array( $elements ) ) return;

        foreach ( $elements as $element ) {
                if ( empty( $element['element_type']['element'] ) ) continue;
                $type = $element['element_type']['element'];

                // Per-element CSS Class (addable-popup field): sanitize each token so a
                // user can safely target this element wrapper with custom CSS.
                $extra_class = '';
                if ( ! empty( $element['element_css_class'] ) ) {
                        $safe = array();
                        foreach ( preg_split( '/\s+/', trim( (string) $element['element_css_class'] ) ) as $cls ) {
                                $cls = sanitize_html_class( $cls );
                                if ( $cls !== '' ) { $safe[] = $cls; }
                        }
                        if ( $safe ) { $extra_class = ' ' . implode( ' ', $safe ); }
                }

                echo '<div class="header-element header-element--' . esc_attr( $type ) . esc_attr( unysonplus_element_visibility_classes( $element ) ) . esc_attr( $extra_class ) . '">';
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

        // Style + size ride the theme's button preset classes (btn btn-{preset}
        // btn-{size}) from Theme Settings > General > Buttons — no inline styles here.
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


if ( ! function_exists( 'unysonplus_render_icon_text' ) ) :
/**
 * Icon Text element — an icon + a line of text, optionally a smart link
 * (Website / Email → mailto: / Phone → tel:). Generalises the old Phone element,
 * so it also covers email, address, hours, website, etc.
 *
 * @param array $settings icon_text element settings.
 */
function unysonplus_render_icon_text( $settings ) {
        $icon = ! empty( $settings['icontext_icon']['icon-class'] ) ? $settings['icontext_icon']['icon-class'] : '';
        $text = isset( $settings['icontext_text'] ) ? trim( (string) $settings['icontext_text'] ) : '';
        if ( $text === '' && $icon === '' ) { return; }

        $type = ! empty( $settings['icontext_link_type'] ) ? $settings['icontext_link_type'] : 'none';
        $val  = isset( $settings['icontext_link'] ) ? trim( (string) $settings['icontext_link'] ) : '';
        // Email / Phone fall back to the visible text when no explicit target is set.
        if ( $val === '' && ( $type === 'email' || $type === 'phone' ) ) { $val = $text; }

        // Enqueue the icon's pack so a non-FA glyph renders (FA loads globally).
        if ( ! empty( $settings['icontext_icon'] ) && function_exists( 'fw' ) && isset( fw()->backend )
                && method_exists( fw()->backend, 'option_type' ) && isset( fw()->backend->option_type( 'icon-v2' )->packs_loader ) ) {
                fw()->backend->option_type( 'icon-v2' )->packs_loader->enqueue_pack_for_icon( $settings['icontext_icon'] );
        }

        $href = ''; $rel = '';
        switch ( $type ) {
                case 'url':
                        $href = esc_url( $val );
                        // External URL → new tab (mirrors the tag_list convention).
                        $host = wp_parse_url( $val, PHP_URL_HOST );
                        if ( $host && $host !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
                                $rel = ' target="_blank" rel="noopener noreferrer"';
                        }
                        break;
                case 'email':
                        $href = 'mailto:' . antispambot( $val );
                        break;
                case 'phone':
                        $href = 'tel:' . preg_replace( '/[^0-9+]/', '', $val );
                        break;
        }

        $inner  = $icon !== '' ? '<i class="' . esc_attr( $icon ) . '" aria-hidden="true"></i> ' : '';
        $inner .= '<span>' . esc_html( $text ) . '</span>';

        if ( $href !== '' ) {
                echo '<a class="header-icon-text" href="' . $href . '"' . $rel . '>' . $inner . '</a>'; // phpcs:ignore -- href pre-escaped per type
        } else {
                echo '<span class="header-icon-text">' . $inner . '</span>';
        }
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


if ( ! function_exists( 'unysonplus_social_brand_color' ) ) :
/**
 * Brand hex for a social network, matched by the profile's name (lowercased). Used
 * by the "Use Brand Colors" style option. Unknown networks return '' (fall back to
 * the configured Icon/Background colors).
 *
 * @param string $name
 * @return string hex or ''
 */
function unysonplus_social_brand_color( $name ) {
        $key = preg_replace( '/[^a-z0-9]/', '', strtolower( (string) $name ) );
        $map = array(
                'facebook' => '#1877f2', 'fb' => '#1877f2',
                'x' => '#000000', 'twitter' => '#1da1f2',
                'instagram' => '#e4405f', 'ig' => '#e4405f',
                'youtube' => '#ff0000', 'linkedin' => '#0a66c2',
                'tiktok' => '#000000', 'pinterest' => '#bd081c',
                'github' => '#181717', 'whatsapp' => '#25d366',
                'telegram' => '#0088cc', 'dribbble' => '#ea4c89',
                'behance' => '#1769ff', 'discord' => '#5865f2',
                'reddit' => '#ff4500', 'vimeo' => '#1ab7ea',
                'snapchat' => '#fffc00', 'twitch' => '#9146ff',
                'spotify' => '#1db954', 'medium' => '#000000',
                'threads' => '#000000', 'mastodon' => '#6364ff',
                'tumblr' => '#36465d', 'soundcloud' => '#ff5500',
                'email' => '#0d6efd', 'mail' => '#0d6efd',
        );
        return isset( $map[ $key ] ) ? $map[ $key ] : '';
}
endif;

if ( ! function_exists( 'unysonplus_social_fa_class' ) ) :
/**
 * Fallback Font Awesome (v6 brands) class for a social network, matched by the
 * profile name — used when a profile has no explicit icon set, so a profile named
 * "Facebook" / "X" / "Instagram" shows the right glyph out of the box (the theme
 * loads a FA6 kit on the front end). Unknown networks return ''.
 *
 * @param string $name
 * @return string e.g. 'fab fa-facebook-f', or ''
 */
function unysonplus_social_fa_class( $name ) {
        $key = preg_replace( '/[^a-z0-9]/', '', strtolower( (string) $name ) );
        $map = array(
                'facebook' => 'fab fa-facebook-f', 'fb' => 'fab fa-facebook-f',
                'x' => 'fab fa-x-twitter', 'twitter' => 'fab fa-twitter',
                'instagram' => 'fab fa-instagram', 'ig' => 'fab fa-instagram',
                'youtube' => 'fab fa-youtube', 'linkedin' => 'fab fa-linkedin-in',
                'tiktok' => 'fab fa-tiktok', 'pinterest' => 'fab fa-pinterest-p',
                'github' => 'fab fa-github', 'whatsapp' => 'fab fa-whatsapp',
                'telegram' => 'fab fa-telegram', 'dribbble' => 'fab fa-dribbble',
                'behance' => 'fab fa-behance', 'discord' => 'fab fa-discord',
                'reddit' => 'fab fa-reddit-alien', 'vimeo' => 'fab fa-vimeo-v',
                'snapchat' => 'fab fa-snapchat', 'twitch' => 'fab fa-twitch',
                'spotify' => 'fab fa-spotify', 'medium' => 'fab fa-medium',
                'threads' => 'fab fa-threads', 'mastodon' => 'fab fa-mastodon',
                'tumblr' => 'fab fa-tumblr', 'soundcloud' => 'fab fa-soundcloud',
                'email' => 'fas fa-envelope', 'mail' => 'fas fa-envelope',
        );
        return isset( $map[ $key ] ) ? $map[ $key ] : '';
}
endif;

if ( ! function_exists( 'unysonplus_render_social_icons' ) ) :
function unysonplus_render_social_icons() {
        if ( ! function_exists( 'fw_get_db_settings_option' ) ) return;
        $social_profiles = fw_get_db_settings_option( 'social_profiles' );
        if ( empty( $social_profiles ) || ! is_array( $social_profiles ) ) return;

        // Global icon style (Social tab → social_style). Stored flat (groups aren't stored).
        $style = fw_get_db_settings_option( 'social_style', array() );
        $style = is_array( $style ) ? $style : array();
        $shape = ! empty( $style['social_icon_style'] ) ? preg_replace( '/[^a-z-]/', '', $style['social_icon_style'] ) : 'bare';
        $fx    = ! empty( $style['social_icon_hover_fx'] ) ? preg_replace( '/[^a-z-]/', '', $style['social_icon_hover_fx'] ) : 'none';
        $brand = ! empty( $style['social_icon_brand'] ) && $style['social_icon_brand'] === 'yes';

        $wrap = array( 'header-social-icons', 'social-icons', 'social-style-' . $shape );
        if ( $fx !== 'none' ) { $wrap[] = 'social-fx-' . $fx; }
        if ( $brand )         { $wrap[] = 'social-brand'; }

        // icon-v2 packs loader (enqueues the pack CSS for a picked icon so it renders).
        $packs = ( function_exists( 'fw' ) && isset( fw()->backend ) && method_exists( fw()->backend, 'option_type' ) )
                ? ( isset( fw()->backend->option_type( 'icon-v2' )->packs_loader ) ? fw()->backend->option_type( 'icon-v2' )->packs_loader : null )
                : null;

        echo '<div class="' . esc_attr( implode( ' ', $wrap ) ) . '">';
        foreach ( $social_profiles as $profile ) {
                if ( empty( $profile['link'] ) ) continue;
                $name    = ! empty( $profile['name'] ) ? $profile['name'] : '';
                $new_tab = ! isset( $profile['new_tab'] ) || $profile['new_tab'] !== 'no';
                $target  = $new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';

                if ( $packs && ! empty( $profile['icon'] ) ) {
                        $packs->enqueue_pack_for_icon( $profile['icon'] );
                }

                $brand_attr = '';
                if ( $brand ) {
                        $hex = unysonplus_social_brand_color( $name );
                        if ( $hex !== '' ) { $brand_attr = ' style="--social-brand:' . esc_attr( $hex ) . '"'; }
                }

                // Icon: the picked one, else a name-matched Font Awesome fallback.
                $icon_class = ! empty( $profile['icon']['icon-class'] ) ? $profile['icon']['icon-class'] : unysonplus_social_fa_class( $name );

                echo '<a href="' . esc_url( $profile['link'] ) . '" class="header-social-icon"' . $target . $brand_attr . ' aria-label="' . esc_attr( $name ) . '" title="' . esc_attr( $name ) . '">';
                if ( $icon_class !== '' ) {
                        echo '<i class="' . esc_attr( $icon_class ) . '" aria-hidden="true"></i>';
                } else {
                        echo '<span class="header-social-icon__label">' . esc_html( $name ) . '</span>';
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
