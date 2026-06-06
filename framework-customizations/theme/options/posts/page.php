<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/*
 * Field groups for the consolidated "Page Settings" box (rendered as tabs).
 * Hoisted to variables so the tab structure below stays readable. The option
 * IDs are unchanged from when these lived in separate meta boxes, so existing
 * saved post meta and the runtime consumers (inc/includes/layout.php for the
 * hero header, framework dynamic-css for page_custom_css) keep working.
 */
$hero_header_options = [
	'header_image' => [
		'label' => __( 'Header Image', 'unysonplus' ),
		'desc'  => __( 'Full-width banner image at the top of the page.', 'unysonplus' ),
		'type'  => 'upload',
		'value' => [],
	],
	'header_height' => [
		'label'   => __( 'Header Height', 'unysonplus' ),
		'type'    => 'radio',
		'value'   => 'auto',
		'choices' => [
			'auto'       => __( 'Auto', 'unysonplus' ),
			'small'      => __( 'Small (220px)', 'unysonplus' ),
			'medium'     => __( 'Medium (380px)', 'unysonplus' ),
			'large'      => __( 'Large (560px)', 'unysonplus' ),
			'fullscreen' => __( 'Fullscreen (100vh)', 'unysonplus' ),
		],
	],
	'header_overlay_color' => [
		'label' => __( 'Overlay Color', 'unysonplus' ),
		'type'  => 'color-picker',
		'value' => '',
	],
	'header_overlay_opacity' => [
		'label'      => __( 'Overlay Opacity', 'unysonplus' ),
		'desc'       => __( '0 = transparent, 100 = opaque.', 'unysonplus' ),
		'type'       => 'slider',
		'value'      => 0,
		'properties' => [ 'min' => 0, 'max' => 100, 'step' => 5 ],
	],
	'header_content_position' => [
		'label'   => __( 'Title Position', 'unysonplus' ),
		'type'    => 'select',
		'value'   => 'center',
		'choices' => [
			'top'    => __( 'Top', 'unysonplus' ),
			'center' => __( 'Center', 'unysonplus' ),
			'bottom' => __( 'Bottom', 'unysonplus' ),
		],
	],
];

$custom_code_options = [
	'page_custom_css' => [
		'label'      => __( 'Custom CSS (this page only)', 'unysonplus' ),
		'desc'       => __( 'Emitted inline in &lt;head&gt;. Loaded only on this page.', 'unysonplus' ),
		'type'       => 'code-editor',
		'value'      => '',
		'mode'       => 'css', // top-level key the code-editor option type reads
	],
	'page_custom_js' => [
		'label'      => __( 'Custom JS (this page only)', 'unysonplus' ),
		'desc'       => __( 'Emitted inline before &lt;/body&gt;. Loaded only on this page.', 'unysonplus' ),
		'type'       => 'code-editor',
		'value'      => '',
		'mode'       => 'javascript',
	],
];

$options = [
	'page_side' => [
		'title'    => __( 'Page Options', 'unysonplus' ),
		'type'     => 'box',
		'context'  => 'side',
		'priority' => 'low',
		'options'  => [
			'page_settings_group' => [
				'type' => 'group',
				'options' => [
					'page_header' => [
						'label'   => __( 'Header Settings', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '',
						'desc'    => __( 'Options for the header on this page. ',	'unysonplus' ),
						'choices' => [
							''  => __( 'Default', 'unysonplus' ),
							'transparent' => __( 'Transparent', 'unysonplus' ),
							'd-none' => __( 'Hide the header on this page', 'unysonplus' ),
						],
					],
					'hide_page_title' => [
						'label' => false,
						'type'  => 'checkbox',
						'value' => false,
						'text'  => __( 'Hide Page Title', 'unysonplus' ),
					],
					'hide_footer_widgets' => [
						'label' => false,
						'type'  => 'checkbox',
						'value' => false,
						'text'  => __( 'Hide Footer Widgets', 'unysonplus' ),
					],
				],
			],
		],
	],

	'page_layout_overrides' => [
		'title'    => __( 'Layout Overrides', 'unysonplus' ),
		'type'     => 'box',
		'context'  => 'side',
		'priority' => 'low',
		'options'  => [
			'sidebar_override' => [
				'label'   => __( 'Sidebar Position', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'default',
				'choices' => [
					'default' => __( 'Default (from template / global)', 'unysonplus' ),
					'none'    => __( 'None', 'unysonplus' ),
					'left'    => __( 'Left', 'unysonplus' ),
					'right'   => __( 'Right', 'unysonplus' ),
				],
			],
			'content_width' => [
				'label'   => __( 'Content Width', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'default',
				'choices' => [
					'default' => __( 'Default', 'unysonplus' ),
					'narrow'  => __( 'Narrow (~720px)', 'unysonplus' ),
					'wide'    => __( 'Wide (100%)', 'unysonplus' ),
					'full'    => __( 'Full (edge-to-edge)', 'unysonplus' ),
				],
			],
			'page_bg_color' => [
				'label' => __( 'Page Background Color', 'unysonplus' ),
				'type'  => 'color-picker',
				'value' => '',
			],
			'page_bg_image' => [
				'label' => __( 'Page Background Image', 'unysonplus' ),
				'type'  => 'upload',
				'value' => [],
			],
		],
	],

	'page_visibility' => [
		'title'    => __( 'Visibility', 'unysonplus' ),
		'type'     => 'box',
		'context'  => 'side',
		'priority' => 'low',
		'options'  => [
			'hide_site_header' => [
				'label'        => __( 'Hide Site Header', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'No',  'unysonplus' ) ],
			],
			'hide_site_footer' => [
				'label'        => __( 'Hide Site Footer', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'No',  'unysonplus' ) ],
			],
			'hide_featured_image' => [
				'label'        => __( 'Hide Featured Image', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'No',  'unysonplus' ) ],
			],
			'show_breadcrumbs' => [
				'label'   => __( 'Show Breadcrumbs', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'default',
				'choices' => [
					'default' => __( 'Default', 'unysonplus' ),
					'yes'     => __( 'Yes', 'unysonplus' ),
					'no'      => __( 'No', 'unysonplus' ),
				],
			],
			'show_comments' => [
				'label'   => __( 'Show Comments', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'default',
				'choices' => [
					'default' => __( 'Default', 'unysonplus' ),
					'yes'     => __( 'Yes', 'unysonplus' ),
					'no'      => __( 'No', 'unysonplus' ),
				],
			],
		],
	],

	// Consolidated page-content settings: one postbox under the builder with
	// Hero Header / General / Custom Code as tabs (was three stacked boxes).
	'page_main_settings' => [
		'title'    => __( 'Page Settings', 'unysonplus' ),
		'type'     => 'box',
		'context'  => 'normal',
		'priority' => 'default',
		'options'  => [
			'tab_hero_header' => [
				'title'   => __( 'Hero Header', 'unysonplus' ),
				'type'    => 'tab',
				'options' => $hero_header_options,
			],
			'tab_general' => [
				'title'   => __( 'General', 'unysonplus' ),
				'type'    => 'tab',
				'options' => [
					fw()->theme->get_options( 'page-options' ),
				],
			],
			'tab_custom_code' => [
				'title'   => __( 'Custom Code', 'unysonplus' ),
				'type'    => 'tab',
				'options' => $custom_code_options,
			],
		],
	],
];
