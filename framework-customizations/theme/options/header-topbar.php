<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * HEADER → TOP BAR — the optional row above the main header (announcement,
 * contact info, secondary links, social icons …).
 *
 * No Enable switch: like the footer rows, the Top Bar renders only when at least
 * one of its columns has an element. Stored under the `header_topbar` multi key.
 * All visual styling lives in the shared Custom Styling block
 * (`topbar_custom_styling`) — output as utility classes (padding) or scoped rules
 * in the generated CSS file (bg / typography / link / borders), never inline.
 */

$options = [
	'header_topbar' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_topbar' => [
				'type'    => 'group',
				'options' => [
					'topbar_left'   => unysonplus_header_column( __( 'Top Bar — Left Column', 'unysonplus' ) ),
					'topbar_center' => unysonplus_header_column( __( 'Top Bar — Center Column', 'unysonplus' ) ),
					'topbar_right'  => unysonplus_header_column( __( 'Top Bar — Right Column', 'unysonplus' ) ),
					'topbar_custom_styling' => unysonplus_hf_custom_styling( 'topbar' ),
				],
			],
		],
	],
];
