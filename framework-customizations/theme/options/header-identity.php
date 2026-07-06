<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

// Pre-fill the Site Title Color presets from the theme's Color Palette
// (Theme Settings → Colors). Keys are the `text-{slug}` utility classes the
// plugin emits — `:root .text-{slug}{ color:var(--color-{slug}) !important }`
// (framework/includes/css-tokens.php) — so the chosen preset paints the title
// verbatim. The slug derivation mirrors css-tokens.php exactly.
$unysonplus_site_title_colors = array();
if ( function_exists( 'unysonplus_option_color_palette' ) ) {
	foreach ( unysonplus_option_color_palette() as $unysonplus_color_name => $unysonplus_color_hex ) {
		$unysonplus_color_slug = trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( $unysonplus_color_name ) ), '-' );
		if ( '' === $unysonplus_color_slug ) {
			continue;
		}
		$unysonplus_site_title_colors[ 'text-' . $unysonplus_color_slug ] = array(
			'label' => $unysonplus_color_name,
			'color' => $unysonplus_color_hex,
		);
	}
}

$options = [
	'header_logo' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			'site_title' => [
				'label' => __( 'Site Title', 'unysonplus' ),
				'desc'  => __( 'Your website\'s name (used as the text logo when no image is uploaded).', 'unysonplus' ),
				'type'  => 'text',
				'value' => get_bloginfo( 'name' ),
			],
			'title_size' => [
				'label' => __( 'Site Title Font Size', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px', 'rem', 'em' ],
				'value' => [ 'value' => '', 'unit' => 'rem' ],
				'min'   => 0,
				'desc'  => __( 'Font size of the text site title (e.g. 1.5rem or 24px). Leave empty for the theme default. Ignored when an image logo is shown.', 'unysonplus' ),
			],
			'title_weight' => [
				'label'   => __( 'Site Title Font Weight', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '',
				'choices' => [
					''    => __( 'Default', 'unysonplus' ),
					'300' => __( 'Light (300)', 'unysonplus' ),
					'400' => __( 'Regular (400)', 'unysonplus' ),
					'500' => __( 'Medium (500)', 'unysonplus' ),
					'600' => __( 'Semibold (600)', 'unysonplus' ),
					'700' => __( 'Bold (700)', 'unysonplus' ),
					'800' => __( 'Extrabold (800)', 'unysonplus' ),
					'900' => __( 'Black (900)', 'unysonplus' ),
				],
				'desc'    => __( 'Font weight of the text site title. Ignored when an image logo is shown.', 'unysonplus' ),
			],
			'color' => [
				'label'   => __( 'Site Title Color', 'unysonplus' ),
				'desc'    => __( 'Color of the text site title (used when no image logo is set). Pick a palette preset or a custom color.', 'unysonplus' ),
				'type'    => 'predefined-colors-color-picker-compact',
				'picker'  => 'color-picker',
				'value'   => [ 'predefined' => '', 'custom' => '' ],
				'choices' => $unysonplus_site_title_colors,
			],
			'image' => [
				'label'       => __( 'Logo Upload', 'unysonplus' ),
				'desc'        => __( 'Synced two-way with the WordPress Custom Logo (Appearance &rarr; Customize &rarr; Site Identity): set it here and it updates there, or set it there and it appears here. Leave empty to use whatever Custom Logo is set.', 'unysonplus' ),
				'type'        => 'upload',
				'images_only' => true,
			],
			'image_2x' => [
				'label'       => __( 'Logo (Retina / 2&times;)', 'unysonplus' ),
				'desc'        => __( 'Optional high-resolution version of the logo, served to Retina / high-DPI screens via srcset. Upload one at exactly twice the display size of the main logo.', 'unysonplus' ),
				'type'        => 'upload',
				'images_only' => true,
			],
			'sticky_image' => [
				'label'       => __( 'Sticky-Header Logo', 'unysonplus' ),
				'desc'        => __( 'Optional alternate logo shown once the header sticks on scroll (requires Sticky Header in Header &rarr; Layout). Handy for a compact or dark variant. Leave empty to keep the main logo.', 'unysonplus' ),
				'type'        => 'upload',
				'images_only' => true,
			],
			'mobile_image' => [
				'label'       => __( 'Mobile Logo', 'unysonplus' ),
				'desc'        => __( 'Optional logo shown on small screens (below 768px). Leave empty to use the main logo everywhere.', 'unysonplus' ),
				'type'        => 'upload',
				'images_only' => true,
			],
			'transparent_image' => [
				'label'       => __( 'Transparent-Header Logo', 'unysonplus' ),
				'desc'        => __( 'Optional logo shown while the header is transparent / overlaying the hero (Header &rarr; Layout &rarr; Behavior = Transparent, or a per-page Transparent header). Usually a light/white variant. Swaps back to the main logo once the header sticks. Leave empty to keep the main logo.', 'unysonplus' ),
				'type'        => 'upload',
				'images_only' => true,
			],
			'alt' => [
				'label' => __( 'Logo Alt Text', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
				'desc'  => __( 'Alternative text for the image logo (accessibility / SEO). Leave empty to fall back to the Site Title.', 'unysonplus' ),
			],
			'favicon' => [
				'label'       => __( 'Favicon / Site Icon', 'unysonplus' ),
				'desc'        => __( 'Square icon (512&times;512 recommended). Synced two-way with the WordPress Site Icon (Settings &rarr; General / Customize &rarr; Site Identity), so browsers, tabs and mobile bookmarks pick it up. Leave empty to use the existing Site Icon.', 'unysonplus' ),
				'type'        => 'upload',
				'images_only' => true,
			],
			'width' => [
				'label' => __( 'Logo Width', 'unysonplus' ),
				'type'  => 'unit-input',
				'units' => [ 'px', 'rem', 'em' ],
				'value' => [ 'value' => '', 'unit' => 'px' ],
				'min'   => 0,
				'desc'  => __( 'Display width of the image logo (e.g. 300px or 12rem). Leave empty to auto-size to the header height. Ignored for the text site title.', 'unysonplus' ),
			],
			'tagline' => [
				'label'        => __( 'Hide Tagline', 'unysonplus' ),
				'type'         => 'switch',
				'left-choice'  => [ 'value' => '', 'label' => __( 'No', 'unysonplus' ) ],
				'right-choice' => [ 'value' => ' d-none', 'label' => __( 'Yes', 'unysonplus' ) ],
				'value'        => '',
				'desc'         => __( 'Select Yes to hide the tagline.', 'unysonplus' ),
			],
			'tagline_text' => [
				'label' => __( 'Tagline Text', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
				'desc'  => __( 'Header-only tagline. Leave empty to use the WordPress Tagline (Settings &rarr; General). Setting it here does not change the site-wide tagline.', 'unysonplus' ),
			],
			'tagline_color' => [
				'label'   => __( 'Tagline Color', 'unysonplus' ),
				'desc'    => __( 'Color of the header tagline. Pick a palette preset or a custom color.', 'unysonplus' ),
				'type'    => 'predefined-colors-color-picker-compact',
				'picker'  => 'color-picker',
				'value'   => [ 'predefined' => '', 'custom' => '' ],
				'choices' => $unysonplus_site_title_colors,
			],
		],
	],
];
