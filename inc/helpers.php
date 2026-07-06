<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * This is for Helper functions and classes
 */

if ( ! function_exists( 'unysonplus_attr_to_html' ) ) :
	/**
	 * Render an associative array of HTML attributes to a string.
	 *
	 * Wraps the framework's fw_attr_to_html() when available, with a native
	 * fallback so front-end templates never fatal if the Unyson+ plugin is
	 * inactive (the plugin is required + TGM-enforced, but this keeps the theme
	 * resilient during updates / misconfiguration).
	 *
	 * @param array $attr name => value (true = boolean attr; false/null = skip).
	 * @return string e.g. `class="x" id="y"` (no leading space).
	 */
	function unysonplus_attr_to_html( $attr ) {
		if ( function_exists( 'fw_attr_to_html' ) ) {
			return fw_attr_to_html( $attr );
		}
		if ( ! is_array( $attr ) ) {
			return '';
		}
		$html = '';
		foreach ( $attr as $name => $value ) {
			if ( $value === false || $value === null ) {
				continue;
			}
			if ( $value === true ) {
				$html .= ' ' . esc_attr( $name );
				continue;
			}
			$html .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
		}
		return ltrim( $html );
	}
endif;

if(!function_exists('unysonplus_container_start')) :
        function unysonplus_container_start() {

        if ( function_exists('fw_ext_page_builder_is_builder_post') 
                && !fw_ext_page_builder_is_builder_post(get_the_ID()) ) {

                echo '<div class="fw-container"><div class="fw-row">';
        }

        }
endif;

if(!function_exists('unysonplus_container_end')) :
        function unysonplus_container_end() {

        if ( function_exists('fw_ext_page_builder_is_builder_post') 
                && !fw_ext_page_builder_is_builder_post(get_the_ID()) ) {

                echo '</div></div>';
        }

        }
endif;


if(! function_exists('unysonplus_logo')) :
        /**
         * The Logo
         */
        function unysonplus_logo() {                     
                $header_logo = fw_get_db_settings_option('header_logo');
                if ( ! isset( $header_logo ) ) $header_logo = array();
                $has_unyson_image = ! empty( $header_logo['image'] ) && ! empty( $header_logo['image']['url'] );

                // Alt text for the image logo: explicit override, then Site Title, then WP name.
                $unysonplus_logo_alt = ! empty( $header_logo['alt'] )
                        ? $header_logo['alt']
                        : ( ! empty( $header_logo['site_title'] ) ? $header_logo['site_title'] : get_bloginfo( 'name' ) );

                if ( $has_unyson_image ) {
                        $img_attr = array();
                        $logo_w  = isset( $header_logo['width'] ) ? $header_logo['width'] : '';
                        // Only an exact pixel width can drive a raster resize; rem/em/empty
                        // serve the original and let CSS (--logo-width) scale it.
                        $logo_px = ( is_array( $logo_w ) && isset( $logo_w['unit'], $logo_w['value'] ) && 'px' === $logo_w['unit'] && is_numeric( $logo_w['value'] ) ) ? (int) $logo_w['value'] : 0;
                        if( $logo_px > 0 ) {
                                $img_attr['src']                = fw_resize( $header_logo['image']['url'], $logo_px, 0, false );
                                $img_attr['width']      = $logo_px;
                                // intrinsic height omitted here; display height handled by CSS (height:auto)
                        }else{
                                $img_attr['src']                = $header_logo['image']['url'];
                                $meta = wp_prepare_attachment_for_js($header_logo['image']['attachment_id']);
                                $img_attr['width']      = $meta['width'];  
                                $img_attr['height'] = $meta['height'];
                        }
                        // Retina / 2x: serve a high-DPI source via srcset when provided.
                        if ( ! empty( $header_logo['image_2x']['url'] ) ) {
                                $img_attr['srcset'] = $img_attr['src'] . ' 1x, ' . $header_logo['image_2x']['url'] . ' 2x';
                        }
                        $img_attr['alt']   = $unysonplus_logo_alt;
                        $img_attr['class'] = 'site-logo site-logo--default img-fluid';

                        $logo = fw_html_tag( 'img', $img_attr );

                        // Optional sticky-header and mobile logo variants. CSS decides
                        // which one is visible (see .site-logo--sticky / --mobile in
                        // style.css); the wrapper gets has-sticky-logo / has-mobile-logo.
                        if ( ! empty( $header_logo['sticky_image']['url'] ) ) {
                                $logo .= fw_html_tag( 'img', array(
                                        'src'   => $header_logo['sticky_image']['url'],
                                        'alt'   => $unysonplus_logo_alt,
                                        'class' => 'site-logo site-logo--sticky img-fluid',
                                ) );
                        }
                        if ( ! empty( $header_logo['mobile_image']['url'] ) ) {
                                $logo .= fw_html_tag( 'img', array(
                                        'src'   => $header_logo['mobile_image']['url'],
                                        'alt'   => $unysonplus_logo_alt,
                                        'class' => 'site-logo site-logo--mobile img-fluid',
                                ) );
                        }
                        // Transparent-header variant — shown while the header is transparent
                        // and not yet stuck (see .site-logo--transparent in style.css).
                        if ( ! empty( $header_logo['transparent_image']['url'] ) ) {
                                $logo .= fw_html_tag( 'img', array(
                                        'src'   => $header_logo['transparent_image']['url'],
                                        'alt'   => $unysonplus_logo_alt,
                                        'class' => 'site-logo site-logo--transparent img-fluid',
                                ) );
                        }
                } elseif ( ( $custom_logo_id = get_theme_mod( 'custom_logo' ) ) && ( $custom_logo_url = wp_get_attachment_url( $custom_logo_id ) ) ) {
                        $img_attr = [];
                        $img_attr['src']    = $custom_logo_url;
                        $meta = wp_get_attachment_metadata( $custom_logo_id );
                        $img_attr['width']  = ! empty( $meta['width'] )  ? $meta['width']  : '';
                        $img_attr['height'] = ! empty( $meta['height'] ) ? $meta['height'] : '';
                        $img_attr['alt']    = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
                        $img_attr['class']  = 'site-logo img-fluid';

                        $logo = fw_html_tag( 'img', $img_attr );
                } else {
                        $logo = ! empty( $header_logo['site_title'] ) ? $header_logo['site_title'] : get_bloginfo( 'name' );
                }

                // Site Title Color (Header → Identity): mutually-exclusive preset
                // class or custom hex from the compact predefined-colors picker.
                // Colors the text site title; harmless when an image logo shows.
                $unysonplus_brand_link_attr = array( 'href' => esc_url( home_url( '/' ) ), 'rel' => 'home' );
                $unysonplus_logo_color      = isset( $header_logo['color'] ) ? $header_logo['color'] : array();
                if ( is_array( $unysonplus_logo_color ) ) {
                        // Custom hex is emitted to the generated CSS file (.site-title a);
                        // the predefined palette is a class. No inline style here.
                        if ( ! empty( $unysonplus_logo_color['predefined'] ) ) {
                                $unysonplus_brand_link_attr['class'] = $unysonplus_logo_color['predefined'];
                        }
                }
                //if ( is_front_page() || is_home() ) {
                //      $tag = 'h1';
                //}else{
                        $tag = 'div';
                //}
                // Wrapper flags so style.css can gate the sticky / mobile logo swaps.
                $unysonplus_brand_class = array( 'site-title', 'navbar-brand' );
                if ( ! empty( $header_logo['sticky_image']['url'] ) )      { $unysonplus_brand_class[] = 'has-sticky-logo'; }
                if ( ! empty( $header_logo['mobile_image']['url'] ) )      { $unysonplus_brand_class[] = 'has-mobile-logo'; }
                if ( ! empty( $header_logo['transparent_image']['url'] ) ) { $unysonplus_brand_class[] = 'has-transparent-logo'; }
                echo fw_html_tag( $tag, array( 'class' => join( ' ', $unysonplus_brand_class ) ),
                        fw_html_tag( 'a', $unysonplus_brand_link_attr, $logo)
                 );

                // Tagline: header-only override (Header → Identity) wins over the
                // site-wide WordPress Tagline; falls back to it when empty.
                $description = ! empty( $header_logo['tagline_text'] )
                        ? esc_html( $header_logo['tagline_text'] )
                        : get_bloginfo( 'description', 'display' );
                if ( $description || is_customize_preview() ) {
                        $description_class = array( 'site-description' );
                        if( !empty($header_logo['tagline'])) $description_class[] = $header_logo['tagline'];
                        // Tagline color: mutually-exclusive palette preset class or custom hex.
                        $unysonplus_desc_attr  = array();
                        $unysonplus_tag_color  = isset( $header_logo['tagline_color'] ) ? $header_logo['tagline_color'] : array();
                        if ( is_array( $unysonplus_tag_color ) ) {
                                // Custom hex is emitted to the generated CSS file (.site-description);
                                // the predefined palette is a class. No inline style here.
                                if ( ! empty( $unysonplus_tag_color['predefined'] ) ) {
                                        $description_class[] = $unysonplus_tag_color['predefined'];
                                }
                        }
                        $unysonplus_desc_attr['class'] = join( ' ', $description_class );
                        echo fw_html_tag( 'p', $unysonplus_desc_attr, $description );
                }
        }
endif;


/**
 * Getter function for Featured Content Plugin.
 *
 * @return array An array of WP_Post objects.
 */
function fw_theme_get_featured_posts() {
        /**
         * @param array|bool $posts Array of featured posts, otherwise false.
         */
        return apply_filters( 'fw_theme_get_featured_posts', array() );
}


if ( ! function_exists( 'unysonplus_paging_nav' ) ) :

function unysonplus_paging_nav( $wp_query = null ) {

    if ( ! $wp_query ) {
        $wp_query = $GLOBALS['wp_query'];
    }

    if ( $wp_query->max_num_pages < 2 ) {
        return;
    }

    $paged = max( 1, get_query_var( 'paged' ) );

    $links = paginate_links( array(
        'base'      => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ),
        'current'   => $paged,
        'total'     => $wp_query->max_num_pages,
        'mid_size'  => 5,
        'prev_next' => true,
        'prev_text' => '<i class="fa fa-angle-double-left"></i>',
        'next_text' => '<i class="fa fa-angle-double-right"></i>',
    ) );

    if ( $links ) : ?>
        <nav class="navigation paging-navigation" role="navigation">
            <h4 class="screen-reader-text"><?php _e( 'Posts navigation', 'unysonplus' ); ?></h4>
            <div class="pagination loop-pagination">
                <?php echo $links; ?>
            </div>
        </nav>
    <?php endif;

}

endif;


if ( ! function_exists( 'unysonplus_post_nav' ) ) :

function unysonplus_post_nav() {

    if ( is_page() ) {
        return;
    }

    $previous = is_attachment() ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
    $next     = get_adjacent_post( false, '', false );

    if ( ! $previous && ! $next ) {
        return;
    }

    $post_type_obj = get_post_type_object( get_post_type() );
    $post_type     = $post_type_obj ? $post_type_obj->labels->singular_name : '';

?>

<div class="fw-container">
    <div class="fw-row">
        <div class="fw-col-md-12">

        <nav class="navigation post-navigation" role="navigation">

            <div class="screen-reader-text">
                <?php printf( __( '%s navigation', 'unysonplus' ), $post_type ); ?>
            </div>

            <div class="nav-links fw-row mb-4">

                <?php if ( $previous ) : ?>
                    <?php previous_post_link(
                        '<div class="meta-nav fw-col-md-6">%link</div>',
                        '<h5 class="previous">Previous ' . esc_html( $post_type ) . '</h5><h4>%title</h4>'
                    ); ?>
                <?php else : ?>
                    <div class="meta-nav fw-col-md-6"></div>
                <?php endif; ?>

                <?php if ( $next ) : ?>
                    <?php next_post_link(
                        '<div class="meta-nav text-md-end fw-col-md-6">%link</div>',
                        '<h5 class="next">Next ' . esc_html( $post_type ) . '</h5><h4>%title</h4>'
                    ); ?>
                <?php endif; ?>

            </div>

        </nav>

        </div>
    </div>
</div>

<?php
}

endif;

add_action( 'unysonplus_after_entry', 'unysonplus_post_nav' );


if ( ! function_exists( 'unysonplus_tags_list' ) ) :
    /**
     * Prints HTML with meta information for the post tags.
     */
    function unysonplus_tags_list() {
        $tags_list = get_the_tag_list( '', esc_html__( ', ', 'unysonplus' ) );

        if ( $tags_list ) {
            printf(
                '<div class="tags-links"><span class="screen-reader-text">%s</span>%s</div>',
                esc_html__( 'Tags: ', 'unysonplus' ),
                wp_kses_post( $tags_list )
            );
        }
    }
    add_action( 'unysonplus_entry_footer', 'unysonplus_tags_list' );
endif;


if ( ! function_exists( 'unysonplus_cdn_fallback' ) ) :
/**
 * Load CDN with local fallback
 */
function unysonplus_cdn_fallback( $cdn_url, $local_url ) {
    $transient_name = 'cdn_is_up_' . md5( $cdn_url );
    $cdnIsUp = get_transient( $transient_name );

    if ( $cdnIsUp ) {
        return $cdn_url;
    }

    $cdn_response = wp_remote_head( $cdn_url, ['timeout' => 2] ); // lightweight check

    if ( is_wp_error( $cdn_response ) || wp_remote_retrieve_response_code( $cdn_response ) !== 200 ) {
        return $local_url;
    }

    set_transient( $transient_name, true, 20 * MINUTE_IN_SECONDS );
    return $cdn_url;
}
endif;


/*
if ( ! function_exists( 'unysonplus_author_info_box' ) ) :
        /**
         * Display Author Info Box
         */
        /*
function unysonplus_author_info_box() {
    $content_posts = fw_get_db_settings_option('content_posts');

    if ( empty( $content_posts['author-box'] ) ) {
        return;
    }

    if ( ! is_single() ) {
        return;
    }

    global $post;

    $author_id = $post->post_author;

    // Get author's display name or fallback to nickname
    $display_name = get_the_author_meta( 'display_name', $author_id );
    if ( empty( $display_name ) ) {
        $display_name = get_the_author_meta( 'nickname', $author_id );
    }

    // Get author's bio, website, and posts URL
    $user_description = get_the_author_meta( 'user_description', $author_id );
    $user_website     = get_the_author_meta( 'url', $author_id );
    $user_posts       = get_author_posts_url( $author_id );

    // Get avatar
    $avatar = get_avatar( get_the_author_meta( 'user_email', $author_id ), 90, null, null, [
        'class' => 'alignleft',
    ]);

    // Start building HTML
    $author_details = $avatar;

    if ( ! empty( $display_name ) ) {
        $author_details .= sprintf(
            '<h3 class="author-name">%s %s</h3>',
            esc_html__( 'About', 'unysonplus' ),
            esc_html( $display_name )
        );
    }

    if ( ! empty( $user_description ) ) {
        $author_details .= '<p class="author_details">' . wp_kses_post( nl2br( $user_description ) ) . '</p>';
    }

    // Author links
    $links = sprintf(
        '<a href="%s">%s</a>',
        esc_url( $user_posts ),
        sprintf( esc_html__( 'View all posts by %s', 'unysonplus' ), esc_html( $display_name ) )
    );

    if ( ! empty( $user_website ) ) {
        $links .= ' | <a href="' . esc_url( $user_website ) . '" target="_blank" rel="nofollow">' . esc_html__( 'Website', 'unysonplus' ) . '</a>';
    }

    $author_details .= '<p class="author_links">' . $links . '</p>';

    echo '<div class="author-bio">' . $author_details . '</div>';
}
add_action( 'unysonplus_entry_footer', 'unysonplus_author_info_box' );
endif;


// Allow HTML in author bio section 
remove_filter('pre_user_description', 'wp_filter_kses');
endif;
*/

if ( ! function_exists( 'fw_theme_posted_on' ) ) :
/**
 * Print HTML with meta information for the current post-date/time and author.
 */
function fw_theme_posted_on() {

    // Display sticky label on homepage for sticky posts
    if ( is_sticky() && is_home() && ! is_paged() ) {
        echo '<span class="featured-post">' . esc_html__( 'Sticky', 'unysonplus' ) . '</span>';
    }

    // Prepare post date and author
    $permalink = esc_url( get_permalink() );
    $datetime  = esc_attr( get_the_date( 'c' ) );
    $date      = esc_html( get_the_date() );
    $author_id = get_the_author_meta( 'ID' );
    $author_url = esc_url( get_author_posts_url( $author_id ) );
    $author_name = esc_html( get_the_author() );

    // Print post meta
    printf(
        '<span class="entry-date"><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s">%3$s</time></a></span> ' .
        '<span class="byline"><span class="author vcard"><a class="url fn n" href="%4$s" rel="author">%5$s</a></span></span>',
        $permalink,
        $datetime,
        $date,
        $author_url,
        $author_name
    );
}
endif;


/**
 * Determine if a blog has more than one category.
 *
 * @return bool True if blog has more than 1 category, false otherwise.
 */
function fw_theme_categorized_blog() {
    // Try to get cached category count
    $all_the_cool_cats = get_transient( 'fw_theme_category_count' );

    if ( false === $all_the_cool_cats ) {
        // Get all categories that have at least one post
        $all_the_cool_cats = get_categories( array(
            'hide_empty' => 1,
        ) );

        // Count them
        $all_the_cool_cats = (int) count( $all_the_cool_cats );

        // Cache the count
        set_transient( 'fw_theme_category_count', $all_the_cool_cats );
    }

    // Return true if more than one category
    return $all_the_cool_cats > 1;
}


/**
 * Display an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index/archive
 * views, or a div element when on single views.
 */
function fw_theme_post_thumbnail() {
    if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
        return;
    }

    $current_position = false;
    if ( function_exists( 'fw_ext_sidebars_get_current_position' ) ) {
        $current_position = fw_ext_sidebars_get_current_position();
    }

    // Decide which thumbnail size to use
    $thumbnail_size = ( in_array( $current_position, array( 'full', 'left' ) )
                        || is_page_template( 'page-templates/full-width.php' )
                        || empty( $current_position ) )
                    ? 'fw-theme-full-width'
                    : '';

    // Output thumbnail wrapped properly
    if ( is_singular() ) {
        echo '<div class="post-thumbnail">' . get_the_post_thumbnail( null, $thumbnail_size ) . '</div>';
    } else {
        echo '<a class="post-thumbnail" href="' . esc_url( get_permalink() ) . '">' 
             . get_the_post_thumbnail( null, $thumbnail_size ) 
             . '</a>';
    }
}


if ( ! function_exists( 'unysonplus_get_image_sizes' ) ) :
/**
 * Retrieve all registered image sizes with width, height, and crop information.
 *
 * @return array $sizes Data for all currently-registered image sizes.
 */
function unysonplus_get_image_sizes() {
    global $_wp_additional_image_sizes;

    $sizes = array();

    foreach ( get_intermediate_image_sizes() as $size ) {

        if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ), true ) ) {
            $sizes[ $size ] = array(
                'width'  => (int) get_option( "{$size}_size_w" ),
                'height' => (int) get_option( "{$size}_size_h" ),
                'crop'   => (bool) get_option( "{$size}_crop" ),
            );
        } elseif ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
            $sizes[ $size ] = array(
                'width'  => $_wp_additional_image_sizes[ $size ]['width'],
                'height' => $_wp_additional_image_sizes[ $size ]['height'],
                'crop'   => $_wp_additional_image_sizes[ $size ]['crop'],
            );
        }
    }

    return $sizes;
}
endif;


if ( ! function_exists( 'unysonplus_get_image_size' ) ) :
/**
 * Get size information for a specific image size.
 *
 * @uses    unysonplus_get_image_sizes()
 * @param   string $size The image size for which to retrieve data.
 * @return  string|false Size dimensions in 'widthxheight' format or false if the size doesn't exist.
 */
function unysonplus_get_image_size( $size ) {
    $sizes = unysonplus_get_image_sizes();

    if ( isset( $sizes[ $size ] ) ) {
        $width  = (int) $sizes[ $size ]['width'];
        $height = (int) $sizes[ $size ]['height'];

        if ( $height === 0 ) {
            $height = 'Auto';
        }

        return $width . 'x' . $height;
    }

    return false;
}
endif;


if ( ! function_exists( 'unysonplus_get_image_width' ) ) :
/**
 * Get the width of a specific image size.
 *
 * @uses   unysonplus_get_image_size()
 * @param  string $size The image size for which to retrieve data.
 * @return int|false Width of the image size or false if the size doesn't exist.
 */
function unysonplus_get_image_width( $size ) {
    $size_data = unysonplus_get_image_size( $size );

    if ( ! $size_data || ! isset( $size_data['width'] ) ) {
        return false;
    }

    return (int) $size_data['width'];
}
endif;


if ( ! function_exists( 'unysonplus_get_image_height' ) ) :
/**
 * Get the height of a specific image size.
 *
 * @uses   unysonplus_get_image_size()
 * @param  string $size The image size for which to retrieve data.
 * @return int|false Height of the image size or false if the size doesn't exist.
 */
function unysonplus_get_image_height( $size ) {
    $size_data = unysonplus_get_image_size( $size );

    if ( ! $size_data || ! isset( $size_data['height'] ) ) {
        return false;
    }

    return (int) $size_data['height'];
}
endif;


if ( ! function_exists( 'unysonplus_excerpt' ) ) :
/**
 * Generate a custom excerpt with word limit and no images/captions.
 *
 * @param int $limit Number of words to show.
 * @return string Cleaned excerpt.
 */
function unysonplus_excerpt( $limit = 20 ) {

    // Get raw excerpt and remove images
    $excerpt = get_the_excerpt();
    $excerpt = preg_replace( '/<img[^>]*>/i', '', $excerpt ); // remove <img> tags
    $excerpt = preg_replace( '/<div id="attachment_\d+" class="wp-caption.*?<\/div>/i', '', $excerpt ); // remove captions

    // Remove shortcodes
    $excerpt = strip_shortcodes( $excerpt );

    // Trim to $limit words
    $excerpt = wp_trim_words( $excerpt, $limit, '...' );

    return $excerpt;
}
endif;


if ( ! function_exists( 'unysonplus_array_keys_exist' ) ) :
/**
 * Checks if multiple keys exist in an array.
 *
 * @param array $array The array to check.
 * @param array|string ...$keys Keys to check. Can pass an array or multiple string arguments.
 * @return bool True if all keys exist, false otherwise.
 */
function unysonplus_array_keys_exist( array $array, $keys ) {
    // Ensure $keys is an array
    if ( ! is_array( $keys ) ) {
        $keys = func_get_args();
        array_shift( $keys ); // remove $array from arguments
    }

    foreach ( $keys as $key ) {
        if ( ! array_key_exists( $key, $array ) ) {
            return false;
        }
    }

    return true;
}
endif;


/*
 * Woocommerce
 */

if ( ! function_exists( 'unysonplus_is_woocommerce_activated' ) ) {
        /**
         * Check if WooCommerce is activated
         */
        function unysonplus_is_woocommerce_activated() {
                return class_exists( 'WooCommerce' ) ? true : false;
        }
}


if ( ! function_exists( 'unysonplus_header_cart' ) ) {
        /**
         * Display Header Cart
         *
         * @since  1.0.0
         * @uses  unysonplus_is_woocommerce_activated() check if WooCommerce is activated
         * @return void
         */
        function unysonplus_header_cart() {
                if ( unysonplus_is_woocommerce_activated() ) {
                        if ( is_cart() ) {
                                $class = 'current-menu-item';
                        } else {
                                $class = '';
                        }
                        ?>
                <ul id="site-header-cart" class="site-header-cart menu">
                        <li class="<?php echo esc_attr( $class ); ?>">
                                <?php unysonplus_cart_link(); ?>
                        </li>
                        <li>
                                <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
                        </li>
                </ul>
                        <?php
                }
        }
}


if ( ! function_exists( 'unysonplus_cart_link' ) ) {
        /**
         * Cart Link
         * Displayed a link to the cart including the number of items present and the cart total
         * Also use sprintf( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'unysonplus' )
         * @return void
         * @since  1.0.0
         */
        function unysonplus_cart_link() {
                ?>
                        <a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'storefront' ); ?>">
                                <?php /* translators: %d: number of items in cart */ ?>
                                <?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?> <span class="count"><?php echo wp_kses_data( sprintf( _n( '(%d)', '(%d)', WC()->cart->get_cart_contents_count(), 'unysonplus' ), WC()->cart->get_cart_contents_count() ) ); ?></span>
                        </a>
                <?php
        }
}