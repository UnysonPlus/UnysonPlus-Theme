<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * Theme Includes
 */
require_once dirname( __FILE__ ) .'/inc/init.php';

/**
 *  Theme Update Checker - Enable automatic updates from GitHub (branch: master)
 */
if ( file_exists( dirname( __FILE__ ) . '/inc/plugin-update-checker/plugin-update-checker.php' ) ) {
	require_once dirname( __FILE__ ) . '/inc/plugin-update-checker/plugin-update-checker.php';

	$unysonplus_theme_update_checker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		'https://github.com/UnysonPlus/UnysonPlus-Theme/',
		dirname( __FILE__ ) . '/style.css', // style.css => PUC treats this as a theme
		'unysonplus-theme'                  // theme directory slug (must match install folder)
	);

	// Track the branch that holds the stable release (no GitHub release needed)
	$unysonplus_theme_update_checker->setBranch( 'master' );
}
