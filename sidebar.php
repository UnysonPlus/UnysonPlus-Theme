<?php
/**
 * The sidebar containing the main widget area (right).
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package Unysonplus
 */

if ( ! is_active_sidebar( 'sidebar-right' ) ) {
	return;
}

/**
 * Fires just before the right sidebar <aside> renders.
 */
do_action( 'unysonplus_before_sidebar' );
?>

<aside id="sidebar" class="sidebar widget-area sidebar--right" role="complementary">
	<?php dynamic_sidebar( 'sidebar-right' ); ?>
</aside>

<?php
/**
 * Fires just after the right sidebar </aside> closes.
 */
do_action( 'unysonplus_after_sidebar' );
