<?php
/**
 * The sidebar containing the left widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package Unysonplus
 */

if ( ! is_active_sidebar( 'sidebar-left' ) ) {
	return;
}

/**
 * Fires just before the left sidebar <aside> renders.
 */
do_action( 'unysonplus_before_sidebar' );
?>

<aside id="sidebar" class="sidebar widget-area sidebar--left" role="complementary">
	<?php dynamic_sidebar( 'sidebar-left' ); ?>
</aside>

<?php
/**
 * Fires just after the left sidebar </aside> closes.
 */
do_action( 'unysonplus_after_sidebar' );
