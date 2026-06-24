<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */

$options = [
	fw()->theme->get_options( 'general-settings' ),
	fw()->theme->get_options( 'header-settings' ),
	fw()->theme->get_options( 'pages-settings' ),
	fw()->theme->get_options( 'blog-settings' ),
	fw()->theme->get_options( 'social-settings' ),
	fw()->theme->get_options( 'footer-settings' ),
];

// WooCommerce settings tab — only when WooCommerce is active.
if ( class_exists( 'WooCommerce' ) ) {
	$options[] = fw()->theme->get_options( 'woocommerce-settings' );
}

$options[] = fw()->theme->get_options( 'misc' );

/**
 * Developer Tools → "Show Demo Options" (Miscellaneous tab, off by default)
 * reveals the demo option-type showcase tab. Reads the saved switch value and
 * appends the demo tab only when enabled — keeps it out of clients' way.
 */
$dev_tools = function_exists( 'fw_get_db_settings_option' ) ? fw_get_db_settings_option( 'misc_dev_tools' ) : null;
if ( is_array( $dev_tools ) && ! empty( $dev_tools['dev_show_demo'] ) && $dev_tools['dev_show_demo'] === 'yes' ) {
	$options[] = fw()->theme->get_options( 'demo-box' );
}