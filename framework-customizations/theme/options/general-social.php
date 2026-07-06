<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Social Profiles + Social Icon Style — site-wide social links (consumed by the
 * header Social Icons element and the footer) plus their global styling.
 *
 * Stored under `social_profiles` (the list) and `social_style` (the look). The
 * style leaves feed the `--social-*` CSS vars + wrapper classes emitted by
 * theme-vars.php / the render (unysonplus_render_social_icons). The preset loader
 * (group `social_style`) sets the style leaves in one click.
 */

/* Palette-preset color control (kind 'text' → glyph, 'bg' → background). */
$color = function ( $label, $desc, $kind ) {
	if ( function_exists( 'sc_color_field_compact' ) ) {
		return sc_color_field_compact( array( 'label' => $label, 'desc' => $desc, 'kind' => $kind ) );
	}
	return array( 'label' => $label, 'desc' => $desc, 'type' => 'color-picker', 'value' => '' );
};

/* Icon-style preview tiles — a mini social chip in each shape (inline data-URI SVG,
   caption baked in, matching the header-design / menu-style tiles). */
$style_svg = function ( $variant, $label ) {
	$w = 104; $h = 66; $accent = '#2271b1'; $cx = 52; $cy = 24; $s = 30;
	$x = $cx - $s / 2; $y = $cy - $s / 2;
	$white_dot  = '<circle cx="' . $cx . '" cy="' . $cy . '" r="6" fill="#ffffff"/>';
	$accent_dot = '<circle cx="' . $cx . '" cy="' . $cy . '" r="6" fill="' . $accent . '"/>';
	$chip = ''; $glyph = '';
	switch ( $variant ) {
		case 'circle':
			$chip = '<circle cx="' . $cx . '" cy="' . $cy . '" r="15" fill="' . $accent . '"/>'; $glyph = $white_dot; break;
		case 'circle-outline':
			$chip = '<circle cx="' . $cx . '" cy="' . $cy . '" r="15" fill="none" stroke="' . $accent . '" stroke-width="1.5"/>'; $glyph = $accent_dot; break;
		case 'rounded':
			$chip = '<rect x="' . $x . '" y="' . $y . '" width="' . $s . '" height="' . $s . '" rx="7" fill="' . $accent . '"/>'; $glyph = $white_dot; break;
		case 'square':
			$chip = '<rect x="' . $x . '" y="' . $y . '" width="' . $s . '" height="' . $s . '" rx="2" fill="' . $accent . '"/>'; $glyph = $white_dot; break;
		case 'square-outline':
			$chip = '<rect x="' . $x . '" y="' . $y . '" width="' . $s . '" height="' . $s . '" rx="2" fill="none" stroke="' . $accent . '" stroke-width="1.5"/>'; $glyph = $accent_dot; break;
		case 'bare':
		default:
			$glyph = '<circle cx="' . $cx . '" cy="' . $cy . '" r="9" fill="' . $accent . '"/>'; break;
	}
	$text = '<text x="' . ( $w / 2 ) . '" y="' . ( $h - 5 ) . '" text-anchor="middle" font-family="-apple-system,Segoe UI,Roboto,sans-serif" font-size="10" fill="#50575e">' . $label . '</text>';
	$svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $w . ' ' . $h . '" width="' . $w . '" height="' . $h . '">' . $chip . $glyph . $text . '</svg>';
	return 'data:image/svg+xml,' . rawurlencode( $svg );
};
$style_choice = function ( $variant, $label ) use ( $style_svg ) {
	$uri = $style_svg( $variant, $label );
	return array(
		'small' => array( 'height' => 66, 'src' => $uri ),
		'large' => array( 'height' => 92, 'src' => $uri ),
	);
};

/* Default profiles ship with real Font Awesome brand icons (the FA kit loads on the
   front end, so they render out of the box instead of falling back to plain text). */
$defaults = array(
	array( 'name' => 'Facebook',  'link' => 'https://facebook.com/',  'icon' => array( 'type' => 'icon-font', 'icon-class' => 'fab fa-facebook-f' ), 'new_tab' => 'yes' ),
	array( 'name' => 'X',         'link' => 'https://x.com/',         'icon' => array( 'type' => 'icon-font', 'icon-class' => 'fab fa-x-twitter' ),  'new_tab' => 'yes' ),
	array( 'name' => 'Instagram', 'link' => 'https://instagram.com/', 'icon' => array( 'type' => 'icon-font', 'icon-class' => 'fab fa-instagram' ),  'new_tab' => 'yes' ),
);

$options = array(

	/* Quick-start looks for the icons (sets the style leaves below). */
	'social_presets' => array(
		'type'         => 'preset-loader',
		'label'        => __( 'Social Icon Presets', 'unysonplus' ),
		'desc'         => __( 'Pick a ready-made icon look, then fine-tune the style below.', 'unysonplus' ),
		'preset_group' => 'social_style',
	),

	/* The style/design of the icons (shape, size, colors, hover). */
	'social_style' => array(
		'type'          => 'multi',
		'label'         => false,
		'inner-options' => array(
			'group_social_style' => array(
				'type'    => 'group',
				'options' => array(
					'social_icon_style' => array(
						'label'   => __( 'Icon Style', 'unysonplus' ),
						'desc'    => __( 'Shape / fill of each icon. Bare shows just the glyph; the others draw a shaped chip behind it.', 'unysonplus' ),
						'type'    => 'image-picker',
						'value'   => 'bare',
						'choices' => array(
							'bare'           => $style_choice( 'bare',           __( 'Bare', 'unysonplus' ) ),
							'circle'         => $style_choice( 'circle',         __( 'Circle', 'unysonplus' ) ),
							'circle-outline' => $style_choice( 'circle-outline', __( 'Circle Outline', 'unysonplus' ) ),
							'rounded'        => $style_choice( 'rounded',        __( 'Rounded', 'unysonplus' ) ),
							'square'         => $style_choice( 'square',         __( 'Square', 'unysonplus' ) ),
							'square-outline' => $style_choice( 'square-outline', __( 'Square Outline', 'unysonplus' ) ),
						),
					),
					'social_icon_size' => array(
						'label' => __( 'Icon Size', 'unysonplus' ),
						'desc'  => __( 'Diameter of the chip (or glyph size for the Bare style).', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => array( 'rem', 'px', 'em' ),
						'value' => array( 'value' => '2.25', 'unit' => 'rem' ),
						'min'   => 0,
					),
					'social_icon_gap' => array(
						'label' => __( 'Gap Between Icons', 'unysonplus' ),
						'type'  => 'unit-input',
						'units' => array( 'rem', 'px', 'em' ),
						'value' => array( 'value' => '0.5', 'unit' => 'rem' ),
						'min'   => 0,
					),
					'social_icon_brand' => array(
						'label'        => __( 'Use Brand Colors', 'unysonplus' ),
						'desc'         => __( 'Color each icon with its network\'s brand color (Facebook blue, etc.). Overrides the Icon / Background colors below.', 'unysonplus' ),
						'type'         => 'switch',
						'value'        => 'no',
						'right-choice' => array( 'value' => 'yes', 'label' => __( 'On', 'unysonplus' ) ),
						'left-choice'  => array( 'value' => 'no',  'label' => __( 'Off', 'unysonplus' ) ),
					),
					'social_icon_color'       => $color( __( 'Icon Color', 'unysonplus' ), __( 'Glyph color. Leave empty to inherit the surrounding text color.', 'unysonplus' ), 'text' ),
					'social_icon_bg'          => $color( __( 'Background', 'unysonplus' ), __( 'Chip background (filled styles). Leave empty for transparent.', 'unysonplus' ), 'bg' ),
					'social_icon_hover_color' => $color( __( 'Icon Hover Color', 'unysonplus' ), __( 'Glyph color on hover.', 'unysonplus' ), 'text' ),
					'social_icon_hover_bg'    => $color( __( 'Background Hover', 'unysonplus' ), __( 'Chip background on hover.', 'unysonplus' ), 'bg' ),
					'social_icon_hover_fx' => array(
						'label'   => __( 'Hover Effect', 'unysonplus' ),
						'type'    => 'select',
						'value'   => 'none',
						'choices' => array(
							'none'  => __( 'None', 'unysonplus' ),
							'lift'  => __( 'Lift', 'unysonplus' ),
							'scale' => __( 'Scale up', 'unysonplus' ),
							'fill'  => __( 'Fill (fade background in)', 'unysonplus' ),
						),
					),
				),
			),
		),
	),

	/* The profiles themselves. */
	'social_profiles' => array(
		'label'        => false,
		'type'         => 'addable-box',
		'value'        => $defaults,
		'box-options'  => array(
			'name' => array(
				'label' => __( 'Name', 'unysonplus' ),
				'desc'  => __( 'Network name (used as the link label / aria-label). Its brand color is matched by name (facebook, x, instagram, youtube, linkedin, …).', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			),
			'link' => array(
				'label' => __( 'URL', 'unysonplus' ),
				'desc'  => __( 'Full profile URL, including https://', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			),
			'icon' => array(
				'type'         => 'icon-v2',
				'preview_size' => 'medium',
				'modal_size'   => 'medium',
				'label'        => __( 'Icon', 'unysonplus' ),
				'desc'         => __( 'Pick an icon for this profile.', 'unysonplus' ),
			),
			'new_tab' => array(
				'label'        => __( 'Open in New Tab', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => array( 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ),
				'left-choice'  => array( 'value' => 'no',  'label' => __( 'No', 'unysonplus' ) ),
				'desc'         => __( 'Open the link in a new browser tab.', 'unysonplus' ),
			),
		),
		'template' => '<p><strong>{{- name }}</strong><br>{{- link }}</p>',
	),
);
