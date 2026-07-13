<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/*
 * Per-page settings — one consolidated "Page Settings" postbox under the
 * builder, laid out as tabs that MIRROR the global Pages tab
 * (Theme Settings → Pages): Layout / Page Title-Hero / Header & Footer /
 * Elements / General / Custom Code. Every field defaults to a "Global /
 * inherit" value so an untouched page follows the site-wide cascade
 * (unysonplus_resolve_layout / unysonplus_pages_get); a page only diverges
 * where the editor deliberately overrides.
 *
 * The option IDs are unchanged from when these lived in separate side/normal
 * meta boxes, so existing saved post meta and the runtime consumers
 * (inc/includes/layout.php for the hero header + layout cascade,
 * inc/hooks.php + template-parts/header-builder.php for header behavior,
 * framework dynamic-css for page_custom_css) keep working.
 */

if ( ! function_exists( 'unysonplus_page_meta_color' ) ) :
	/**
	 * Compact color-preset field for the per-page meta, guarded so the theme
	 * still loads if the shortcodes styling helper is absent. Falls back to a
	 * plain color-picker (whose stored string the resolvers tolerate).
	 */
	function unysonplus_page_meta_color( $label, $desc = '', $kind = 'bg', $picker = 'color-picker' ) {
		if ( function_exists( 'sc_color_field_compact' ) ) {
			return sc_color_field_compact( array(
				'label'  => $label,
				'desc'   => $desc,
				'kind'   => $kind,
				'picker' => $picker,
			) );
		}
		return array( 'label' => $label, 'desc' => $desc, 'type' => $picker, 'value' => '' );
	}
endif;

/* Image-picker choices for the layout diagrams (labels baked into the SVGs);
   matches Pages → Layout + General → Sidebar. Stored value stays the plain key. */
$upw_layout_svg = get_template_directory_uri() . '/assets/svg/layout';
$upw_layout_picker = function ( array $pairs, $small = 104, $large = 150 ) use ( $upw_layout_svg ) {
	$out = [];
	foreach ( $pairs as $value => $file ) {
		$out[ $value ] = [
			'small' => [ 'height' => $small, 'src' => $upw_layout_svg . '/' . $file ],
			'large' => [ 'height' => $large, 'src' => $upw_layout_svg . '/' . $file ],
		];
	}
	return $out;
};

/* --- Layout ----------------------------------------------------------- */
$layout_options = [
	'sidebar_override' => [
		'label'   => __( 'Sidebar Position', 'unysonplus' ),
		'desc'    => __( 'Override the sidebar for this page only. "Global" follows the template / Pages default.', 'unysonplus' ),
		'type'    => 'image-picker',
		'value'   => 'default',
		'choices' => $upw_layout_picker( [
			'default' => 'sb-inherit.svg',
			'none'    => 'sb-none.svg',
			'left'    => 'sb-left.svg',
			'right'   => 'sb-right.svg',
		] ),
	],
	'content_width' => [
		'label'   => __( 'Content Width', 'unysonplus' ),
		'type'    => 'image-picker',
		'value'   => 'default',
		'choices' => $upw_layout_picker( [
			'default' => 'cw-global.svg',
			'narrow'  => 'cw-narrow.svg',
			'wide'    => 'cw-wide.svg',
			'full'    => 'cw-full.svg',
		] ),
	],
	'page_bg_color' => unysonplus_page_meta_color(
		__( 'Page Background Color', 'unysonplus' ),
		__( 'Leave on the preset "Default" to inherit the site background.', 'unysonplus' ),
		'bg'
	),
	'page_bg_image' => [
		'label' => __( 'Page Background Image', 'unysonplus' ),
		'type'  => 'upload',
		'value' => [],
	],
];

/* --- Page Title / Hero ------------------------------------------------ */
$hero_header_options = [
	'header_image' => [
		'label' => __( 'Header Image', 'unysonplus' ),
		'desc'  => __( 'Full-width banner image at the top of the page. Empty inherits the global Pages → Page Title / Hero image.', 'unysonplus' ),
		'type'  => 'upload',
		'value' => [],
	],
	'header_height' => [
		'label'   => __( 'Header Height', 'unysonplus' ),
		'type'    => 'radio',
		'value'   => 'auto',
		'choices' => [
			'auto'       => __( 'Global', 'unysonplus' ),
			'small'      => __( 'Small (220px)', 'unysonplus' ),
			'medium'     => __( 'Medium (380px)', 'unysonplus' ),
			'large'      => __( 'Large (560px)', 'unysonplus' ),
			'fullscreen' => __( 'Fullscreen (100vh)', 'unysonplus' ),
		],
	],
	'header_overlay_color' => unysonplus_page_meta_color(
		__( 'Overlay Color', 'unysonplus' ),
		__( 'Tint over the header image. Preset "Default" inherits the global hero overlay.', 'unysonplus' ),
		'bg',
		'rgba-color-picker'
	),
	'header_overlay_opacity' => [
		'label'      => __( 'Overlay Opacity', 'unysonplus' ),
		'desc'       => __( '0 = inherit global, otherwise 0 transparent → 100 opaque.', 'unysonplus' ),
		'type'       => 'slider',
		'value'      => 0,
		'properties' => [ 'min' => 0, 'max' => 100, 'step' => 5 ],
	],
	'header_content_position' => [
		'label'   => __( 'Title Position', 'unysonplus' ),
		'type'    => 'select',
		'value'   => 'default',
		'choices' => [
			'default' => __( 'Global', 'unysonplus' ),
			'top'     => __( 'Top', 'unysonplus' ),
			'center'  => __( 'Center', 'unysonplus' ),
			'bottom'  => __( 'Bottom', 'unysonplus' ),
		],
	],
	'hide_page_title' => [
		'label' => false,
		'type'  => 'checkbox',
		'value' => false,
		'text'  => __( 'Hide the page title', 'unysonplus' ),
	],
];

/* --- Header & Footer -------------------------------------------------- */
$header_footer_options = [
	'page_header' => [
		'label'   => __( 'Header', 'unysonplus' ),
		'desc'    => __( 'How the site header behaves on this page.', 'unysonplus' ),
		'type'    => 'select',
		'value'   => '',
		'choices' => [
			''            => __( 'Global (default header)', 'unysonplus' ),
			'transparent' => __( 'Transparent (overlays the first section)', 'unysonplus' ),
			'd-none'      => __( 'Hidden (no header on this page)', 'unysonplus' ),
		],
	],
	'hide_site_footer' => [
		'label'        => __( 'Hide Site Footer', 'unysonplus' ),
		'desc'         => __( 'Remove the whole footer on this page.', 'unysonplus' ),
		'type'         => 'switch',
		'value'        => 'no',
		'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
		'left-choice'  => [ 'value' => 'no',  'label' => __( 'No',  'unysonplus' ) ],
	],
	'hide_footer_widgets' => [
		'label'        => __( 'Hide Footer Widgets', 'unysonplus' ),
		'desc'         => __( 'Keep the footer bar but drop its widget area on this page.', 'unysonplus' ),
		'type'         => 'switch',
		'value'        => 'no',
		'right-choice' => [ 'value' => 'yes', 'label' => __( 'Yes', 'unysonplus' ) ],
		'left-choice'  => [ 'value' => 'no',  'label' => __( 'No',  'unysonplus' ) ],
	],
];

/* --- Elements --------------------------------------------------------- */
$elements_options = [
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
			'default' => __( 'Global', 'unysonplus' ),
			'yes'     => __( 'Yes', 'unysonplus' ),
			'no'      => __( 'No', 'unysonplus' ),
		],
	],
	'show_comments' => [
		'label'   => __( 'Show Comments', 'unysonplus' ),
		'type'    => 'select',
		'value'   => 'default',
		'choices' => [
			'default' => __( 'Global', 'unysonplus' ),
			'yes'     => __( 'Yes', 'unysonplus' ),
			'no'      => __( 'No', 'unysonplus' ),
		],
	],
];

/* --- Custom Code ------------------------------------------------------ */
$custom_code_options = [
	'page_custom_css' => [
		'label' => __( 'Custom CSS (this page only)', 'unysonplus' ),
		'desc'  => __( 'Emitted inline in &lt;head&gt;. Loaded only on this page.', 'unysonplus' ),
		'type'  => 'code-editor',
		'value' => '',
		'mode'  => 'css', // top-level key the code-editor option type reads
	],
	'page_custom_js' => [
		'label' => __( 'Custom JS (this page only)', 'unysonplus' ),
		'desc'  => __( 'Emitted inline before &lt;/body&gt;. Loaded only on this page.', 'unysonplus' ),
		'type'  => 'code-editor',
		'value' => '',
		'mode'  => 'javascript',
	],
];

$options = [
	// One postbox under the builder; tabs mirror the global Pages tab so the
	// per-page overrides read as "the same knobs, for this page only".
	'page_main_settings' => [
		'title'    => __( 'Page Settings', 'unysonplus' ),
		'type'     => 'box',
		'context'  => 'normal',
		'priority' => 'default',
		'options'  => [
			'tab_layout' => [
				'title'   => __( 'Layout', 'unysonplus' ),
				'type'    => 'tab',
				'options' => $layout_options,
			],
			'tab_hero_header' => [
				'title'   => __( 'Page Title / Hero', 'unysonplus' ),
				'type'    => 'tab',
				'options' => $hero_header_options,
			],
			'tab_header_footer' => [
				'title'   => __( 'Header / Footer', 'unysonplus' ),
				'type'    => 'tab',
				'options' => $header_footer_options,
			],
			'tab_elements' => [
				'title'   => __( 'Elements', 'unysonplus' ),
				'type'    => 'tab',
				'options' => $elements_options,
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
