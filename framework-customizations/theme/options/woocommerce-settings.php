<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * WooCommerce top-level Theme Settings tab — a POINTER, not a duplicate.
 *
 * The actual shop / catalog / single-product / behavior settings are owned by the
 * WooCommerce *extension* (Unyson+ → Extensions → WooCommerce), which bridges them
 * to the theme's `unysonplus_woocommerce_*` filters. This tab just links there so
 * the settings are discoverable from Theme Settings without a conflicting second
 * copy. Only aggregated into the form when WooCommerce is active (see settings.php).
 */

$woo_ext_url = admin_url( 'admin.php?page=fw-extensions&sub-page=extension&extension=woocommerce' );

$woo_pointer_html = '<div style="max-width:70ch">'
	. '<p><strong>' . esc_html__( 'WooCommerce shop &amp; catalog settings are managed by the WooCommerce extension.', 'unysonplus' ) . '</strong></p>'
	. '<p>' . esc_html__( 'Columns, products per page, sidebar, single-product gallery, catalog mode, sale badge, AJAX cart and breadcrumb live there (and apply through this theme automatically). Enable it under Unyson+ → Extensions if it is not already, then configure it.', 'unysonplus' ) . '</p>'
	. '<p><a class="button button-primary" href="' . esc_url( $woo_ext_url ) . '">' . esc_html__( 'Open WooCommerce Extension Settings', 'unysonplus' ) . '</a></p>'
	. '</div>';

$options = [
	'woocommerce_container' => [
		'title'   => __( 'WooCommerce', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'woocommerce' => [
				'title'   => __( 'WooCommerce', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					'woo_pointer' => [
						'type'  => 'html-full',
						'label' => false,
						'html'  => $woo_pointer_html,
					],
				],
			],
		],
	],
];
