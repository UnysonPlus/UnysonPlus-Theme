<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Portfolio → Single Project sub-tab.
 *
 * Display settings for the single-project view. Stored under the
 * `portfolio_single` multi; bridged into the Portfolio extension via
 * unysonplus_portfolio_get() + the fw:ext:portfolio:setting filter
 * (inc/includes/portfolio.php). "Inherit" defers to the extension's own
 * Settings-page value.
 */

$options = [
	'portfolio_single' => [
		'type'          => 'multi',
		'label'         => false,
		'desc'          => false,
		'inner-options' => [
			'show_gallery_single' => [
				'label'   => __( 'Project Gallery', 'unysonplus' ),
				'desc'    => __( 'The project\'s image gallery above the content.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (extension setting)', 'unysonplus' ),
					'yes'     => __( 'Show', 'unysonplus' ),
					'no'      => __( 'Hide', 'unysonplus' ),
				],
			],
			'single_columns' => [
				'label'   => __( 'Gallery Columns', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (extension setting)', 'unysonplus' ),
					'1'       => '1',
					'2'       => '2',
					'3'       => '3',
					'4'       => '4',
					'5'       => '5',
					'6'       => '6',
				],
			],
			'show_meta_single' => [
				'label'   => __( 'Project Details List', 'unysonplus' ),
				'desc'    => __( 'Client, date, services and website beneath the content.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (extension setting)', 'unysonplus' ),
					'yes'     => __( 'Show', 'unysonplus' ),
					'no'      => __( 'Hide', 'unysonplus' ),
				],
			],
			'enable_prevnext' => [
				'label'   => __( 'Previous / Next Navigation', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (extension setting)', 'unysonplus' ),
					'yes'     => __( 'Show', 'unysonplus' ),
					'no'      => __( 'Hide', 'unysonplus' ),
				],
			],
			'prevnext_same_category' => [
				'label'   => __( 'Navigate Within The Same Category', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (extension setting)', 'unysonplus' ),
					'yes'     => __( 'Yes', 'unysonplus' ),
					'no'      => __( 'No', 'unysonplus' ),
				],
			],
			'enable_related' => [
				'label'   => __( 'Related Projects', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (extension setting)', 'unysonplus' ),
					'yes'     => __( 'Show', 'unysonplus' ),
					'no'      => __( 'Hide', 'unysonplus' ),
				],
			],
			'related_count' => [
				'label' => __( 'Related Count', 'unysonplus' ),
				'desc'  => __( 'How many related projects to display. Leave empty to inherit.', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			],
			'related_heading' => [
				'label' => __( 'Related Heading', 'unysonplus' ),
				'desc'  => __( 'Heading above the related projects. Leave empty to inherit.', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			],
		],
	],
];
