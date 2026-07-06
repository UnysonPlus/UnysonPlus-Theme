<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * General → Base sub-tab — small site-wide "polish" styles that no other tab owns:
 * text-selection color, a custom scrollbar, and the keyboard focus outline.
 *
 * Stored under the `general_base` multi key; merged into the layout read set by
 * theme-vars.php, which emits the --selection-* / --scrollbar-* / --focus-* CSS
 * vars consumed by style.css. Every field is opt-in (empty = browser/theme default),
 * so the tab is fully backward-compatible. The custom scrollbar is gated by the
 * `custom-scrollbar` body class (layout.php) so styling only kicks in when a color
 * is set — a bare `::-webkit-scrollbar` rule would otherwise restyle every default
 * scrollbar.
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
