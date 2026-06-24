<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }
/**
 * First-run onboarding — a dismissible welcome notice plus an Appearance →
 * "Getting Started" page with a live setup checklist (logo, menus, homepage,
 * widgets, theme settings). Pure perceived-completeness polish: it discovers
 * the real state of the install and links straight to the screen that fixes
 * each gap, so a brand-new site never feels empty.
 *
 * This is a bespoke admin dashboard (like the plugin's Shortcodes screen), so
 * per CLAUDE.md it is intentionally NOT built from the box→group settings
 * convention — it hand-rolls its own minimal markup/CSS.
 *
 * Auto-loaded by Theme_Includes from inc/includes/. All hooks are admin-only.
 */

if ( ! is_admin() ) { return; }

if ( ! function_exists( 'unysonplus_onboarding_menu' ) ) :
	/** Register Appearance → Getting Started. */
	function unysonplus_onboarding_menu() {
		add_theme_page(
			__( 'Getting Started', 'unysonplus' ),
			__( 'Getting Started', 'unysonplus' ),
			'edit_theme_options',
			'unysonplus-getting-started',
			'unysonplus_onboarding_render_page'
		);
	}
	add_action( 'admin_menu', 'unysonplus_onboarding_menu' );
endif;

if ( ! function_exists( 'unysonplus_onboarding_steps' ) ) :
	/**
	 * The checklist model: each step reports whether it's done + where to fix it.
	 * Status detection is intentionally cheap (option/menu/sidebar reads).
	 *
	 * @return array<int,array>
	 */
	function unysonplus_onboarding_steps() {
		$has_logo = has_custom_logo();
		if ( ! $has_logo && function_exists( 'fw_get_db_settings_option' ) ) {
			$identity = fw_get_db_settings_option( 'header_identity' );
			$has_logo = ! empty( $identity['logo']['url'] ) || ! empty( $identity['logo'] );
		}

		$has_menu     = ( has_nav_menu( 'primary' ) || has_nav_menu( 'primary-right' ) || has_nav_menu( 'footer' ) );
		$static_front = ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) > 0 );
		$has_footer_w = is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' );

		$settings_url = admin_url( 'admin.php?page=fw-settings' );

		return array(
			array(
				'title' => __( 'Add your logo & site identity', 'unysonplus' ),
				'desc'  => __( 'Upload a logo and set the site title/tagline so the header looks finished.', 'unysonplus' ),
				'done'  => (bool) $has_logo,
				'url'   => admin_url( 'customize.php?autofocus[section]=title_tagline' ),
				'cta'   => __( 'Set logo', 'unysonplus' ),
			),
			array(
				'title' => __( 'Choose your brand colours & fonts', 'unysonplus' ),
				'desc'  => __( 'Set the colour palette and typography in Theme Settings — every element inherits them.', 'unysonplus' ),
				'done'  => null, // not auto-detectable; always offered
				'url'   => $settings_url,
				'cta'   => __( 'Open Theme Settings', 'unysonplus' ),
			),
			array(
				'title' => __( 'Create a navigation menu', 'unysonplus' ),
				'desc'  => __( 'Build a menu and assign it to the Primary location so visitors can get around.', 'unysonplus' ),
				'done'  => (bool) $has_menu,
				'url'   => admin_url( 'nav-menus.php' ),
				'cta'   => __( 'Manage menus', 'unysonplus' ),
			),
			array(
				'title' => __( 'Set a static homepage', 'unysonplus' ),
				'desc'  => __( 'Point the front page at a page you build with the page builder (or keep the blog).', 'unysonplus' ),
				'done'  => (bool) $static_front,
				'url'   => admin_url( 'options-reading.php' ),
				'cta'   => __( 'Choose homepage', 'unysonplus' ),
			),
			array(
				'title' => __( 'Populate the footer', 'unysonplus' ),
				'desc'  => __( 'Add footer columns/widgets or build the footer in Theme Settings → Footer.', 'unysonplus' ),
				'done'  => (bool) $has_footer_w,
				'url'   => admin_url( 'widgets.php' ),
				'cta'   => __( 'Edit widgets', 'unysonplus' ),
			),
		);
	}
endif;

if ( ! function_exists( 'unysonplus_onboarding_render_page' ) ) :
	/** Render the Getting Started dashboard. */
	function unysonplus_onboarding_render_page() {
		$steps = unysonplus_onboarding_steps();
		$theme = wp_get_theme( get_template() );
		?>
		<div class="wrap unysonplus-onboarding">
			<h1><?php
				/* translators: %s: theme name */
				printf( esc_html__( 'Welcome to %s', 'unysonplus' ), esc_html( $theme->get( 'Name' ) ) );
			?></h1>
			<p class="unysonplus-onboarding__lead">
				<?php esc_html_e( 'A few quick steps to get your site looking complete. Everything here is optional — you can change it anytime.', 'unysonplus' ); ?>
			</p>

			<ol class="unysonplus-onboarding__steps">
				<?php foreach ( $steps as $step ) :
					$is_done = ( true === $step['done'] );
					$mark    = ( null === $step['done'] ) ? '–' : ( $is_done ? '✓' : '○' );
					?>
					<li class="unysonplus-onboarding__step<?php echo $is_done ? ' is-done' : ''; ?>">
						<span class="unysonplus-onboarding__mark" aria-hidden="true"><?php echo esc_html( $mark ); ?></span>
						<span class="unysonplus-onboarding__body">
							<strong><?php echo esc_html( $step['title'] ); ?></strong>
							<span class="unysonplus-onboarding__desc"><?php echo esc_html( $step['desc'] ); ?></span>
						</span>
						<a class="button<?php echo $is_done ? '' : ' button-primary'; ?>" href="<?php echo esc_url( $step['url'] ); ?>"><?php echo esc_html( $step['cta'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ol>

			<div class="unysonplus-onboarding__more">
				<h2><?php esc_html_e( 'Good to know', 'unysonplus' ); ?></h2>
				<ul>
					<li><?php
						printf(
							/* translators: %s: list of page template names */
							esc_html__( 'Page templates included: %s — pick one from the Page editor → Template box.', 'unysonplus' ),
							'<code>Full Width</code>, <code>Landing</code>, <code>No Header</code>, <code>No Footer</code>, <code>Boxed Narrow</code>, <code>Sidebar Left/Right</code>'
						);
					?></li>
					<li><?php esc_html_e( 'Header & Footer are built visually in Theme Settings → Header / Footer (drag-and-drop columns).', 'unysonplus' ); ?></li>
					<li><?php esc_html_e( 'Export or import all your theme settings from Theme Settings → Miscellaneous.', 'unysonplus' ); ?></li>
				</ul>
			</div>
		</div>

		<style>
			.unysonplus-onboarding__lead { font-size: 14px; max-width: 70ch; color: #50575e; }
			.unysonplus-onboarding__steps { margin: 1.5rem 0; padding: 0; list-style: none; max-width: 920px; }
			.unysonplus-onboarding__step { display: flex; align-items: center; gap: 1rem; background: #fff; border: 1px solid #dcdcde; border-radius: 8px; padding: 1rem 1.25rem; margin: 0 0 .75rem; }
			.unysonplus-onboarding__step.is-done { opacity: .72; }
			.unysonplus-onboarding__mark { flex: 0 0 1.75rem; width: 1.75rem; height: 1.75rem; line-height: 1.75rem; text-align: center; border-radius: 50%; background: #f0f0f1; font-weight: 700; }
			.unysonplus-onboarding__step.is-done .unysonplus-onboarding__mark { background: #00a32a; color: #fff; }
			.unysonplus-onboarding__body { flex: 1 1 auto; display: flex; flex-direction: column; gap: .15rem; }
			.unysonplus-onboarding__desc { color: #646970; font-size: 13px; }
			.unysonplus-onboarding__step .button { flex: 0 0 auto; }
			.unysonplus-onboarding__more { max-width: 920px; }
			.unysonplus-onboarding__more ul { list-style: disc; padding-left: 1.25rem; color: #50575e; }
		</style>
		<?php
	}
endif;

/* -------------------------------------------------------------------------
 * First-run welcome notice — shown until the user opens Getting Started or
 * dismisses it. Per-user (user meta), so each admin sees it once.
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'unysonplus_onboarding_flag_on_activation' ) ) :
	/** On theme activation, clear the dismissed flag so the notice re-greets. */
	function unysonplus_onboarding_flag_on_activation() {
		delete_metadata( 'user', 0, 'unysonplus_onboarding_dismissed', '', true );
	}
	add_action( 'after_switch_theme', 'unysonplus_onboarding_flag_on_activation' );
endif;

if ( ! function_exists( 'unysonplus_onboarding_notice' ) ) :
	/** The dismissible welcome notice. */
	function unysonplus_onboarding_notice() {
		if ( ! current_user_can( 'edit_theme_options' ) ) { return; }
		$screen = get_current_screen();
		if ( $screen && 'appearance_page_unysonplus-getting-started' === $screen->id ) { return; } // don't nag on the page itself
		if ( get_user_meta( get_current_user_id(), 'unysonplus_onboarding_dismissed', true ) ) { return; }

		$page_url    = admin_url( 'themes.php?page=unysonplus-getting-started' );
		$dismiss_url = wp_nonce_url( add_query_arg( 'unysonplus_dismiss_onboarding', '1' ), 'unysonplus_dismiss_onboarding' );
		?>
		<div class="notice notice-info is-dismissible">
			<p>
				<strong><?php esc_html_e( 'UnysonPlus theme', 'unysonplus' ); ?>:</strong>
				<?php esc_html_e( 'Welcome! A short setup checklist will help your new site look complete.', 'unysonplus' ); ?>
				<a href="<?php echo esc_url( $page_url ); ?>" class="button button-primary" style="margin-left:.5rem"><?php esc_html_e( 'Get started', 'unysonplus' ); ?></a>
				<a href="<?php echo esc_url( $dismiss_url ); ?>" style="margin-left:.5rem"><?php esc_html_e( 'Dismiss', 'unysonplus' ); ?></a>
			</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'unysonplus_onboarding_notice' );
endif;

if ( ! function_exists( 'unysonplus_onboarding_handle_dismiss' ) ) :
	/** Persist the dismissal (also fired by the native "x" via no-op reload). */
	function unysonplus_onboarding_handle_dismiss() {
		if ( empty( $_GET['unysonplus_dismiss_onboarding'] ) ) { return; }
		if ( ! current_user_can( 'edit_theme_options' ) ) { return; }
		check_admin_referer( 'unysonplus_dismiss_onboarding' );
		update_user_meta( get_current_user_id(), 'unysonplus_onboarding_dismissed', 1 );
		wp_safe_redirect( remove_query_arg( array( 'unysonplus_dismiss_onboarding', '_wpnonce' ) ) );
		exit;
	}
	add_action( 'admin_init', 'unysonplus_onboarding_handle_dismiss' );
endif;
