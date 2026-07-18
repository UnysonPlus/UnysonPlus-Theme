<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * General → Base sub-tab — small site-wide "polish" styles that no other tab owns:
 * text-selection color, text-selection / copy protection, a custom scrollbar, and
 * the keyboard focus outline.
 *
 * Stored under the `general_base` multi key; merged into the layout read set by
 * theme-vars.php, which emits the --selection-* / --scrollbar-* / --focus-* CSS
 * vars consumed by style.css. Every field is opt-in (empty/Off = browser/theme
 * default), so the tab is fully backward-compatible. The custom scrollbar is gated
 * by the `custom-scrollbar` body class (layout.php) so styling only kicks in when a
 * color is set — a bare `::-webkit-scrollbar` rule would otherwise restyle every
 * default scrollbar. The Content-protection switches likewise add opt-in body
 * classes (up-noselect / up-nocontext / up-nocopy) that style.css + theme.js key off.
 */

/* Palette-preset color control (falls back to a raw picker if the shortcodes helper
   isn't loaded). kind 'bg' → bg-{slug} choices, 'text' → text-{slug}. */
$color = function ( $label, $desc, $kind ) {
	if ( function_exists( 'sc_color_field_compact' ) ) {
		return sc_color_field_compact( [ 'label' => $label, 'desc' => $desc, 'kind' => $kind ] );
	}
	return [ 'label' => $label, 'desc' => $desc, 'type' => 'color-picker', 'value' => '' ];
};

$options = [
	'general_base' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [

			/* ===== Text selection ===== */
			'group_base_selection' => [
				'type'    => 'group',
				'options' => [
					'base_selection_bg'    => $color( __( 'Selection Background', 'unysonplus' ), __( 'Highlight color when a visitor selects text. Leave empty for the browser default.', 'unysonplus' ), 'bg' ),
					'base_selection_color' => $color( __( 'Selection Text Color', 'unysonplus' ), __( 'Text color inside a selection. Leave empty to keep the text\'s own color.', 'unysonplus' ), 'text' ),
				],
			],

			/* ===== Content protection (opt-in deterrents) ===== */
			'group_base_protection' => [
				'type'    => 'group',
				'options' => [
					'base_disable_text_selection' => [
						'type'         => 'switch',
						'label'        => __( 'Disable Text Selection', 'unysonplus' ),
						'desc'         => __( 'Stop visitors from selecting / highlighting text on the site — a light copy-paste deterrent. Form fields (search, logins, comments) stay selectable. NOTE: this only discourages casual copying; the content is still readable via View Source, Reader mode, or DevTools, so it is not real protection. Off by default.', 'unysonplus' ),
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
					],
					'base_disable_right_click' => [
						'type'         => 'switch',
						'label'        => __( 'Disable Right-Click', 'unysonplus' ),
						'desc'         => __( 'Block the right-click / long-press context menu (makes "Save image as…" and "Copy" less obvious). Deterrent only. Form fields keep their menu. Off by default.', 'unysonplus' ),
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
					],
					'base_disable_copy' => [
						'type'         => 'switch',
						'label'        => __( 'Disable Copy', 'unysonplus' ),
						'desc'         => __( 'Intercept copy / cut (Ctrl/Cmd+C, Ctrl/Cmd+X) of page content. Deterrent only; form fields stay copyable so search and logins work. Off by default.', 'unysonplus' ),
						'value'        => 'no',
						'right-choice' => [ 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ],
						'left-choice'  => [ 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ],
					],
				],
			],

			/* ===== Custom scrollbar ===== */
			'group_base_scrollbar' => [
				'type'    => 'group',
				'options' => [
					'base_scrollbar_color' => $color( __( 'Scrollbar Color', 'unysonplus' ), __( 'Color of the scrollbar thumb. Set this to enable the custom scrollbar; leave empty for the browser default.', 'unysonplus' ), 'bg' ),
					'base_scrollbar_width' => [
						'label' => __( 'Scrollbar Width', 'unysonplus' ),
						'desc'  => __( 'Thickness of the scrollbar (WebKit browsers). Only used when a Scrollbar Color is set.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px' ],
						'value' => [ 'value' => '10', 'unit' => 'px' ],
						'min'   => 0,
					],
				],
			],

			/* ===== Focus outline ===== */
			'group_base_focus' => [
				'type'    => 'group',
				'options' => [
					'base_focus_color' => $color( __( 'Focus Outline Color', 'unysonplus' ), __( 'Color of the keyboard-focus ring around links, buttons and fields. Leave empty to use the primary color.', 'unysonplus' ), 'bg' ),
					'base_focus_width' => [
						'label' => __( 'Focus Outline Width', 'unysonplus' ),
						'desc'  => __( 'Thickness of the focus ring. Leave empty for the default (2px). A visible focus ring is an accessibility feature — avoid removing it.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'px' ],
						'value' => [ 'value' => '', 'unit' => 'px' ],
						'min'   => 0,
					],
				],
			],
		],
	],
];
