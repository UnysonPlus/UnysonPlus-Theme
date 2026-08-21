<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

//wp_die($hook);
// Load only on ?page=mypluginname
wp_enqueue_style(
	'bootstrap-grid',
	get_template_directory_uri() . '/assets/css/bootstrap-grid.min.css',
	array(),
	'1.0'
);
wp_enqueue_style(
	'post-editor',
	get_template_directory_uri() . '/assets/css/post-editor.css',
	array(),
	'1.0'
);
wp_enqueue_style(
	'fw-settings',
	get_template_directory_uri() . '/assets/css/fw-settings.css'
);

// Typography tab — live specimen preview. Injects a preview panel at the top of the Typography
// options and renders H1–H6 + Body from the live field values. Enqueued unconditionally (this file
// is included from include_child_first(), so the admin_enqueue_scripts `$hook` arg is NOT in scope
// here — matching the sibling styles above, which also load on every admin page). The script
// self-guards: it only injects a panel when it finds the Typography fields, so it's inert elsewhere.
$upw_theme_ver = wp_get_theme()->get( 'Version' );
wp_enqueue_style(
	'unysonplus-typography-preview',
	get_template_directory_uri() . '/assets/css/typography-preview.css',
	array(),
	$upw_theme_ver
);
wp_enqueue_script(
	'unysonplus-typography-preview',
	get_template_directory_uri() . '/assets/js/typography-preview.js',
	array( 'jquery' ),
	$upw_theme_ver,
	true
);

// Make the specimen sit on the SITE's own canvas + ink so the preview reads like the front end
// (a dark site shows light text on a dark panel, not the invisible near-white-on-white it would
// otherwise be). Text = Typography body colour; background = Site Background — with a CONTRAST
// GUARD: many sites (this theme included) keep a white body token while their SECTIONS paint dark
// and the ink is light, so if bg and ink don't contrast we flip the panel to a dark/light canvas
// that matches the ink's mode. Colours resolve through the theme's own preset->CSS helper.
if ( function_exists( 'fw_get_db_settings_option' ) ) {
	$upw_res = function ( $v ) {
		if ( is_array( $v ) ) {
			if ( isset( $v['color']['value'] ) )      { $v = $v['color']['value']; }
			elseif ( isset( $v['value'] ) )           { $v = $v['value']; }
		}
		$c = function_exists( 'unysonplus_preset_color_to_css' ) ? unysonplus_preset_color_to_css( $v ) : ( is_string( $v ) ? $v : '' );
		return is_string( $c ) ? trim( $c ) : '';
	};
	$upw_lum = function ( $hex ) {
		$hex = ltrim( (string) $hex, '#' );
		if ( strlen( $hex ) === 3 ) { $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2]; }
		if ( ! preg_match( '/^[0-9a-f]{6}$/i', $hex ) ) { return null; }
		$r = hexdec( substr( $hex, 0, 2 ) ) / 255; $g = hexdec( substr( $hex, 2, 2 ) ) / 255; $b = hexdec( substr( $hex, 4, 2 ) ) / 255;
		return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
	};

	$upw_typo = (array) fw_get_db_settings_option( 'typography', array() );
	$upw_gl   = (array) fw_get_db_settings_option( 'general_layout', array() );

	$upw_text = '';
	if ( isset( $upw_typo['body']['color'] ) ) { $upw_text = $upw_res( $upw_typo['body']['color'] ); }
	if ( $upw_text === '' ) { $upw_text = '#212529'; }

	$upw_bg = isset( $upw_gl['site_background'] ) ? $upw_res( $upw_gl['site_background'] ) : '';
	if ( $upw_bg === '' ) { $upw_bg = '#ffffff'; }

	// Contrast guard — if the resolved bg and ink are too close (e.g. light ink on the white body
	// token of a dark-section site), replace bg with a canvas matching the ink's light/dark mode.
	$lt = $upw_lum( $upw_text ); $lb = $upw_lum( $upw_bg );
	if ( $lt !== null && $lb !== null && abs( $lt - $lb ) < 0.35 ) {
		$upw_bg = ( $lt > 0.5 ) ? '#17171b' : '#ffffff';
	}

	// Heading ink: an explicit per-heading colour wins in JS; otherwise headings share the body ink.
	wp_localize_script( 'unysonplus-typography-preview', 'upwTypoPrev', array(
		'bg'   => $upw_bg,
		'text' => $upw_text,
	) );
}
/*
if($hook == 'post.php' || $hook == 'post-new.php') {	
	wp_enqueue_style(
		'post-editor',
		get_template_directory_uri() . '/css/post-editor.css',
		array(),
		'1.0'
	);
} 

if($hook == 'appearance_page_fw-settings') {
	wp_enqueue_style( 
		'fw-settings', 
		get_template_directory_uri() . '/css/fw-settings.css' 
	);
}

/*wp_enqueue_script(
	'lastimosa-admin-theme-script',
	get_template_directory_uri() . '/js/admin-functions.js',
	array(),
	'1.0',
	true
); */