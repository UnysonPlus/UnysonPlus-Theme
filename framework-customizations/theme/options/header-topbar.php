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
	// Quick-start: fill the columns with a ready-made top bar, then edit the elements.
	'topbar_presets' => [
		'type'         => 'preset-loader',
		'label'        => __( 'Top Bar Presets', 'unysonplus' ),
		'desc'         => __( 'Populate the columns below with sample top-bar content in one click, then fine-tune each element.', 'unysonplus' ),
		'preset_group' => 'header_topbar',
	],
	'header_topbar' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_topbar' => [
				'type'    => 'group',
				'options' => [
					'topbar_columns_note' => unysonplus_hf_columns_note(),
					/* Position & Motion lives on the MAIN header (Header → Layout), and its Sticky setting
					   pins `.site-header`, which contains this bar. That is the right default — a bar with a
					   phone number or a language switcher is often meant to stay reachable — but it is not the
					   commoner shape. A utility bar carrying an address, opening hours or a contact line is
					   usually written to scroll away while the nav alone pins, so the viewport is not spending
					   30-odd pixels on an address the visitor has already read. Measured across 114 captured
					   sites: 91 have a header, 68 of those pin it, and 47 pair a NON-sticky utility bar with a
					   sticky header — about half of every site with a header at all.
					   Implemented by moving `position:sticky` off `.site-header` and onto `.header-main`, so
					   the bar simply scrolls out of flow above it (no JS, exactly as a hand-built site does
					   it). Has no effect when the header is Static. */
					'topbar_unstick' => [
						'type'  => 'switch',
						'label' => __( 'Scrolls Away', 'unysonplus' ),
						'desc'  => __( 'Let the Top Bar scroll out of view while the main header stays pinned. Only applies when Header Position is Sticky or Transparent overlay.', 'unysonplus' ),
						'help'  => __( 'Off, the whole header pins on scroll and the Top Bar stays visible with it. On, only the main header row pins and the bar scrolls away with the page — the usual treatment for an address, opening hours or a contact line, which free up vertical space once read.', 'unysonplus' ),
						'value' => false,
					],
					'topbar_left'   => unysonplus_header_column( __( 'Top Bar — Left Column', 'unysonplus' ), [], 'header_topbar' ),
					'topbar_center' => unysonplus_header_column( __( 'Top Bar — Center Column', 'unysonplus' ), [], 'header_topbar' ),
					'topbar_right'  => unysonplus_header_column( __( 'Top Bar — Right Column', 'unysonplus' ), [], 'header_topbar' ),
					'topbar_custom_styling' => unysonplus_hf_custom_styling( 'topbar' ),
				],
			],
		],
	],
];
