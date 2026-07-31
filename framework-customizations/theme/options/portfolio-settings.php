<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Portfolio main tab (registered from settings.php only while the Portfolio
 * extension is active).
 *
 * Sub-tabs:
 *   - Archive & Cards (portfolio-archive.php) — archive grid + project cards.
 *   - Single Project  (portfolio-single.php)  — single-project view blocks.
 *
 * These are DISPLAY settings; data-level portfolio options (galleries,
 * project details fields, tags, permalinks) stay on the extension's own
 * Settings page (Unyson → Extensions → Portfolio) and the WP Permalinks
 * screen. Values here override the extension unless set to Inherit — the
 * bridge lives in inc/includes/portfolio.php.
 */

$options = [
	'portfolio_settings_container' => [
		'title'   => __( 'Portfolio', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'portfolio' => [
				'title'   => __( 'Portfolio Settings', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					'tab_portfolio_presets' => [
						'title'   => __( 'Presets', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'portfolio_presets_box' => [
								'title'   => __( 'Portfolio Presets', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'portfolio_presets' => [
										'type'         => 'preset-loader',
										'label'        => __( 'Portfolio Presets', 'unysonplus' ),
										'desc'         => __( 'Start from a persona — Case Study, Gallery, Cards or Showcase — then fine-tune under Archive & Cards. Or upload a preset JSON you exported earlier.', 'unysonplus' ),
										'preset_group' => 'portfolio_archive',
									],
								],
							],
						],
					],
					'tab_portfolio_archive' => [
						'title'   => __( 'Archive & Cards', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'portfolio_archive_box' => [
								'title'   => __( 'Archive & Cards', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'portfolio-archive' ),
								],
							],
						],
					],
					'tab_portfolio_single' => [
						'title'   => __( 'Single Project', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'portfolio_single_box' => [
								'title'   => __( 'Single Project', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									fw()->theme->get_options( 'portfolio-single' ),
								],
							],
						],
					],
				],
			],
		],
	],
];
