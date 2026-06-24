<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

/**
 * Header builder template.
 *
 * Reads the four header option ids — `header_layout` (chrome), `header_topbar`,
 * `header_main`, `header_bottombar` — resolved per-request by
 * unysonplus_get_active_header_config(), and renders topbar + main + bottombar
 * each with three inline slots (left, center, right). The top/bottom bars render
 * only when a slot has content (no Enable switch). Background colors and
 * dimensions come from CSS custom properties set by inc/includes/theme-vars.php
 * — no inline styles here.
 *
 * When Unyson is inactive, defaults synthesize a minimal header (logo +
 * primary menu) so a fresh install renders cleanly.
 */

$unyson = function_exists( 'fw_get_db_settings_option' );

// Builder mode: when the active header preset was authored with the page builder
// (Header & Footer Builder extension), render its content inside <header> with
// the Type/Behavior classes, then bail. Otherwise fall through to the slot path.
$hf_render = function_exists( 'unysonplus_get_active_header_render' )
	? unysonplus_get_active_header_render()
	: array( 'mode' => 'slots' );

if ( $hf_render['mode'] === 'builder' && function_exists( 'fw_ext_hfbuilder_render' ) ) {
	$hf_type     = sanitize_html_class( $hf_render['type'] );
	$hf_behavior = sanitize_html_class( $hf_render['behavior'] );

	$h_classes = array( 'site-header', 'site-header--' . $hf_type, 'site-header--' . $hf_behavior );
	// These behaviors all need the sticky observer (navigation.js toggles .is-stuck
	// on #masthead.header-sticky); the CSS keys shrink / solidify off .is-stuck.
	if ( in_array( $hf_render['behavior'], array( 'sticky', 'sticky-shrink', 'hide-on-scroll', 'transparent-overlay' ), true ) ) {
		$h_classes[] = 'header-sticky';
	}
	if ( $hf_render['behavior'] === 'transparent-overlay' ) {
		$h_classes[] = 'site-header--transparent';
	}
	if ( function_exists( 'fw_get_db_post_option' )
	     && fw_get_db_post_option( get_the_ID(), 'page_header' ) === 'd-none' ) {
		$h_classes[] = 'd-none';
	}

	$needs_drawer = in_array( $hf_type, array( 'off-canvas', 'fullscreen-overlay' ), true );
	?>
	<header id="masthead" class="<?php echo esc_attr( implode( ' ', $h_classes ) ); ?>" role="banner" data-hf-type="<?php echo esc_attr( $hf_type ); ?>" data-hf-behavior="<?php echo esc_attr( $hf_behavior ); ?>">
		<?php do_action( 'unysonplus_header_top' ); ?>
		<?php echo fw_ext_hfbuilder_render( $hf_render['post_id'], 'header' ); // phpcs:ignore — builder output. ?>
		<?php do_action( 'unysonplus_header_bottom' ); ?>
	</header>

	<?php if ( $needs_drawer ) : ?>
		<div id="primary-navigation-drawer"
		     class="primary-navigation-drawer<?php echo $hf_type === 'fullscreen-overlay' ? ' primary-navigation-drawer--overlay' : ''; ?>"
		     hidden
		     aria-hidden="true">
			<div class="primary-navigation-drawer__scrim" data-drawer-close></div>
			<div class="primary-navigation-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site menu', 'unysonplus' ); ?>">
				<button type="button" class="primary-navigation-drawer__close" data-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'unysonplus' ); ?>">&times;</button>
				<?php
				// The visible bar (logo + [menu_toggle]) is builder-authored; the
				// drawer shows the primary menu so a [menu_toggle] works out of the box.
				if ( function_exists( 'unysonplus_nav_menu' ) ) {
					unysonplus_nav_menu( 'primary' );
				}
				?>
			</div>
		</div>
	<?php endif; ?>
	<?php
	return;
}

// Slot mode: resolve the active config. unysonplus_get_active_header_config()
// returns an array keyed by the four header option ids (chrome + the three rows),
// resolved from the active preset's meta or the global Theme Settings. Fall back
// to the global settings per id when a key is missing (defensive — mirrors the
// footer builder).
$header_cfg = isset( $hf_render['config'] ) && is_array( $hf_render['config'] ) ? $hf_render['config'] : array();
$get_section = function ( $id ) use ( $header_cfg, $unyson ) {
	if ( isset( $header_cfg[ $id ] ) && is_array( $header_cfg[ $id ] ) ) { return $header_cfg[ $id ]; }
	if ( $unyson ) { $v = fw_get_db_settings_option( $id, array() ); return is_array( $v ) ? $v : array(); }
	return array();
};

$chrome    = $get_section( 'header_layout' );
$topbar    = $get_section( 'header_topbar' );
$main      = $get_section( 'header_main' );
$bottombar = $get_section( 'header_bottombar' );

$container = ! empty( $chrome['container'] ) ? $chrome['container'] : 'container';

// Resolve the header behavior. The `header_behavior` select supersedes the
// legacy `sticky_header` switch; per-page "Transparent" overrides it for that
// page. Emitting the same classes + data-hf-behavior as the builder header lets
// the shared header-footer-builder.css + header-behaviors.js drive the slot
// header too (shrink / hide-on-scroll / etc).
$behavior = ! empty( $chrome['header_behavior'] ) ? $chrome['header_behavior'] : '';
if ( $behavior === '' && ! empty( $chrome['sticky_header'] ) && $chrome['sticky_header'] === 'yes' ) {
	$behavior = 'sticky';
}
if ( function_exists( 'fw_get_db_post_option' )
     && fw_get_db_post_option( get_the_ID(), 'page_header' ) === 'transparent' ) {
	$behavior = 'transparent-overlay';
}
if ( $behavior === '' ) { $behavior = 'static'; }

// Top / Bottom bars no longer have an Enable switch — a row is "on" when any of
// its columns holds an element (mirrors the footer rows).
$topbar_left   = ! empty( $topbar['topbar_left'] )   ? $topbar['topbar_left']   : array();
$topbar_center = ! empty( $topbar['topbar_center'] ) ? $topbar['topbar_center'] : array();
$topbar_right  = ! empty( $topbar['topbar_right'] )  ? $topbar['topbar_right']  : array();
$topbar_enabled = ! empty( $topbar_left ) || ! empty( $topbar_center ) || ! empty( $topbar_right );

$bottombar_left   = ! empty( $bottombar['bottombar_left'] )   ? $bottombar['bottombar_left']   : array();
$bottombar_center = ! empty( $bottombar['bottombar_center'] ) ? $bottombar['bottombar_center'] : array();
$bottombar_right  = ! empty( $bottombar['bottombar_right'] )  ? $bottombar['bottombar_right']  : array();
$bottombar_enabled = ! empty( $bottombar_left ) || ! empty( $bottombar_center ) || ! empty( $bottombar_right );

$main_left   = ! empty( $main['main_left'] )   ? $main['main_left']   : array();
$main_center = ! empty( $main['main_center'] ) ? $main['main_center'] : array();
$main_right  = ! empty( $main['main_right'] )  ? $main['main_right']  : array();

// Per-section wrapper container + classes from each row's Custom Styling block.
// Visual styling (bg / typography / link / border) is in the generated CSS file
// (inc/includes/hf-custom-css.php); only container / css-class / padding are
// class-based here — no inline element styles.
if ( function_exists( 'unysonplus_hf_section_render_attrs' ) ) {
	$topbar_attr    = unysonplus_hf_section_render_attrs( isset( $topbar['topbar_custom_styling'] ) ? $topbar['topbar_custom_styling'] : array(), 'topbar', $container );
	$main_attr      = unysonplus_hf_section_render_attrs( isset( $main['main_custom_styling'] ) ? $main['main_custom_styling'] : array(), 'main', $container );
	$bottombar_attr = unysonplus_hf_section_render_attrs( isset( $bottombar['bottombar_custom_styling'] ) ? $bottombar['bottombar_custom_styling'] : array(), 'bottombar', $container );
} else {
	$topbar_attr = $main_attr = $bottombar_attr = array( 'container' => $container, 'class' => '' );
}

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

// Build header classes from the resolved behavior (same contract as the builder
// header: site-header--{behavior} + header-sticky + transparent + data-hf-behavior).
$header_classes = array( 'site-header', 'site-header--' . sanitize_html_class( $behavior ) );
if ( in_array( $behavior, array( 'sticky', 'sticky-shrink', 'hide-on-scroll', 'transparent-overlay' ), true ) ) {
	$header_classes[] = 'header-sticky';
}
if ( $behavior === 'transparent-overlay' ) {
	$header_classes[] = 'site-header--transparent';
}
if ( function_exists( 'fw_get_db_post_option' )
     && fw_get_db_post_option( get_the_ID(), 'page_header' ) === 'd-none' ) {
	$header_classes[] = 'd-none';
}

// Collapse-to-mobile-menu breakpoint (lg = below 992, md = below 768).
$mobile_bp = ! empty( $chrome['mobile_breakpoint'] ) ? $chrome['mobile_breakpoint'] : 'lg';
$header_classes[] = 'header-collapse-' . sanitize_html_class( $mobile_bp );

// Pick a Bootstrap color-mode based on the header background luma.
$header_theme = '';
$bg_color     = ! empty( $chrome['bg_color'] ) ? $chrome['bg_color'] : '';
if ( $bg_color && preg_match( '/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/', $bg_color, $m ) ) {
	$luma = ( 0.299 * (int) $m[1] + 0.587 * (int) $m[2] + 0.114 * (int) $m[3] ) / 255;
	if ( $luma < 0.5 ) { $header_theme = 'dark'; }
}

// Layout-mode-aware drawer class (overlay variant takes over the viewport).
// header_mode is a site-wide choice (Header → Layout), not per-slot, so read the
// global value with the legacy general_layout key as fallback.
$layout_mode    = function_exists( 'unysonplus_header_layout_get' )
	? unysonplus_header_layout_get( 'header_mode', function_exists( 'unysonplus_layout_get' ) ? unysonplus_layout_get( 'layout_header_mode', 'top' ) : 'top' )
	: 'top';
$drawer_classes = array( 'primary-navigation-drawer' );
if ( $layout_mode === 'overlay' ) {
	$drawer_classes[] = 'primary-navigation-drawer--overlay';
}
?>

<header id="masthead" class="<?php echo esc_attr( implode( ' ', $header_classes ) ); ?>" role="banner" data-hf-behavior="<?php echo esc_attr( $behavior ); ?>"<?php echo $header_theme ? ' data-bs-theme="' . esc_attr( $header_theme ) . '"' : ''; ?>>

	<?php do_action( 'unysonplus_header_top' ); ?>

	<?php if ( $topbar_enabled ) : ?>
	<div class="header-topbar<?php echo $topbar_attr['class']; // phpcs:ignore — pre-escaped ?>">
		<div class="<?php echo esc_attr( unysonplus_fw_container_class( $topbar_attr['container'] ) ); ?>">
			<div class="header-row">
				<div class="header-col header-col--start"><?php  unysonplus_render_header_column( $topbar_left,   'start'  ); ?></div>
				<div class="header-col header-col--center"><?php unysonplus_render_header_column( $topbar_center, 'center' ); ?></div>
				<div class="header-col header-col--end"><?php    unysonplus_render_header_column( $topbar_right,  'end'    ); ?></div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="header-main<?php echo $main_attr['class']; // phpcs:ignore — pre-escaped ?>">
		<div class="<?php echo esc_attr( unysonplus_fw_container_class( $main_attr['container'] ) ); ?>">
			<div class="header-row">
				<?php if ( ! empty( $main_left ) ) : ?>
				<div class="header-col header-col--start">
					<?php unysonplus_render_header_column( $main_left, 'start' ); ?>
				</div>
				<?php endif; ?>
				<?php if ( ! empty( $main_center ) ) : ?>
				<div class="header-col header-col--center">
					<?php unysonplus_render_header_column( $main_center, 'center' ); ?>
				</div>
				<?php endif; ?>
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

	<?php if ( $bottombar_enabled ) : ?>
	<div class="header-bottombar<?php echo $bottombar_attr['class']; // phpcs:ignore — pre-escaped ?>">
		<div class="<?php echo esc_attr( unysonplus_fw_container_class( $bottombar_attr['container'] ) ); ?>">
			<div class="header-row">
				<div class="header-col header-col--start"><?php  unysonplus_render_header_column( $bottombar_left,   'start'  ); ?></div>
				<div class="header-col header-col--center"><?php unysonplus_render_header_column( $bottombar_center, 'center' ); ?></div>
				<div class="header-col header-col--end"><?php    unysonplus_render_header_column( $bottombar_right,  'end'    ); ?></div>
			</div>
		</div>
	</div>
	<?php endif; ?>

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
