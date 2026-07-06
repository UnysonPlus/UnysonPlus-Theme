<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * This is for filter functions
 * add_filter() and add_action()
 */


function unysonplus_theme_support() {

        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support( 'post-thumbnails' );

        // Custom logo.
        $logo_width  = 120;
        $logo_height = 90;

        // If the retina setting is active, double the recommended width and height.
        if ( get_theme_mod( 'retina_logo', false ) ) {
                $logo_width  = floor( $logo_width * 2 );
                $logo_height = floor( $logo_height * 2 );
        }

        add_theme_support( 'custom-logo',
                array(
                        'height'      => $logo_height,
                        'width'       => $logo_width,
                        'flex-height' => true,
                        'flex-width'  => true,
                )
        );

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support( 'title-tag' );
        
        // WooCommerce support — declared only when WooCommerce is active so the
        // theme stays neutral without it. The full compatibility layer (content
        // wrappers, sidebar routing, shop styles) lives in inc/includes/woocommerce.php.
        if ( class_exists( 'WooCommerce' ) ) {
                add_theme_support( 'woocommerce' );
                add_theme_support( 'wc-product-gallery-zoom' );
                add_theme_support( 'wc-product-gallery-lightbox' );
                add_theme_support( 'wc-product-gallery-slider' );
        }

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
                'html5',
                array(
                        'search-form',
                        'comment-form',
                        'comment-list',
                        'gallery',
                        'caption',
                        'script',
                        'style',
                )
        );
        
        /*
         * Enable support for Post Formats.
         * See http://codex.wordpress.org/Post_Formats
         */
        add_theme_support( 'post-formats', array( 'aside', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video', 'audio' ) );

        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         */
        load_theme_textdomain( 'unysonplus', get_template_directory() . '/languages' );

        // Add support for full and wide align images.
        add_theme_support( 'align-wide' );

        // Add support for responsive embeds.
        add_theme_support( 'responsive-embeds' );


        // Add theme support for selective refresh for widgets.
        add_theme_support( 'customize-selective-refresh-widgets' );
        
        // This theme uses its own gallery styles.
        add_filter( 'use_default_gallery_style', '__return_false' );

}

add_action( 'after_setup_theme', 'unysonplus_theme_support' );



if( ! function_exists('unysonplus_upload_filter') ) :
        function unysonplus_upload_filter( $file ){
                        $file['name'] = ucwords(str_replace( '-', ' ', $file['name']));
                        return $file;
        }
        add_filter('wp_handle_upload_prefilter', 'unysonplus_upload_filter' );
endif;


if( ! function_exists('unysonplus_move_jquery_scripts') ) :
        /**
         * Move jQuery to the footer. 
         */
        function unysonplus_move_jquery_scripts() {
                        wp_scripts()->add_data( 'jquery', 'group', 1 );
                        wp_scripts()->add_data( 'jquery-core', 'group', 1 );
                        wp_scripts()->add_data( 'jquery-migrate', 'group', 1 );
        }
        add_action( 'wp_enqueue_scripts', 'unysonplus_move_jquery_scripts' );
endif;


if(!function_exists('unysonplus_layerslider_overrides')) :
        function unysonplus_layerslider_overrides() {
        /**
         * Register your custom function to override some LayerSlider data
         */
                        // Disable auto-updates
                        $GLOBALS['lsAutoUpdateBox'] = false;
        }
        add_action('layerslider_ready', 'unysonplus_layerslider_overrides');
endif;


if(!function_exists('_action_theme_process_google_fonts')) {
        /**
        * Embed Google Font 
        */
        function _action_theme_process_google_fonts()
        {
                if ( ! function_exists( 'fw_get_google_fonts' ) || ! function_exists( 'fw_get_db_settings_option' ) ) {
                        return;
                }
                $google_fonts = fw_get_google_fonts();
                if ( ! is_array( $google_fonts ) ) {
                        return;
                }

                // Collect Google-font families from the theme Typography settings
                // (body + h1–h6). The legacy header_menu / footer_widgets /
                // footer_menu / footer_copyright reads were removed — those option
                // ids no longer exist in this theme and only produced "array offset
                // on null" warnings on every settings save. Header / footer
                // per-section typography loads its own Google fonts via
                // inc/includes/hf-custom-css.php.
                // Effective heading + body (+ per-heading) families from the Typography
                // preset / pairing — so a chosen preset's Google fonts are loaded too.
                $include_from_google = array();
                $families = array();
                if ( function_exists( 'unysonplus_typography_config' ) ) {
                        $cfg = unysonplus_typography_config( fw_get_db_settings_option( 'typography', array() ) );
                        $families = isset( $cfg['google'] ) ? $cfg['google'] : array();
                }
                foreach ( $families as $family ) {
                        if ( $family !== '' && isset( $google_fonts[ $family ] ) ) {
                                $include_from_google[ $family ] = $google_fonts[ $family ];
                        }
                }

                $google_fonts_links = fw_theme_get_remote_fonts( $include_from_google );
                // Cache the <link> markup; printed in <head> by
                // _action_theme_print_google_fonts_link().
                update_option( 'fw_theme_google_fonts_link', $google_fonts_links );
        }
        add_action('fw_settings_form_saved', '_action_theme_process_google_fonts', 999, 2);
}

if (!function_exists('fw_theme_get_remote_fonts')) :
        /**
         * Get remote fonts
         * @param array $include_from_google
         */
        function fw_theme_get_remote_fonts($include_from_google) {
                if ( ! sizeof( $include_from_google ) ) {
                                return '';
                }

                $html = '<link href="https://fonts.googleapis.com/css?family=';

                foreach ( $include_from_google as $font => $styles ) {
                        $html .= str_replace( ' ', '+', $font );
                        if ( !empty($styles['variants']) ) {
                                $html .= ':' . implode( ',', $styles['variants'] );
                        }
                        $html .= '|';
                }

                $html = substr( $html, 0, -1 );
                $html .= '&display=swap" rel="stylesheet" type="text/css">';

                return $html;
        }
endif;


if (!function_exists('_action_theme_print_google_fonts_link')) :
        /**
         * Print google fonts link
         */
        function _action_theme_print_google_fonts_link() {
                $google_fonts_link = get_option('fw_theme_google_fonts_link', '');
                if($google_fonts_link != ''){
                                echo $google_fonts_link;
                }
        }
        add_action('wp_head', '_action_theme_print_google_fonts_link');
endif;


if ( ! function_exists( 'unysonplus_google_fonts_resource_hints' ) ) :
        /**
         * Speed up Google Fonts: open the connection to the font hosts early so the
         * browser doesn't pay DNS + TLS + TCP cost only after it discovers the
         * stylesheet (and again for the font files on gstatic). Only emitted when
         * the theme actually outputs a Google Fonts <link> — self-hosted Custom
         * Fonts are same-origin and need no hint. Uses the core wp_resource_hints
         * filter so WordPress dedupes and prints them in <head> before the link.
         *
         * @param array  $hints
         * @param string $relation_type
         * @return array
         */
        function unysonplus_google_fonts_resource_hints( $hints, $relation_type ) {
                if ( '' === (string) get_option( 'fw_theme_google_fonts_link', '' ) ) {
                        return $hints;
                }
                if ( 'preconnect' === $relation_type ) {
                        $hints[] = 'https://fonts.googleapis.com';
                        $hints[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
                }
                return $hints;
        }
        add_filter( 'wp_resource_hints', 'unysonplus_google_fonts_resource_hints', 10, 2 );
endif;

// Front end: typography tokens are compiled into the generated CSS file
// (inc/includes/hf-custom-css.php). Admin keeps the inline emit for the
// page-builder editor preview.
add_action( 'admin_head', 'unysonplus_emit_css_tokens', 1 );


if ( ! function_exists( 'unysonplus_theme_widgets_init' ) ) :
        /**
         * Register widget areas
         * @internal
         */
        function unysonplus_theme_widgets_init() {
                $beforeWidget = '<aside id="%1$s" class="widget %2$s pb-3 pb-md-0">';
                $afterWidget  = '</aside>';
                $beforeTitle  = '<div class="widget-title"><span>';
                $afterTitle   = '</span></div>';
                register_sidebar(array('name' => __( 'Right Sidebar Area', 'unysonplus' ), 'id' => 'sidebar-right', 'description' => '', 'before_widget' => $beforeWidget, 'after_widget'  => $afterWidget, 'before_title'  => $beforeTitle, 'after_title'   => $afterTitle, ) );
                register_sidebar(array('name' => __( 'Left Sidebar Area', 'unysonplus' ), 'id' => 'sidebar-left', 'description' => '', 'before_widget' => $beforeWidget, 'after_widget'  => $afterWidget, 'before_title'  => $beforeTitle, 'after_title'   => $afterTitle, ) );
                for ( $i = 1; $i <= 3; $i++ ) {
                        register_sidebar( array(
                                'name'          => sprintf( __( 'Header Widget Area %d', 'unysonplus' ), $i ),
                                'id'            => 'header-' . $i,
                                'before_widget' => $beforeWidget,
                                'after_widget'  => $afterWidget,
                                'before_title'  => $beforeTitle,
                                'after_title'   => $afterTitle,
                                'description'   => '',
                        ) );
                }
                for ( $i = 1; $i <= 5; $i++ ) {
                        register_sidebar( array(
                                'name'          => sprintf( __( 'Footer Column %d', 'unysonplus' ), $i ),
                                'id'            => 'footer-' . $i,
                                'before_widget' => $beforeWidget,
                                'after_widget'  => $afterWidget,
                                'before_title'  => $beforeTitle,
                                'after_title'   => $afterTitle,
                                'description'   => '',
                        ) );
                }
        }
        add_action( 'widgets_init', 'unysonplus_theme_widgets_init' );
endif;


if ( ! function_exists( 'unysonplus_add_body_class' ) ) :
/**
 * Add custom body classes based on post type, page builder, WooCommerce, and page options.
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function unysonplus_add_body_class( $classes ) {
    global $post;

    if ( isset( $post ) ) {
        // Add post-type and slug class
        $classes[] = $post->post_type . '-' . $post->post_name;

        // Add transparent header class if set in post options
        $page_header = fw_get_db_post_option( $post->ID, 'page_header' );
        if ( $page_header === 'transparent' ) {
            $classes[] = 'transparent';
        }

        // Add page-builder class if used
        if ( function_exists( 'fw_ext_page_builder_is_builder_post' ) 
             && fw_ext_page_builder_is_builder_post( $post->ID ) ) {
            $classes[] = 'unyson page-builder';
        }

        // Add body classes from page options
        if ( ! is_404() ) {
            $page_options = fw_get_db_post_option( $post->ID, 'page_options', array() );
            if ( ! empty( $page_options['body_class'] ) ) {
                $classes[] = sanitize_html_class( $page_options['body_class'] );
            }
        }
    }

    // Add WooCommerce classes if plugin is active
    if ( function_exists( 'unysonplus_is_woocommerce_activated' ) && unysonplus_is_woocommerce_activated() ) {
        $classes[] = 'woocommerce woocommerce-active';
    }

    return $classes;
}
add_filter( 'body_class', 'unysonplus_add_body_class' );
endif;


if(! function_exists('_filter_theme_post_classes') ) :
        /**
         * Extend the default WordPress post classes.
         *
         * Adds a post class to denote:
         * Non-password protected page with a post thumbnail.
         *
         * @param array $classes A list of existing post class values.
         *
         * @return array The filtered post class list.
         * @internal
         */
        function _filter_theme_post_classes( $classes ) {
                if ( ! post_password_required() && ! is_attachment() && has_post_thumbnail() ) {
                        $classes[] = 'has-post-thumbnail';
                }

                return $classes;
        }
        add_filter( 'post_class', '_filter_theme_post_classes' );
endif;


if(! function_exists('unysonplus_entry_title') ) :
        /**
         * Entry Title
         *
         * Wrapped by the `unysonplus_before_entry_title` and
         * `unysonplus_after_entry_title` action hooks so plugins / child
         * themes can inject markup around the heading without forking
         * a template.
         */
        function unysonplus_entry_title() {
                $page_header = fw_get_db_post_option( get_the_ID(), 'page_header' );
                $hide_page_title = fw_get_db_post_option( get_the_ID(), 'hide_page_title' );
                if( ! $hide_page_title && $page_header != 'transparent' ) :
                        /** Fires before the entry <h1>/<h2> title prints. */
                        do_action( 'unysonplus_before_entry_title' );

                        if ( is_category() ) {
                                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2><!-- .entry-header -->' );
                        }else{
                                the_title( '<h1 class="entry-title">', '</h1><!-- .entry-header -->' );
                        }

                        /** Fires after the entry <h1>/<h2> title prints. */
                        do_action( 'unysonplus_after_entry_title' );
                endif;
        }
        add_action('unysonplus_entry_header','unysonplus_entry_title');
endif;


if(! function_exists('unysonplus_breadcrumbs') ) :
        /**
         * Breadcrumbs
         */
        function unysonplus_breadcrumbs() {
                // Gated by Pages → Defaults → "Show Breadcrumbs on Pages" (default no).
                $enabled = ! function_exists( 'unysonplus_pages_get' )
                        || unysonplus_pages_get( 'pages_show_breadcrumbs', 'no' ) === 'yes';
                if( $enabled && !is_front_page() && function_exists('fw_ext_breadcrumbs') && is_page() ) {
                        fw_ext_breadcrumbs();
                }
        }
        add_action('unysonplus_entry_header','unysonplus_breadcrumbs');
endif;


if(! function_exists('unysonplus_excerpt_more') ) :
        /**
         * Add Read More
         */
        function unysonplus_excerpt_more($more) {
         global $post;
         return '<a class="read-more" href="'. get_permalink($post->ID) . '">Read More</a>';
        }
        add_filter('excerpt_more', 'unysonplus_excerpt_more');
endif;


if(! function_exists('unysonplus_get_the_archive_title') ) :
        /**
         * Remove “Category:”, “Tag:”, “Author:” from the_archive_title
         */
        function unysonplus_get_the_archive_title() {
         if ( is_category() ) {    
                        $title = single_cat_title( '', false );    
                } elseif ( is_tag() ) {    
                        $title = single_tag_title( '', false );    
                } elseif ( is_author() ) {    
                        $title = '<span class="vcard">' . get_the_author() . '</span>' ;    
                } elseif ( is_tax() ) { //for custom post types
                        $title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
                } elseif (is_post_type_archive()) {
                        $title = post_type_archive_title( '', false );
                } else {
                 $title = '';
         }
        return $title; 
        }
        add_filter('get_the_archive_title', 'unysonplus_get_the_archive_title');
endif;


/* Woocommerce */

/**
* Add a custom link to the end of a specific menu that uses the wp_nav_menu() function
*/
if ( !function_exists('unysonplus_add_wc_items_to_nav_menu') && unysonplus_is_woocommerce_activated() ) :
        function unysonplus_add_wc_items_to_nav_menu( $items, $args ) {
                if (is_user_logged_in() && $args->theme_location == 'primary-right') {
                        $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-account nav-item"><a href="'. get_permalink( get_option('woocommerce_myaccount_page_id') ) .'" class="nav-link">My Account</a></li>';
                }
                elseif (!is_user_logged_in() && $args->theme_location == 'primary-right') {
                        $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-login nav-item"><a href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '" class="nav-link">Sign in  /  Register</a></li>';
                }
                $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-cart nav-item d-sm-block d-md-none"><a href="' . get_permalink( wc_get_page_id( 'cart' ) ) . '" class="nav-link">Cart</a></li>';
                return $items;
        }
        add_filter( 'wp_nav_menu_items', 'unysonplus_add_wc_items_to_nav_menu', 10, 2 );
endif;


function unysonplus_wc_ajax_add_to_cart_js() {
    if (function_exists('is_product') && is_product()) {
        wp_enqueue_script('woocommerce-ajax-add-to-cart', plugin_dir_url(__FILE__) . 'assets/ajax-add-to-cart.js', array('jquery'), '', true);
    }
}
add_action('wp_enqueue_scripts', 'unysonplus_wc_ajax_add_to_cart_js', 99);


if(! function_exists('unysonplus_wc_bootstrap_form_field_args') ) :
        /**
         * Add Bootstrap form styling to WooCommerce fields
         *
         * @since  1.0
         * @refer  http://bit.ly/2zWFMiq
         */
        function unysonplus_wc_bootstrap_form_field_args ($args, $key, $value) { 

          $args['input_class'][] = 'form-control'; 
          return $args; 
        }
        add_filter('woocommerce_form_field_args','unysonplus_wc_bootstrap_form_field_args', 10, 3);
endif;


/**
 * Emoji removal moved to inc/includes/misc.php (Misc > Performance toggle).
 * Enable "Disable WordPress emojis" in the Miscellaneous tab to remove
 * the emoji detection script + styles.
 */


if (! function_exists('unysonplus_include_custom_option_types')) :
        /** @internal */
        function unysonplus_include_custom_option_types() {
                require_once dirname(__FILE__) . '/includes/option-types/fw-multi-inline/class-fw-option-type-fw-multi-inline.php';
                require_once dirname(__FILE__) . '/includes/option-types/preset-loader/class-fw-option-type-preset-loader.php';
        }
add_action('fw_option_types_init', 'unysonplus_include_custom_option_types');
endif;


if ( is_plugin_active( 'unyson/unyson.php' ) ) :
        // This will force deactivate Styling extension because I don't need it.
        add_action('admin_footer', '_action_theme_disable_fw_styling');
        function _action_theme_disable_fw_styling() {
                        if (fw()->extensions->manager->can_activate() && fw_ext('styling')) {
                                        fw()->extensions->manager->deactivate_extensions(array('styling' => array()));
                        }
        }
        // And this will hide the Styling extension activator in the Unyson dashboard
        if (defined('FW')):
                        /** @internal */
                        function _action_hide_extensions_from_the_list() {
                                        //global $current_screen; fw_print($current_screen); // debug

                                        if (fw_current_screen_match(array('only' => array('id' => 'toplevel_page_fw-extensions')))) {
                                                        echo '<style type="text/css"> #fw-ext-styling { display: none; } </style>';
                                        }
                        }
                        add_action('admin_print_scripts', '_action_hide_extensions_from_the_list');
        endif;
endif;



if(! function_exists('unysonplus_social_profiles')){
        /**
         * Social Profiles
         */
        function unysonplus_social_profiles() { ?>
                <?php if(empty($menu_atts['social_profiles'])): ?>
                <ul class="social-profiles nav navbar-nav ms-auto">
                <?php
                        $social_profiles = c_get_option('social_profiles');
                        for($i=0; $i < count($social_profiles); $i++): 
                                if(!empty($social_profiles[$i]['link'])):
                                        echo '<li><a href="'. $social_profiles[$i]['link'].'" target="_blank"><i class="fa '. $social_profiles[$i]['fa_code'].'" aria-hidden="true"></i>
                </a></li>';
                                endif;
                        endfor; 
                ?>
                </ul>
                <?php endif; 
        }
        //add_action('unysonplus_navbar_right','unysonplus_social_profiles');
}


/**
 * Add More Menu Items
 */
/*add_filter( 'wp_nav_menu_items', 'your_custom_menu_item', 10, 2 );
function your_custom_menu_item ( $items, $args ) {
    if ($args->theme_location == 'main') {
        $items .= '
              <li class="pull-right"><a href="./">Default <span class="sr-only">(current)</span></a></li>
              <li class="pull-right"><a href="../navbar-static-top/">Static top</a></li>
              <li class="pull-right"><a href="../navbar-fixed-top/">Fixed top</a></li>
            ';
    }
    return $items;
}*/

if(! function_exists('unysonplus_header_info')){
        function unysonplus_header_info() { ?>
        <?php $header_info = fw_get_db_settings_option('header_info'); 
                if ($header_info['content']):?>
                <div class="float-lg-end"><?php 
                        echo $header_info['content']; ?>
                </div><?php 
        endif;
        }
}


if(! function_exists('unysonplus_inline_info')) :
/**
 * Main Menu
 */
function unysonplus_inline_info() {
        $header_menu    = fw_get_db_settings_option('header_menu');
        if($header_menu['inline_info']['selected'] == 'text') {
                $inline_info_class = array();
                $inline_info_class[] = 'info float-end';
                $inline_info_class[] = $header_menu['inline_info']['text']['color'];
                echo '<div '. fw_attr_to_html(array('class' => join( ' ', $inline_info_class ))) .'>'.$header_menu['inline_info']['text']['content'].'</div>';
        }
}
//add_action('unysonplus_menu','unysonplus_inline_info');
endif;


if (! function_exists('unysonplus_post_thumbnail') ) :
/**
 * Display an optional post thumbnail. NOT USED... To be deleted soon
 */
function unysonplus_post_thumbnail() {
        if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
                return;
        }

        if ( is_singular() ) :
        ?>

        <div class="post-thumbnail">
                <?php the_post_thumbnail(); ?>
        </div><!-- .post-thumbnail -->

        <?php else : ?>

        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
                <?php
                        the_post_thumbnail( 'post-thumbnail', array( 'alt' => get_the_title() ) );
                ?>
        </a>

        <?php endif; // End is_singular()
}
endif;


/**
 * Legacy unysonplus_back_to_top() removed in v2.1.x.
 * Use Theme Settings → Miscellaneous → Scroll to Top to enable the
 * floating button. The inline footer-builder back_to_top element
 * (placed via the Unyson footer builder) also still works.
 */


if(!function_exists('unysonplus_theme_mods')) :
  /*
   * Theme mods
   */
  function unysonplus_theme_mods() {
    $theme_layout = fw_get_db_settings_option('theme_layout'); 
                if( isset($theme_layout['layout']['selected']) && ($theme_layout['layout']['selected'] == 'container')){
                        $box_class = esc_attr( unysonplus_fw_container_class( $theme_layout['layout']['selected'] ) );
                        $box_container_start = '<box '. fw_attr_to_html( array('class' => $box_class) ) .'>';
      set_theme_mod( 'box_container_start', $box_container_start );
                        set_theme_mod( 'box_container_end', '</box>' );
                }else{
            set_theme_mod( 'box_container_start', '' );
                        set_theme_mod( 'box_container_end', '' ); 
    }
  }
  add_action( 'wp', 'unysonplus_theme_mods' );
endif;


// Add Featured Images in Admin columns
function posts_columns($defaults)
{
    $defaults['post_thumbs'] = __('Thumbs');
    return $defaults;
}
add_filter('manage_posts_columns', 'posts_columns', 5);

function posts_custom_columns($column_name, $id)
{
    if ($column_name === 'post_thumbs')
    {
        echo the_post_thumbnail(array(
            80,
            60
        ));
    }
}
add_action('manage_posts_custom_column', 'posts_custom_columns', 5, 2);