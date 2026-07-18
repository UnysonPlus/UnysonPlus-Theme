<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );
/**
 * Header mode: BUILDER-authored (Header & Footer Builder extension).
 *
 * Renders the visually-built header inside <header> with the Type/Behavior
 * classes, plus the off-canvas / fullscreen drawer when the builder header type
 * needs one. Data resolved by the router (template-parts/header-builder.php).
 *
 * @var array $args { h_classes, hf_type, hf_behavior, post_id, needs_drawer, is_radial, is_concentric, overlay_corner }
 */
$d = $args;
?>

<header id="masthead" class="<?php echo esc_attr( $d['h_classes'] ); ?>" role="banner" data-hf-type="<?php echo esc_attr( $d['hf_type'] ); ?>" data-hf-behavior="<?php echo esc_attr( $d['hf_behavior'] ); ?>">
	<?php do_action( 'unysonplus_header_top' ); ?>
	<?php echo fw_ext_hfbuilder_render( $d['post_id'], 'header' ); // phpcs:ignore — builder output. ?>
	<?php do_action( 'unysonplus_header_bottom' ); ?>
</header>

<?php if ( ! empty( $d['needs_drawer'] ) ) :
	$drawer_cls = 'primary-navigation-drawer';
	if ( $d['hf_type'] === 'fullscreen-overlay' ) {
		$drawer_cls .= ' primary-navigation-drawer--overlay';
		$drawer_cls .= ' primary-navigation-drawer--cc-' . sanitize_html_class( ! empty( $d['overlay_color_mode'] ) ? $d['overlay_color_mode'] : 'shade' );
	}
	if ( ! empty( $d['is_radial'] ) )              { $drawer_cls .= ' primary-navigation-drawer--radial'; }
	if ( ! empty( $d['is_concentric'] ) ) {
		$drawer_cls .= ' primary-navigation-drawer--concentric primary-navigation-drawer--corner-' . sanitize_html_class( ! empty( $d['overlay_corner'] ) ? $d['overlay_corner'] : 'tr' );
	}
	?>
	<div id="primary-navigation-drawer" class="<?php echo esc_attr( $drawer_cls ); ?>" hidden aria-hidden="true">
		<div class="primary-navigation-drawer__scrim" data-drawer-close></div>
		<div class="primary-navigation-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site menu', 'unysonplus' ); ?>">
			<?php unysonplus_render_drawer_close(); ?>
			<?php
			// The visible bar (logo + [menu_toggle]) is builder-authored; the drawer
			// shows the primary menu so a [menu_toggle] works out of the box.
			if ( ! empty( $d['is_radial'] ) && function_exists( 'unysonplus_nav_menu' ) ) : ?>
				<div class="primary-navigation-drawer__disc">
					<?php unysonplus_nav_menu( 'primary' ); ?>
					<div class="primary-navigation-drawer__hub"><?php if ( function_exists( 'unysonplus_logo' ) ) { unysonplus_logo(); } ?></div>
				</div>
			<?php elseif ( function_exists( 'unysonplus_nav_menu' ) ) :
				unysonplus_nav_menu( 'primary' );
			endif; ?>
		</div>
	</div>
<?php endif; ?>
