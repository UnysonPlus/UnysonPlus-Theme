<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );
/**
 * Header mode: TOP - Floating Pill (a CSS treatment over Default today).
 *
 * Self-contained markup for the standard horizontal header. All data is resolved
 * by the router (template-parts/header-builder.php) and handed in via $args; this
 * file only renders layout, calling the shared element renderer to fill columns.
 *
 * @var array $args Resolved header data (classes, sections, drawer, …).
 */
$d = $args;
?>

<header id="masthead" class="<?php echo esc_attr( $d['header_classes'] ); ?>" role="banner" data-hf-behavior="<?php echo esc_attr( $d['behavior'] ); ?>"<?php echo $d['header_theme'] ? ' data-bs-theme="' . esc_attr( $d['header_theme'] ) . '"' : ''; ?><?php echo $d['header_style'] ? ' style="' . esc_attr( $d['header_style'] ) . '"' : ''; ?>>

	<?php do_action( 'unysonplus_header_top' ); ?>

	<?php if ( $d['topbar_enabled'] ) : ?>
	<div class="header-topbar<?php echo $d['topbar_attr']['class']; // phpcs:ignore — pre-escaped ?>">
		<div class="<?php echo esc_attr( unysonplus_fw_container_class( $d['topbar_attr']['container'] ) ); ?>">
			<div class="header-row">
				<div class="header-col header-col--start"><?php  unysonplus_render_header_column( $d['topbar_left'],   'start'  ); ?></div>
				<div class="header-col header-col--center"><?php unysonplus_render_header_column( $d['topbar_center'], 'center' ); ?></div>
				<div class="header-col header-col--end"><?php    unysonplus_render_header_column( $d['topbar_right'],  'end'    ); ?></div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="header-main<?php echo $d['main_attr']['class']; // phpcs:ignore — pre-escaped ?>">
		<div class="<?php echo esc_attr( unysonplus_fw_container_class( $d['main_attr']['container'] ) ); ?>">
			<div class="header-row">
				<?php if ( ! empty( $d['main_left'] ) ) : ?>
				<div class="header-col header-col--start">
					<?php unysonplus_render_header_column( $d['main_left'], 'start' ); ?>
				</div>
				<?php endif; ?>
				<?php if ( ! empty( $d['main_center'] ) ) : ?>
				<div class="header-col header-col--center">
					<?php unysonplus_render_header_column( $d['main_center'], 'center' ); ?>
				</div>
				<?php endif; ?>
				<div class="header-col header-col--end">
					<?php unysonplus_render_header_column( $d['main_right'], 'end' ); ?>
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

	<?php if ( $d['bottombar_enabled'] ) : ?>
	<div class="header-bottombar<?php echo $d['bottombar_attr']['class']; // phpcs:ignore — pre-escaped ?>">
		<div class="<?php echo esc_attr( unysonplus_fw_container_class( $d['bottombar_attr']['container'] ) ); ?>">
			<div class="header-row">
				<div class="header-col header-col--start"><?php  unysonplus_render_header_column( $d['bottombar_left'],   'start'  ); ?></div>
				<div class="header-col header-col--center"><?php unysonplus_render_header_column( $d['bottombar_center'], 'center' ); ?></div>
				<div class="header-col header-col--end"><?php    unysonplus_render_header_column( $d['bottombar_right'],  'end'    ); ?></div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php do_action( 'unysonplus_header_bottom' ); ?>

</header>

<div id="primary-navigation-drawer" class="<?php echo esc_attr( $d['drawer_classes'] ); ?>" hidden aria-hidden="true">
	<div class="primary-navigation-drawer__scrim" data-drawer-close></div>
	<div class="primary-navigation-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site menu', 'unysonplus' ); ?>">
		<button type="button" class="primary-navigation-drawer__close" data-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'unysonplus' ); ?>">&times;</button>
		<?php unysonplus_nav_menu( 'primary' ); ?>
	</div>
</div>
