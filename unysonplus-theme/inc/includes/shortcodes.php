<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

add_shortcode( 'sitemap', 'unysonplus_sitemap_shortcode' );

function unysonplus_sitemap_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'type'    => 'post',
        'orderby' => 'title',
        'order'   => 'ASC',
        'limit'   => -1,
    ), $atts, 'sitemap' );

    $post_type = sanitize_key( $atts['type'] );

    if ( $post_type === 'posts' ) {
        $post_type = 'post';
    } elseif ( $post_type === 'pages' ) {
        $post_type = 'page';
    }

    if ( ! post_type_exists( $post_type ) ) {
        return '<p>Invalid post type: <strong>' . esc_html( $post_type ) . '</strong></p>';
    }

    $post_type_obj = get_post_type_object( $post_type );

    $query = new WP_Query( array(
        'post_type'      => $post_type,
        'posts_per_page' => intval( $atts['limit'] ),
        'orderby'        => sanitize_key( $atts['orderby'] ),
        'order'          => strtoupper( $atts['order'] ) === 'DESC' ? 'DESC' : 'ASC',
        'post_status'    => 'publish',
        'no_found_rows'  => true,
    ) );

    if ( ! $query->have_posts() ) {
        return '<p>No ' . esc_html( $post_type_obj->labels->name ) . ' found.</p>';
    }

    $output = '<div class="sitemap-list">';
    $output .= '<h2>' . esc_html( $post_type_obj->labels->name ) . '</h2>';
    $output .= '<ul>';

    while ( $query->have_posts() ) {
        $query->the_post();
        $output .= '<li><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></li>';
    }

    wp_reset_postdata();

    $output .= '</ul>';
    $output .= '</div>';

    return $output;
}
