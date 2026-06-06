<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
} //http://manual.unyson.io/en/latest/options/introduction.html#content

$options = [
	'main' => [
		'title'   => 'Casino Review Information',
		'type'    => 'box',
		'options' => [
			fw()->theme->get_options( 'casino-reviews-options' ),
		],
	],
];