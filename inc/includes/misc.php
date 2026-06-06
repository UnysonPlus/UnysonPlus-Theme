<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }

/**
 * Miscellaneous tab — runtime handlers.
 *
 * Each option group declared in
 * framework-customizations/theme/options/misc.php is wired here to the
 * appropriate WordPress action / filter. Everything no-ops gracefully
 * when the Unyson+ plugin is inactive or the option is unset.
 */

if ( ! function_exists( 'unysonplus_misc_get' ) ) :
/**
 * Read a Misc option.
 *
 * Each sub-tab in framework-customizations/theme/options/misc.php wraps
 * its leaf fields in a `multi` container (misc_scroll_top,
 * misc_dark_mode, …). This getter maps each known key to its bucket
 * and reads from there. Falls back to a flat read for unknown keys
 * (e.g., legacy callers or pre-restructure data).
 *
 * @param string $key
 * @param mixed  $default
 * @return mixed
 */
function unysonplus_misc_get( $key, $default = '' ) {
	if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return $default; }

	static $key_to_bucket = array(
		// Scroll to Top
		'scroll_top_enable'           => 'misc_scroll_top',
		'scroll_top_position'         => 'misc_scroll_top',
		'scroll_top_offset'           => 'misc_scroll_top',
		'scroll_top_text'             => 'misc_scroll_top',
		'scroll_top_bg_color'         => 'misc_scroll_top',
		'scroll_top_text_color'       => 'misc_scroll_top',

		// Dark Mode
		'dark_mode_enable'            => 'misc_dark_mode',
		'dark_mode_default'           => 'misc_dark_mode',
		'dark_mode_position'          => 'misc_dark_mode',
		'dark_mode_show_label'        => 'misc_dark_mode',

		// Custom CSS
		'custom_css'                  => 'misc_custom_css',

		// Custom Scripts
		'custom_head_scripts'         => 'misc_custom_scripts',
		'custom_body_open_scripts'    => 'misc_custom_scripts',
		'custom_footer_scripts'       => 'misc_custom_scripts',

		// Analytics
		'analytics_ga4_id'            => 'misc_analytics',
		'analytics_gtm_id'            => 'misc_analytics',
		'analytics_meta_pixel_id'     => 'misc_analytics',
		'analytics_clarity_id'        => 'misc_analytics',

		// Performance
		'perf_disable_emojis'         => 'misc_performance',
		'perf_disable_embeds'         => 'misc_performance',
		'perf_remove_rsd_wlw'         => 'misc_performance',
		'perf_disable_jquery_migrate' => 'misc_performance',
		'perf_remove_version_meta'    => 'misc_performance',
		'perf_disable_xmlrpc'         => 'misc_performance',

		// 404
		'404_page_id'                 => 'misc_404',
		'404_show_search'             => 'misc_404',
		'404_show_recent_posts'       => 'misc_404',

		// Maintenance
		'maintenance_enabled'         => 'misc_maintenance',
		'maintenance_title'           => 'misc_maintenance',
		'maintenance_message'         => 'misc_maintenance',
		'maintenance_logo'            => 'misc_maintenance',
		'maintenance_allowed_roles'   => 'misc_maintenance',
	);

	if ( isset( $key_to_bucket[ $key ] ) ) {
		$bucket = fw_get_db_settings_option( $key_to_bucket[ $key ], array() );
		if ( is_array( $bucket ) && array_key_exists( $key, $bucket ) ) {
			$val = $bucket[ $key ];
			if ( $val !== null && $val !== '' ) {
				// Unyson stores `switch` fields inside `multi` containers as
				// boolean true/false. Handlers everywhere check `=== 'yes'`,
				// so normalize bools back to the expected string form.
				if ( is_bool( $val ) ) { return $val ? 'yes' : 'no'; }
				return $val;
			}
		}
		return $default;
	}

	// Unknown key — fall back to flat read.
	$val = fw_get_db_settings_option( $key, null );
	if ( $val !== null && $val !== '' ) {
		if ( is_bool( $val ) ) { return $val ? 'yes' : 'no'; }
		return $val;
	}
	return $default;
}
endif;




/* ============================================================
 * Scroll to Top
 * ============================================================ */

if ( ! function_exists( 'unysonplus_render_scroll_top_button' ) ) :
function unysonplus_render_scroll_top_button() {
	if ( unysonplus_misc_get( 'scroll_top_enable' ) !== 'yes' ) { return; }

	$position   = unysonplus_misc_get( 'scroll_top_position', 'right' );
	// scroll_top_offset is a unit-input {value,unit} (px / vh). vh is resolved to
	// pixels at runtime by scroll-top.js (depends on viewport height). Accept a
	// legacy plain string/number too — treat it as px.
	$offset_raw  = unysonplus_misc_get( 'scroll_top_offset', array( 'value' => '300', 'unit' => 'px' ) );
	if ( is_array( $offset_raw ) ) {
		$offset_val  = ( isset( $offset_raw['value'] ) && $offset_raw['value'] !== '' ) ? (float) $offset_raw['value'] : 300;
		$offset_unit = ( isset( $offset_raw['unit'] ) && $offset_raw['unit'] === 'vh' ) ? 'vh' : 'px';
	} else {
		$offset_val  = ( $offset_raw !== '' ) ? (float) $offset_raw : 300;
		$offset_unit = 'px';
	}
	$offset_val = max( 0, $offset_val );
	$text       = unysonplus_misc_get( 'scroll_top_text', '' );
	$bg         = unysonplus_misc_get( 'scroll_top_bg_color', '' );
	$fg         = unysonplus_misc_get( 'scroll_top_text_color', '#ffffff' );

	$style_parts = array();
	if ( $bg !== '' ) { $style_parts[] = 'background-color:' . $bg; }
	if ( $fg !== '' ) { $style_parts[] = 'color:' . $fg; }
	$style_attr = $style_parts ? ' style="' . esc_attr( implode( ';', $style_parts ) ) . '"' : '';

	$position = ( $position === 'left' ) ? 'left' : 'right';
	?>
	<button type="button"
	        class="scroll-to-top scroll-to-top--<?php echo esc_attr( $position ); ?>"
	        data-offset="<?php echo esc_attr( $offset_val ); ?>"
	        data-offset-unit="<?php echo esc_attr( $offset_unit ); ?>"
	        aria-label="<?php esc_attr_e( 'Scroll to top', 'unysonplus' ); ?>"
	        hidden<?php echo $style_attr; ?>>
		<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
			<path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/>
		</svg>
		<?php if ( $text !== '' ) : ?>
			<span class="scroll-to-top__label"><?php echo esc_html( $text ); ?></span>
		<?php endif; ?>
	</button>
	<?php
}
endif;
add_action( 'wp_footer', 'unysonplus_render_scroll_top_button', 20 );


/* ============================================================
 * Dark Mode
 * ============================================================ */

if ( ! function_exists( 'unysonplus_dark_mode_is_enabled' ) ) :
function unysonplus_dark_mode_is_enabled() {
	return unysonplus_misc_get( 'dark_mode_enable' ) === 'yes';
}
endif;

if ( ! function_exists( 'unysonplus_emit_dark_mode_boot' ) ) :
/**
 * Inline no-FOUC bootstrap script. Runs in <head> before stylesheets
 * load, sets data-bs-theme + data-theme-mode on <html> from
 * localStorage or the admin-set default.
 */
function unysonplus_emit_dark_mode_boot() {
	if ( ! unysonplus_dark_mode_is_enabled() ) { return; }

	$default = unysonplus_misc_get( 'dark_mode_default', 'auto' );
	if ( ! in_array( $default, array( 'auto', 'light', 'dark' ), true ) ) {
		$default = 'auto';
	}
	?>
<script id="unysonplus-theme-mode-boot">
(function(){
	var d='<?php echo esc_js( $default ); ?>';
	var key='unysonplus-theme-mode';
	var stored=null;
	try{stored=window.localStorage.getItem(key);}catch(e){}
	var mode=stored||d;
	if(mode!=='light'&&mode!=='dark'&&mode!=='auto'){mode=d;}
	var actual=mode==='auto'
		?(window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light')
		:mode;
	var h=document.documentElement;
	h.setAttribute('data-bs-theme',actual);
	h.setAttribute('data-theme-mode',mode);
})();
</script>
	<?php
}
endif;
add_action( 'wp_head', 'unysonplus_emit_dark_mode_boot', 1 );

if ( ! function_exists( 'unysonplus_render_dark_mode_toggle' ) ) :
function unysonplus_render_dark_mode_toggle() {
	if ( ! unysonplus_dark_mode_is_enabled() ) { return; }

	$default = unysonplus_misc_get( 'dark_mode_default', 'auto' );
	if ( ! in_array( $default, array( 'auto', 'light', 'dark' ), true ) ) {
		$default = 'auto';
	}

	$position = unysonplus_misc_get( 'dark_mode_position', 'bottom-left' );
	$valid_positions = array( 'bottom-left', 'bottom-right', 'top-left', 'top-right' );
	if ( ! in_array( $position, $valid_positions, true ) ) {
		$position = 'bottom-left';
	}

	$show_label = unysonplus_misc_get( 'dark_mode_show_label' ) === 'yes';

	$classes = array( 'theme-toggle', 'theme-toggle--' . $position );
	if ( $show_label ) { $classes[] = 'theme-toggle--with-label'; }
	?>
	<button type="button"
	        class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
	        data-default-mode="<?php echo esc_attr( $default ); ?>"
	        aria-label="<?php esc_attr_e( 'Toggle color mode', 'unysonplus' ); ?>">
		<span class="theme-toggle__icon theme-toggle__icon--light" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/></svg>
		</span>
		<span class="theme-toggle__icon theme-toggle__icon--dark" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.78.78 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278z"/></svg>
		</span>
		<span class="theme-toggle__icon theme-toggle__icon--auto" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M8 15A7 7 0 1 1 8 1v14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/></svg>
		</span>
		<span class="theme-toggle__label"
		      data-label-light="<?php esc_attr_e( 'Light', 'unysonplus' ); ?>"
		      data-label-dark="<?php esc_attr_e( 'Dark', 'unysonplus' ); ?>"
		      data-label-auto="<?php esc_attr_e( 'Auto', 'unysonplus' ); ?>"></span>
	</button>
	<?php
}
endif;
add_action( 'wp_footer', 'unysonplus_render_dark_mode_toggle', 20 );


/* ============================================================
 * Custom CSS
 * ============================================================ */

if ( ! function_exists( 'unysonplus_get_site_custom_css' ) ) :
function unysonplus_get_site_custom_css() {
	$css = trim( (string) unysonplus_misc_get( 'custom_css', '' ) );
	return $css === '' ? '' : wp_strip_all_tags( $css );
}
endif;

// Contribute the site-wide Custom CSS to the plugin's presets stylesheet (so
// it's combiner-absorbed and no longer its own inline <style> block).
if ( ! function_exists( 'unysonplus_filter_global_css' ) ) :
function unysonplus_filter_global_css( $css ) {
	$own = unysonplus_get_site_custom_css();
	if ( $own === '' ) { return $css; }
	return $css === '' ? $own : $css . "\n" . $own;
}
endif;
add_filter( 'unysonplus_global_css', 'unysonplus_filter_global_css' );

// Legacy inline fallback: only when the plugin's presets pipeline is NOT
// loaded (plugin inactive) — otherwise the CSS is folded into presets-*.css
// and emitting here too would duplicate it.
if ( ! function_exists( 'unysonplus_emit_custom_css' ) ) :
function unysonplus_emit_custom_css() {
	if ( function_exists( 'unysonplus_build_presets_css_string' ) ) { return; }
	$css = unysonplus_get_site_custom_css();
	if ( $css === '' ) { return; }
	echo '<style id="unysonplus-custom-css">' . "\n" . $css . "\n" . '</style>' . "\n";
}
endif;
add_action( 'wp_head', 'unysonplus_emit_custom_css', 999 );


/* ============================================================
 * Custom Head / Body-Open / Footer Scripts
 * ============================================================ */

if ( ! function_exists( 'unysonplus_emit_head_scripts' ) ) :
function unysonplus_emit_head_scripts() {
	$html = (string) unysonplus_misc_get( 'custom_head_scripts', '' );
	if ( trim( $html ) === '' ) { return; }
	echo "\n" . $html . "\n";
}
endif;
add_action( 'wp_head', 'unysonplus_emit_head_scripts', 999 );

if ( ! function_exists( 'unysonplus_emit_body_open_scripts' ) ) :
function unysonplus_emit_body_open_scripts() {
	$html = (string) unysonplus_misc_get( 'custom_body_open_scripts', '' );
	if ( trim( $html ) === '' ) { return; }
	echo "\n" . $html . "\n";
}
endif;
add_action( 'wp_body_open', 'unysonplus_emit_body_open_scripts', 5 );

if ( ! function_exists( 'unysonplus_emit_footer_scripts' ) ) :
function unysonplus_emit_footer_scripts() {
	$html = (string) unysonplus_misc_get( 'custom_footer_scripts', '' );
	if ( trim( $html ) === '' ) { return; }
	echo "\n" . $html . "\n";
}
endif;
add_action( 'wp_footer', 'unysonplus_emit_footer_scripts', 999 );


/* ============================================================
 * Analytics & Tracking
 * ============================================================ */

if ( ! function_exists( 'unysonplus_emit_analytics_head' ) ) :
function unysonplus_emit_analytics_head() {
	$ga4     = trim( (string) unysonplus_misc_get( 'analytics_ga4_id', '' ) );
	$gtm     = trim( (string) unysonplus_misc_get( 'analytics_gtm_id', '' ) );
	$pixel   = trim( (string) unysonplus_misc_get( 'analytics_meta_pixel_id', '' ) );
	$clarity = trim( (string) unysonplus_misc_get( 'analytics_clarity_id', '' ) );

	if ( $ga4 !== '' ) {
		$id = preg_replace( '/[^A-Za-z0-9_-]/', '', $ga4 );
		?>
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $id ); ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js( $id ); ?>');</script>
		<?php
	}

	if ( $gtm !== '' ) {
		$id = preg_replace( '/[^A-Za-z0-9_-]/', '', $gtm );
		?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $id ); ?>');</script>
		<?php
	}

	if ( $pixel !== '' ) {
		$id = preg_replace( '/[^0-9]/', '', $pixel );
		?>
<!-- Meta Pixel -->
<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','<?php echo esc_js( $id ); ?>');fbq('track','PageView');</script>
		<?php
	}

	if ( $clarity !== '' ) {
		$id = preg_replace( '/[^A-Za-z0-9]/', '', $clarity );
		?>
<!-- Microsoft Clarity -->
<script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y)})(window,document,"clarity","script","<?php echo esc_js( $id ); ?>");</script>
		<?php
	}
}
endif;
add_action( 'wp_head', 'unysonplus_emit_analytics_head', 25 );

if ( ! function_exists( 'unysonplus_emit_analytics_body_open' ) ) :
function unysonplus_emit_analytics_body_open() {
	$gtm   = trim( (string) unysonplus_misc_get( 'analytics_gtm_id', '' ) );
	$pixel = trim( (string) unysonplus_misc_get( 'analytics_meta_pixel_id', '' ) );

	if ( $gtm !== '' ) {
		$id = preg_replace( '/[^A-Za-z0-9_-]/', '', $gtm );
		echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $id ) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
	}
	if ( $pixel !== '' ) {
		$id = preg_replace( '/[^0-9]/', '', $pixel );
		echo '<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=' . esc_attr( $id ) . '&ev=PageView&noscript=1" alt=""></noscript>' . "\n";
	}
}
endif;
add_action( 'wp_body_open', 'unysonplus_emit_analytics_body_open', 10 );


/* ============================================================
 * Performance Tweaks
 * ============================================================ */

if ( ! function_exists( 'unysonplus_apply_perf_tweaks' ) ) :
function unysonplus_apply_perf_tweaks() {
	if ( unysonplus_misc_get( 'perf_disable_emojis' ) === 'yes' ) {
		remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles',     'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles',  'print_emoji_styles' );
		remove_filter( 'the_content_feed',    'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss',    'wp_staticize_emoji' );
		remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email' );
	}

	if ( unysonplus_misc_get( 'perf_disable_embeds' ) === 'yes' ) {
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		add_action( 'wp_enqueue_scripts', function () { wp_dequeue_script( 'wp-embed' ); }, 100 );
	}

	if ( unysonplus_misc_get( 'perf_remove_rsd_wlw' ) === 'yes' ) {
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
	}

	if ( unysonplus_misc_get( 'perf_remove_version_meta' ) === 'yes' ) {
		remove_action( 'wp_head', 'wp_generator' );
	}

	if ( unysonplus_misc_get( 'perf_disable_xmlrpc' ) === 'yes' ) {
		add_filter( 'xmlrpc_enabled', '__return_false' );
	}
}
endif;
add_action( 'init', 'unysonplus_apply_perf_tweaks', 15 );

if ( ! function_exists( 'unysonplus_perf_disable_jquery_migrate' ) ) :
function unysonplus_perf_disable_jquery_migrate( $scripts ) {
	if ( is_admin() ) { return; }
	if ( unysonplus_misc_get( 'perf_disable_jquery_migrate' ) !== 'yes' ) { return; }
	if ( ! empty( $scripts->registered['jquery'] ) ) {
		$jquery = $scripts->registered['jquery'];
		if ( ! empty( $jquery->deps ) ) {
			$jquery->deps = array_diff( $jquery->deps, array( 'jquery-migrate' ) );
		}
	}
}
endif;
add_action( 'wp_default_scripts', 'unysonplus_perf_disable_jquery_migrate' );


/* ============================================================
 * 404 Page (page picker — branch lives in 404.php)
 * ============================================================ */

if ( ! function_exists( 'unysonplus_misc_404_page_id' ) ) :
/**
 * Resolve the configured 404 replacement page ID. Returns 0 when the
 * option is unset or points at a missing / unpublished post.
 */
function unysonplus_misc_404_page_id() {
	$id = (int) unysonplus_misc_get( '404_page_id', 0 );
	if ( $id <= 0 ) { return 0; }
	$post = get_post( $id );
	if ( ! $post || $post->post_status !== 'publish' || $post->post_type !== 'page' ) {
		return 0;
	}
	return $id;
}
endif;


/* ============================================================
 * Maintenance Mode
 * ============================================================ */

if ( ! function_exists( 'unysonplus_maintenance_user_is_allowed' ) ) :
function unysonplus_maintenance_user_is_allowed() {
	if ( ! is_user_logged_in() ) { return false; }
	$allowed = unysonplus_misc_get( 'maintenance_allowed_roles', array( 'administrator' ) );
	if ( ! is_array( $allowed ) ) { $allowed = array( 'administrator' ); }
	$user = wp_get_current_user();
	if ( ! $user || empty( $user->roles ) ) { return false; }
	return (bool) array_intersect( $allowed, (array) $user->roles );
}
endif;

if ( ! function_exists( 'unysonplus_maintenance_redirect' ) ) :
function unysonplus_maintenance_redirect() {
	if ( unysonplus_misc_get( 'maintenance_enabled' ) !== 'yes' ) { return; }
	if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) { return; }
	if ( wp_doing_ajax() || wp_doing_cron() ) { return; }
	if ( $GLOBALS['pagenow'] === 'wp-login.php' ) { return; }
	if ( unysonplus_maintenance_user_is_allowed() ) { return; }

	$title   = (string) unysonplus_misc_get( 'maintenance_title', __( "We'll be right back", 'unysonplus' ) );
	$message = (string) unysonplus_misc_get( 'maintenance_message', '' );
	$logo    = unysonplus_misc_get( 'maintenance_logo', '' );
	$logo_url = ( is_array( $logo ) && ! empty( $logo['url'] ) ) ? $logo['url'] : '';

	nocache_headers();
	status_header( 503 );
	header( 'Retry-After: 3600' );
	header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
	?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<meta name="robots" content="noindex,nofollow">
	<title><?php echo esc_html( $title ); ?> &mdash; <?php bloginfo( 'name' ); ?></title>
	<style>
		html,body{margin:0;padding:0;height:100%;font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;background:#f5f5f5;color:#212529;}
		body{display:flex;align-items:center;justify-content:center;padding:2rem;}
		.maintenance-page{max-width:600px;width:100%;background:#fff;border-radius:8px;padding:2.5rem;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,0.08);}
		.maintenance-page__logo{max-width:200px;height:auto;margin:0 auto 1.5rem;display:block;}
		.maintenance-page__title{margin:0 0 1rem;font-size:1.75rem;}
		.maintenance-page__body{font-size:1rem;line-height:1.6;color:#555;}
		.maintenance-page__body :last-child{margin-bottom:0;}
	</style>
</head>
<body>
	<main class="maintenance-page" role="main">
		<?php if ( $logo_url ) : ?>
			<img class="maintenance-page__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		<?php endif; ?>
		<h1 class="maintenance-page__title"><?php echo esc_html( $title ); ?></h1>
		<div class="maintenance-page__body"><?php echo wp_kses_post( wpautop( $message ) ); ?></div>
	</main>
</body>
</html>
	<?php
	exit;
}
endif;
add_action( 'template_redirect', 'unysonplus_maintenance_redirect', 0 );
