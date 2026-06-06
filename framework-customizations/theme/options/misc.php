<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Miscellaneous Main tab — site-wide tweaks that don't belong under
 * Header, Footer, or Typography. Handled by inc/includes/misc.php.
 *
 * Storage convention: each sub-tab's fields live inside a `multi`
 * container (misc_scroll_top, misc_dark_mode, etc.). This mirrors how
 * header_layout / general_layout / typography are stored — Unyson's
 * storage layer reliably namespaces leaf values under a `multi` parent
 * key. Without the wrapper, nested `tab > box > tab > box > field`
 * options weren't being persisted correctly on this theme.
 *
 * Field keys keep their feature prefix (scroll_top_enable, etc.) so
 * existing handler code reads them via unysonplus_misc_get( $key )
 * unchanged — that helper maps each key to its bucket internally.
 */

// Build the 404 Page dropdown choices from published pages.
$pages_choices = [ '' => __( '— Use default 404 template —', 'unysonplus' ) ];
$pages = get_pages( [ 'sort_column' => 'post_title', 'sort_order' => 'ASC' ] );
if ( is_array( $pages ) ) {
	foreach ( $pages as $page ) {
		$pages_choices[ $page->ID ] = $page->post_title;
	}
}

// Build the maintenance allow-roles list from registered roles.
$role_choices = [];
if ( function_exists( 'wp_roles' ) ) {
	foreach ( wp_roles()->get_names() as $role_key => $role_name ) {
		$role_choices[ $role_key ] = translate_user_role( $role_name );
	}
}

$options = [
	'misc_container' => [
		'title'   => __( 'Miscellaneous', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'misc' => [
				'title'   => __( 'Miscellaneous', 'unysonplus' ),
				'type'    => 'box',
				'options' => [

					/* --- Scroll to Top --- */
					'tab_scroll_top' => [
						'title'   => __( 'Scroll to Top', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'box' => [
								'title'   => __( 'Floating Scroll-to-Top Button', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'misc_scroll_top' => [
										'type'          => 'multi',
										'label'         => false,
										'inner-options' => [
											'scroll_top_enable' => [
												'label' => __( 'Enable', 'unysonplus' ),
												'desc'  => __( 'Show a fixed-position scroll-to-top button on every page.', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'no',
											],
											'scroll_top_position' => [
												'label' => __( 'Position', 'unysonplus' ),
												'type'  => 'radio',
												'value' => 'right',
												'choices' => [
													'right' => __( 'Bottom-right', 'unysonplus' ),
													'left'  => __( 'Bottom-left', 'unysonplus' ),
												],
											],
											'scroll_top_offset' => [
												'label' => __( 'Show after scrolling', 'unysonplus' ),
												'desc'  => __( 'Distance scrolled before the button appears. px is an absolute distance; vh is a fraction of the screen height (100vh = one full screen).', 'unysonplus' ),
												'type'  => 'unit-input',
												'units' => [ 'px', 'vh' ],
												'value' => [ 'value' => '300', 'unit' => 'px' ],
												'min'   => 0,
											],
											'scroll_top_text' => [
												'label' => __( 'Button label', 'unysonplus' ),
												'desc'  => __( 'Optional text shown next to the arrow icon. Leave empty for icon-only.', 'unysonplus' ),
												'type'  => 'text',
												'value' => '',
											],
											'scroll_top_bg_color' => [
												'label' => __( 'Background color', 'unysonplus' ),
												'type'  => 'color-picker',
												'value' => '',
											],
											'scroll_top_text_color' => [
												'label' => __( 'Icon / text color', 'unysonplus' ),
												'type'  => 'color-picker',
												'value' => '#ffffff',
											],
										],
									],
								],
							],
						],
					],

					/* --- Dark Mode --- */
					'tab_dark_mode' => [
						'title'   => __( 'Dark Mode', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'box' => [
								'title'   => __( 'Dark Mode Toggle', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'misc_dark_mode' => [
										'type'          => 'multi',
										'label'         => false,
										'inner-options' => [
											'dark_mode_enable' => [
												'label' => __( 'Enable', 'unysonplus' ),
												'desc'  => __( 'Adds a floating light/dark/auto toggle to every page. Uses Bootstrap 5.3 data-bs-theme so components inherit the dark palette automatically.', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'no',
											],
											'dark_mode_default' => [
												'label' => __( 'Default mode', 'unysonplus' ),
												'desc'  => __( 'What new visitors see before they click the toggle.', 'unysonplus' ),
												'type'  => 'radio',
												'value' => 'auto',
												'choices' => [
													'auto'  => __( 'Auto (follow system preference)', 'unysonplus' ),
													'light' => __( 'Light', 'unysonplus' ),
													'dark'  => __( 'Dark', 'unysonplus' ),
												],
											],
											'dark_mode_position' => [
												'label' => __( 'Toggle button position', 'unysonplus' ),
												'type'  => 'radio',
												'value' => 'bottom-left',
												'choices' => [
													'bottom-left'  => __( 'Bottom-left', 'unysonplus' ),
													'bottom-right' => __( 'Bottom-right', 'unysonplus' ),
													'top-left'     => __( 'Top-left', 'unysonplus' ),
													'top-right'    => __( 'Top-right', 'unysonplus' ),
												],
											],
											'dark_mode_show_label' => [
												'label' => __( 'Show text label', 'unysonplus' ),
												'desc'  => __( 'Show "Light" / "Dark" / "Auto" text alongside the icon.', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'no',
											],
										],
									],
								],
							],
						],
					],

					/* --- Custom CSS --- */
					'tab_custom_css' => [
						'title'   => __( 'Custom CSS', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'box' => [
								'title'   => __( 'Custom CSS', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'misc_custom_css' => [
										'type'          => 'multi',
										'label'         => false,
										'inner-options' => [
											'custom_css' => [
												'label'  => false,
												'desc'   => __( 'Emitted in &lt;style id="unysonplus-custom-css"&gt; just before &lt;/head&gt;. Loads after all theme + plugin stylesheets so it wins the cascade.', 'unysonplus' ),
												'type'   => 'code-editor',
												'value'  => '',
												'mode'   => 'css',
												'height' => 400,
											],
										],
									],
								],
							],
						],
					],

					/* --- Custom Header & Footer Scripts --- */
					'tab_custom_scripts' => [
						'title'   => __( 'Custom Scripts', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'box' => [
								'title'   => __( 'Custom Header & Footer Scripts', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'misc_custom_scripts' => [
										'type'          => 'multi',
										'label'         => false,
										'inner-options' => [
											'custom_head_scripts' => [
												'label'  => __( 'Inside <head>', 'unysonplus' ),
												'desc'   => __( 'Pasted verbatim before &lt;/head&gt;. Wrap JS in &lt;script&gt; tags.', 'unysonplus' ),
												'type'   => 'code-editor',
												'value'  => '',
												'mode'   => 'htmlmixed',
												'height' => 200,
											],
											'custom_body_open_scripts' => [
												'label'  => __( 'After opening <body>', 'unysonplus' ),
												'desc'   => __( 'Pasted verbatim immediately after &lt;body&gt; opens. Used for tag-manager &lt;noscript&gt; fallbacks.', 'unysonplus' ),
												'type'   => 'code-editor',
												'value'  => '',
												'mode'   => 'htmlmixed',
												'height' => 200,
											],
											'custom_footer_scripts' => [
												'label'  => __( 'Before </body>', 'unysonplus' ),
												'desc'   => __( 'Pasted verbatim before &lt;/body&gt; closes.', 'unysonplus' ),
												'type'   => 'code-editor',
												'value'  => '',
												'mode'   => 'htmlmixed',
												'height' => 200,
											],
										],
									],
								],
							],
						],
					],

					/* --- Analytics & Tracking --- */
					'tab_analytics' => [
						'title'   => __( 'Analytics & Tracking', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'box' => [
								'title'   => __( 'Analytics & Tracking IDs', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'misc_analytics' => [
										'type'          => 'multi',
										'label'         => false,
										'inner-options' => [
											'analytics_ga4_id' => [
												'label' => __( 'Google Analytics 4 Measurement ID', 'unysonplus' ),
												'desc'  => __( 'Format: G-XXXXXXXXXX. Leave empty to disable.', 'unysonplus' ),
												'type'  => 'text',
												'value' => '',
											],
											'analytics_gtm_id' => [
												'label' => __( 'Google Tag Manager Container ID', 'unysonplus' ),
												'desc'  => __( 'Format: GTM-XXXXXXX. Emits both the &lt;head&gt; script and &lt;noscript&gt; iframe.', 'unysonplus' ),
												'type'  => 'text',
												'value' => '',
											],
											'analytics_meta_pixel_id' => [
												'label' => __( 'Meta (Facebook) Pixel ID', 'unysonplus' ),
												'desc'  => __( 'Numeric ID, e.g. 1234567890123456.', 'unysonplus' ),
												'type'  => 'text',
												'value' => '',
											],
											'analytics_clarity_id' => [
												'label' => __( 'Microsoft Clarity Project ID', 'unysonplus' ),
												'desc'  => __( '10-character alphanumeric ID from clarity.microsoft.com.', 'unysonplus' ),
												'type'  => 'text',
												'value' => '',
											],
										],
									],
								],
							],
						],
					],

					/* --- Performance Tweaks --- */
					'tab_performance' => [
						'title'   => __( 'Performance', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'box' => [
								'title'   => __( 'Performance Tweaks', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'misc_performance' => [
										'type'          => 'multi',
										'label'         => false,
										'inner-options' => [
											'perf_disable_emojis' => [
												'label' => __( 'Disable WordPress emojis', 'unysonplus' ),
												'desc'  => __( 'Removes emoji detection script + styles from every page.', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'no',
											],
											'perf_disable_embeds' => [
												'label' => __( 'Disable oEmbed discovery', 'unysonplus' ),
												'desc'  => __( 'Removes WP oEmbed JSON/XML discovery links and the legacy wp-embed.js loader.', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'no',
											],
											'perf_remove_rsd_wlw' => [
												'label' => __( 'Remove RSD / WLW link tags', 'unysonplus' ),
												'desc'  => __( 'Legacy Windows Live Writer + Really Simple Discovery autodiscovery tags. Safe to remove on most sites.', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'no',
											],
											'perf_disable_jquery_migrate' => [
												'label' => __( 'Deregister jquery-migrate', 'unysonplus' ),
												'desc'  => __( 'Drops the legacy compatibility shim. Modern themes / plugins do not need it.', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'no',
											],
											'perf_remove_version_meta' => [
												'label' => __( 'Remove WordPress version meta tag', 'unysonplus' ),
												'desc'  => __( 'Hides the &lt;meta name="generator"&gt; tag from front-end source.', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'no',
											],
											'perf_disable_xmlrpc' => [
												'label' => __( 'Disable XML-RPC', 'unysonplus' ),
												'desc'  => __( 'Turns off the /xmlrpc.php endpoint. Disable only if no apps depend on it (Jetpack, mobile apps).', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'no',
											],
										],
									],
								],
							],
						],
					],

					/* --- 404 Page --- */
					'tab_404' => [
						'title'   => __( '404 Page', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'box' => [
								'title'   => __( '404 Not Found Page', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'misc_404' => [
										'type'          => 'multi',
										'label'         => false,
										'inner-options' => [
											'404_page_id' => [
												'label'   => __( 'Use this page as the 404', 'unysonplus' ),
												'desc'    => __( 'Pick a regular WordPress page to render in place of the default 404.php template.', 'unysonplus' ),
												'type'    => 'select',
												'value'   => '',
												'choices' => $pages_choices,
											],
											'404_show_search' => [
												'label' => __( 'Show search form (default template only)', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'yes',
											],
											'404_show_recent_posts' => [
												'label' => __( 'Show recent posts (default template only)', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'no',
											],
										],
									],
								],
							],
						],
					],

					/* --- Maintenance Mode --- */
					'tab_maintenance' => [
						'title'   => __( 'Maintenance Mode', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'box' => [
								'title'   => __( 'Maintenance Mode', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'misc_maintenance' => [
										'type'          => 'multi',
										'label'         => false,
										'inner-options' => [
											'maintenance_enabled' => [
												'label' => __( 'Enable maintenance mode', 'unysonplus' ),
												'desc'  => __( 'Serves a 503 splash page to visitors. Admin pages and allowlisted roles always pass through.', 'unysonplus' ),
												'type'  => 'switch',
												'value' => 'no',
											],
											'maintenance_title' => [
												'label' => __( 'Title', 'unysonplus' ),
												'type'  => 'text',
												'value' => __( "We'll be right back", 'unysonplus' ),
											],
											'maintenance_message' => [
												'label'  => __( 'Message', 'unysonplus' ),
												'type'   => 'wp-editor',
												'value'  => __( 'Our site is undergoing scheduled maintenance. Please check back shortly.', 'unysonplus' ),
												'reinit' => true,
											],
											'maintenance_logo' => [
												'label' => __( 'Logo', 'unysonplus' ),
												'desc'  => __( 'Optional image displayed above the title.', 'unysonplus' ),
												'type'  => 'upload',
												'value' => '',
											],
											'maintenance_allowed_roles' => [
												'label'   => __( 'Roles that bypass the splash', 'unysonplus' ),
												'desc'    => __( 'Logged-in users with any of these roles see the live site as normal.', 'unysonplus' ),
												'type'    => 'multi-select',
												'value'   => [ 'administrator' ],
												'population' => 'array',
												'choices' => $role_choices,
											],
										],
									],
								],
							],
						],
					],

					/* --- Developer Tools --- */
					'tab_dev_tools' => [
						'title'   => __( 'Developer Tools', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'box' => [
								'title'   => __( 'Developer Tools', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'misc_dev_tools' => [
										'type'          => 'multi',
										'label'         => false,
										'inner-options' => [
											'dev_show_demo' => [
												'label'        => __( 'Show Demo Options', 'unysonplus' ),
												'desc'         => __( 'Reveal the "Demo" tab — a reference showcase of every option type, for developers. Keep off on production sites. Save once to apply.', 'unysonplus' ),
												'type'         => 'switch',
												'value'        => 'no',
												'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
												'left-choice'  => [ 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ],
											],
										],
									],
								],
							],
						],
					],

					/* --- Export / Import Design --- */
					'tab_export_import' => [
						'title'   => __( 'Export / Import', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'box' => [
								'title'   => __( 'Design Export / Import', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'misc_export_import' => [
										'type'  => 'html-full',
										'label' => false,
										// Rendered by inc/includes/settings-export-import.php.
										'html'  => function_exists( 'unysonplus_settings_io_misc_field_html' )
											? unysonplus_settings_io_misc_field_html()
											: '',
									],
								],
							],
						],
					],

					/* --- Reset Settings --- */
					'tab_reset_settings' => [
						'title'   => __( 'Reset Settings', 'unysonplus' ),
						'type'    => 'tab',
						'options' => [
							'box' => [
								'title'   => __( 'Reset All Theme Settings', 'unysonplus' ),
								'type'    => 'box',
								'options' => [
									'misc_reset_settings' => [
										'type'  => 'html-full',
										'label' => false,
										/*
										 * Reuses the framework full-reset POST flag
										 * (_fw_reset_options), so it triggers the existing
										 * reset-everything handler and its confirm dialog with
										 * no extra JS. The per-tab reset lives in the header/footer.
										 */
										'html'  => '<p>' . esc_html__( 'This restores every option on every Theme Settings tab to its default value. This cannot be undone.', 'unysonplus' ) . '</p>'
										         . '<input type="submit" name="_fw_reset_options"'
										         . ' value="' . esc_attr__( 'Reset All Theme Settings', 'unysonplus' ) . '"'
										         . ' class="button-secondary button-large fw-settings-form-reset-btn" />',
									],
								],
							],
						],
					],

				],
			],
		],
	],
];
