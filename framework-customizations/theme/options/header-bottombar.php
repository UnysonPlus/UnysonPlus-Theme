<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * HEADER → BOTTOM BAR — the optional row below the main header (secondary menu,
 * category nav, breadcrumbs …).
 *
 * No Enable switch: like the footer rows, the Bottom Bar renders only when at
 * least one of its columns has an element. Stored under the `header_bottombar`
 * multi key. All visual styling lives in the shared Custom Styling block
 * (`bottombar_custom_styling`) — output as utility classes (padding) or scoped
 * rules in the generated CSS file (bg / typography / link / borders), never
 * inline.
 */

$options = [
	'header_bottombar' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_bottombar' => [
				'type'    => 'group',
				'options' => [
					'bottombar_left'   => unysonplus_header_column( __( 'Bottom Bar — Left Column', 'unysonplus' ) ),
					'bottombar_center' => unysonplus_header_column( __( 'Bottom Bar — Center Column', 'unysonplus' ) ),
					'bottombar_right'  => unysonplus_header_column( __( 'Bottom Bar — Right Column', 'unysonplus' ) ),
					'bottombar_custom_styling' => unysonplus_hf_custom_styling( 'bottombar' ),
				],
			],
		],
	],
];
