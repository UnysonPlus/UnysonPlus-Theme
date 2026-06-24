<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * HEADER → MENU — primary navigation styling.
 *
 * Maps to the `--menu-*` CSS custom properties that style.css already consumes
 * (link color, hover/active color, link padding, dropdown background). Values are
 * folded into the generated front-end stylesheet by inc/includes/theme-vars.php;
 * unset fields fall back to the style.css defaults. Stored under `header_menu`.
 */

$options = [
	'header_menu' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'group_menu' => [
				'type'    => 'group',
				'options' => [
					'menu_link_color' => [
						'label' => __( 'Menu Link Color', 'unysonplus' ),
						'desc'  => __( 'Color of top-level menu links. Leave empty to use the body text color.', 'unysonplus' ),
						'type'  => 'color-picker',
						'value' => '',
					],
					'menu_link_hover_color' => [
						'label' => __( 'Menu Link Hover / Active Color', 'unysonplus' ),
						'desc'  => __( 'Color of menu links on hover and for the current page. Leave empty to use the primary color.', 'unysonplus' ),
						'type'  => 'color-picker',
						'value' => '',
					],
					'menu_link_padding_x' => [
						'label' => __( 'Link Horizontal Spacing', 'unysonplus' ),
						'desc'  => __( 'Left/right padding inside each menu link.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'menu_link_padding_y' => [
						'label' => __( 'Link Vertical Spacing', 'unysonplus' ),
						'desc'  => __( 'Top/bottom padding inside each menu link.', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => [ 'rem', 'px', 'em' ],
						'value' => [ 'value' => '', 'unit' => 'rem' ],
						'min'   => 0,
					],
					'menu_dropdown_bg' => [
						'label' => __( 'Dropdown Background', 'unysonplus' ),
						'desc'  => __( 'Background color of sub-menu dropdown panels.', 'unysonplus' ),
						'type'  => 'color-picker',
						'value' => '',
					],
				],
			],
		],
	],
];
