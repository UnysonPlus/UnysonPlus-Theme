<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Demo-only helper: build a `predefined-colors-color-picker-compact` choices
 * array from the live plugin palette. Keyed by `text-{slug}` or `bg-{slug}`
 * so the saved class name can be emitted verbatim by the consuming view.
 *
 * Duplicated in demo.php — both demo files load via separate
 * fw_get_variables_from_file() calls in demo-box.php and either can run
 * first depending on tab order, so each defines the helper behind a
 * function_exists guard. The second loader skips re-declaration cleanly.
 */
if ( ! function_exists( 'unysonplus_demo_compact_choices' ) ) :
	function unysonplus_demo_compact_choices( $kind /* 'text' | 'bg' */ ) {
		$prefix  = $kind === 'text' ? 'text-' : 'bg-';
		$choices = [];
		if ( ! function_exists( 'unysonplus_color_preset_slug_map' ) ) {
			return $choices;
		}
		foreach ( unysonplus_color_preset_slug_map() as $slug => $hex ) {
			$choices[ $prefix . $slug ] = [
				'label' => ucwords( str_replace( '-', ' ', $slug ) ),
				'color' => $hex,
			];
		}
		return $choices;
	}
endif;

$options = [
	'demo_text_2'                      => [
		'label' => __( 'Text', 'unysonplus' ),
		'type'  => 'text',
		'value' => 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_short_text_2'                => [
		'label' => __( 'Short Text', 'unysonplus' ),
		'type'  => 'short-text',
		'value' => '7',
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_number_2'                    => [
		'label'        => __( 'Number', 'unysonplus' ),
		'type'         => 'number',
		'value'        => 7,
		'min'          => 0,
		'max'          => 100,
		'step'         => 1,
		'numeric_type' => 'integer',
		'desc'         => __( 'HTML5 number input with min/max/step. Saved as integer.', 'unysonplus' ),
	],
	'demo_number_float_2'              => [
		'label' => __( 'Number (float)', 'unysonplus' ),
		'type'  => 'number',
		'value' => 1.5,
		'step'  => 0.1,
		'desc'  => __( 'Unbounded number input. Saved as float.', 'unysonplus' ),
	],
	'demo_password_2'                  => [
		'label' => __( 'Password', 'unysonplus' ),
		'type'  => 'password',
		'value' => 'Dotted text',
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_hidden_2'                    => [
		'label' => false,
		'type'  => 'hidden',
		'value' => '{some: "json"}',
		'desc'  => false,
	],
	'demo_textarea_2'                  => [
		'label' => __( 'Textarea', 'unysonplus' ),
		'type'  => 'textarea',
		'value' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => [
			'icon' => 'video',
			'html' => '<iframe width="420" height="236" src="https://player.vimeo.com/video/101070863" frameborder="0" allowfullscreen></iframe>'
		],
	],
	'demo_wp_editor_2'                 => [
		'label' => __( 'Rich Text Editor', 'unysonplus' ),
		'type'  => 'wp-editor',
		'value' => 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
		'reinit' => true,
	],
	'demo_html_2'                      => [
		'label' => __( 'HTML', 'unysonplus' ),
		'type'  => 'html',
		'value' => '{some: "json"}',
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'html'  => '<em>Lorem</em> <b>ipsum</b> <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAANbY1E9YMgAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAADWSURBVDjLlZNNCsIwEEZzKW/jyoVbD+Aip/AGgmvRldCKNxDBv4LSfSG7kBZix37BQGiapA48ZpjMvIZAGRExwDmnESw7MMvsHnMFTdOQUsqjrmtXsggKEEVReCDseZc/HbOgoCxLDytwUEFBVVUe/fjNDguEEFGSAiml4Xq+DdZJAV78sM1oOpnT/fI0oEYPZ0lBtjuaBWSttcHtRQWvx9sMrlcb7+HQwxlmojfI9ycziGyj34sK3AV8zd7KFSYFCCwO1aMFsQgK8DO1bRsFM0HBP9i9L2ONMKHNZV7xAAAAAElFTkSuQmCC">',
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_checkbox_2'                  => [
		'label' => __( 'Checkbox', 'unysonplus' ),
		'type'  => 'checkbox',
		'value' => true,
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'text'  => __( 'Custom text', 'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_checkboxes_2'                => [
		'label'   => __( 'Checkboxes', 'unysonplus' ),
		'type'    => 'checkboxes',
		'value'   => [
			'c1' => false,
			'c2' => true,
		],
		'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'choices' => [
			'c1' => __( 'Checkbox 1 Custom Text', 'unysonplus' ),
			'c2' => __( 'Checkbox 2 Custom Text', 'unysonplus' ),
			'c3' => __( 'Checkbox 3 Custom Text', 'unysonplus' ),
		],
		'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_switch_2'                    => [
		'label'        => __( 'Switch', 'unysonplus' ),
		'type'         => 'switch',
		'right-choice' => [
			'value' => 'yes',
			'label' => __( 'Yes', 'unysonplus' )
		],
		'left-choice'  => [
			'value' => 'no',
			'label' => __( 'No', 'unysonplus' )
		],
		'value'        => 'yes',
		'desc'         => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'         => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_select_2'                    => [
		'label'   => __( 'Select', 'unysonplus' ),
		'type'    => 'select',
		'value'   => 'c',
		'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'choices' => [
			''  => '---',
			'a' => __( 'Lorem ipsum', 'unysonplus' ),
			'b' => [
				'text' => __( 'Consectetur', 'unysonplus' ),
				'attr' => [
					'label'         => 'Label overrides text',
					'data-whatever' => 'some data',
				],
			],
			[
				'attr'    => [
					'label'         => __( 'Optgroup Label', 'unysonplus' ),
					'data-whatever' => 'some data',
				],
				'choices' => [
					'c' => __( 'Sed ut perspiciatis', 'unysonplus' ),
					'd' => __( 'Excepteur sint occaecat', 'unysonplus' ),
				],
			],
			1   => __( 'One', 'unysonplus' ),
			2   => __( 'Two', 'unysonplus' ),
			3   => __( 'Three', 'unysonplus' ),
		],
		'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_short_select_2'              => [
		'label'   => __( 'Short Select', 'unysonplus' ),
		'type'    => 'short-select',
		'value'   => '7',
		'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'choices' => [
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
		],
		'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_select_multiple_2'           => [
		'label'   => __( 'Select Multiple', 'unysonplus' ),
		'type'    => 'select-multiple',
		'value'   => [ 'c', '2' ],
		'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'choices' => [
			''  => '---',
			'a' => __( 'Lorem ipsum', 'unysonplus' ),
			'b' => [
				'text' => __( 'Consectetur', 'unysonplus' ),
				'attr' => [
					'label'         => 'Label overrides text',
					'data-whatever' => 'some data',
				],
			],
			[
				'attr'    => [
					'label'         => __( 'Optgroup Label', 'unysonplus' ),
					'data-whatever' => 'some data',
				],
				'choices' => [
					'c' => __( 'Sed ut perspiciatis', 'unysonplus' ),
					'd' => __( 'Excepteur sint occaecat', 'unysonplus' ),
				],
			],
			1   => __( 'One', 'unysonplus' ),
			2   => __( 'Two', 'unysonplus' ),
			3   => __( 'Three', 'unysonplus' ),
		],
		'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_group_multi_select_2'        => [
		'type'    => 'group',
		'options' => [
			'demo_multi_select_posts_2'      => [
				'type'       => 'multi-select',
				'label'      => __( 'Multi-Select: Posts', 'unysonplus' ),
				'population' => 'posts',
				'source'     => 'page',
				'desc'       => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'       => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
			'demo_multi_select_taxonomies_2' => [
				'type'       => 'multi-select',
				'label'      => __( 'Multi-Select: Taxonomies', 'unysonplus' ),
				'population' => 'taxonomy',
				'source'     => 'category',
				'desc'       => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'       => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
			'demo_multi_select_users_2'      => [
				'type'       => 'multi-select',
				'label'      => __( 'Multi-Select: Users', 'unysonplus' ),
				'population' => 'users',
				'source'     => 'administrator',
				'desc'       => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'       => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
			'demo_multi_select_array_2'      => [
				'type'       => 'multi-select',
				'label'      => __( 'Multi-Select: Custom Array', 'unysonplus' ),
				'population' => 'array',
				'choices'    => [
					'hello' => __( 'Hello', 'unysonplus' ),
					'world' => __( 'World', 'unysonplus' ),
				],
				'desc'       => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'       => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
		],
	],
	'demo_radio_2'                     => [
		'label'   => __( 'Radio', 'unysonplus' ),
		'type'    => 'radio',
		'value'   => 'c2',
		'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'choices' => [
			'c1' => __( 'Radio 1 Custom Text', 'unysonplus' ),
			'c2' => __( 'Radio 2 Custom Text', 'unysonplus' ),
			'c3' => __( 'Radio 3 Custom Text', 'unysonplus' ),
		],
		'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_radio_text_2'                => [
		'label'   => __( 'Radio Text', 'unysonplus' ),
		'type'    => 'radio-text',
		'value'   => '75',
		'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'choices' => [
			'25'  => __( '25%', 'unysonplus' ),
			'50'  => __( '50%', 'unysonplus' ),
			'100' => __( '100%', 'unysonplus' ),
		],
		'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_image_picker_2'              => [
		'label'   => __( 'Image Picker', 'unysonplus' ),
		'type'    => 'image-picker',
		'value'   => '',
		'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'choices' => [
			'choice-1' => [
				'small' => [
					'height' => 70,
					'src'    => get_template_directory_uri() . '/images/image-picker-demo/thumb1.jpg'
				],
				'large' => [
					'height' => 214,
					'src'    => get_template_directory_uri() . '/images/image-picker-demo/tooltip1.jpg'
				],
			],
			'choice-2' => [
				'small' => [
					'height' => 70,
					'src'    => get_template_directory_uri() . '/images/image-picker-demo/thumb2.jpg'
				],
				'large' => [
					'height' => 214,
					'src'    => get_template_directory_uri() . '/images/image-picker-demo/tooltip2.jpg'
				],
			],
		],
		'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_icon_2'                      => [
		'label' => __( 'Icon', 'unysonplus' ),
		'type'  => 'icon',
		'value' => 'fa fa-linux',
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_upload_2'                    => [
		'label'       => __( 'Single Upload', 'unysonplus' ),
		'desc'        => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'type'        => 'upload',
		'images_only' => false,
		'help'        => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_upload_images_2'             => [
		'label' => __( 'Single Upload (Images Only)', 'unysonplus' ),
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'type'  => 'upload',
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_multi_upload_2'              => [
		'label'       => __( 'Multi Upload', 'unysonplus' ),
		'desc'        => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'type'        => 'multi-upload',
		'images_only' => false,
		'help'        => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_multi_upload_images_2'       => [
		'label' => __( 'Multi Upload (Images Only)', 'unysonplus' ),
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'type'  => 'multi-upload',
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_color_picker_2'              => [
		'label' => __( 'Color Picker', 'unysonplus' ),
		'type'  => 'color-picker',
		'value' => '',
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_rgba_color_picker_2' => [
		'label' => __( 'RGBA Color Picker', 'unysonplus' ),
		'type'  => 'rgba-color-picker',
		'value' => 'rgba(255, 0, 0, .5)',
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_predefined_colors_2' => [
		'label'   => __( 'Predefined Colors (pre-filled)', 'unysonplus' ),
		'type'    => 'predefined-colors',
		'value'   => '#3f51b5', // present in the default palette ("Blue")
		'blank'   => true,
		'choices' => unysonplus_option_color_palette(),
		'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'    => __( 'Same option type as in the other demo page but with an initial swatch selected so the load-from-saved-value path is visible. Saved value remains a single hex string.',
			'unysonplus' ),
	],
	'demo_predefined_colors_color_picker_2' => [
		'label'  => __( 'Predefined Colors + Color Picker (pre-filled)', 'unysonplus' ),
		'type'   => 'predefined-colors-color-picker',
		'value'  => [
			'predefined' => '',
			'custom'     => '#0d3c54', // demonstrates the "custom" half active
		],
		'colors' => [
			'predefined' => [
				'type'    => 'predefined',
				'choices' => unysonplus_option_color_palette(),
			],
			'custom'     => [
				'type'   => 'custom',
				'picker' => 'color-picker',
			],
		],
		'desc'   => __( 'Pre-filled with the custom-picker half active (<code>custom => "#0d3c54"</code>). Switch by editing this entry\'s <code>value</code> — set <code>predefined</code> instead to see the preset half load selected.',
			'unysonplus' ),
		'help'   => __( 'Saved value is <code>{ predefined: "", custom: "" }</code>. The JS keeps the two halves mutually exclusive — picking a swatch clears <code>custom</code>; opening the picker clears <code>predefined</code>. Switch <code>picker</code> to <code>rgba-color-picker</code> for alpha.',
			'unysonplus' ),
	],
	'demo_predefined_colors_color_picker_compact_2' => [
		'label'   => __( 'Predefined Colors + Color Picker (Compact, pre-filled)', 'unysonplus' ),
		'type'    => 'predefined-colors-color-picker-compact',
		'picker'  => 'color-picker',
		'value'   => [
			'predefined' => 'bg-primary', // present in the default palette
			'custom'     => '',
		],
		'choices' => unysonplus_demo_compact_choices( 'bg' ),
		'desc'    => __( 'Pre-filled with the preset half active (<code>predefined => "bg-primary"</code>). On load the trigger paints itself with the matching preset color + label; the dropdown marks the same option as selected.',
			'unysonplus' ),
		'help'    => __( 'A consuming view would emit <code>class="bg-primary"</code> here. Flip to the custom-picker half by setting <code>predefined => ""</code> + <code>custom => "#abc123"</code>; the view then emits <code>style="background: #abc123"</code>. Near-white presets (e.g. White, Light Gray) render with a gray chip behind the label so they stay readable against the white panel background.',
			'unysonplus' ),
	],
	'demo_gradient_2'                  => [
		'label' => __( 'Gradient', 'unysonplus' ),
		'type'  => 'gradient',
		'value' => [
			'primary'   => '#ffffff',
			'secondary' => '#ffffff'
		],
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_gradient_v2_2'               => [
		'label' => __( 'Gradient V2', 'unysonplus' ),
		'type'  => 'gradient-v2',
		'value' => [
			'type'  => 'radial',
			'angle' => 90,
			'stops' => [
				[ 'color' => 'rgba(42, 123, 155, 1)', 'position' => 0 ],
				[ 'color' => 'rgba(87, 199, 133, 1)', 'position' => 50 ],
				[ 'color' => 'rgba(237, 221, 83, 1)', 'position' => 100 ],
			],
		],
		'desc'  => __( 'Advanced gradient picker: a read-only CSS output that opens a dropdown editor. This entry ships pre-set, so its output shows a radial gradient that round-trips on save; clear it with the x for the blank state.', 'unysonplus' ),
	],
	'demo_rgba_color_picker_2' => [
		'label' => __( 'RGBA Color Picker', 'unysonplus' ),
		'type'  => 'rgba-color-picker',
		'value' => '',
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_background_image_2'          => [
		'label'   => __( 'Background Image', 'unysonplus' ),
		'type'    => 'background-image',
		'value'   => 'none',
		'choices' => [
			'none' => [
				'icon' => get_template_directory_uri() . '/images/patterns/no_pattern.jpg',
				'css'  => [
					'background-image' => 'none'
				]
			],
			'bg-1' => [
				'icon' => get_template_directory_uri() . '/images/patterns/diagonal_bottom_to_top_pattern_preview.jpg',
				'css'  => [
					'background-image'  => 'url("' . get_template_directory_uri() . '/images/patterns/diagonal_bottom_to_top_pattern.png' . '")',
					'background-repeat' => 'repeat',
				]
			],
			'bg-2' => [
				'icon' => get_template_directory_uri() . '/images/patterns/diagonal_top_to_bottom_pattern_preview.jpg',
				'css'  => [
					'background-image'  => 'url("' . get_template_directory_uri() . '/images/patterns/diagonal_top_to_bottom_pattern.png' . '")',
					'background-repeat' => 'repeat',
				]
			],
			'bg-3' => [
				'icon' => get_template_directory_uri() . '/images/patterns/dots_pattern_preview.jpg',
				'css'  => [
					'background-image'  => 'url("' . get_template_directory_uri() . '/images/patterns/dots_pattern.png' . '")',
					'background-repeat' => 'repeat',
				]
			],
			'bg-4' => [
				'icon' => get_template_directory_uri() . '/images/patterns/romb_pattern_preview.jpg',
				'css'  => [
					'background-image'  => 'url("' . get_template_directory_uri() . '/images/patterns/romb_pattern.png' . '")',
					'background-repeat' => 'repeat',
				]
			],
			'bg-5' => [
				'icon' => get_template_directory_uri() . '/images/patterns/square_pattern_preview.jpg',
				'css'  => [
					'background-image'  => 'url("' . get_template_directory_uri() . '/images/patterns/square_pattern.png' . '")',
					'background-repeat' => 'repeat',
				]
			],
			'bg-6' => [
				'icon' => get_template_directory_uri() . '/images/patterns/noise_pattern_preview.jpg',
				'css'  => [
					'background-image'  => 'url("' . get_template_directory_uri() . '/images/patterns/noise_pattern.png' . '")',
					'background-repeat' => 'repeat',
				]
			],
			'bg-7' => [
				'icon' => get_template_directory_uri() . '/images/patterns/vertical_lines_pattern_preview.jpg',
				'css'  => [
					'background-image'  => 'url("' . get_template_directory_uri() . '/images/patterns/vertical_lines_pattern.png' . '")',
					'background-repeat' => 'repeat',
				]
			],
			'bg-8' => [
				'icon' => get_template_directory_uri() . '/images/patterns/waves_pattern_preview.jpg',
				'css'  => [
					'background-image'  => 'url("' . get_template_directory_uri() . '/images/patterns/waves_pattern.png' . '")',
					'background-repeat' => 'repeat',
				]
			],
		],
		'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_background_pro_2'            => [
		'label' => __( 'Background Pro', 'unysonplus' ),
		'type'  => 'background-pro',
		'desc'  => __( 'Composite background option (v1). Four tabs: Color / Gradient / Image / Video. Values stack as CSS layers — color underneath, gradient over, image over, video on top. The dot on each tab indicates that layer has a value.', 'unysonplus' ),
		'value' => [
			'color' => [
				'value' => [
					'predefined' => '',
					'custom'     => '#0d3c54',
				],
			],
			'gradient' => [
				'enabled' => 'yes',
				'data'    => [
					'type'  => 'linear',
					'angle' => 135,
					'stops' => [
						[ 'color' => 'rgba(42, 123, 155, 1)',  'position' => 0 ],
						[ 'color' => 'rgba(87, 199, 133, 1)',  'position' => 50 ],
						[ 'color' => 'rgba(237, 221, 83, 1)',  'position' => 100 ],
					],
				],
			],
		],
	],
	// Composite Margin + Padding widget with a plus-cross layout. See
	// framework/includes/option-types/spacing/README.md for the saved value
	// shape and a copy-pasteable view-side flatten example.
	'demo_spacing_2'                   => [
		'label' => __( 'Spacing (Margin + Padding)', 'unysonplus' ),
		'type'  => 'spacing',
		'desc'  => __( 'Composite spacing option. Two columns side-by-side; each has an All Sides select plus a Top / Right / Bottom / Left quadrant arranged like a "+". Values are Bootstrap utility class names sourced from the live spacing scale.', 'unysonplus' ),
	],
	'demo_spacing_margin_only_2'       => [
		'label' => __( 'Spacing (Margin Only)', 'unysonplus' ),
		'type'  => 'spacing',
		'mode'  => 'margin',
		'desc'  => __( 'Same composite, scoped to the margin column. The padding subtree is force-reset to defaults on save.', 'unysonplus' ),
	],
	'demo_spacing_padding_only_2'      => [
		'label' => __( 'Spacing (Padding Only)', 'unysonplus' ),
		'type'  => 'spacing',
		'mode'  => 'padding',
		'desc'  => __( 'Same composite, scoped to the padding column.', 'unysonplus' ),
	],
	'demo_unit_input_2'                => [
		'label' => __( 'Unit Input', 'unysonplus' ),
		'type'  => 'unit-input',
		'value' => [ 'value' => '1.5', 'unit' => 'rem' ],
		'desc'  => __( 'Numeric field + a configurable unit dropdown (defaults px / em / rem). Saved value is <code>{ value, unit }</code>; consume with <code>FW_Option_Type_Unit_Input::to_string( $val )</code>.', 'unysonplus' ),
	],
	'demo_unit_input_separate_2'       => [
		'label'    => __( 'Unit Input (separate units)', 'unysonplus' ),
		'type'     => 'unit-input',
		'units'    => [ 'inches', 'cm', 'm' ],
		'separate' => true,
		'min'      => 0,
		'step'     => 0.5,
		'value'    => [ 'value' => '3', 'unit' => 'm' ],
		'desc'     => __( 'Custom unit list with <code>separate => true</code> → "3 m" (space-separated) for human measurements. Shows optional min/step attributes.', 'unysonplus' ),
	],
	'demo_box_shadow_2'                => [
		'label' => __( 'Box Shadow', 'unysonplus' ),
		'type'  => 'box-shadow',
		'value' => [ 'x' => 0, 'y' => 10, 'blur' => 30, 'spread' => -4, 'color' => 'rgba(102,16,242,0.45)', 'inset' => false ],
		'desc'  => __( 'Structured box-shadow builder with a 300px live preview and generated CSS string. Consume with <code>FW_Option_Type_Box_Shadow::to_css( $val )</code>.', 'unysonplus' ),
	],
	'demo_button_presets_2'            => [
		'label'         => __( 'Button Presets', 'unysonplus' ),
		'type'          => 'button-presets',
		'color-choices' => function_exists( 'unysonplus_demo_compact_choices' ) ? unysonplus_demo_compact_choices( 'bg' ) : [],
		'value'         => [],
		'desc'          => __( 'The full button builder: collapsible presets with Default / Hover / Active / Focus / Disabled state tabs (colors, spacing, border, box-shadow), a Font control, transition, and custom CSS. Each preset compiles to a <code>.btn-{slug}</code> class.', 'unysonplus' ),
	],
	'demo_button_style_picker_2'       => [
		'label'        => __( 'Button Style Picker', 'unysonplus' ),
		'type'         => 'button-style-picker',
		'choices'      => function_exists( 'sc_get_button_style_choices' ) ? sc_get_button_style_choices() : [],
		'preview_text' => __( 'Button', 'unysonplus' ),
		'desc'         => __( 'A dropdown that previews each Button Preset as a real button. Reused by the Button shortcode Style/Size pickers. Stores the class string, e.g. <code>btn-primary</code>.', 'unysonplus' ),
	],
	'demo_typography_2'                => [
		'label' => __( 'Typography', 'unysonplus' ),
		'type'  => 'typography',
		'value' => [
			'size'   => 17,
			'family' => 'Verdana',
			'style'  => '300italic',
			'color'  => '#0000ff'
		],
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_typography-v2_2'                => [
		'label' => __( 'Typography V2', 'unysonplus' ),
		'type'  => 'typography-v2',
		'value'      => [
			'family'    => 'Amarante',
//			For standard fonts, instead of subset and variation you should set 'style' and 'weight'.
//			'style' => 'italic',
//			'weight' => 700,
			'subset'    => 'latin-ext',
			'variation' => 'regular',
			'size'      => 14,
			'line-height' => 13,
			'letter-spacing' => -2,
			'color'     => '#0000ff'
		],
		'components' => [
			'family'         => true,
			//'style', 'weight', 'subset', 'variation' will appear and disappear along with 'family'
			'size'           => true,
			'line-height'    => true,
			'letter-spacing' => true,
			'color'          => true
		],
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
	],
	'demo_datetime_range_2'            => [
		'type'             => 'datetime-range',
		'attr'             => [ 'class' => 'custom-class', 'data-foo' => 'bar' ],
		'label'            => __( 'Demo date range', 'unysonplus' ),
		'desc'             => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'             => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
		'datetime-pickers' => [
			'from' => [
				'timepicker' => false,
				'datepicker' => true,
			],
			'to'   => [
				'timepicker' => false,
				'datepicker' => true,
			]
		],
		'value'            => [
			'from' => '',
			'to'   => ''
		]
	],
	'demo_datetime_picker_2'           => [
		'type'            => 'datetime-picker',
		'value'           => '',
		'attr'            => [ 'class' => 'custom-class', 'data-foo' => 'bar' ],
		'label'           => __( 'Date & Time picker', 'unysonplus' ),
		'desc'            => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'            => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
		'datetime-picker' => [
			'format'        => 'd-m-Y H:i',
			'extra-formats' => [],
			'moment-format' => 'DD-MM-YYYY HH:mm',
			'scrollInput'   => false,
			'maxDate'       => false,
			'minDate'       => false,
			'timepicker'    => true,
			'datepicker'    => true,
			'defaultTime'   => '12:00'
		]
	],
	'demo_slider_2' => [
		'label' => __( 'Slider', 'unysonplus' ),
		'type'  => 'slider',
		'value' => 10,
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium', 'unysonplus' )
		),
	],
	'demo_range_slider_2' => [
		'label' => __( 'Range Slider', 'unysonplus' ),
		'type'  => 'range-slider',
		'value' => [
			'from' => 30,
			'to' => 50
		],
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium', 'unysonplus' )
		),
	],
	'demo_addable_popup_2'             => [
		'label'         => __( 'Addable Popup', 'unysonplus' ),
		'type'          => 'addable-popup',
		'desc'          => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'template'      => '{{- demo_text }}',
		'popup-options' => [
			'demo_text'                => [
				'label' => __( 'Text', 'unysonplus' ),
				'type'  => 'text',
				'value' => 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
			'demo_image_picker'        => [
				'label'   => __( 'Image Picker', 'unysonplus' ),
				'type'    => 'image-picker',
				'value'   => '',
				'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'attr'    => [
					'data-height' => 70
				],
				'choices' => [
					'choice-1' => [
						'small' => [
							'height' => 70,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/thumb1.jpg'
						],
						'large' => [
							'height' => 214,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/tooltip1.jpg'
						],
					],
					'choice-2' => [
						'small' => [
							'height' => 70,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/thumb2.jpg'
						],
						'large' => [
							'height' => 214,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/tooltip2.jpg'
						],
					],
				],
				'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
			'demo_upload_images'       => [
				'label' => __( 'Single Upload (Images Only)', 'unysonplus' ),
				'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'type'  => 'upload',
				'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
			'demo_addable_popup_inner' => [
				'label'         => __( 'Addable Popup', 'unysonplus' ),
				'type'          => 'addable-popup',
				'desc'          => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'template'      => 'Title color-picker value : {{- demo_color_picker }}',
				'popup-options' => [
					'demo_multi_upload_images' => [
						'label' => __( 'Multi Upload (images only)', 'unysonplus' ),
						'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
							'unysonplus' ),
						'type'  => 'multi-upload',
						'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
							__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
								'unysonplus' ),
							__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
								'unysonplus' )
						),
					],
					'demo_color_picker'        => [
						'label' => __( 'Color Picker', 'unysonplus' ),
						'type'  => 'color-picker',
						'value' => '',
						'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
							'unysonplus' ),
						'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
							__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
								'unysonplus' ),
							__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
								'unysonplus' )
						),
					]
				]
			],
		],
	],
	'demo_addable_option_2'            => [
		'label'  => __( 'Addable Option', 'unysonplus' ),
		'type'   => 'addable-option',
		'option' => [
			'type' => 'text',
		],
		'value'  => [ 'Option 1', 'Option 2', 'Option 3' ],
		'desc'   => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'   => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		)
	],
	'demo_addable_box_2'               => [
		'label'        => __( 'Addable Box', 'unysonplus' ),
		'type'         => 'addable-box',
		'value'        => [],
		'desc'         => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'         => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
				'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'unysonplus' )
		),
		'box-controls' => [//'custom' => '<small class="dashicons dashicons-smiley" title="Custom"></small>',
		],
		'box-options'  => [
			'demo_text'     => [
				'label' => __( 'Text', 'unysonplus' ),
				'type'  => 'text',
				'value' => 'Lorem ipsum dolor sit amet',
				'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
			'demo_textarea' => [
				'label' => __( 'Textarea', 'unysonplus' ),
				'type'  => 'textarea',
				'value' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
				'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'  => [
					'icon' => 'video',
					'html' => '<iframe width="420" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>'
				],
			],
		],
		'template'     => '{{- demo_text }}',
	],
	'demo_group_2'                     => [
		'type'    => 'group',
		'options' => [
			'demo_code_editor'         => [
				'label'       => __( 'Code Editor', 'unysonplus' ),
				'type'        => 'code-editor',
				'mode'        => 'css',
				'value'       => "selector {\n\tcolor: #2563eb;\n\tpadding: 2rem 0;\n}",
				'desc'        => __( 'Syntax-highlighted code field (WordPress CodeMirror). The top-level "mode" key selects the language: css, javascript, htmlmixed, php, json or xml.', 'unysonplus' ),
				'placeholder' => "/* Write CSS here */\nselector { … }",
			],
			'demo_text_in_group_2'     => [
				'label' => __( 'Text in Group', 'unysonplus' ),
				'type'  => 'text',
				'value' => 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
			'demo_password_in_group_2' => [
				'label' => __( 'Password in Group', 'unysonplus' ),
				'type'  => 'password',
				'value' => 'Dotted text',
				'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
		],
	],
	'demo_multi_2'                     => [
		'label'         => false,
		'type'          => 'multi',
		'value'         => [],
		'desc'          => false,
		'inner-options' => [
			'demo_text'     => [
				'label' => __( 'Text in Multi', 'unysonplus' ),
				'type'  => 'text',
				'value' => 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
				'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
			'demo_textarea' => [
				'label' => __( 'Textarea in Multi', 'unysonplus' ),
				'type'  => 'textarea',
				'value' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
				'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			],
		],
	],
	'demo_popover_2' => [
		'label'         => __( 'Popover', 'unysonplus' ),
		'type'          => 'popover',
		'value'         => 'a',
		'desc'          => __( 'Collapses an option into a compact trigger that expands an in-flow panel on click (like the color picker dropdown, but anchored inline — not the modal "popup" type). With a single inner option the value passes straight through, so it is a drop-in for that option without the inline clutter.', 'unysonplus' ),
		'help'          => __( 'Click the field to reveal the hosted control; pick a value and it collapses again, showing the selection. The "summary" map turns the saved value into the friendly label on the trigger.', 'unysonplus' ),
		'summary'       => [
			'a' => __( 'Dots', 'unysonplus' ),
			'b' => __( 'Romb', 'unysonplus' ),
			'c' => __( 'Squares', 'unysonplus' ),
			'd' => __( 'Waves', 'unysonplus' ),
		],
		'inner-options' => [
			'pattern' => [
				'type'    => 'image-picker',
				'label'   => false,
				'value'   => 'a',
				'choices' => [
					'a' => get_template_directory_uri() . '/images/patterns/dots_pattern_preview.jpg',
					'b' => get_template_directory_uri() . '/images/patterns/romb_pattern_preview.jpg',
					'c' => get_template_directory_uri() . '/images/patterns/square_pattern_preview.jpg',
					'd' => get_template_directory_uri() . '/images/patterns/waves_pattern_preview.jpg',
				],
			],
		],
	],
	'demo_popover_tabs_2' => [
		'label'         => __( 'Popover (Tabs)', 'unysonplus' ),
		'type'          => 'popover',
		'trigger_label' => __( 'Edit settings…', 'unysonplus' ),
		'desc'          => __( 'A popover hosting several options organized into tabs (like Background Pro). Multiple options / tabs → the value is a hash keyed by inner option id; the tab grouping is purely visual. Option ids must be unique across all tabs.', 'unysonplus' ),
		'help'          => __( 'Click to open, switch tabs to reach each group of controls. The trigger keeps a static label here because there is no single value to summarise.', 'unysonplus' ),
		'tabs'          => [
			'content' => [
				'label'   => __( 'Content', 'unysonplus' ),
				'options' => [
					'title'    => [ 'type' => 'text', 'label' => __( 'Title', 'unysonplus' ), 'value' => '' ],
					'subtitle' => [ 'type' => 'text', 'label' => __( 'Subtitle', 'unysonplus' ), 'value' => '' ],
				],
			],
			'style' => [
				'label'   => __( 'Style', 'unysonplus' ),
				'options' => [
					'color' => [ 'type' => 'color-picker', 'label' => __( 'Color', 'unysonplus' ), 'value' => '#2271b1' ],
					'size'  => [
						'type'    => 'select',
						'label'   => __( 'Size', 'unysonplus' ),
						'value'   => 'md',
						'choices' => [
							'sm' => __( 'Small', 'unysonplus' ),
							'md' => __( 'Medium', 'unysonplus' ),
							'lg' => __( 'Large', 'unysonplus' ),
						],
					],
				],
			],
			'advanced' => [
				'label'   => __( 'Advanced', 'unysonplus' ),
				'options' => [
					'css_class' => [ 'type' => 'text', 'label' => __( 'CSS Class', 'unysonplus' ), 'value' => '' ],
				],
			],
		],
	],
	'demo_multi_picker_select_2'       => [
		'type'         => 'multi-picker',
		'label'        => false,
		'desc'         => false,
		'picker'       => [
			'gadget' => [
				'label'   => __( 'Multi Picker: Select', 'unysonplus' ),
				'type'    => 'select',
				'choices' => [
					'phone'  => __( 'Phone', 'unysonplus' ),
					'laptop' => __( 'Laptop', 'unysonplus' )
				],
				'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				)
			]
		],
		'choices'      => [
			'phone'  => [
				'price'  => [
					'type'  => 'text',
					'label' => __( 'Price', 'unysonplus' ),
				],
				'memory' => [
					'type'    => 'select',
					'label'   => __( 'Memory', 'unysonplus' ),
					'choices' => [
						'16' => __( '16Gb', 'unysonplus' ),
						'32' => __( '32Gb', 'unysonplus' ),
						'64' => __( '64Gb', 'unysonplus' ),
					]
				]
			],
			'laptop' => [
				'price'  => [
					'type'  => 'text',
					'label' => __( 'Price', 'unysonplus' ),
				],
				'webcam' => [
					'type'  => 'switch',
					'label' => __( 'Webcam', 'unysonplus' ),
				]
			],
		],
		'show_borders' => false,
	],
	'demo_multi_picker_radio_2'        => [
		'type'         => 'multi-picker',
		'label'        => false,
		'desc'         => false,
		'value'        => [
			'gadget' => 'laptop',
		],
		'picker'       => [
			'gadget' => [
				'label'   => __( 'Multi Picker: Radio', 'unysonplus' ),
				'type'    => 'radio',
				'choices' => [
					'phone'  => __( 'Phone', 'unysonplus' ),
					'laptop' => __( 'Laptop', 'unysonplus' )
				],
				'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				)
			]
		],
		'choices'      => [
			'phone'  => [
				'price'  => [
					'type'  => 'text',
					'label' => __( 'Price', 'unysonplus' ),
				],
				'memory' => [
					'type'    => 'select',
					'label'   => __( 'Memory', 'unysonplus' ),
					'choices' => [
						'16' => __( '16Gb', 'unysonplus' ),
						'32' => __( '32Gb', 'unysonplus' ),
						'64' => __( '64Gb', 'unysonplus' ),
					]
				]
			],
			'laptop' => [
				'price'  => [
					'type'  => 'text',
					'label' => __( 'Price', 'unysonplus' ),
				],
				'webcam' => [
					'type'  => 'switch',
					'label' => __( 'Webcam', 'unysonplus' ),
				]
			],
		],
		'show_borders' => false,
	],
	'demo_multi_picker_image_picker_2' => [
		'type'         => 'multi-picker',
		'label'        => false,
		'desc'         => false,
		'picker'       => [
			'gadget' => [
				'label'   => __( 'Multi Picker: Image Picker', 'unysonplus' ),
				'type'    => 'image-picker',
				'choices' => [
					'phone'  => [
						'label' => __( 'Phone', 'unysonplus' ),
						'small' => [
							'height' => 70,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/thumb1.jpg'
						],
						'large' => [
							'height' => 214,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/tooltip1.jpg'
						],
					],
					'laptop' => [
						'label' => __( 'Laptop', 'unysonplus' ),
						'small' => [
							'height' => 70,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/thumb2.jpg'
						],
						'large' => [
							'height' => 214,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/tooltip2.jpg'
						],
					]
				],
				'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'    => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				)
			]
		],
		'choices'      => [
			'phone'  => [
				'price'  => [
					'type'  => 'text',
					'label' => __( 'Price', 'unysonplus' ),
				],
				'memory' => [
					'type'    => 'select',
					'label'   => __( 'Memory', 'unysonplus' ),
					'choices' => [
						'16' => __( '16Gb', 'unysonplus' ),
						'32' => __( '32Gb', 'unysonplus' ),
						'64' => __( '64Gb', 'unysonplus' ),
					]
				]
			],
			'laptop' => [
				'price'  => [
					'type'  => 'text',
					'label' => __( 'Price', 'unysonplus' ),
				],
				'webcam' => [
					'type'  => 'switch',
					'label' => __( 'Webcam', 'unysonplus' ),
				]
			],
		],
		'show_borders' => false,
	],
	'demo_multi_picker_popover_2'      => [
		'type'         => 'multi-picker',
		'label'        => __( 'Multi Picker: Popover', 'unysonplus' ),
		'desc'         => __( 'The same Image Picker multi-picker, collapsed behind a compact trigger (popover display mode, "popover" => true). Click the trigger to choose; picking an image reveals its options inside the panel, and the trigger reflects the current pick. Keeps a section tidy when the picker has many tiles.', 'unysonplus' ),
		'popover'      => true,
		'picker'       => [
			'gadget' => [
				'label'   => false,
				'type'    => 'image-picker',
				'choices' => [
					'phone'  => [
						'label' => __( 'Phone', 'unysonplus' ),
						'small' => [
							'height' => 70,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/thumb1.jpg'
						],
						'large' => [
							'height' => 214,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/tooltip1.jpg'
						],
					],
					'laptop' => [
						'label' => __( 'Laptop', 'unysonplus' ),
						'small' => [
							'height' => 70,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/thumb2.jpg'
						],
						'large' => [
							'height' => 214,
							'src'    => get_template_directory_uri() . '/images/image-picker-demo/tooltip2.jpg'
						],
					]
				],
			]
		],
		'choices'      => [
			'phone'  => [
				'price'  => [
					'type'  => 'text',
					'label' => __( 'Price', 'unysonplus' ),
				],
				'memory' => [
					'type'    => 'select',
					'label'   => __( 'Memory', 'unysonplus' ),
					'choices' => [
						'16' => __( '16Gb', 'unysonplus' ),
						'32' => __( '32Gb', 'unysonplus' ),
						'64' => __( '64Gb', 'unysonplus' ),
					]
				]
			],
			'laptop' => [
				'price'  => [
					'type'  => 'text',
					'label' => __( 'Price', 'unysonplus' ),
				],
				'webcam' => [
					'type'  => 'switch',
					'label' => __( 'Webcam', 'unysonplus' ),
				]
			],
		],
		'show_borders' => false,
	],
	'demo_multi_picker_switch_2'       => [
		'type'         => 'multi-picker',
		'label'        => false,
		'desc'         => false,
		'picker'       => [
			'gadget' => [
				'label'        => __( 'Multi Picker: Switch', 'unysonplus' ),
				'type'         => 'switch',
				'right-choice' => [
					'value' => 'laptop',
					'label' => __( 'Laptop', 'unysonplus' )
				],
				'left-choice'  => [
					'value' => 'phone',
					'label' => __( 'Phone', 'unysonplus' )
				],
				'value'        => 'yes',
				'desc'         => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
					'unysonplus' ),
				'help'         => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
					__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
						'unysonplus' ),
					__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium',
						'unysonplus' )
				),
			]
		],
		'choices'      => [
			'phone'  => [
				'price'  => [
					'type'  => 'text',
					'label' => __( 'Price', 'unysonplus' ),
				],
				'memory' => [
					'type'    => 'select',
					'label'   => __( 'Memory', 'unysonplus' ),
					'choices' => [
						'16' => __( '16Gb', 'unysonplus' ),
						'32' => __( '32Gb', 'unysonplus' ),
						'64' => __( '64Gb', 'unysonplus' ),
					]
				]
			],
			'laptop' => [
				'price'  => [
					'type'  => 'text',
					'label' => __( 'Price', 'unysonplus' ),
				],
				'webcam' => [
					'type'  => 'switch',
					'label' => __( 'Webcam', 'unysonplus' ),
				]
			],
		],
		'show_borders' => false,
	],
];
