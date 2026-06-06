<?php
/**
 * Unyson+ theme header
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
  </head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'unysonplus' ); ?></a>
	<?php if ( ! ( function_exists( 'unysonplus_should_hide_site_header' ) && unysonplus_should_hide_site_header() ) ) : ?>
		<?php do_action( 'unysonplus_before_header' ); ?>
		<?php get_template_part( 'template-parts/header', 'builder' ); ?>
		<?php do_action( 'unysonplus_after_header' ); ?>
	<?php endif; ?>

	<div id="content" class="site-content">
