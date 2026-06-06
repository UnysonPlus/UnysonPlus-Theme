<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = [
	'main' => [
		'title'   => __( 'Post Settings', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			fw()->theme->get_options( 'post-options' ),
		],
	],
];