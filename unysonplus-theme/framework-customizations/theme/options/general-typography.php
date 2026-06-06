<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
$options = [
	'typography' => [
		'type'    => 'multi',
		'label' => false,
		/*'attr' => array(
			'class' => '',
		),*/
		'inner-options' => [
			'preview'                => [
				'label' => __( 'Font Preview', 'unysonplus' ),
				'type'  => 'typography',
				'value' => [
					'family' => 'Open Sans',
				],
				'components' => [
					'family' => true,
					'size'   => false,
					'style'  => false,
					'color'  => false
				],
				'desc'  => __( 'Checkout the font styles. For preview purposes only.', 'unysonplus' ),
			],
			'h1' => [
				'label' => __( 'H1 Heading', 'unysonplus' ),
				'type'  => 'typography-v2',
				'value'      => [
					'family'    => 'Raleway',
		//			For standard fonts, instead of subset and variation you should set 'style' and 'weight'.
					'style' => 'normal',
		//			'weight' => 700,
		//			'subset'    => 'latin-ext',
					'variation' => 'regular',
					'size'      => 40,
					'line-height' => 1.2,
					'letter-spacing' => 0,
					'color'     => '#000000'
				],
				'components' => [
					'family'         => true,
					//'style', 'weight', 'subset', 'variation' will appear and disappear along with 'family'
					'size'           => true,
					'line-height'    => true,
					'letter-spacing' => true,
					'color'          => true
				],
			],
			'h2' => [
				'label' => __( 'H2 Heading', 'unysonplus' ),
				'type'  => 'typography-v2',
				'value'      => [
					'family'    => 'Raleway',
		//			For standard fonts, instead of subset and variation you should set 'style' and 'weight'.
		//			'style' => 'italic',
		//			'weight' => 700,
					'subset'    => 'latin-ext',
					'variation' => 'regular',
					'size'      => 32,
					'line-height' => 1.2,
					'letter-spacing' => 0,
					'color'     => '#000000'
				],
				'components' => [
					'family'         => true,
					//'style', 'weight', 'subset', 'variation' will appear and disappear along with 'family'
					'size'           => true,
					'line-height'    => true,
					'letter-spacing' => true,
					'color'          => true
				],
			],
			'h3' => [
				'label' => __( 'H3 Heading', 'unysonplus' ),
				'type'  => 'typography-v2',
				'value'      => [
					'family'    => 'Raleway',
		//			For standard fonts, instead of subset and variation you should set 'style' and 'weight'.
		//			'style' => 'italic',
		//			'weight' => 700,
					'subset'    => 'latin-ext',
					'variation' => 'regular',
					'size'      => 28,
					'line-height' => 1.2,
					'letter-spacing' => 0,
					'color'     => '#000000'
				],
				'components' => [
					'family'         => true,
					//'style', 'weight', 'subset', 'variation' will appear and disappear along with 'family'
					'size'           => true,
					'line-height'    => true,
					'letter-spacing' => true,
					'color'          => true
				],
			],
			'h4' => [
				'label' => __( 'H4 Heading', 'unysonplus' ),
				'type'  => 'typography-v2',
				'value'      => [
					'family'    => 'Raleway',
		//			For standard fonts, instead of subset and variation you should set 'style' and 'weight'.
		//			'style' => 'italic',
		//			'weight' => 700,
					'subset'    => 'latin-ext',
					'variation' => 'regular',
					'size'      => 24,
					'line-height' => 1.2,
					'letter-spacing' => 0,
					'color'     => '#000000'
				],
				'components' => [
					'family'         => true,
					//'style', 'weight', 'subset', 'variation' will appear and disappear along with 'family'
					'size'           => true,
					'line-height'    => true,
					'letter-spacing' => true,
					'color'          => true
				],
			],
			'h5' => [
				'label' => __( 'H5 Heading', 'unysonplus' ),
				'type'  => 'typography-v2',
				'value'      => [
					'family'    => 'Raleway',
		//			For standard fonts, instead of subset and variation you should set 'style' and 'weight'.
		//			'style' => 'italic',
		//			'weight' => 700,
					'subset'    => 'latin-ext',
					'variation' => 'regular',
					'size'      => 20,
					'line-height' => 1.2,
					'letter-spacing' => 0,
					'color'     => '#000000'
				],
				'components' => [
					'family'         => true,
					//'style', 'weight', 'subset', 'variation' will appear and disappear along with 'family'
					'size'           => true,
					'line-height'    => true,
					'letter-spacing' => true,
					'color'          => true
				],
			],
			'h6' => [
				'label' => __( 'H6 Heading', 'unysonplus' ),
				'type'  => 'typography-v2',
				'value'      => [
					'family'    => 'Raleway',
		//			For standard fonts, instead of subset and variation you should set 'style' and 'weight'.
		//			'style' => 'italic',
		//			'weight' => 700,
					'subset'    => 'latin-ext',
					'variation' => 'regular',
					'size'      => 18,
					'line-height' => 1.2,
					'letter-spacing' => 0,
					'color'     => '#000000'
				],
				'components' => [
					'family'         => true,
					//'style', 'weight', 'subset', 'variation' will appear and disappear along with 'family'
					'size'           => true,
					'line-height'    => true,
					'letter-spacing' => true,
					'color'          => true
				],
			],
			'body' => [
				'label' => __( 'Body Text', 'unysonplus' ),
				'type'  => 'typography-v2',
				'value'      => [
					'family'    => 'Open Sans',
		//			For standard fonts, instead of subset and variation you should set 'style' and 'weight'.
		//			'style' => 'italic',
		//			'weight' => 700,
					'subset'    => 'cyrillic',
					'variation' => 'regular',
					'size'      => 16,
					'line-height' => 1.5,
					'letter-spacing' => 0,
					'color'     => '#000000'
				],
				'components' => [
					'family'         => true,
					//'style', 'weight', 'subset', 'variation' will appear and disappear along with 'family'
					'size'           => true,
					'line-height'    => true,
					'letter-spacing' => true,
					'color'          => true
				],
				'desc' => __( 'The main typography of the site\'s content.', 'unysonplus' ),
				'help'  => 	__( 'This includes the paragraphs and lists.',	'unysonplus' ),
			],
			'body_link'              => [
				'label' => __( '', 'unysonplus' ),
				'desc'  => __( 'Body Link Color', 'unysonplus' ),
				'type'  => 'color-picker',
				'value' => '#000000',
			],
			'body_link_hover'              => [
				'label' => __( '', 'unysonplus' ),
				'desc'  => __( 'Body Link Hover Color', 'unysonplus' ),
				'type'  => 'color-picker',
				'value' => '#333333',
			],
		],
	],
];