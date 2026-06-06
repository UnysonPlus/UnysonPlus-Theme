<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

//$uri = get_template_directory_uri();

$options = [
	'page_options' => [
		'type' => 'multi',
		'label' => false,
		/*'attr' => array(
			'class' => '',
		),*/
		'inner-options' => [
			'body_class' => [
				'label' => __( 'Body Class', 'unysonplus' ),
				'type'  => 'text',
				'desc'  => __( 'CSS class for the /<body/> tag', 'unysonplus' ),
			],
			/*'hide_title' => array(
				'label'        => __( 'Hide Title?', 'unysonplus' ),
				'type'         => 'switch',
				'right-choice' => array(
					'value' => true,
					'label' => __( 'Yes', 'unysonplus' )
				),
				'left-choice'  => array(
					'value' => false,
					'label' => __( 'No', 'unysonplus' )
				),
				'value'        => false,
				
			),
			'footer_scripts' => array(
				'label' => __( 'Footer Scripts', 'unysonplus' ),
				'type'  => 'textarea',
				'desc'  => __( 'Enter Footer Scripts. Include &#039;&lt;script&gt; ... &lt;/script&gt;&#039; tags', 'unysonplus' ),
			),*/
		],
	],
];
