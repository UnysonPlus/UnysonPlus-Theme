<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['id'] = 'unysonplus';

// Extensions auto-activated on plugin install (FW activates these + their
// parents). Keep this lean — only the editing essentials. Everything else
// stays available for the user to enable from Unyson+ → Extensions on demand.
// ('page-builder' and 'wp-shortcodes' pull in their hidden parent 'shortcodes'.)
$manifest['supported_extensions'] = array(
	'page-builder'    => array(),
	'wp-shortcodes'   => array(),
	'theme-builder'   => array(),
	'asset-optimizer' => array(),
	'live-editor'     => array(),
	'snippets'        => array(),
);
