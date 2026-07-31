<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Portfolio → Archive & Cards sub-tab.
 *
 * Display settings for the portfolio archive / category pages and the project
 * cards. Stored under the `portfolio_archive` multi; read via
 * unysonplus_portfolio_get() (inc/includes/portfolio.php), which bridges the
 * values into the Portfolio extension through the fw:ext:portfolio:setting
 * filter. "Inherit" leaves the extension's own Settings-page value in charge.
 */

$options = [
	'portfolio_archive' => [
		'type'          => 'multi',
		'label'         => false,
		'desc'          => false,
		'inner-options' => [
			'archive_columns' => [
				'label'   => __( 'Archive Columns', 'unysonplus' ),
				'desc'    => __( 'Projects per row on the archive / category pages. Inherit uses the Portfolio extension setting.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (extension setting)', 'unysonplus' ),
					'1'       => '1',
					'2'       => '2',
					'3'       => '3',
					'4'       => '4',
				],
			],
			'archive_per_page' => [
				'label' => __( 'Projects Per Page', 'unysonplus' ),
				'desc'  => __( 'Projects per archive page before pagination. Leave empty to inherit.', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			],
			'orderby' => [
				'label'   => __( 'Order Projects By', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit'    => __( 'Inherit (extension setting)', 'unysonplus' ),
					'date'       => __( 'Date published', 'unysonplus' ),
					'menu_order' => __( 'Custom order (Page Attributes / Order)', 'unysonplus' ),
					'title'      => __( 'Title', 'unysonplus' ),
					'rand'       => __( 'Random', 'unysonplus' ),
				],
			],
			'order' => [
				'label'   => __( 'Order Direction', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (extension setting)', 'unysonplus' ),
					'DESC'    => __( 'Descending (newest / Z–A first)', 'unysonplus' ),
					'ASC'     => __( 'Ascending (oldest / A–Z first)', 'unysonplus' ),
				],
			],
			'featured_first' => [
				'label'   => __( 'Featured Projects First', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (extension setting)', 'unysonplus' ),
					'yes'     => __( 'Yes', 'unysonplus' ),
					'no'      => __( 'No', 'unysonplus' ),
				],
			],
			'archive_filter_bar' => [
				'label'   => __( 'Category Filter Bar', 'unysonplus' ),
				'desc'    => __( 'Category filter links above the archive grid (real category URLs — pagination-safe and crawlable).', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (extension setting)', 'unysonplus' ),
					'yes'     => __( 'Show', 'unysonplus' ),
					'no'      => __( 'Hide', 'unysonplus' ),
				],
			],
			'archive_gap' => [
				'label'   => __( 'Grid Gap', 'unysonplus' ),
				'desc'    => __( 'Spacing between project cards.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '24',
				'choices' => [
					'0'  => __( 'None', 'unysonplus' ),
					'12' => __( 'Tight (12px)', 'unysonplus' ),
					'16' => __( 'Compact (16px)', 'unysonplus' ),
					'24' => __( 'Normal (24px)', 'unysonplus' ),
					'32' => __( 'Roomy (32px)', 'unysonplus' ),
					'40' => __( 'Spacious (40px)', 'unysonplus' ),
				],
			],
			'archive_ratio' => [
				'label'   => __( 'Card Image Ratio', 'unysonplus' ),
				'desc'    => __( '"Original" keeps each image\'s natural proportions.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '4-3',
				'choices' => [
					'1-1'  => __( 'Square (1:1)', 'unysonplus' ),
					'4-3'  => __( 'Landscape (4:3)', 'unysonplus' ),
					'3-2'  => __( 'Landscape (3:2)', 'unysonplus' ),
					'16-9' => __( 'Wide (16:9)', 'unysonplus' ),
					'3-4'  => __( 'Portrait (3:4)', 'unysonplus' ),
					'auto' => __( 'Original', 'unysonplus' ),
				],
			],
			'archive_hover' => [
				'label'   => __( 'Card Hover Style', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'zoom',
				'choices' => [
					'zoom'      => __( 'Image zoom', 'unysonplus' ),
					'overlay'   => __( 'Overlay caption (title slides over the image)', 'unysonplus' ),
					'grayscale' => __( 'Grayscale to color', 'unysonplus' ),
					'none'      => __( 'None', 'unysonplus' ),
				],
			],
			'card_show_category' => [
				'label'        => __( 'Show Category On Cards', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'no',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no', 'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'card_show_summary' => [
				'label'        => __( 'Show Summary On Cards', 'unysonplus' ),
				'desc'         => __( 'The project\'s Short Summary line, when one is set.', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no', 'label' => __( 'Hide', 'unysonplus' ) ],
			],
		],
	],
];
