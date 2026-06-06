<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Demo-only helper: build a `predefined-colors-color-picker-compact` choices
 * array from the live plugin palette. Keyed by `text-{slug}` or `bg-{slug}`
 * so the saved class name can be emitted verbatim by the consuming view.
 *
 * Shared between this file and demo-2.php; declared with a function_exists
 * guard since both demo files load on the same admin request.
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
	'demo_text'                      => [
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
	'demo_short_text'                => [
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
	'demo_number'                    => [
		'label'        => __( 'Number', 'unysonplus' ),
		'type'         => 'number',
		'value'        => 7,
		'min'          => 0,
		'max'          => 100,
		'step'         => 1,
		'numeric_type' => 'integer',
		'desc'         => __( 'HTML5 number input with min/max/step. Saved as integer.', 'unysonplus' ),
	],
	'demo_number_float'              => [
		'label' => __( 'Number (float)', 'unysonplus' ),
		'type'  => 'number',
		'value' => 1.5,
		'step'  => 0.1,
		'desc'  => __( 'Unbounded number input. Saved as float.', 'unysonplus' ),
	],
	'demo_password'                  => [
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
	'demo_hidden'                    => [
		'label' => false,
		'type'  => 'hidden',
		'value' => '{some: "json"}',
		'desc'  => false,
	],
	'demo_textarea'                  => [
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
	'demo_wp_editor'                 => [
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
	'demo_html'                      => [
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
	'demo_code_editor'               => [
		'label'       => __( 'Code Editor', 'unysonplus' ),
		'type'        => 'code-editor',
		'mode'        => 'css',
		'value'       => "selector {\n\tcolor: #2563eb;\n\tpadding: 2rem 0;\n}",
		'desc'        => __( 'Syntax-highlighted code field (WordPress CodeMirror). The top-level "mode" key selects the language: css, javascript, htmlmixed, php, json or xml.', 'unysonplus' ),
		'placeholder' => "/* Write CSS here */\nselector { … }",
	],
	'demo_checkbox'                  => [
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
	'demo_checkboxes'                => [
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
	'demo_switch'                    => [
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
	'demo_select'                    => [
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
	'demo_short_select'              => [
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
	'demo_select_multiple'           => [
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
	'demo_group_multi_select'        => [
		'type'    => 'group',
		'options' => [
			'demo_multi_select_posts'      => [
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
			'demo_multi_select_taxonomies' => [
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
			'demo_multi_select_users'      => [
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
			'demo_multi_select_array'      => [
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
	'demo_radio'                     => [
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
	'demo_radio_text'                => [
		'label'   => __( 'Radio Text', 'unysonplus' ),
		'type'    => 'radio-text',
		'value'   => '50',
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
	'demo_image_picker'              => [
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
	'demo_icon'                      => [
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
	'demo_upload'                    => [
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
	'demo_upload_images'             => [
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
	'demo_multi_upload'              => [
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
	'demo_multi_upload_images'       => [
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
	'demo_color_picker'              => [
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
	'demo_rgba_color_picker' => [
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
	'demo_predefined_colors' => [
		'label'   => __( 'Predefined Colors', 'unysonplus' ),
		'type'    => 'predefined-colors',
		'value'   => '',
		'blank'   => true,
		'choices' => unysonplus_option_color_palette(),
		'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'    => __( 'Swatch grid backed by a hidden &lt;select&gt;. Saved value is a single hex string (e.g. <code>#3f51b5</code>) or empty when nothing is selected. Palette comes from <code>unysonplus_option_color_palette()</code>, which reads Theme Settings → General → Colors and falls back to a built-in default. <code>blank => true</code> lets the user click an already-selected swatch to deselect.',
			'unysonplus' ),
	],
	'demo_predefined_colors_color_picker' => [
		'label'  => __( 'Predefined Colors + Color Picker', 'unysonplus' ),
		'type'   => 'predefined-colors-color-picker',
		'value'  => [
			'predefined' => '',
			'custom'     => '',
		],
		'colors' => [
			'predefined' => [
				'type'    => 'predefined',
				'choices' => unysonplus_option_color_palette(),
			],
			'custom'     => [
				'type'   => 'custom',
				'picker' => 'color-picker', // or 'rgba-color-picker' for alpha
			],
		],
		'desc'   => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'   => __( 'Hybrid control. Saved value is a two-key array: <code>{ predefined: "", custom: "" }</code>. Only one half is meant to be live at a time — picking a swatch clears <code>custom</code>; opening the picker clears <code>predefined</code> (the JS handles this mutual exclusion automatically). Switch <code>picker</code> from <code>color-picker</code> to <code>rgba-color-picker</code> to allow alpha.',
			'unysonplus' ),
	],
	'demo_predefined_colors_color_picker_compact' => [
		'label'   => __( 'Predefined Colors + Color Picker (Compact)', 'unysonplus' ),
		'type'    => 'predefined-colors-color-picker-compact',
		'picker'  => 'color-picker',
		'value'   => [
			'predefined' => '',
			'custom'     => '',
		],
		'choices' => unysonplus_demo_compact_choices( 'bg' ),
		'desc'    => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
			'unysonplus' ),
		'help'    => __( 'Compact dropdown variant of the wide hybrid. Each option shows BOTH a colored swatch and the preset name painted in that color. Near-white presets (luminance > 0.95) get a subtle gray chip behind the label so they don\'t disappear against the panel background. Saved value shape: <code>{ predefined: "bg-red", custom: "" }</code> when a preset is picked, or <code>{ predefined: "", custom: "#abc123" }</code> when a custom color is picked. Consumers emit <code>predefined</code> as <code>class="..."</code> directly; <code>custom</code> as inline <code>style="…"</code>. This demo uses <code>bg-{slug}</code> keys; switch the call to <code>unysonplus_demo_compact_choices( "text" )</code> to get <code>text-{slug}</code> keys for a text-color context.',
			'unysonplus' ),
	],
	'demo_gradient'                  => [
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
	'demo_gradient_v2'               => [
		'label' => __( 'Gradient V2', 'unysonplus' ),
		'type'  => 'gradient-v2',
		'value' => [
			'type'  => 'linear',
			'angle' => 90,
			'stops' => [], // Blank by default: read-only output stays empty until you open the dropdown and add stops.
		],
		'desc'  => __( 'Advanced gradient picker: a read-only CSS output that opens a dropdown editor (unlimited stops, linear/radial, angle, RGBA, live preview). Blank = no gradient.', 'unysonplus' ),
	],
	'demo_background_image'          => [
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
	'demo_background_pro'            => [
		'label' => __( 'Background Pro', 'unysonplus' ),
		'type'  => 'background-pro',
		'desc'  => __( 'Composite background option (v1). Four tabs: Color / Gradient / Image / Video. Values stack as CSS layers — color underneath, gradient over, image over, video on top. The dot on each tab indicates that layer has a value.', 'unysonplus' ),
	],
	// Composite Margin + Padding widget with a plus-cross layout. See
	// framework/includes/option-types/spacing/README.md for the saved value
	// shape and a copy-pasteable view-side flatten example.
	'demo_spacing'                   => [
		'label' => __( 'Spacing (Margin + Padding)', 'unysonplus' ),
		'type'  => 'spacing',
		'desc'  => __( 'Composite spacing option. Two columns side-by-side; each has an All Sides select plus a Top / Right / Bottom / Left quadrant arranged like a "+". Values are Bootstrap utility class names sourced from the live spacing scale.', 'unysonplus' ),
	],
	'demo_spacing_margin_only'       => [
		'label' => __( 'Spacing (Margin Only)', 'unysonplus' ),
		'type'  => 'spacing',
		'mode'  => 'margin',
		'desc'  => __( 'Same composite, scoped to the margin column. The padding subtree is force-reset to defaults on save.', 'unysonplus' ),
	],
	'demo_spacing_padding_only'      => [
		'label' => __( 'Spacing (Padding Only)', 'unysonplus' ),
		'type'  => 'spacing',
		'mode'  => 'padding',
		'desc'  => __( 'Same composite, scoped to the padding column.', 'unysonplus' ),
	],
	'demo_unit_input'                => [
		'label' => __( 'Unit Input', 'unysonplus' ),
		'type'  => 'unit-input',
		'value' => [ 'value' => '24', 'unit' => 'px' ],
		'desc'  => __( 'Numeric field + a configurable unit dropdown (defaults px / em / rem). Saved value is <code>{ value, unit }</code>; consume with <code>FW_Option_Type_Unit_Input::to_string( $val )</code> → "24px".', 'unysonplus' ),
	],
	'demo_unit_input_separate'       => [
		'label'    => __( 'Unit Input (separate units)', 'unysonplus' ),
		'type'     => 'unit-input',
		'units'    => [ 'inches', 'cm', 'm' ],
		'separate' => true,
		'min'      => 0,
		'step'     => 0.5,
		'value'    => [ 'value' => '24', 'unit' => 'inches' ],
		'desc'     => __( 'Same control with a custom unit list and <code>separate => true</code>, so <code>to_string()</code> emits a space — "24 inches" — for human measurements rather than CSS. Also shows the optional min/step number attributes.', 'unysonplus' ),
	],
	'demo_box_shadow'                => [
		'label' => __( 'Box Shadow', 'unysonplus' ),
		'type'  => 'box-shadow',
		'value' => [ 'x' => 0, 'y' => 6, 'blur' => 18, 'spread' => 0, 'color' => 'rgba(0,0,0,0.25)', 'inset' => false ],
		'desc'  => __( 'Structured box-shadow builder: X / Y / blur / spread / color / inset, with a 300px live preview on top and the generated CSS string below. Consume with <code>FW_Option_Type_Box_Shadow::to_css( $val )</code>.', 'unysonplus' ),
	],
	'demo_button_presets'            => [
		'label'         => __( 'Button Presets', 'unysonplus' ),
		'type'          => 'button-presets',
		'color-choices' => function_exists( 'unysonplus_demo_compact_choices' ) ? unysonplus_demo_compact_choices( 'bg' ) : [],
		'value'         => [],
		'desc'          => __( 'The full button builder used by Theme Settings → General → Buttons. Collapsible presets, each with Default / Hover / Active / Focus / Disabled state tabs (colors, spacing, border, box-shadow), a Font control, a transition, and a custom-CSS box. Each preset compiles to a <code>.btn-{slug}</code> class.', 'unysonplus' ),
	],
	'demo_button_style_picker'       => [
		'label'        => __( 'Button Style Picker', 'unysonplus' ),
		'type'         => 'button-style-picker',
		'choices'      => function_exists( 'sc_get_button_style_choices' ) ? sc_get_button_style_choices() : [],
		'preview_text' => __( 'Button', 'unysonplus' ),
		'desc'         => __( 'A dropdown that previews each Button Preset as a real button (trigger + every row). Reused by the Button shortcode\'s Style and Size pickers. Stores the class string, e.g. <code>btn-primary</code>.', 'unysonplus' ),
	],
	'demo_typography'                => [
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
	'demo_typography-v2'                => [
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
	'demo_datetime_range'            => [
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
	'demo_datetime_picker'           => [
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
	'demo_slider' => [
		'label' => __( 'Slider', 'unysonplus' ),
		'type'  => 'slider',
		'value' => 10,
		'desc'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'unysonplus' ),
		'help'  => sprintf( "%s \n\n'\"<br/><br/>\n\n <b>%s</b>",
			__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'unysonplus' ),
			__( 'Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium', 'unysonplus' )
		),
	],
	'demo_range_slider' => [
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
	'demo_addable_popup'             => [
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
				'choices' => [
					'choice-1' => [
						'label' => __( 'First Image', 'unysonplus' ),
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
						'label' => __( 'Second Image', 'unysonplus' ),
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
	'demo_addable_option' => [
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
	'demo_addable_box'               => [
		'label'        => __( 'Addable Box', 'unysonplus' ),
		'type'         => 'addable-box',
		'value'        => [],
		'desc'         => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'unysonplus' ),
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
		'template' => '{{- demo_text }}',
		'limit' => 3,
	],
	'demo_group' => [
		'type'    => 'group',
		'options' => [
			'demo_text_in_group' => [
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
			'demo_password_in_group' => [
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
	'demo_multi'                     => [
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
	'demo_multi_picker_select'       => [
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
	'demo_multi_picker_radio'        => [
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
	'demo_multi_picker_image_picker' => [
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
	'demo_multi_picker_switch'       => [
		'type'         => 'multi-picker',
		'label'        => false,
		'desc'         => false,
		'picker'       => [
			'gadget' => [
				'label'        => __( 'Switch', 'unysonplus' ),
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
	'demo_popover' => [
		'label'         => __( 'Popover', 'unysonplus' ),
		'type'          => 'popover',
		'value'         => 'a',
		'desc'          => __( 'Collapses an option into a compact trigger that expands an in-flow panel on click (like the color picker dropdown, but anchored inline — not the modal "popup" type). With a single inner option the value passes straight through, so it is a drop-in for that option without the inline clutter.', 'unysonplus' ),
		'help'          => __( 'Click the field to reveal the hosted control; pick a value and it collapses again, showing the selection. The "summary" map turns the saved value into the friendly label on the trigger.', 'unysonplus' ),
		// value => trigger label
		'summary'       => [
			'a' => __( 'Dots', 'unysonplus' ),
			'b' => __( 'Romb', 'unysonplus' ),
			'c' => __( 'Squares', 'unysonplus' ),
			'd' => __( 'Waves', 'unysonplus' ),
		],
		// One inner option → its value is the popover's value (passthrough).
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
	'demo_popover_tabs' => [
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
];
