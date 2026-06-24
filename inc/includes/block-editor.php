<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }
/**
 * Block / classic editor integration.
 *
 * - Registers the editor stylesheet (assets/css/editor-style.css) so content
 *   in the block editor resembles the front end (font, colours, headings).
 * - theme.json (at the theme root) supplies the colour palette, font families,
 *   font sizes and layout (content/wide width) to the editor — this file only
 *   wires the editor CSS; WordPress reads theme.json automatically.
 *
 * Auto-loaded by Theme_Includes from inc/includes/.
 */

if ( ! function_exists( 'unysonplus_block_editor_support' ) ) :
	function unysonplus_block_editor_support() {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor-style.css' );
		// Let blocks opt into the theme's responsive embeds / wide alignment.
		add_theme_support( 'responsive-embeds' );
	}
	add_action( 'after_setup_theme', 'unysonplus_block_editor_support' );
endif;
