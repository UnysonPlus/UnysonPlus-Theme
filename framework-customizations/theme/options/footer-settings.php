<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

// Footer sub-tabs, in order: Layout · Pre · Main · Post · [extra bars] · Copyright.
// Copyright is spliced in LAST after any registered extra bars, so it always stays
// the bottom bar (see inc/includes/footer-builder.php → unysonplus_footer_extra_bars).
$footer_subtabs = [
	'tab_layout' => [
		'title'   => __( 'Footer Layout', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'footer_widget_box' => [
				'title'   => __( 'Overall Footer Layout', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					fw()->theme->get_options( 'footer-layout' ),
				],
			],
		],
	],
	'tab_pre' => [
		'title'   => __( 'Pre-Footer', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'footer_widget_box' => [
				'title'   => __( 'Pre Footer', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					fw()->theme->get_options( 'footer-pre' ),
				],
			],
		],
	],
	'tab_main' => [
		'title'   => __( 'Main Footer', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'footer_menu_box' => [
				'title'   => __( 'Main Footer', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					fw()->theme->get_options( 'footer-main' ),
				],
			],
		],
	],
	'tab_post' => [
		'title'   => __( 'Post-Footer', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'footer_widget_box' => [
				'title'   => __( 'Post Footer', 'unysonplus' ),
				'type'    => 'box',
				'options' => [
					fw()->theme->get_options( 'footer-post' ),
				],
			],
		],
	],
];

// Extra footer bars registered via the `unysonplus_footer_extra_bars` filter. Each
// reuses the standard columns control (count + ratio + Auto Width + elements), so it
// has full parity with the built-in bars; stored under `footer_x_<id>_columns`.
if ( function_exists( 'unysonplus_footer_extra_bars' ) && function_exists( 'unysonplus_footer_columns_field' ) ) {
	foreach ( unysonplus_footer_extra_bars() as $bar ) {
		$footer_subtabs[ 'tab_x_' . $bar['prefix'] ] = [
			'title'   => $bar['label'],
			'type'    => 'tab',
			'options' => [
				'footer_x_box' => [
					'title'   => $bar['label'],
					'type'    => 'box',
					'options' => [
						[ $bar['prefix'] . '_columns' => unysonplus_footer_columns_field( $bar['prefix'], $bar['max'], 1 ) ],
					],
				],
			],
		];
	}
}

// Copyright — always last.
$footer_subtabs['tab_copyright'] = [
	'title'   => __( 'Copyright', 'unysonplus' ),
	'type'    => 'tab',
	'options' => [
		'footer_copyright_box' => [
			'title'   => __( 'Footer Copyright Settings', 'unysonplus' ),
			'type'    => 'box',
			'options' => [
				fw()->theme->get_options( 'footer-copyright' ),
			],
		],
	],
];

$options = [
	'footer_container' => [
		'title'   => __( 'Footer', 'unysonplus' ),
		'type'    => 'tab',
		'options' => [
			'footer' => [
				'title'   => __( 'Footer Settings', 'unysonplus' ),
				'type'    => 'box',
				'options' => $footer_subtabs,
			],
		],
	],
];
