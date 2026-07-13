<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

// Logo-layout image-picker thumbnails live at assets/svg/logo/*.svg. A filemtime
// cache-buster (?v=<mtime>) forces browsers to refetch when the art changes.
$svg_logo      = get_template_directory_uri() . '/assets/svg/logo';
$svg_logo_path = get_template_directory() . '/assets/svg/logo';
$logo_ver      = function ( $file ) use ( $svg_logo_path ) {
	$ver = @filemtime( $svg_logo_path . '/' . $file ); // phpcs:ignore -- optional cache-buster
	return $ver ? '?v=' . $ver : '';
};
// Build one image-picker choice (small + large tile + accessible label) from an SVG file.
$logo_tile = function ( $file, $label, $h = 58 ) use ( $svg_logo, $logo_ver ) {
	$src = $svg_logo . '/' . $file . $logo_ver( $file );
	return array(
		'small' => array( 'height' => $h, 'src' => $src ),
		'large' => array( 'height' => $h * 2, 'src' => $src ),
		'label' => $label,
	);
};

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

/*
 * Logo Type is a MULTI-PICKER (Unyson's native conditional-reveal): an image-picker
 * (Simple vs Custom) that reveals ONLY the relevant sub-options — the image fields for
 * "Simple Logo", or the icon + wordmark + layout fields for "Custom Logo Layout". This
 * nests the saved values under header_logo[logo_type][simple|custom][...]; the front end
 * reads them through unysonplus_header_logo_cfg() (which flattens both the new nested and
 * the legacy flat shape), and a one-time admin migration lifts old flat saves into the
 * nested shape (see inc/includes/identity-sync.php). Favicon stays a flat, shared field.
 */
// Default Logo Type: a site that already has a logo image (WP Custom Logo) defaults to
// Simple; a fresh site (no image) renders the text lockup, so it defaults to Custom.
$unysonplus_default_logo_type = get_theme_mod( 'custom_logo' ) ? 'simple' : 'custom';

$options = [
	'header_logo' => [
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => [
			// TWO groups (house style: box → group). Each group renders with a bottom border
			// (backend-options.css: .fw-backend-options-group:not(.show-borders)), so Logo
			// Type and Favicon read as separated sections inside the "Site Identity" box —
			// matching Footer → Layout, which uses several groups for the same effect. A
			// single group would put its one border at the very bottom (merged with the box
			// edge) and look border-less. Group ids are containers only — NOT stored — so the
			// saved shape stays header_logo[logo_type] / header_logo[favicon]; no migration.
			'group_logo' => [
			'type'    => 'group',
			'options' => [
			'logo_type' => [
				'type'         => 'multi-picker',
				'label'        => false,
				'desc'         => false,
				'picker'       => [
					'logo_type' => [
						'label'   => __( 'Logo Type', 'unysonplus' ),
						'desc'    => __( 'Choose how the logo is built. <b>Simple Logo</b> = an uploaded image (synced with the WordPress Custom Logo). <b>Custom Logo Layout</b> = a text wordmark with an optional icon, arranged with the Logo Layout — the modern "icon + wordmark" lockup, all real editable text.', 'unysonplus' ),
						'type'    => 'image-picker',
						'choices' => array(
							'custom' => $logo_tile( 'type-custom.svg', __( 'Custom Logo Layout (icon + text)', 'unysonplus' ), 66 ),
							'simple' => $logo_tile( 'type-simple.svg', __( 'Simple Logo (image)', 'unysonplus' ), 66 ),
						),
					],
				],
				// Smart default: if a logo image already exists, default to Simple; otherwise a
				// fresh site has no image, so the text lockup is what renders — default to Custom.
				'value'   => array( 'logo_type' => $unysonplus_default_logo_type ),
				'choices' => array(
					/* ---------- SIMPLE LOGO → an uploaded image ---------- */
					'simple' => array(
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
						'width' => [
							'label' => __( 'Logo Width', 'unysonplus' ),
							'type'  => 'unit-input',
							'units' => [ 'px', 'rem', 'em' ],
							'value' => [ 'value' => '', 'unit' => 'px' ],
							'min'   => 0,
							'desc'  => __( 'Display width of the image logo (e.g. 300px or 12rem). Leave empty to auto-size to the header height.', 'unysonplus' ),
						],
					),
					/* ---------- CUSTOM LOGO LAYOUT → text wordmark + optional icon ----------
					   Ordered Content → Styling → Advanced: first WHAT the logo is (title,
					   tagline, icon, layout, frame), then HOW it looks (sizes / weights / colors),
					   then the Custom CSS escape hatch. */
					'custom' => array(
						// --- Content ---
						'site_title' => [
							'label' => __( 'Site Title', 'unysonplus' ),
							'desc'  => __( 'Your website\'s name — the text wordmark. <b>Synced with Settings &rarr; General &rarr; Site Title</b>: edit it here or there and both update. Leave empty to use the WordPress Site Title.', 'unysonplus' ),
							'type'  => 'text',
							'value' => get_bloginfo( 'name' ),
						],
						'tagline_text' => [
							'label' => __( 'Tagline Text', 'unysonplus' ),
							'type'  => 'text',
							'value' => '',
							'desc'  => __( 'Your tagline — the Eyebrow / sub-line of the lockup. <b>Synced with Settings &rarr; General &rarr; Tagline</b>: edit it here or there and both update. Leave empty to use the WordPress Tagline.', 'unysonplus' ),
						],
						'logo_icon' => [
							'label' => __( 'Logo Icon', 'unysonplus' ),
							'desc'  => __( 'Optional brand mark shown with the text site title (e.g. a Lucide icon) — the modern "icon + wordmark" logo. Rendered as an inline SVG so it recolors cleanly. Which SIDE the icon sits on is set by the Logo Layout below.', 'unysonplus' ),
							'type'  => 'icon-v2',
						],
						'logo_layout' => [
							'label'   => __( 'Logo Layout', 'unysonplus' ),
							'desc'    => __( 'How the lockup arranges the Logo Icon, Site Title and Tagline — this is also the tagline control. <b>Inline</b> = icon + title only, no tagline. <b>Stacked</b> = title with the tagline under it, beside the icon. <b>Eyebrow</b> = a small uppercase tagline ABOVE the title, beside the icon (the "brand OS" lockup). Each comes with the icon on the <b>left</b> or <b>right</b>.', 'unysonplus' ),
							'type'    => 'image-picker',
							'value'   => 'inline-left',
							'blank'   => false,
							'choices' => array(
								'inline-left'   => $logo_tile( 'inline-left.svg', __( 'Inline — icon left', 'unysonplus' ) ),
								'inline-right'  => $logo_tile( 'inline-right.svg', __( 'Inline — icon right', 'unysonplus' ) ),
								'stacked-left'  => $logo_tile( 'stacked-left.svg', __( 'Stacked — icon left', 'unysonplus' ) ),
								'stacked-right' => $logo_tile( 'stacked-right.svg', __( 'Stacked — icon right', 'unysonplus' ) ),
								'eyebrow-left'  => $logo_tile( 'eyebrow-left.svg', __( 'Eyebrow — icon left', 'unysonplus' ) ),
								'eyebrow-right' => $logo_tile( 'eyebrow-right.svg', __( 'Eyebrow — icon right', 'unysonplus' ) ),
							),
						],
						'logo_icon_frame' => [
							'label'   => __( 'Logo Icon Frame', 'unysonplus' ),
							'type'    => 'image-picker',
							'value'   => 'none',
							'blank'   => false,
							'choices' => array(
								'none'     => $logo_tile( 'frame-none.svg', __( 'None (plain icon)', 'unysonplus' ), 54 ),
								'rounded'  => $logo_tile( 'frame-rounded.svg', __( 'Rounded box', 'unysonplus' ), 54 ),
								'squircle' => $logo_tile( 'frame-squircle.svg', __( 'Squircle', 'unysonplus' ), 54 ),
								'circle'   => $logo_tile( 'frame-circle.svg', __( 'Circle', 'unysonplus' ), 54 ),
								'square'   => $logo_tile( 'frame-square.svg', __( 'Square', 'unysonplus' ), 54 ),
								'hexagon'  => $logo_tile( 'frame-hexagon.svg', __( 'Hexagon', 'unysonplus' ), 54 ),
							),
							'desc'    => __( 'Wrap the logo icon in a subtle bordered frame — the "app icon" look (a tile behind the mark). Choose the tile shape, or None for a plain icon.', 'unysonplus' ),
						],
						// --- Styling ---
						'title_size' => [
							'label' => __( 'Site Title Font Size', 'unysonplus' ),
							'type'  => 'unit-input',
							'units' => [ 'px', 'rem', 'em' ],
							'value' => [ 'value' => '', 'unit' => 'rem' ],
							'min'   => 0,
							'desc'  => __( 'Font size of the text site title (e.g. 1.5rem or 24px). Leave empty for the theme default.', 'unysonplus' ),
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
							'desc'    => __( 'Font weight of the text site title.', 'unysonplus' ),
						],
						'color' => [
							'label'   => __( 'Site Title Color', 'unysonplus' ),
							'desc'    => __( 'Color of the text site title. Pick a palette preset or a custom color.', 'unysonplus' ),
							'type'    => 'predefined-colors-color-picker-compact',
							'picker'  => 'color-picker',
							'value'   => [ 'predefined' => '', 'custom' => '' ],
							'choices' => $unysonplus_site_title_colors,
						],
						'tagline_color' => [
							'label'   => __( 'Tagline Color', 'unysonplus' ),
							'desc'    => __( 'Color of the header tagline. Pick a palette preset or a custom color.', 'unysonplus' ),
							'type'    => 'predefined-colors-color-picker-compact',
							'picker'  => 'color-picker',
							'value'   => [ 'predefined' => '', 'custom' => '' ],
							'choices' => $unysonplus_site_title_colors,
						],
						'logo_icon_color' => [
							'label'   => __( 'Logo Icon Color', 'unysonplus' ),
							'desc'    => __( 'Color of the logo icon. Pick a palette preset or a custom color. Leave empty to inherit the Site Title Color.', 'unysonplus' ),
							'type'    => 'predefined-colors-color-picker-compact',
							'picker'  => 'color-picker',
							'value'   => [ 'predefined' => '', 'custom' => '' ],
							'choices' => $unysonplus_site_title_colors,
						],
						'logo_icon_size' => [
							'label' => __( 'Logo Icon Size', 'unysonplus' ),
							'type'  => 'unit-input',
							'units' => [ 'px', 'rem', 'em' ],
							'value' => [ 'value' => '', 'unit' => 'em' ],
							'min'   => 0,
							'desc'  => __( 'Size of the logo icon (e.g. 1.5rem or 24px). Leave empty for the default (~1.4em, so it reads slightly larger than the wordmark).', 'unysonplus' ),
						],
						// --- Advanced ---
						'logo_custom_css' => [
							'label' => __( 'Logo Custom CSS', 'unysonplus' ),
							'type'  => 'code-editor',
							'value' => '',
							'mode'  => 'css',
							'desc'  => __( 'Advanced: extra CSS for the logo lockup, output in the header styles. Target the lockup hooks: <code>.site-logo__mark</code> (icon), <code>.site-logo__mark--framed</code> (the frame tile), <code>.site-logo__eyebrow</code> / <code>.site-logo__sub</code> (tagline lines), <code>.site-title-text</code> (title), <code>.site-title</code> (whole brand link). E.g. a glow: <code>.site-logo__mark{filter:drop-shadow(0 0 8px rgba(97,218,251,.6))}</code>.', 'unysonplus' ),
						],
					),
				),
			],
			], // group_logo options
			], // group_logo

			// Favicon / Site Icon is shared by BOTH logo types (always visible) — it is the
			// browser-tab icon, not part of the header lockup, so it gets its own group.
			'group_favicon' => [
			'type'    => 'group',
			'options' => [
			'favicon' => [
				'label'       => __( 'Favicon / Site Icon', 'unysonplus' ),
				'desc'        => __( 'Square icon (512&times;512 recommended). Synced two-way with the WordPress Site Icon (Settings &rarr; General / Customize &rarr; Site Identity), so browsers, tabs and mobile bookmarks pick it up. Leave empty to use the existing Site Icon.', 'unysonplus' ),
				'type'        => 'upload',
				'images_only' => true,
			],
			], // group_favicon options
			], // group_favicon
		],
	],
];
