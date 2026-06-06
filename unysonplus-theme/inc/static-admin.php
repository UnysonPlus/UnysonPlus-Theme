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