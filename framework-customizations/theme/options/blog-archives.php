<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Blog → Archives & Search sub-tab.
 *
 * Defaults for category / tag / author / date archives (archive.php, which
 * category.php delegates to) and search results (search.php). Stored under the
 * `blog_archives` multi; read via unysonplus_archive_get() in
 * inc/includes/blog.php. Layout "Inherit" reuses the Blog Index settings.
 */

$options = [
	'blog_archives' => [
		'type'          => 'multi',
		'label'         => false,
		'desc'          => false,
		'inner-options' => [
			'archive_header' => [
				'label'        => __( 'Archive Header', 'unysonplus' ),
				'desc'         => __( 'The title block at the top of archive / search pages.', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'archive_show_description' => [
				'label'        => __( 'Term Description', 'unysonplus' ),
				'desc'         => __( 'Show the category / tag / taxonomy description under the title.', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'archive_author_bio' => [
				'label'        => __( 'Author Bio (author archives)', 'unysonplus' ),
				'type'         => 'switch',
				'value'        => 'yes',
				'right-choice' => [ 'value' => 'yes', 'label' => __( 'Show', 'unysonplus' ) ],
				'left-choice'  => [ 'value' => 'no',  'label' => __( 'Hide', 'unysonplus' ) ],
			],
			'archive_layout' => [
				'label'   => __( 'Archive Layout', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (Blog Index)', 'unysonplus' ),
					'list'    => __( 'List', 'unysonplus' ),
					'grid'    => __( 'Grid', 'unysonplus' ),
					'masonry' => __( 'Masonry', 'unysonplus' ),
				],
			],
			'archive_columns' => [
				'label'   => __( 'Archive Columns', 'unysonplus' ),
				'desc'    => __( 'For Grid / Masonry. "Inherit" uses the Blog Index columns.', 'unysonplus' ),
				'type'    => 'select',
				'value'   => '',
				'choices' => [ '' => __( 'Inherit', 'unysonplus' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' ],
			],
			'archive_sidebar' => [
				'label'   => __( 'Archive Sidebar', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (use global)', 'unysonplus' ),
					'none'    => __( 'No sidebar', 'unysonplus' ),
					'left'    => __( 'Left', 'unysonplus' ),
					'right'   => __( 'Right', 'unysonplus' ),
				],
			],
			'search_layout' => [
				'label'   => __( 'Search Results Layout', 'unysonplus' ),
				'type'    => 'select',
				'value'   => 'inherit',
				'choices' => [
					'inherit' => __( 'Inherit (Blog Index)', 'unysonplus' ),
					'list'    => __( 'List', 'unysonplus' ),
					'grid'    => __( 'Grid', 'unysonplus' ),
				],
			],
			'search_empty_message' => [
				'label' => __( 'Search "No Results" Message', 'unysonplus' ),
				'desc'  => __( 'Optional. Leave blank for the default message.', 'unysonplus' ),
				'type'  => 'text',
				'value' => '',
			],
		],
	],
];
