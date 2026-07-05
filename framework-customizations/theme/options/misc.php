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

					// Scroll to Top moved to the top-level "Site-wide UX" tab
					// (inc/includes/site-wide-ux.php). Its `misc_scroll_top` storage key is
					// unchanged, so unysonplus_misc_get( 'scroll_top_*' ) still resolves.

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

				],
			],
		],
	],
];
