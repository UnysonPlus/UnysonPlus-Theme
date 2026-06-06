<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * This is for Shortcode Helper functions and classes
 */

if(! function_exists( 'unysonplus_option_color_palette_defaults' )) :
	/**
	 * Color palette default values
	 */
	function unysonplus_option_color_palette_defaults() {
		return array(
			array(
				'name'  =>'Black',
				'color' =>'#000'),
			array(
				'name'  =>'White',
				'color' =>'#fff'),
			array(
				'name'  =>'Gray',
				'color' =>'#636c72'),
			array(
				'name'  =>'Light Gray',
				'color' =>'#bdbdbd'),
			array(
				'name'  =>'Red',
				'color' =>'#d9534f'),
			array(
				'name'  =>'Pink',
				'color' =>'#e91e63'),
			array(
				'name'  =>'Purple',
				'color' =>'#9c27b0'),
			array(
				'name'  =>'Deep Purple',
				'color' =>'#673ab7'),
			array(
				'name'  =>'Indigo',
				'color' =>'#3f51b5'),
			array(
				'name'  =>'Blue',
				'color' =>'#286090'),
			array(
				'name'  =>'Light Blue',
				'color' =>'#03a9f4'),
			array(
				'name'  =>'Cyan',
				'color' =>'#00bcd4'),
			array(
				'name'  =>'Teal',
				'color' =>'#009688'),
			array(
				'name'  =>'Green',
				'color' =>'#5cb85c'),
			array(
				'name'  =>'Light Green',
				'color' =>'#8bc34a'),
			array(
				'name'  =>'Lime',
				'color' =>'#cddc39'),
			array(
				'name'  =>'Yellow',
				'color' =>'#ffeb3b'),
			array(
				'name'  =>'Amber',
				'color' =>'#ffc107'),
			array(
				'name'  =>'Orange',
				'color' =>'#ff9800'),
			array(
				'name'  =>'Deep Orange',
				'color' =>'#ff5722'),
			array(
				'name'  =>'Brown',
				'color' =>'#795548'),
			array(
				'name'  =>'Blue Gray',
				'color' =>'#607d8b'),
		);
	}
endif;


if(! function_exists( 'unysonplus_option_color_palette' )) :
	/**
	 * Get predefined colors
	 */
	function unysonplus_option_color_palette() {
		// Prefer the plugin getter (which already handles theme-override fallback to plugin defaults).
		if ( function_exists( 'unysonplus_get_color_presets' ) ) {
			$theme_colors = unysonplus_get_color_presets();
		} else {
			$theme_colors = fw_get_db_settings_option('theme_colors');
			if(!isset($theme_colors)) {
				$theme_colors = unysonplus_option_color_palette_defaults();
			}
		}
		$predefined_colors = array();
		foreach($theme_colors as $theme_color) {
			$predefined_colors[$theme_color['name']] = $theme_color['color'];
		}
		return $predefined_colors;
	}
endif;


if ( ! function_exists( 'unysonplus_option_color_select' ) ) :
/**
 * Generate a color select option for Unyson framework.
 *
 * @param string $label Color label for description.
 * @param string $color Optional prefix for class (default: 'text').
 * @return array Option array for Unyson select field.
 */
function unysonplus_option_color_select( $label, $color = 'text' ) {

    $color_palette = array(
        '' => __( 'Default', 'unysonplus' ),
    );

    $theme_colors = unysonplus_option_color_palette();

    foreach ( $theme_colors as $key => $value ) {
        $option_key = sanitize_title_with_dashes( $color ) . '-' . sanitize_title_with_dashes( $key );
        $color_palette[ $option_key ] = __( $key, 'unysonplus' );
    }

    // Check if in admin
    $is_admin = is_admin();

    $desc = $is_admin
        ? sprintf( __( '%s color.', 'unysonplus' ), $label )
        : sprintf(
            __( '%s color. Add or modify the color palettes by clicking <a href="%s" target="_blank">here</a>.', 'unysonplus' ),
            $label,
            admin_url( 'themes.php?page=fw-settings#fw-options-tab-tab_colors' )
        );

    return array(
        'label'   => '', // Could be dynamic if needed
        'type'    => 'select',
        'value'   => '',
        'desc'    => $desc,
        'choices' => $color_palette,
    );
}
endif;


if(! function_exists('unysonplus_option_color_picker')) :
	/**
	 * Color Picker
	 */
	function unysonplus_option_color_picker($label = NULL, $default = '#ffffff', $desc = NULL) {
		$option = array(
			'type' => 'predefined-colors-color-picker',
			'label' => __($label, 'unysonplus'),
			'desc'	=> __($desc, 'unysonplus'),
			'value' => array(
				'predefined' => '', // you can set default value
				'custom' => $default // or default value for picker
			),
			'colors' => array(
				'predefined' => array(
					'type' =>'predefined',
					'choices' => unysonplus_option_color_palette(),
				),
				'custom' => array(
					'type' =>'custom',
					'picker' => 'color-picker', // color-picker|rgba-color-picker
				),
			),
			'help'  => __('Set your predefined color swatches in <a href="'.admin_url().'themes.php?page=fw-settings#fw-options-tab-tab_colors" target="_blank">here</a>', 'unysonplus')
		);
		return $option;
	}
endif;


if(! function_exists( 'unysonplus_option_button_color_defaults' )) :
	/**
	 * Button color default values, in the SKIN shape the `button-presets` option
	 * expects: a nested `states` map whose color fields are compact-picker values
	 * { predefined: <color-preset-slug>, custom: '' }. Prefer the plugin's
	 * canonical defaults (palette-slug based) when available so the two stay in
	 * sync; the local copy is only a fallback.
	 */
	function unysonplus_option_button_color_defaults() {
		if ( function_exists( 'unysonplus_default_button_color_presets' ) ) {
			return unysonplus_default_button_color_presets();
		}
		$p = function ( $slug ) { return array( 'predefined' => (string) $slug, 'custom' => '' ); };
		$preset = function ( $id, $name, $d_text, $d_bg, $h_bg ) use ( $p ) {
			return array(
				'id'         => $id,
				'color_name' => $name,
				'states'     => array(
					'default'  => array( 'text_color' => $p( $d_text ), 'bg_color' => $p( $d_bg ), 'border_color' => $p( $d_bg ) ),
					'hover'    => array( 'text_color' => $p( $d_text ), 'bg_color' => $p( $h_bg ),  'border_color' => $p( $h_bg ) ),
					'active'   => array(),
					'focus'    => array(),
					'disabled' => array(),
				),
			);
		};
		return array(
			$preset( '0000000001', 'Default', 'gray',  'light-gray', 'gray' ),
			$preset( '0000000002', 'Primary', 'white', 'blue',       'indigo' ),
			$preset( '0000000003', 'Success', 'white', 'green',      'teal' ),
			$preset( '0000000004', 'Info',    'white', 'cyan',       'light-blue' ),
			$preset( '0000000005', 'Warning', 'black', 'amber',      'orange' ),
			$preset( '0000000006', 'Danger',  'white', 'red',        'pink' ),
		);
	}
endif;


if(! function_exists( 'unysonplus_option_button_colors' )) :
	/**
	 * Color palette default values
	 */
	function unysonplus_option_button_colors() {
		$button_colors = fw_get_db_settings_option('button_colors');
		if(!isset($button_colors)) {
			$button_colors = unysonplus_option_button_color_defaults();
		}
		$predefined_colors = array();
		foreach($button_colors as $button_color) {
			$predefined_colors['btn-'.sanitize_title_with_dashes($button_color['color_name'])] = $button_color['color_name'];
		}
		return $predefined_colors;
	}
endif;


if(! function_exists( 'unysonplus_option_button_size_defaults' )) :
	/**
	 * Button size default values
	 */
	function unysonplus_option_button_size_defaults() {
		// Size = dimensions only. font_size / padding_* / border_radius use the
		// unit-input shape array('value'=>.., 'unit'=>..); line_height is a plain
		// (usually unitless) string. border-width belongs to the Button Preset.
		$u = function ( $value, $unit = 'px' ) { return array( 'value' => (string) $value, 'unit' => $unit ); };
		return array(
			array( 'id' => '0000010005', 'size_name' => 'Extra Large', 'slug' => 'xl', 'font_size' => $u( 22 ), 'line_height' => '1.4', 'padding_y' => $u( 14 ), 'padding_x' => $u( 24 ), 'border_radius' => $u( 10 ) ),
			array( 'id' => '0000010004', 'size_name' => 'Large',       'slug' => 'lg', 'font_size' => $u( 20 ), 'line_height' => '1.4', 'padding_y' => $u( 12 ), 'padding_x' => $u( 20 ), 'border_radius' => $u( 8 )  ),
			array( 'id' => '0000010003', 'size_name' => 'Medium',      'slug' => 'md', 'font_size' => $u( 16 ), 'line_height' => '1.4', 'padding_y' => $u( 8 ),  'padding_x' => $u( 16 ), 'border_radius' => $u( 6 )  ),
			array( 'id' => '0000010002', 'size_name' => 'Small',       'slug' => 'sm', 'font_size' => $u( 13 ), 'line_height' => '1.4', 'padding_y' => $u( 6 ),  'padding_x' => $u( 12 ), 'border_radius' => $u( 5 )  ),
			array( 'id' => '0000010001', 'size_name' => 'Extra Small', 'slug' => 'xs', 'font_size' => $u( 12 ), 'line_height' => '1.4', 'padding_y' => $u( 2 ),  'padding_x' => $u( 6 ),  'border_radius' => $u( 3 )  ),
		);
	}
endif;


if( ! function_exists( 'unysonplus_option_button_sizes' )) :
	/**
	 * Color palette default values
	 */
	function unysonplus_option_button_sizes() {
		$button_colors = fw_get_db_settings_option('button_sizes');
		if(!isset($button_colors)) {
			$button_colors = unysonplus_option_button_size_defaults();
		}
		$predefined_colors = array();
		foreach($button_colors as $button_color) {
			$predefined_colors['btn-'.sanitize_title_with_dashes($button_color['slug'])] = $button_color['size_name'];
		}
		return $predefined_colors;
	}
endif;


if( ! function_exists( 'unysonplus_option_font_sizes' )) :
	/**
	 * Color palette default values
	 */
	function unysonplus_option_font_sizes() {
		$typography = fw_get_db_settings_option('typography');
		$font_sizes = $typography['font_sizes'];
		if( !isset($font_sizes) ) {
			return;
		}
		$font_sizes_choices = array();
		$font_sizes_choices[''] = 'Default';
		foreach($font_sizes as $font_size) {
			$font_sizes_choices[sanitize_title_with_dashes($font_size['name'])] = $font_size['size'] . 'px - ' . $font_size['name'];
		}
		if( !empty($typography['h1']['size']) ) 		$font_sizes_choices['h1'] = $typography['h1']['size'] . 'px - Same size with h1 heading';
		if( !empty($typography['h2']['size']) ) 		$font_sizes_choices['h2'] = $typography['h2']['size'] . 'px - Same size with h2 heading';
		if( !empty($typography['h3']['size']) ) 		$font_sizes_choices['h3'] = $typography['h3']['size'] . 'px - Same size with h3 heading';
		if( !empty($typography['h4']['size']) ) 		$font_sizes_choices['h4'] = $typography['h4']['size'] . 'px - Same size with h4 heading';
		if( !empty($typography['h5']['size']) ) 		$font_sizes_choices['h5'] = $typography['h5']['size'] . 'px - Same size with h5 heading';
		if( !empty($typography['h6']['size']) ) 		$font_sizes_choices['h6'] = $typography['h6']['size'] . 'px - Same size with h6 heading';
		if( !empty($typography['body']['size']) ) 	$font_sizes_choices['p'] = $typography['body']['size'] . 'px - Same size with paragraph text';
		return $font_sizes_choices;
	}
endif;


if( ! function_exists('unysonplus_option_text_transform')) :
	/**
	* Text Transformation
	*/
	function unysonplus_option_text_transform($label=NULL,$desc=NULL) {
		return array(
			'type'    => 'select',
			'label'   => __($label, 'unysonplus'),
			'desc'		=> __($desc, 'unysonplus'),
			'value'   => '',
			'choices' => array(
				''  => 'none',
				'text-lowercase' => 'lowercased text',
				'text-uppercase' => 'UPPERCASED TEXT',
				'text-capitalize' => 'Capitalized Text',
			)
		); 
	}
endif;


if( ! function_exists('unysonplus_option_css_tag')) :
	/**
	* CSS Tag
	*/
	function unysonplus_option_css_tag( $label=NULL, $desc=NULL, $default='h2' ) {
		return array(
			'type'    => 'select',
			'label'   => __( $label, 'unysonplus' ),
			'desc'		=> __( $desc, 'unysonplus' ),
			'value'   => $default,
			'choices' => array(
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6',
				'p' => 'p',
			)
		); 
	}
endif;


if( ! function_exists('unysonplus_option_bg_atts')):
	/**
 * Option attributes for background
 */
	function unysonplus_option_bg_atts($name) {
		$uri = get_template_directory_uri();
		return array(
			'label'         => false,
			'type'          => 'multi',
			'value'         => array(),
			'desc'          => false,
			'inner-options' => array(
				'image'    => array(
					'label'   => __( $name.' Background', 'unysonplus' ),
					'type'    => 'background-image',
					'value'   => 'none',
					'choices' => array(
						'none' => array(
							'icon' => $uri . '/images/patterns/no_pattern.jpg',
							'css'  => array(
								'background-image' => 'none',
							)
						),
						'bg-1' => array(
							'icon' => $uri . '/images/patterns/diagonal_bottom_to_top_pattern_preview.jpg',
							'css'  => array(
								'background-image'  => 'url("' . $uri . '/images/patterns/diagonal_bottom_to_top_pattern.png' . '")',
							)
						),
						'bg-2' => array(
							'icon' => $uri . '/images/patterns/diagonal_top_to_bottom_pattern_preview.jpg',
							'css'  => array(
								'background-image'  => 'url("' . $uri . '/images/patterns/diagonal_top_to_bottom_pattern.png' . '")',
							)
						),
						'bg-3' => array(
							'icon' => $uri . '/images/patterns/dots_pattern_preview.jpg',
							'css'  => array(
								'background-image'  => 'url("' . $uri . '/images/patterns/dots_pattern.png' . '")',
							)
						),
						'bg-4' => array(
							'icon' => $uri . '/images/patterns/romb_pattern_preview.jpg',
							'css'  => array(
								'background-image'  => 'url("' . $uri . '/images/patterns/romb_pattern.png' . '")',
							)
						),
						'bg-5' => array(
							'icon' => $uri . '/images/patterns/square_pattern_preview.jpg',
							'css'  => array(
								'background-image'  => 'url("' . $uri . '/images/patterns/square_pattern.png' . '")',
							)
						),
						'bg-6' => array(
							'icon' => $uri . '/images/patterns/noise_pattern_preview.jpg',
							'css'  => array(
								'background-image'  => 'url("' . $uri . '/images/patterns/noise_pattern.png' . '")',
							)
						),
						'bg-7' => array(
							'icon' => $uri . '/images/patterns/vertical_lines_pattern_preview.jpg',
							'css'  => array(
								'background-image'  => 'url("' . $uri . '/images/patterns/vertical_lines_pattern.png' . '")',
							)
						),
						'bg-8' => array(
							'icon' => $uri . '/images/patterns/waves_pattern_preview.jpg',
							'css'  => array(
								'background-image'  => 'url("' . $uri . '/images/patterns/waves_pattern.png' . '")',
							)
						),
					),
				),
				'color' => unysonplus_option_color_picker('Background Color','', 'Background color'),
				'position' => array(
					'label' => __( '', 'unysonplus' ),
					'desc'  => __( 'Image position', 'unysonplus' ),
					'type'  => 'select',
					'value' => 'top center',
					'choices' => array(
						'top left' 			=> __( 'Top Left', 'unysonplus' ),
						'top center' 		=> __( 'Top Center', 'unysonplus' ),
						'top right' 		=> __( 'Top Right', 'unysonplus' ),
						'center left' 	=> __( 'Center Left', 'unysonplus' ),
						'center center' => __( 'Center Center', 'unysonplus' ),
						'center right' 	=> __( 'Center Right', 'unysonplus' ),
						'bottom left' 	=> __( 'Bottom Left', 'unysonplus' ),
						'bottom center' => __( 'Bottom Center', 'unysonplus' ),
						'bottom right' 	=> __( 'Bottom Right', 'unysonplus' ),
					)
				),
				'repeat' => array(
					'label' => __( '', 'unysonplus' ),
					'desc'  => __( 'Image repeat', 'unysonplus' ),
					'type'  => 'select',
					/*'attr'  => array( 'class' => '' ),*/
					'value' => 'repeat',
					'choices' => array(
						'no-repeat' => __( 'Display Once (No-Repeat)', 'unysonplus' ),
						'repeat' 		=> __( 'Full Tile (Repeat XY Axis)', 'unysonplus' ),
						'repeat-x' 	=> __( 'Horizontal Tile (Repeat X Axis)', 'unysonplus' ),
						'repeat-y' 	=> __( 'Vertical Tile (Repeat Y Axis)', 'unysonplus' ),
					)
				),
				'attachment' => array(
					'label' => __( '', 'unysonplus' ),
					'desc'  => __( 'Image attachment', 'unysonplus' ),
					'type'  => 'select',
					'value' => 'scroll',
					'choices' => array(
						'scroll' => __( 'Scroll', 'unysonplus' ),
						'fixed' => __( 'Fixed', 'unysonplus' ),
					),
					'help'	=> __( '<p><strong>scroll</strong> - The background scrolls along with the page. This is default</p>
									<p><strong>fixed</strong> - The background is fixed with regard to the viewport.</p>
									', 'unysonplus'),
				),
				'size' => array(
					'type' 	=> 'fw-multi-inline',
					'label' => __('', 'unysonplus'),
					'desc'  => __( 'Image size', 'unysonplus' ),
					'value' => array(
						'selected' 	 	=> 'auto',	
						'custom'		=> '',
					),
					'help'  => __( '<p><strong>auto</strong> -	Default value. The background image contains its width and height.</p>
								<p><strong>cover</strong> - Scale the background image to be as large as possible so that the background area is completely covered by the background image. Some parts of the background image may not be in view within the background positioning area.</p>
								<p><strong>contain</strong> - Scale the image to the largest size such that both its width and its height can fit inside the content area.</p>
								<p><strong>custom</strong> - Counts for the width and height of the background image. i.e.:<br />
								400px - it counts for the width, and the height is set to auto.<br />
								300px 100px - the first sets the background image\'s width and the second sets the height. </p>', 'unysonplus' ),
					'fw_multi_options' => array(
						'selected' => array(
							'label' => __( '', 'unysonplus' ),
							'desc'  => __( '', 'unysonplus' ),
							'title' => false,
							'type'  => 'select',
							'choices' => array(
								'auto' => __( 'Auto', 'unysonplus' ),
								'cover' => __( 'Cover', 'unysonplus' ),
								'contain' => __( 'Contain', 'unysonplus' ),
								'custom' => __( 'Custom Value', 'unysonplus' ),
							)
						),
						'custom' => array(
							'type' 	=>'short-text',
							'title' => false,
						),
					)
				),
				'overlay' => array(
					'type'  => 'multi-picker',
					'label' => false,
					'desc'  => false,
					'picker' => array(
						'selected' => array(
							'type'  => 'switch',
							'label' => __( 'Overlay', 'unysonplus' ),
							'desc'  => __( 'Enable background overlay?', 'unysonplus' ),
							'value' => 'no',
							'right-choice' => array(
								'value' => 'yes',
								'label' => __('Yes', 'unysonplus'),
							),
							'left-choice' => array(
								'value' => 'no',
								'label' => __('No', 'unysonplus'),
							),
						),
					),
					'choices' => array(
						'yes' => array(
							'color' => unysonplus_option_color_picker('','', 'Color 1'),
							'gradient' => unysonplus_option_color_picker('','', 'Color 2. Select second color to enable gradient.'),
							'direction' => array(
								'label' => __( '', 'unysonplus' ),
								'desc'  => __( 'Gradient direction.', 'unysonplus' ),
								'type'  => 'select',
								'value' => 'bottom',
								'choices' => array(
									'bottom' 		=> __( 'Top to bottom', 'unysonplus' ),
									'top' 			=> __( 'Bottom to top', 'unysonplus' ),
									'right' 		=> __( 'Left to right', 'unysonplus' ),
									'left' 			=> __( 'Right to left', 'unysonplus' ),
									'top left' 		=> __( 'Top to left', 'unysonplus' ),
									'top right' 	=> __( 'Top to right', 'unysonplus' ),
									'bottom left' 	=> __( 'Bottom to left', 'unysonplus' ),
									'bottom right' 	=> __( 'Bottom to right', 'unysonplus' ),
								),
							),
							'opacity' => array(
								'type'  => 'slider',
								'value' => 100,
								'properties' => array(
									'min' => 0,
									'max' => 1,
									'step' => .1,
								),
								'label' => __( '', 'unysonplus' ),
								'desc'  => __( 'Select the overlay color opacity in %', 'unysonplus' ),
							)
						),
					),
				),
			),	
		);
	}
endif;


if(! function_exists('unysonplus_option_link')) :
	/**
	 * Link Options
	 */
	function unysonplus_option_link() {
		return array(
			'type'         => 'multi-picker',
			'label'        => false,
			'desc'         => false,
			'picker'       => array(
				'selected' => array(
					'label'   => __( 'Link', 'unysonplus' ),
					'desc'  => __( 'Select your link source.', 'unysonplus' ),
					'type'    => 'select',
					'choices' => array(
						'manual'=> __( 'Manual', 'unysonplus' ),
						'page' 	=> __( 'Page', 'unysonplus' ),
						'post' 	=> __( 'Blog Post', 'unysonplus' ),
						'media' => __( 'Media', 'unysonplus' ),
					),
				)
			),
			'choices'      => array(
				'manual'  => array(
					'href'   => array(
						'label' => __( '', 'unysonplus' ),
						'type'  => 'text',
						'value' => '',
						'desc'  => __( 'Enter the URL. Leave Manual Link empty to disable.', 'unysonplus' )
					),
					'target'      => array(
						'label'   => __( '', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '_self',
						'desc'    => __( 'Target attribute. How the link will be opened.','unysonplus' ),
						'choices' => array(
							'_self'  	=> __( 'Open link in same window', 'unysonplus' ),
							'_blank'  	=> __( 'Open link in new window', 'unysonplus' ),
							//'lightbox' 	=> __( 'Open link inside a lightbox', 'unysonplus' ),
							//'modal' 	=> __( 'Open link inside bootstrap modal', 'unysonplus' ),
						),
					),
				),
				'page' => array(
					'href'      => array(
						'type'  => 'multi-select',
						'label' => __( '', 'unysonplus' ),
						'desc'  => __( 'Enter the title of the page.', 'unysonplus' ),
						'population' => 'posts',
						'source'=> 'page',
						'limit' => 1,
					),
					'target'      => array(
						'label'   => __( '', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '_self',
						'desc'    => __( 'Target attribute. How the link will be opened.','unysonplus' ),
						'choices' => array(
							'_self'  	=> __( 'Open link in same window', 'unysonplus' ),
							'_blank'  	=> __( 'Open link in new window', 'unysonplus' ),
							//'lightbox' 	=> __( 'Open link inside a lightbox', 'unysonplus' ),
							//'modal' 	=> __( 'Open link inside bootstrap modal', 'unysonplus' ),
						),
					),
				),
				'post' => array(
					'href'      => array(
						'type'       => 'multi-select',
						'label'      => __( '', 'unysonplus' ),
						'desc'  => __( 'Enter the title of the post.', 'unysonplus' ),
						'population' => 'posts',
						'source'     => 'post',
						'limit' => 1,
					),
					'target'      => array(
						'label'   => __( '', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '_self',
						'desc'    => __( 'Target attribute. How the link will be opened.','unysonplus' ),
						'choices' => array(
							'_self'  	=> __( 'Open link in same window', 'unysonplus' ),
							'_blank'  	=> __( 'Open link in new window', 'unysonplus' ),
							//'lightbox' 	=> __( 'Open link inside a lightbox', 'unysonplus' ),
							//'modal' 	=> __( 'Open link inside bootstrap modal', 'unysonplus' ),
						),
					),
				),
				'media' => array(
					'href'                    => array(
						'label'       => __( '', 'unysonplus' ),
						'desc'        => __( 'Upload your media file or select from Media Library.', 'unysonplus' ),
						'type'        => 'upload',
						'images_only' => false,
					),
					'target'      => array(
						'label'   => __( '', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '_self',
						'desc'    => __( 'Target attribute. How the link will be opened.','unysonplus' ),
						'choices' => array(
							'_self'  	=> __( 'Open link in same window', 'unysonplus' ),
							'_blank'  	=> __( 'Open link in new window', 'unysonplus' ),
							//'lightbox' 	=> __( 'Open link inside a lightbox', 'unysonplus' ),
							//'modal' 	=> __( 'Open link inside bootstrap modal', 'unysonplus' ),
						),
					),
				),
			),
			'show_borders' => false,
		); 
	}
endif;


if( !function_exists('unysonplus_get_option_link') ):
	/**
	 * Get Link
	 */
	function unysonplus_get_option_link( array $link, $content = NULL ) {
		if( !empty($link[$link['selected']]['href']) ) {
			if($link['selected'] == 'page' || $link['selected'] == 'post') {
				$link[$link['selected']]['href'] = get_permalink($link[$link['selected']]['href'][0]);
			}elseif($link['selected'] == 'media') {
				$link[$link['selected']]['href'] = $link[$link['selected']]['href']['url'];
			}
			return fw_html_tag('a', $link[$link['selected']], $content);
		}else{
			return $content;
		}
	}
endif;


if(! function_exists('unysonplus_option_float')) :
	/**
	 * Link Options
	 */
	function unysonplus_option_float( $label = 'Alignment', $desc = 'Floats an element to the left or right, or disable floating, based on the current viewport size.' ) {
		return array(
			'type'    => 'multiple',
			'label'   => __( $label, 'unysonplus' ),
			'desc'		=> __( $desc, 'unysonplus' ),
			'value' => '',
			'choices' => array(
				'' 						=> __('None', 'unysonplus'),
				array(
					'attr'    	=> array(
						'label'         => __( 'For All Devices ( Default )', 'unysonplus' ),
						//'data-whatever' => 'some data',
					),
					'choices' => array(
							'float-start' 		=> __( 'Float left', 'unysonplus' ),
							'float-end' 	=> __( 'Float right', 'unysonplus' ),
						'mx-auto d-block'	=> __( 'Centered', 'unysonplus' ),
						'float-none' 		=> __( 'Don\'t float', 'unysonplus' ),
					),
				),
				array(
					'attr'    	=> array(
						'label'         => __( 'Small devices (landscape phones, 576px and up)', 'unysonplus' ),
					),
					'choices' => array(
							'float-sm-start' 	=> __( 'Float left', 'unysonplus' ),
							'float-sm-end' 	=> __( 'Float right', 'unysonplus' ),
						'mx-sm-auto d-block' 			=> __( 'Centered', 'unysonplus' ),
						'float-sm-none' 	=> __( 'Don\'t float', 'unysonplus' ),
					),
				),
				array(
					'attr'    	=> array(
						'label'         => __( 'Medium devices (tablets, 768px and up)', 'unysonplus' ),
					),
					'choices' => array(
							'float-md-start' 	=> __( 'Float left', 'unysonplus' ),
							'float-md-end' 	=> __( 'Float right', 'unysonplus' ),
						'mx-md-auto d-block' => __( 'Centered', 'unysonplus' ),
						'float-md-none' 	=> __( 'Don\'t float', 'unysonplus' ),
					),
				),
				array(
					'attr'    	=> array(
						'label'         => __( 'Large devices (desktops, 992px and up)', 'unysonplus' ),
					),
					'choices' => array(
							'float-lg-start' 	=> __( 'Float left', 'unysonplus' ),
							'float-lg-end' 	=> __( 'Float right', 'unysonplus' ),
						'mx-lg-auto d-block' 			=> __( 'Centered', 'unysonplus' ),
						'float-lg-none' 	=> __( 'Don\'t float', 'unysonplus' ),
					),
				),
				array(
					'attr'    	=> array(
						'label'         => __( 'Extra large devices (large desktops, 1200px and up)', 'unysonplus' ),
					),
					'choices' => array(
							'float-xl-start' 	=> __( 'Float left', 'unysonplus' ),
							'float-xl-end' 	=> __( 'Float right', 'unysonplus' ),
						'mx-xl-auto d-block' 			=> __( 'Centered', 'unysonplus' ),
						'float-xl-none' 	=> __( 'Don\'t float', 'unysonplus' ),
					),
				),
			),
		);
	}
endif;


if(! function_exists('unysonplus_option_hover_2d')) :
	/**
	 * 2D Hover Option
	 */
	function unysonplus_option_hover_2d() {
		return array(
			'type'    => 'select',
			'label'   => __( '2d Transition', 'unysonplus' ),
			'desc'		=> __( '', 'unysonplus' ),
			'value' => '',
			'choices' => array(
				'' 				=> __( 'None', 'unysonplus'),
				'hvr-grow' 				=> __( 'Grow', 'unysonplus' ),
				'hvr-shrink' 			=> __( 'Shrink', 'unysonplus' ),
				'hvr-pulse' 			=> __( 'Pulse', 'unysonplus' ),
				'hvr-pulse-grow' 	=> __( 'Pulse Grow', 'unysonplus' ),
				'hvr-pulse-shrink'=> __( 'Pulse Shrink', 'unysonplus' ),
				'hvr-push' 				=> __( 'Push', 'unysonplus' ),
				'hvr-pop' 				=> __( 'Pop', 'unysonplus' ),
				'hvr-bounce-in' 	=> __( 'Bounce In', 'unysonplus' ),
				'hvr-bounce-out' 	=> __( 'Bounce Out', 'unysonplus' ),
				'hvr-rotate' 			=> __( 'Rotate', 'unysonplus' ),
				'hvr-grow-rotate' => __( 'Grow Rotate', 'unysonplus' ),
				'hvr-float' 			=> __( 'Float', 'unysonplus' ),
				'hvr-sink' 				=> __( 'Sink', 'unysonplus' ),
				'hvr-bob' 				=> __( 'Bob', 'unysonplus' ),
				'hvr-hang' 				=> __( 'Hang', 'unysonplus' ),
				'hvr-skew' 				=> __( 'Skew', 'unysonplus' ),
				'hvr-skew-forward' 	=> __( 'Skew Forward', 'unysonplus' ),
				'hvr-skew-backward' => __( 'Skew Backward', 'unysonplus' ),
				'hvr-wobble-horizontal' => __( 'Wobble Horizontal', 'unysonplus' ),
				'hvr-wobble-vertical' 	=> __( 'Wobble Vertical', 'unysonplus' ),
				'hvr-wobble-to-bottom-right'=> __( 'Wobble To Bottom Right', 'unysonplus' ),
				'hvr-wobble-to-top-right' 	=> __( 'Wobble To Top Right', 'unysonplus' ),
				'hvr-wobble-top' 	=> __( 'Wobble Top', 'unysonplus' ),
				'hvr-wobble-bottom' => __( 'Wobble Bottom', 'unysonplus' ),
				'hvr-wobble-skew' => __( 'Wobble Skew', 'unysonplus' ),
				'hvr-buzz' 				=> __( 'Buzz', 'unysonplus' ),
				'hvr-buzz-out' 		=> __( 'Buzz Out', 'unysonplus' ),
				'hvr-forward' 		=> __( 'Forward', 'unysonplus' ),
				'hvr-backward' 		=> __( 'Backward', 'unysonplus' ),
			),
		);
	}
endif;


if(! function_exists('unysonplus_option_hover_background')) :
	/**
	 * Background Hover Option
	 */
	function unysonplus_option_hover_background() {
		return array(
			'type'    => 'select',
			'label'   => __( 'Background Transition', 'unysonplus' ),
			'desc'		=> __( '', 'unysonplus' ),
			'value' => '',
			'choices' => array(
				'' 				=> __( 'None', 'unysonplus'),
				'hvr-fade' => __( 'Fade', 'unysonplus' ),
				'hvr-back-pulse' => __( 'Back Pulse', 'unysonplus' ),
				'hvr-sweep-to-right' => __( 'Sweep To Right', 'unysonplus' ),
				'hvr-sweep-to-left' => __( 'Sweep To Left', 'unysonplus' ),
				'hvr-sweep-to-bottom' => __( 'Sweep To Bottom', 'unysonplus' ),
				'hvr-sweep-to-top' => __( 'Sweep To Top', 'unysonplus' ),
				'hvr-bounce-to-right' => __( 'Bounce To Right', 'unysonplus' ),
				'hvr-bounce-to-left' => __( 'Bounce To Left', 'unysonplus' ),
				'hvr-bounce-to-bottom' => __( 'Bounce To Bottom', 'unysonplus' ),
				'hvr-bounce-to-top' => __( 'Bounce To Top', 'unysonplus' ),
				'hvr-radial-out' => __( 'Radial Out', 'unysonplus' ),
				'hvr-radial-in' => __( 'Radial In', 'unysonplus' ),
				'hvr-rectangle-in' => __( 'Rectangle In', 'unysonplus' ),
				'hvr-rectangle-out' => __( 'Rectangle Out', 'unysonplus' ),
				'hvr-shutter-in-horizontal' => __( 'Shutter In Horizontal', 'unysonplus' ),
				'hvr-shutter-out-horizontal' => __( 'Shutter Out Horizontal', 'unysonplus' ),
				'hvr-shutter-in-vertical' => __( 'Shutter In Vertical', 'unysonplus' ),
				'hvr-shutter-out-vertical' => __( 'Shutter Out Vertical', 'unysonplus' ),
			),
		);
	}
endif;


if(! function_exists('unysonplus_option_hover_border')) :
	/**
	 * Border Hover Option
	 */
	function unysonplus_option_hover_border() {
		return array(
			'type'    => 'select',
			'label'   => __( 'Border Transition', 'unysonplus' ),
			'desc'		=> __( '', 'unysonplus' ),
			'value' => '',
			'choices' => array(
				'' 				=> __( 'None', 'unysonplus'),
				'hvr-border-fade' => __( 'Border Fade', 'unysonplus' ),
				'hvr-hollow' => __( 'Hollow', 'unysonplus' ),
				'hvr-trim' => __( 'Trim', 'unysonplus' ),
				'hvr-ripple-out' => __( 'Ripple Out', 'unysonplus' ),
				'hvr-ripple-in' => __( 'Ripple In', 'unysonplus' ),
				'hvr-outline-out' => __( 'Outline Out', 'unysonplus' ),
				'hvr-outline-in' => __( 'Outline In', 'unysonplus' ),
				'hvr-round-corners' => __( 'Round Corners', 'unysonplus' ),
				'hvr-underline-from-left' => __( 'Underline From Left', 'unysonplus' ),
				'hvr-underline-from-center' => __( 'Underline From Center', 'unysonplus' ),
				'hvr-underline-from-right' => __( 'Underline From Right', 'unysonplus' ),
				'hvr-reveal' => __( 'Reveal', 'unysonplus' ),
				'hvr-underline-reveal' => __( 'Underline Reveal', 'unysonplus' ),
				'hvr-overline-reveal' => __( 'Overline Reveal', 'unysonplus' ),
				'hvr-overline-from-left' => __( 'Overline From Left', 'unysonplus' ),
				'hvr-overline-from-center' => __( 'Overline From Center', 'unysonplus' ),
				'hvr-overline-from-right' => __( 'Overline From Right', 'unysonplus' ),
			),
		);
	}
endif;


if(! function_exists('unysonplus_option_hover_shadow')) :
	/**
	 * Shadow and Glow Hover Option
	 */
	function unysonplus_option_hover_shadow() {
		return array(
			'type'    => 'select',
			'label'   => __( 'Shadow and Glow Transition', 'unysonplus' ),
			'desc'		=> __( '', 'unysonplus' ),
			'value' => '',
			'choices' => array(
				'' 				=> __( 'None', 'unysonplus'),
				'hvr-shadow' => __( 'Shadow', 'unysonplus' ),
				'hvr-grow-shadow' => __( 'Grow Shadow', 'unysonplus' ),
				'hvr-float-shadow' => __( 'Float Shadow', 'unysonplus' ),
				'hvr-glow' => __( 'Glow', 'unysonplus' ),
				'hvr-shadow-radial' => __( 'Shadow Radial', 'unysonplus' ),
				'hvr-box-shadow-outset' => __( 'Box Shadow Outset', 'unysonplus' ),
				'hvr-box-shadow-inset' => __( 'Box Shadow Inset', 'unysonplus' ),
			),
		);
	}
endif;


if(! function_exists('unysonplus_option_hover_speech_bubbles')) :
	/**
	 * Speech Bubbles Hover Option
	 */
	function unysonplus_option_hover_speech_bubbles() {
		return array(
			'type'    => 'select',
			'label'   => __( 'Speech Bubbles', 'unysonplus' ),
			'desc'		=> __( '', 'unysonplus' ),
			'value' => '',
			'choices' => array(
				'' 				=> __( 'None', 'unysonplus'),
				'hvr-bubble-top' => __( 'Bubble Top', 'unysonplus' ),
				'hvr-bubble-right' => __( 'Bubble Right', 'unysonplus' ),
				'hvr-bubble-bottom' => __( 'Bubble Bottom', 'unysonplus' ),
				'hvr-bubble-left' => __( 'Bubble Left', 'unysonplus' ),
				'hvr-bubble-float-top' => __( 'Bubble Float Top', 'unysonplus' ),
				'hvr-bubble-float-right' => __( 'Bubble Float Right', 'unysonplus' ),
				'hvr-bubble-float-bottom' => __( 'Bubble Float Bottom', 'unysonplus' ),
				'hvr-bubble-float-left' => __( 'Bubble Float Left', 'unysonplus' ),
			),
		);
	}
endif;


if(! function_exists('unysonplus_option_hover_curls')) :
	/**
	 * Curls Hover Option
	 */
	function unysonplus_option_hover_curls() {
		return array(
			'type'    => 'select',
			'label'   => __( 'Curls', 'unysonplus' ),
			'desc'		=> __( '', 'unysonplus' ),
			'value' => '',
			'choices' => array(
				'' 				=> __( 'None', 'unysonplus'),
				'hvr-curl-top-left' => __( 'Curl Top Left', 'unysonplus' ),
				'hvr-curl-top-right' => __( 'Curl Top Right', 'unysonplus' ),
				'hvr-curl-bottom-right' => __( 'Curl Bottom Right', 'unysonplus' ),
				'hvr-curl-bottom-left' => __( 'Curl Bottom Left', 'unysonplus' ),
			),
		);
	}
endif;


if( ! function_exists('unysonplus_option_alignment') ) :
	function unysonplus_option_alignment() {
		$uri = get_template_directory_uri();
		return array(
			'type'    => 'group',
			'options' => array(
				'alignment' =>	array(
					'label' => __( 'Alignment', 'unysonplus' ),
					'desc'  => __( 'Image alignment', 'unysonplus'),
					'type'  => 'image-picker',
					'value' => '',
					'choices' => array(
						'' => array(
							'small' => array(
								'height' => 50,
								'src' => $uri .'/images/image-picker/align-none.png',
								'title' => __( 'None','unysonplus' )
							),
						),
							'float-start' => array(
							'small' => array(
								'height' => 50,
								'src' => $uri .'/images/image-picker/align-left.png',
								'title' => __( 'Left','unysonplus' )
							),
						),
						'mx-auto d-block' => array(
							'small' => array(
								'height' => 50,
								'src' => $uri .'/images/image-picker/align-center.png',
								'title' => __( 'Center','unysonplus' )
							),
						),
							'float-end' => array(
							'small' => array(
								'height' => 50,
								'src' => $uri .'/images/image-picker/align-right.png',
								'title' => __( 'Right','unysonplus' )
							),
						),
					),
				),
				'alignment_responsive' => array(
					'type' => 'popup',
					'value' => array(
					),
					'label' 		=> __('', 'unysonplus'),
					'desc'  		=> __( '', 'unysonplus' ),
					'popup-title' => __('Responsive Breakpoints', 'unysonplus'),
					'button' 		=> __('Responsive Breakpoints', 'unysonplus'),
					'popup-title' => __('Responsive Breakpoints', 'unysonplus'),
					'size' 			=> 'small', // small, medium, large
					'popup-options' => array(
						'sm' =>	array(
							'label' => __( 'Small', 'unysonplus' ),
							'desc'  => __( 'Small devices (landscape phones, 576px and up)', 'unysonplus' ),
							'type'  => 'image-picker',
							'value' => '',
							'choices' => array(
								'' => array(
									'small' => array(
										'height' => 50,

										'src' => $uri .'/images/image-picker/align-default.png',
										'title' => __( 'Default','unysonplus' )
									),
								),
								'float-sm-none' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-none.png',
										'title' => __( 'None','unysonplus' )
									),
								),
									'float-sm-start' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-left.png',
										'title' => __( 'Left','unysonplus' )
									),
								),
								'mx-sm-auto d-block' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-center.png',
										'title' => __( 'Center','unysonplus' )
									),
								),
									'float-sm-end' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-right.png',
										'title' => __( 'Right','unysonplus' )
									),
								),
							),
						),
						'md' =>	array(
							'label' => __( 'Medium', 'unysonplus' ),
							'desc'  => __( 'Medium devices (tablets, 768px and up)', 'unysonplus' ),
							'type'  => 'image-picker',
							'value' => '',
							'choices' => array(
								'' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-default.png',
										'title' => __( 'Default','unysonplus' )
									),
								),
								'float-md-none' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-none.png',
										'title' => __( 'None','unysonplus' )
									),
								),
									'float-md-start' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-left.png',
										'title' => __( 'Left','unysonplus' )
									),
								),
								'mx-md-auto d-block' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-center.png',
										'title' => __( 'Center','unysonplus' )
									),
								),
									'float-md-end' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-right.png',
										'title' => __( 'Right','unysonplus' )
									),
								),
							),
						),
						'lg' =>	array(
							'label' => __( 'Large', 'unysonplus' ),
							'desc'  => __( 'Large devices (desktops, 992px and up)', 'unysonplus' ),
							'type'  => 'image-picker',
							'value' => '',
							'choices' => array(
								'' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-default.png',
										'title' => __( 'Default','unysonplus' )
									),
								),
								'float-lg-none' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-none.png',
										'title' => __( 'None','unysonplus' )
									),
								),
									'float-lg-start' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-left.png',
										'title' => __( 'Left','unysonplus' )
									),
								),
								'mx-lg-auto d-block' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-center.png',
										'title' => __( 'Center','unysonplus' )
									),
								),
									'float-lg-end' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-right.png',
										'title' => __( 'Right','unysonplus' )
									),
								),
							),
						),
						'xl' =>	array(
							'label' => __( 'Extra Large', 'unysonplus' ),
							'desc'  => __( 'Extra large devices (large desktops, 1200px and up)', 'unysonplus' ),
							'type'  => 'image-picker',
							'value' => '',
							'choices' => array(
								'' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-default.png',
										'title' => __( 'Default','unysonplus' )
									),
								),
								'float-xl-none' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-none.png',
										'title' => __( 'None','unysonplus' )
									),
								),
									'float-xl-start' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-left.png',
										'title' => __( 'Left','unysonplus' )
									),
								),
								'mx-xl-auto d-block' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-center.png',
										'title' => __( 'Center','unysonplus' )
									),
								),
									'float-xl-end' => array(
									'small' => array(
										'height' => 50,
										'src' => $uri .'/images/image-picker/align-right.png',
										'title' => __( 'Right','unysonplus' )
									),
								),
							),
						),
					),
				),
			),
		);
	}
endif;


if(! function_exists('unysonplus_option_text_alignment')) :
	/**
	 *  Options for Text Alignment
	 */
	function unysonplus_option_text_alignment() {
		return array(
			'type'    => 'select',
			'label'   => __('Text Alignment', 'unysonplus'),
			'desc'		=> __('', 'unysonplus'),
			'choices' => array(
				'' 				=> 'Default',
					'text-start' 	=> 'Left aligned text',
				'text-center' 	=> 'Center aligned text',
					'text-end' 	=> 'Right aligned text',
				'text-justify' 	=> 'Justified text',
				'text-nowrap' 	=> 'No wrap text',
			)
		);
	}
endif;


if(! function_exists('unysonplus_options_vertical_center_container')) :
	/**
	 *  Get the image from options
	 */
	function unysonplus_options_vertical_center_container($atts,$tag) {
		if ( isset( $atts['is_vertical_center'] ) && $atts['is_vertical_center'] ) {
			if($tag == 'start') {
				return '<div '. fw_attr_to_html(array('class' => 'vc-container')) .'>';;
			}elseif($tag == 'end'){
				return '</div>';
			}else{
				return;
			}
		}else{
			return;
		}
	}
endif;


if(! function_exists('unysonplus_option_animate')) :
	/**
	 *  Animate Options
	 */
	function unysonplus_option_animate() {
		return array(
			'animation'   => array(
				'label'   => __( 'Animation', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '',
				'desc'    => __( 'Select animation.','unysonplus' ),
				'choices' => array(
					'' => __( 'None', 'unysonplus' ),				
					array(
						'attr'    => array(
							'label'         => __( 'Attention Seekers', 'unysonplus' ),
						),
						'choices' => array(
							'bounce' => __( 'bounce', 'unysonplus' ),
							'flash' => __( 'flash', 'unysonplus' ),
							'pulse' => __( 'pulse', 'unysonplus' ),
							'rubberBand' => __( 'rubberBand', 'unysonplus' ),
							'shake' => __( 'shake', 'unysonplus' ),
							'swing' => __( 'swing', 'unysonplus' ),
							'tada' => __( 'tada', 'unysonplus' ),
							'wobble' => __( 'wobble', 'unysonplus' ),
							'jello' => __( 'jello', 'unysonplus' ),
						),
					),	
					array(
						'attr'    => array(
							'label'         => __( 'Bouncing Entrances', 'unysonplus' ),
						),
						'choices' => array(
							'bounceIn' => __( 'bounceIn', 'unysonplus' ),
							'bounceInDown' => __( 'bounceInDown', 'unysonplus' ),
							'bounceInLeft' => __( 'bounceInLeft', 'unysonplus' ),
							'bounceInRight' => __( 'bounceInRight', 'unysonplus' ),
							'bounceInUp' => __( 'bounceInUp', 'unysonplus' ),
						),
					),	
				/*	array(
						'attr'    => array(
							'label'         => __( 'Bouncing Exits', 'unysonplus' ),
						),
						'choices' => array(
							'bounceOut' => __( 'bounceOut', 'unysonplus' ),
							'bounceOutDown' => __( 'bounceOutDown', 'unysonplus' ),
							'bounceOutLeft' => __( 'bounceOutLeft', 'unysonplus' ),
							'bounceOutRight' => __( 'bounceOutRight', 'unysonplus' ),
							'bounceOutUp' => __( 'bounceOutUp', 'unysonplus' ),
						),
					),	*/
					array(
						'attr'    => array(
							'label'         => __( 'Fading Entrances', 'unysonplus' ),
						),
						'choices' => array(
							'fadeIn' => __( 'fadeIn', 'unysonplus' ),
							'fadeInDown' => __( 'fadeInDown', 'unysonplus' ),
							'fadeInDownBig' => __( 'fadeInDownBig', 'unysonplus' ),
							'fadeInLeft' => __( 'fadeInLeft', 'unysonplus' ),
							'fadeInLeftBig' => __( 'fadeInLeftBig', 'unysonplus' ),
							'fadeInRight' => __( 'fadeInRight', 'unysonplus' ),
							'fadeInRightBig' => __( 'fadeInRightBig', 'unysonplus' ),
							'fadeInUp' => __( 'fadeInUp', 'unysonplus' ),
							'fadeInUpBig' => __( 'fadeInUpBig', 'unysonplus' ),
						),
					),	
				/*	array(
						'attr'    => array(
							'label'         => __( 'Fading Exits', 'unysonplus' ),
						),
						'choices' => array(
							'fadeOut' => __( 'fadeOut', 'unysonplus' ),
							'fadeOutDown' => __( 'fadeOutDown', 'unysonplus' ),
							'fadeOutDownBig' => __( 'fadeOutDownBig', 'unysonplus' ),
							'fadeOutLeft' => __( 'fadeOutLeft', 'unysonplus' ),
							'fadeOutLeftBig' => __( 'fadeOutLeftBig', 'unysonplus' ),
							'fadeOutRight' => __( 'fadeOutRight', 'unysonplus' ),
							'fadeOutRightBig' => __( 'fadeOutRightBig', 'unysonplus' ),
							'fadeOutUp' => __( 'fadeOutUp', 'unysonplus' ),
							'fadeOutUpBig' => __( 'fadeOutUpBig', 'unysonplus' ),
						),
					),	*/
					array(
						'attr'    => array(
							'label'         => __( 'Flippers', 'unysonplus' ),
						),
						'choices' => array(
							'flip' => __( 'flip', 'unysonplus' ),
							'flipInX' => __( 'flipInX', 'unysonplus' ),
							'flipInY' => __( 'flipInY', 'unysonplus' ),
							'flipOutX' => __( 'flipOutX', 'unysonplus' ),
							'flipOutY' => __( 'flipOutY', 'unysonplus' ),
						),
					),	
					array(
						'attr'    => array(
							'label'         => __( 'Lightspeed', 'unysonplus' ),
						),
						'choices' => array(
							'lightSpeedIn' => __( 'lightSpeedIn', 'unysonplus' ),
							'lightSpeedOut' => __( 'lightSpeedOut', 'unysonplus' ),
						),
					),	
					array(
						'attr'    => array(
							'label'         => __( 'Rotating Entrances', 'unysonplus' ),
						),
						'choices' => array(
							'rotateIn' => __( 'rotateIn', 'unysonplus' ),
							'rotateInDownLeft' => __( 'rotateInDownLeft', 'unysonplus' ),
							'rotateInDownRight' => __( 'rotateInDownRight', 'unysonplus' ),
							'rotateInUpLeft' => __( 'rotateInUpLeft', 'unysonplus' ),
							'rotateInUpRight' => __( 'rotateInUpRight', 'unysonplus' ),
						),
					),	
			/*		array(
						'attr'    => array(
							'label'         => __( 'Rotating Exits', 'unysonplus' ),
						),
						'choices' => array(
							'rotateOut' => __( 'rotateOut', 'unysonplus' ),
							'rotateOutDownLeft' => __( 'rotateOutDownLeft', 'unysonplus' ),
							'rotateOutDownRight' => __( 'rotateOutDownRight', 'unysonplus' ),
							'rotateOutUpLeft' => __( 'rotateOutUpLeft', 'unysonplus' ),
							'rotateOutUpRight' => __( 'rotateOutUpRight', 'unysonplus' ),
						),
					),	*/
					array(
						'attr'    => array(
							'label'         => __( 'Sliding Entrances', 'unysonplus' ),
						),
						'choices' => array(
							'slideInUp' => __( 'slideInUp', 'unysonplus' ),
							'slideInDown' => __( 'slideInDown', 'unysonplus' ),
							'slideInLeft' => __( 'slideInLeft', 'unysonplus' ),
							'slideInRight' => __( 'slideInRight', 'unysonplus' ),
						),
					),	
			/*		array(
						'attr'    => array(
							'label'         => __( 'Sliding Exits', 'unysonplus' ),
						),
						'choices' => array(
							'slideOutUp' => __( 'slideOutUp', 'unysonplus' ),
							'slideOutDown' => __( 'slideOutDown', 'unysonplus' ),
							'slideOutLeft' => __( 'slideOutLeft', 'unysonplus' ),
							'slideOutRight' => __( 'slideOutRight', 'unysonplus' ),
						),
					),	*/
					array(
						'attr'    => array(
							'label'         => __( 'Zoom Entrances', 'unysonplus' ),
						),
						'choices' => array(
							'zoomIn' => __( 'zoomIn', 'unysonplus' ),
							'zoomInDown' => __( 'zoomInDown', 'unysonplus' ),
							'zoomInLeft' => __( 'zoomInLeft', 'unysonplus' ),
							'zoomInRight' => __( 'zoomInRight', 'unysonplus' ),
							'zoomInUp' => __( 'zoomInUp', 'unysonplus' ),
						),
					),	
			/*		array(
						'attr'    => array(
							'label'         => __( 'Zoom Exits', 'unysonplus' ),
						),
						'choices' => array(
							'zoomOut' => __( 'zoomOut', 'unysonplus' ),
							'zoomOutDown' => __( 'zoomOutDown', 'unysonplus' ),
							'zoomOutLeft' => __( 'zoomOutLeft', 'unysonplus' ),
							'zoomOutRight' => __( 'zoomOutRight', 'unysonplus' ),
							'zoomOutUp' => __( 'zoomOutUp', 'unysonplus' ),
						),
					),	*/
					array(
						'attr'    => array(
							'label'         => __( 'Specials', 'unysonplus' ),
						),
						'choices' => array(
							'hinge' => __( 'hinge', 'unysonplus' ),
							'rollIn' => __( 'rollIn', 'unysonplus' ),
							'rollOut' => __( 'rollOut', 'unysonplus' ),
						),
					),						
				),
			),
			'duration'                => array(
				'label' => __( 'Duration', 'unysonplus' ),
				'type'  => 'short-text',
				'value' => NULL,
				'desc'  => __( 'Change the animation duration. ',	'unysonplus' ),
				'help'  => sprintf( "%s<br />%s",
					__( 'E.g.: <b>2s</b> for 2 seconds.', 'unysonplus' ),
					__( 'Leave blank to disable.', 'unysonplus' )
				),
			),
			'delay'                => array(
				'label' => __( 'Delay', 'unysonplus' ),
				'type'  => 'short-text',
				'value' => NULL,
				'desc'  => __( 'The delay before the animation starts. ',	'unysonplus' ),
				'help'  => sprintf( "%s<br />%s",
					__( 'E.g.: <b>5s</b> for 5 seconds.', 'unysonplus' ),
					__( 'Leave blank to disable.', 'unysonplus' )
				),
			),
			'offset'                => array(
				'label' => __( 'Offset', 'unysonplus' ),
				'type'  => 'short-text',
				'value' => '',
				'desc'  => __( 'The distance to start the animation (related to the browser bottom).',	'unysonplus' ),
				'help'  => sprintf( "%s<br />%s",
					__( 'E.g.: <b>10</b> for 10px.', 'unysonplus' ),
					__( 'Leave blank to disable.', 'unysonplus' )
				),
			),
			'iteration' => array(
				'label' => __( 'Iteration', 'unysonplus' ),
				'type'  => 'short-text',
				'value' => NULL,
				'desc'  => __( 'Number of times the animation is repeated.','unysonplus' ),
				'help'  => sprintf( "%s<br />%s<br />%s",
					__( 'E.g.: <b>10</b> for 10 times.', 'unysonplus' ),
					__( 'Type <b>infinite</b> for infinite loop.', 'unysonplus' ),
					__( 'Leave blank to disable.', 'unysonplus' )
				),
			),
		);
	}
endif;


if(! function_exists('unysonplus_option_visibility')) :
	/**
	 *  Visibility Options
	 */
	function unysonplus_option_visibility() {
		$user_choices = array(
			'' => __( 'Visible for all', 'unysonplus' ),
			'logged-in' => __( 'Visible for Logged in user', 'unysonplus' ),
			'logged-out' => __( 'Visible for Logged out user', 'unysonplus' ),
		);

		$wp_roles = wp_roles();
		$roles = $wp_roles->get_names();
		foreach($roles as $key => $role) {
			$user_choices['visible-'.$key] = __( 'Visible for '.$role.' user', 'unysonplus' );
		}
		$user_choices['hidden'] = __( 'Hidden', 'unysonplus' );

		return array(
			'label'         => false,
			'type'          => 'multi',
			'value'         => array(),
			'desc'          => false,
			'inner-options' => array(
				'responsive' => array(
					'label'   => __( 'Visibility', 'unysonplus' ),
					'type'    => 'select-multiple',
					'value'   => '',
					'desc'    => __( 'Device\'s Responsiveness Visibility.','unysonplus' ),
					'choices' => array(
						'd-none' 								=> __( 'Hidden on all devices', 'unysonplus' ),
						'd-none d-sm-block' 		=> __( 'Hidden only on Extra Small devices. (x < 577px)', 'unysonplus' ),
						'd-sm-none d-md-block' 	=> __( 'Hidden only on Small devices. (576px > x < 768px)', 'unysonplus' ),
						'd-md-none d-lg-block' 	=> __( 'Hidden only on Medium devices. (767px > x < 993px)', 'unysonplus' ),
						'd-lg-none d-xl-block' 	=> __( 'Hidden only on Large devices. (992px > x < 1201px)', 'unysonplus' ),
						'd-xl-none' 						=> __( 'Hidden only on Extra Large devices. (x > 1200px)', 'unysonplus' ),
						''														=> __( 'Visible on all devices', 'unysonplus' ),
						'd-block d-sm-none' 					=> __( 'Visible only on Extra Small devices. (x < 577px)', 'unysonplus' ),
						'd-none d-sm-block d-md-none'	=> __( 'Visible only on Small devices. (576px > x < 768px)', 'unysonplus' ),
						'd-none d-md-block d-lg-none'	=> __( 'Visible only on Medium devices. (767px > x < 993px)', 'unysonplus' ),
						'd-none d-lg-block d-xl-none'	=> __( 'Visible only on Large devices. (992px > x < 1201px)', 'unysonplus' ),
						'd-none d-xl-block' 					=> __( 'Visible only on Extra Large devices. (x > 1200px)', 'unysonplus' ),
					),
					'help' 	=> sprintf( "%s",
						__( 'Ctrl + Click to select multiple choices.','unysonplus' )
					),
				),
				'user' => array(
					'label'   => __( '', 'unysonplus' ),
					'type'    => 'select-multiple',
					'value'   => '',
					'desc'    => __( 'User Visibility','unysonplus' ),
					'choices' => $user_choices,
					'help' 	=> sprintf( "%s",
						__( 'Ctrl + Click to select multiple choices.','unysonplus' )
					),
				),
			),
		);
	}
endif;


if(! function_exists('unysonplus_options_get_user_visibility')) :
/**
 *  Get Visibility Options
 */
function unysonplus_options_get_user_visibility($atts) {
	
	if(!empty($atts['visibility']['user'])) {
		if(!empty($atts['visibility']['user'][0])) {

			$wp_roles = wp_roles();
			$roles = $wp_roles->get_names();
			$current_user_roles = wp_get_current_user()->roles;
			if(
				( in_array( 'logged-in', $atts['visibility']['user']) && is_user_logged_in() ) || 
				( in_array( 'logged-out', $atts['visibility']['user']) && !is_user_logged_in() ) ||
				( in_array( 'hidden', $atts['visibility']['user']) && !is_user_logged_in() )
			){
			}else{
				if(!empty($current_user_roles)) {
					foreach($roles as $key => $role) {
						foreach($current_user_roles as $current_user_role) {
							$check = 'visible-'.$current_user_role;
							if(!in_array( $check, $atts['visibility']['user'])) {
								$set_visible = true;
							}
						}
					}
					if(isset($set_visible)) return true;
				}else{
					return true;
				}
				
				
			}
		}		
	}
}
endif;


if(! function_exists('unysonplus_get_shortcode_attr')) :
/**
 *  Get Shortcode Attributes
 */
function unysonplus_get_shortcode_attr($atts) {
	//The classes for the block
	$class = array();
	$class[] = $atts['shortcode'];
	if(!empty($atts['animate']['animation'])) {
		$class[] = 'wow';
		$class[] = $atts['animate']['animation'];
	}
	if(!empty($atts['visibility']['responsive'])) {
		$class[] = $atts['visibility']['responsive'];
	}
	if(!empty($atts['visibility']['user'])) {
		if(( $atts['visibility']['user'] == 'logged-in') && !is_user_logged_in() ||
			($atts['visibility']['user'] == 'logged-out') && is_user_logged_in() ||
			($atts['visibility']['user'] == 'hidden')){
			$class[] = 'hidden';
		}
	}
	if(!empty($atts['class'])) {
		$class[] = $atts['class'];
	}
	$class = join( ' ', $class );
	
	//The attributes for the block
	$attr['class'] = $class;
	if(!empty($atts['custom_id'])){
		$attr['id'] = $atts['custom_id'];
	}
	if(!empty($atts['animate']['duration'])){
		$attr['data-wow-duration'] = $atts['animate']['duration'];
	}
	if(!empty($atts['animate']['delay'])){
		$attr['data-wow-delay'] = $atts['animate']['delay'];
	}
	if(!empty($atts['animate']['offset'])){
		$attr['data-wow-offset'] = $atts['animate']['offset'];
	}
	if(!empty($atts['animate']['iteration'])){
		$attr['data-wow-iteration'] = $atts['animate']['iteration'];
	}
	return $attr;
}
endif;


if(!function_exists('unysonplus_option_spacing')) :
	/**
	 * Spacing Options
	 */
	function unysonplus_option_spacing( $default = NULL ) {
		return array(
			'type'         => 'multi-picker',
			'label'        => false,
			'desc'         => false,
			'value'        => array(
				'selected' => 'bootstrap',
				'bootstrap' => $default,
			),
			'picker'       => array(
				'selected' => array(
					'label'   => __( 'Spacing', 'unysonplus' ),
					'type'    => 'select',
					'choices' => array(
						'bootstrap' => __( 'Bootstrap margins and paddings (Recommended)', 'unysonplus' ),
						'custom' 		=> __( 'Custom margins and paddings', 'unysonplus' )
					),
					'desc'    => __( 'Select spacing method.', 'unysonplus' ),
					'help'    => __( 'Using custom method will add new CSS classes for each element.', 'unysonplus' ),
				)
			),
			'choices'      => array(
				'bootstrap'  => array(
					'all'   => unysonplus_option_bs_spacing( '' ),
					'responsive' => array(
						'type' => 'popup',
						'value' => array(
						),
						'label' 		=> __('', 'unysonplus'),
						'desc'  		=> __( '', 'unysonplus' ),
						'popup-title' => __('Responsive Breakpoints', 'unysonplus'),
						'button' 		=> __('Responsive Breakpoints', 'unysonplus'),
						'popup-title' => __('Responsive Breakpoints', 'unysonplus'),
						'size' 			=> 'medium', // small, medium, large
						'popup-options' => array(
							'sm'		=> unysonplus_option_bs_spacing( 'sm' ),
							'md'  	=> unysonplus_option_bs_spacing( 'md' ),
							'lg'  	=> unysonplus_option_bs_spacing( 'lg' ),
							'xl'  	=> unysonplus_option_bs_spacing( 'xl' ),
						),
					),
				),
				'custom' => array(
					'mall'	=> unysonplus_option_box( '', 'Margin for all devices' ),
					'pall' 	=> unysonplus_option_box( '', 'Padding for all devices' ),
					'responsive' => array(
						'type' => 'popup',
						'value' => array(
						),
						'label' 		=> __('', 'unysonplus'),
						'desc'  		=> __( '', 'unysonplus' ),
						'popup-title' => __('Responsive Breakpoints', 'unysonplus'),
						'button' 		=> __('Responsive Breakpoints', 'unysonplus'),
						'popup-title' => __('Responsive Breakpoints', 'unysonplus'),
						'size' 			=> 'medium', // small, medium, large
						'popup-options' => array(
							'msm'		=> unysonplus_option_box( 'Phones', 'Margin for small devices (landscape phones, <strong>576px</strong> and up)' ),
							'psm' 	=> unysonplus_option_box( '', 'Padding for small devices (landscape phones, <strong>576px</strong> and up)' ),
							'mmd'		=> unysonplus_option_box( 'Tablets', 'Margin for medium devices (tablets  phones, <strong>768px</strong> and up)' ),
							'pmd' 	=> unysonplus_option_box( '', 'Padding for medium devices (tablets  phones, <strong>768px</strong> and up)' ),
							'mlg'		=> unysonplus_option_box( 'Desktops', 'Margin for large devices (desktops, <strong>992px</strong> and up)' ),
							'plg' 	=> unysonplus_option_box( '', 'Padding for large devices (desktops, <strong>992px</strong> and up)' ),
							'mxl'		=> unysonplus_option_box( 'Large Desktops', 'Margin for extra large devices (large desktops, <strong>1200px</strong> and up)' ),
							'pxl' 	=> unysonplus_option_box( '', 'Padding for extra large devices (large desktops, <strong>1200px</strong> and up)' ),
						),
					),
				),
			),
			'show_borders' => false,
		);
	}
endif;


if(!function_exists('unysonplus_option_bs_spacing')) :
	/**
	 * Margin & Padding Options
	 */
	function unysonplus_option_bs_spacing( $breakpoint ) {
		if( $breakpoint == 'sm' ) {
			$breakpointlabel = 'Phones';
			$breakpointdesc = 'Margin and Padding for small devices (landscape phones, <strong>576px</strong> and up)';
		}elseif( $breakpoint == 'md' ) {
			$breakpointlabel = 'Tablets';
			$breakpointdesc = 'Margin and Padding for medium devices (tablets  phones, <strong>768px</strong> and up)';
		}elseif( $breakpoint == 'lg' ) {
			$breakpointlabel = 'Desktops';
			$breakpointdesc = 'Margin and Padding for large devices (desktops, <strong>992px</strong> and up)';
		}elseif( $breakpoint == 'xl' ) {
			$breakpointlabel = 'Large Desktops';
			$breakpointdesc = 'Margin and Padding for extra large devices (large desktops, <strong>1200px</strong> and up)';
		}else{
			$breakpointlabel = '';
			$breakpointdesc = 'Margin and Padding for all devices';
		}
		return array(
			'type'      => 'multi-select',
			'label'     => __( $breakpointlabel, 'unysonplus' ),
			'desc'      => __( $breakpointdesc,	'unysonplus' ),
			//'value'			=> array( 'py-4' ),
			'population'=> 'array',
			'choices'   => unysonplus_option_bs_spacing_choices( $breakpoint ),
		);
	}
endif;


if(!function_exists('unysonplus_option_bs_spacing_choices')) :
	/**
	 * Margin & Padding Options
	 */
	function unysonplus_option_bs_spacing_choices( $breakpoint ) {
		return array_merge(
			unysonplus_option_bs_spacing_size_choices( 'm', '', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 't', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 'r', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 'b', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 'l', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 'x', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 'y', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'p', '', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'p', 't', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'p', 'r', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'p', 'b', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'p', 'l', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'p', 'x', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'p', 'y', $breakpoint )
		);
	}
endif;


if(!function_exists('unysonplus_option_bs_margin')) :
	/**
	 * Margin & Padding Options
	 */
	function unysonplus_option_bs_margin( $breakpoint ) {
		if( $breakpoint == 'sm' ) {
			$breakpointlabel = 'Phones';
			$breakpointdesc = 'Margin for small devices (landscape phones, <strong>576px</strong> and up)';
		}elseif( $breakpoint == 'md' ) {
			$breakpointlabel = 'Tablets';
			$breakpointdesc = 'Margin for medium devices (tablets  phones, <strong>768px</strong> and up)';
		}elseif( $breakpoint == 'lg' ) {
			$breakpointlabel = 'Desktops';
			$breakpointdesc = 'Margin for large devices (desktops, <strong>992px</strong> and up)';
		}elseif( $breakpoint == 'xl' ) {
			$breakpointlabel = 'Large Desktops';
			$breakpointdesc = 'Margin for extra large devices (large desktops, <strong>1200px</strong> and up)';
		}else{
			$breakpointlabel = '';
			$breakpointdesc = 'Margin for all devices';
		}
		return array(
			'type'      => 'multi-select',
			'label'     => __( $breakpointlabel, 'unysonplus' ),
			'desc'      => __( $breakpointdesc,	'unysonplus' ),
			//'value'			=> array( 'py-4' ),
			'population'=> 'array',
			'choices'   => unysonplus_option_bs_margin_choices( $breakpoint ),
		);
	}
endif;


if(!function_exists('unysonplus_option_bs_margin_choices')) :
	/**
	 * Margin & Padding Options
	 */
	function unysonplus_option_bs_margin_choices( $breakpoint ) {
		return array_merge(
			unysonplus_option_bs_spacing_size_choices( 'm', '', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 't', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 'r', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 'b', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 'l', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 'x', $breakpoint ),
			unysonplus_option_bs_spacing_size_choices( 'm', 'y', $breakpoint )
		);
	}
endif;


if(!function_exists('unysonplus_option_bs_spacing_size_choices')) :
	/**
	 * Margin & Padding Options
	 */
	function unysonplus_option_bs_spacing_size_choices( $property, $sides, $breakpoint ) {
		$spacer = 16;
		if( $property == 'm' ) {
			$propertytext = 'margin';
		}
		if( $property == 'p' ) {
			$propertytext = 'padding';
		}
		if( $sides == 't' ) {
			$sidestext = ' top';
		}elseif( $sides == 'r' ) {
			$sidestext = ' right';
		}elseif( $sides == 'b' ) {
			$sidestext = ' bottom';
		}elseif( $sides == 'l' ) {
			$sidestext = ' left';
		}elseif( $sides == 'x' ) {
			$sidestext = ' left and right';
		}elseif( $sides == 'y' ) {
			$sidestext = ' top and bottom';
		}else{
			$sidestext = '';
		}
		if( !empty($breakpoint) )		$breakpoint = '-' . $breakpoint;
		return array(
			$property . $sides . $breakpoint . '-0' 	=> __( $propertytext . $sidestext . ' - none ' . ' (' . ($spacer * 0) . 'px)', 'unysonplus' ),
			$property . $sides . $breakpoint . '-1' 	=> __( $propertytext . $sidestext . ' - extra small ' . ' (' . ($spacer * .25) . 'px)', 'unysonplus' ),
			$property . $sides . $breakpoint . '-2' 	=> __( $propertytext . $sidestext . ' - small ' . ' (' . ($spacer * .5) . 'px)', 'unysonplus' ),
			$property . $sides . $breakpoint . '-3' 	=> __( $propertytext . $sidestext . ' - medium ' . ' (' . $spacer . 'px)', 'unysonplus' ),
			$property . $sides . $breakpoint . '-4' 	=> __( $propertytext . $sidestext . ' - large ' . ' (' . ($spacer * 1.5) . 'px)', 'unysonplus' ),
			$property . $sides . $breakpoint . '-5' 	=> __( $propertytext . $sidestext . ' - extra large ' . ' (' . ($spacer * 3) . 'px)', 'unysonplus' ),
			$property . $sides . $breakpoint . '-auto' 	=> __( $propertytext . $sidestext . ' - auto ', 'unysonplus' ),
		);
	}
endif;


if(!function_exists('unysonplus_option_margin')) :
	/**
	 * Margin & Padding Options
	 */
	function unysonplus_option_margin() {
		return array(
			'type'         => 'multi-picker',
			'label'        => false,
			'desc'         => false,
			'value'        => array(
				'selected' => 'bootstrap',
				'bootstrap' => null,
			),
			'picker'       => array(
				'selected' => array(
					'label'   => __( 'Spacing', 'unysonplus' ),
					'type'    => 'select',
					'choices' => array(
						'bootstrap' => __( 'Bootstrap margins (Recommended)', 'unysonplus' ),
						'custom' 		=> __( 'Custom margins', 'unysonplus' )
					),
					'desc'    => __( 'Select spacing method.', 'unysonplus' ),
					'help'    => __( 'Using custom method will add new CSS classes for each element.', 'unysonplus' ),
				)
			),
			'choices'      => array(
				'bootstrap'  => array(
					'all'   => unysonplus_option_bs_margin( '' ),
					'responsive' => array(
						'type' => 'popup',
						'value' => array(
						),
						'label' 		=> __('', 'unysonplus'),
						'desc'  		=> __( '', 'unysonplus' ),
						'popup-title' => __('Responsive Breakpoints', 'unysonplus'),
						'button' 		=> __('Responsive Breakpoints', 'unysonplus'),
						'popup-title' => __('Responsive Breakpoints', 'unysonplus'),
						'size' 			=> 'medium', // small, medium, large
						'popup-options' => array(
							'sm'		=> unysonplus_option_bs_margin( 'sm' ),
							'md'  	=> unysonplus_option_bs_margin( 'md' ),
							'lg'  	=> unysonplus_option_bs_margin( 'lg' ),
							'xl'  	=> unysonplus_option_bs_margin( 'xl' ),
						),
					),
				),
				'custom' => array(
					'mall'	=> unysonplus_option_box( '', 'Margin for all devices' ),
					'responsive' => array(
						'type' => 'popup',
						'value' => array(
						),
						'label' 		=> __('', 'unysonplus'),
						'desc'  		=> __( '', 'unysonplus' ),
						'popup-title' => __('Responsive Breakpoints', 'unysonplus'),
						'button' 		=> __('Responsive Breakpoints', 'unysonplus'),
						'popup-title' => __('Responsive Breakpoints', 'unysonplus'),
						'size' 			=> 'medium', // small, medium, large
						'popup-options' => array(
							'msm'		=> unysonplus_option_box( 'Phones', 'Margin for small devices (landscape phones, <strong>576px</strong> and up)' ),
							'mmd'		=> unysonplus_option_box( 'Tablets', 'Margin for medium devices (tablets  phones, <strong>768px</strong> and up)' ),
							'mlg'		=> unysonplus_option_box( 'Desktops', 'Margin for large devices (desktops, <strong>992px</strong> and up)' ),
							'mxl'		=> unysonplus_option_box( 'Large Desktops', 'Margin for extra large devices (large desktops, <strong>1200px</strong> and up)' ),
						),
					),
				),
			),
			'show_borders' => false,
		);
	}
endif;



if(!function_exists('unysonplus_option_box')) :
	/**
	 * Margin & Padding Options
	 */
	function unysonplus_option_box($label, $desc=NULL, $top=NULL, $right=NULL, $bottom=NULL, $left=NULL) {
		return array(
			'type' 	=> 'fw-multi-inline',
			'label' => __($label, 'unysonplus'),
			'desc' 	=> __($desc, 'unysonplus'),
			'value' => array(
				'top' 	 	=> $top,
				'right'  	=> $right,
				'bottom' 	=> $bottom,
				'left' 	 	=> $left,	
			),
			'help'      => __( 'Input values in pixels. i.e.: 60',	'unysonplus' ),
			'fw_multi_options' => array(
				'top' => array(
					'type' 	=>'short-text',
					'title' => __('Top', 'unysonplus'),
				),
				'right' => array(
					'type' 	=>'short-text',
					'title' => __('Right', 'unysonplus'),
				),
				'bottom' => array(
					'type' 	=>'short-text',
					'title' => __('Bottom', 'unysonplus'),
				),
				'left' => array(
					'type' 	=>'short-text',
					'title' => __('Left', 'unysonplus'),
				),
			)
		);
	}
endif;


if(!function_exists('unysonplus_option_box_border')) :
	/**
	 * Border Options
	 */
	function unysonplus_option_box_border($label,$top='',$right='',$bottom='',$left='') {
		return array(
			'type' => 'checkboxes',
			'label' => __($label, 'unysonplus'),
			//'desc' => __('', 'unysonplus'),
			'value' => array(
				'top' 	=>$top,
				'right' =>$right,
				'bottom'=>$bottom,
				'left' 	=>$left,	
			),
			'choices' => array(
				'top' 	=> __('Top', 'unysonplus'),
				'right' => __('Right', 'unysonplus'),
				'bottom'=> __('Bottom', 'unysonplus'),
				'left' 	=> __('Left', 'unysonplus'),
			),
			'inline' => true,
			'attr'  => array( 'class' => 'border-options'),
		);
	}
endif; 


if(!function_exists('unysonplus_get_options_box_border')) :
	/**
	 * Get Border Options
	 */
	function unysonplus_get_options_box_border($atts) {
		$border = array(
			'border'				=> array('top' => true, 'right' => true, 'bottom' => true, 'left' => true),
			'border-top'		=> array('top' => true),
			'border-right'	=> array('right' => true),
			'border-bottom'	=> array('bottom' => true),
			'border-left'		=> array('left' => true),
			//'border-0'		=> array('top' => true, 'right' => true, 'bottom' => true, 'left' => true),
			'border-top-0'	=> array('right' => true, 'bottom' => true, 'left' => true),
			'border-right-0'=> array('top' => true, 'bottom' => true, 'left' => true),
			'border-bottom-0'=> array('top' => true, 'right' => true, 'left' => true),
			'border-left-0'	=> array('top' => true, 'right' => true, 'bottom' => true),
		);

		while ($bordervalue = current($border)) {
			if ($bordervalue == $atts['side']) {
					$border_value =  key($border);
			}
			next($border);
		}
		
		if(empty($border_value) && array_filter($atts['side'])){
			foreach($atts['side'] as $key => $value)
			{
				$atts['side'][$key] = 'border-'.$key;
			} 
			return join(' ', $atts['side']);
		}else{
			return $border_value;
		}
	}
endif;


if(!function_exists('unysonplus_option_box_border_radius')) :
	/**
	 * Border Radius Options
	 */
	function unysonplus_option_box_border_radius($label) {
		return array(
			'type'    => 'select',
			'label'   => __('', 'unysonplus'),
			'desc'   	=> $label,
			'value'   => '',
			'choices' => array(
				''  						=> 'none',
				'rounded' 			=> 'Rounded',
				'rounded-top'		=> 'Rounded Top',
				'rounded-right' => 'Rounded Right',
				'rounded-bottom'=> 'Rounded Bottom',
				'rounded-left' 	=> 'Rounded Left',
				'rounded-circle'=> 'Circle',
			)
		);
	}
endif; 

// This function is deprecated
if(! function_exists('get_css_box_measurements')){
	function get_css_box_measurements($side_size) {
		if($side_size['select'] == 'custom'):
			return 'unquote("'.$side_size['custom']['size'].'")';
		else:
			return $side_size['select'];
		endif;
	}
}


if( !function_exists('unysonplus_option_advanced_tab') ):
	/**
	 * Get Link
	 */
	function unysonplus_option_advanced_tab() {
		return array(
			'tab_advanced' => array(
				'title'   => __( 'Advanced', 'unysonplus' ),
				'type'    => 'tab',
				'options' => array(
					'advanced_group' => array(
						'title'   => __( 'Advanced', 'unysonplus' ),
						'type'    => 'group',
						'options' => array(
							'id' => array(
								'label' => __('ID', 'unysonplus'),
								'desc'  => __('', 'unysonplus'),
								'type'  => 'text',
							),
							'class' => array(
								'label' => __('Class', 'unysonplus'),
								'desc'  => __('', 'unysonplus'),
								'type'  => 'text',
							)
						),
					),
				),
			)
		
		);
	}
endif;
