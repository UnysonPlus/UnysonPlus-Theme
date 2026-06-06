<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

/**
 * Header builder template.
 *
 * Reads the Unyson `header_layout` option and renders topbar + main header
 * with three slots (left, center, right). Background colors and dimensions
 * come from CSS custom properties set by inc/includes/theme-vars.php — no
 * inline styles here.
 *
 * When Unyson is inactive, defaults synthesize a minimal header (logo +
 * primary menu) so a fresh install renders cleanly.
 */

$unyson = function_exists( 'fw_get_db_settings_option' );

$header_layout = $unyson ? fw_get_db_settings_option( 'header_layout', array() ) : array();
if ( ! is_array( $header_layout ) ) { $header_layout = array(); }

$container = ! empty( $header_layout['container'] ) ? $header_layout['container'] : 'container';
$sticky    = ! empty( $header_layout['sticky_header'] ) && $header_layout['sticky_header'] === 'yes';

$topbar_enabled = ! empty( $header_layout['topbar_settings']['enabled'] ) && $header_layout['topbar_settings']['enabled'] === 'yes';
$topbar_opts    = ! empty( $header_layout['topbar_settings']['yes'] ) ? $header_layout['topbar_settings']['yes'] : array();

$topbar_left   = ! empty( $topbar_opts['topbar_left'] )   ? $topbar_opts['topbar_left']   : array();
$topbar_center = ! empty( $topbar_opts['topbar_center'] ) ? $topbar_opts['topbar_center'] : array();
$topbar_right  = ! empty( $topbar_opts['topbar_right'] )  ? $topbar_opts['topbar_right']  : array();

$main_left   = ! empty( $header_layout['main_left'] )   ? $header_layout['main_left']   : array();
$main_center = ! empty( $header_layout['main_center'] ) ? $header_layout['main_center'] : array();
$main_right  = ! empty( $header_layout['main_right'] )  ? $header_layout['main_right']  : array();

// Fresh-install fallback: when Unyson is inactive OR the header has no
// configured elements, synthesize a logo + primary menu so the header is
// usable out of the box.
if ( ! $unyson || ( empty( $main_left ) && empty( $main_center ) && empty( $main_right ) ) ) {
	$main_left = array(
		array( 'element_type' => array( 'element' => 'logo', 'logo' => array() ) ),
	);
	$main_center = array(
		array( 'element_type' => array(
			'element'   => 'menu_area',
			'menu_area' => array( 'menu_location' => 'primary' ),
		) ),
	);
}

// Build header classes.
$header_classes = array( 'site-header' );
if ( $sticky )                                                       { $header_classes[] = 'header-sticky'; }
if ( function_exists( 'fw_get_db_post_option' )
     && fw_get_db_post_option( get_the_ID(), 'page_header' ) === 'd-none' ) {
	$header_classes[] = 'd-none';
}

// Pick a Bootstrap color-mode based on the header background luma.
$header_theme = '';
$bg_color     = ! empty( $header_layout['bg_color'] ) ? $header_layout['bg_color'] : '';
if ( $bg_color && preg_match( '/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/', $bg_color, $m ) ) {
	$luma = ( 0.299 * (int) $m[1] + 0.587 * (int) $m[2] + 0.114 * (int) $m[3] ) / 255;
	if ( $luma < 0.5 ) { $header_theme = 'dark'; }
}

// Layout-mode-aware drawer class (overlay variant takes over the viewport).
$layout_mode    = function_exists( 'unysonplus_layout_get' )
	? unysonplus_layout_get( 'layout_header_mode', 'top' )
	: 'top';
$drawer_classes = array( 'primary-navigation-drawer' );
if ( $layout_mode === 'overlay' ) {
	$drawer_classes[] = 'primary-navigation-drawer--overlay';
}
?>

<header id="masthead" class="<?php echo esc_attr( implode( ' ', $header_classes ) ); ?>" role="banner"<?php echo $header_theme ? ' data-bs-theme="' . esc_attr( $header_theme ) . '"' : ''; ?>>

	<?php do_action( 'unysonplus_header_top' ); ?>

	<?php if ( $topbar_enabled && ( ! empty( $topbar_left ) || ! empty( $topbar_center ) || ! empty( $topbar_right ) ) ) : ?>
	<div class="header-topbar">
		<div class="<?php echo esc_attr( $container ); ?>">
			<div class="header-row">
				<div class="header-col header-col--start"><?php  unysonplus_render_header_column( $topbar_left,   'start'  ); ?></div>
				<div class="header-col header-col--center"><?php unysonplus_render_header_column( $topbar_center, 'center' ); ?></div>
				<div class="header-col header-col--end"><?php    unysonplus_render_header_column( $topbar_right,  'end'    ); ?></div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="header-main">
		<div class="<?php echo esc_attr( $container ); ?>">
			<div class="header-row">
				<div class="header-col header-col--start">
					<?php unysonplus_render_header_column( $main_left, 'start' ); ?>
				</div>
				<div class="header-col header-col--center">
					<?php unysonplus_render_header_column( $main_center, 'center' ); ?>
				</div>
				<div class="header-col header-col--end">
					<?php unysonplus_render_header_column( $main_right, 'end' ); ?>
					<button type="button"
					        class="menu-toggle"
					        aria-controls="primary-navigation-drawer"
					        aria-expanded="false"
					        aria-label="<?php esc_attr_e( 'Toggle navigation', 'unysonplus' ); ?>">
						<span class="menu-toggle__bar"></span>
						<span class="menu-toggle__bar"></span>
						<span class="menu-toggle__bar"></span>
					</button>
				</div>
			</div>
		</div>
	</div>

	<?php do_action( 'unysonplus_header_bottom' ); ?>

</header>

<div id="primary-navigation-drawer"
     class="<?php echo esc_attr( implode( ' ', $drawer_classes ) ); ?>"
     hidden
     aria-hidden="true">
	<div class="primary-navigation-drawer__scrim" data-drawer-close></div>
	<div class="primary-navigation-drawer__panel"
	     role="dialog"
	     aria-modal="true"
	     aria-label="<?php esc_attr_e( 'Site menu', 'unysonplus' ); ?>">
		<button type="button"
		        class="primary-navigation-drawer__close"
		        data-drawer-close
		        aria-label="<?php esc_attr_e( 'Close menu', 'unysonplus' ); ?>">&times;</button>
		<?php
		// Renders the assigned primary menu, or an admin-only setup
		// notice when none is set. Visitors with no menu see nothing.
		unysonplus_nav_menu( 'primary' );
		?>
	</div>
</div>
