<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Dark Mode options (leaf).
 *
 * Stored under the `misc_dark_mode` multi key (unchanged from when it lived in the
 * Miscellaneous tab, so `unysonplus_misc_get( 'dark_mode_*' )` still resolves and the
 * handler in inc/includes/misc.php needs no change). Composed into the "Site-wide UX"
 * tab (site-wide-ux-settings.php) and, when the Animation Engine is active, injected
 * into its Site-wide UX tab (inc/includes/site-wide-ux.php) — Dark Mode is a global
 * end-user UX affordance (a floating toggle on every page), so it belongs beside
 * Preloader / Scrolling / Scroll-to-Top rather than in the developer-oriented
 * Miscellaneous catch-all.
 */

$options = [
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
];
