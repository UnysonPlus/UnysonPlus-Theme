<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

/**
 * Footer builder template.
 *
 * Renders the 4 configured footer sections (pre / main / post / copyright)
 * when the Unyson plugin is active. Falls back to widget areas + a
 * copyright line otherwise, so a fresh install never shows an empty footer.
 */

if ( ! function_exists( 'fw_get_db_settings_option' ) ) {
	if ( function_exists( 'unysonplus_render_footer_fallback' ) ) {
		unysonplus_render_footer_fallback();
	}
	return;
}

// Builder mode: when the active footer preset was authored with the page builder,
// render its content (footer.php already wraps it in <footer>) and bail.
$hf_footer = function_exists( 'unysonplus_get_active_footer_render' )
	? unysonplus_get_active_footer_render()
	: array( 'mode' => 'slots' );

if ( $hf_footer['mode'] === 'builder' && function_exists( 'fw_ext_hfbuilder_render' ) ) {
	echo fw_ext_hfbuilder_render( $hf_footer['post_id'], 'footer' ); // phpcs:ignore — builder output.
	return;
}

// Resolve the active footer config: the per-content/site-wide preset, or the
// global "Default" footer sections from Theme Settings.
$footer_cfg  = function_exists( 'unysonplus_get_active_footer_config' )
	? unysonplus_get_active_footer_config()
	: array();

ob_start();

// Pre-Footer — no Enable switch; the section renderer skips it when no column has
// content (same behavior as Main Footer below).
$pre_columns = isset( $footer_cfg['pre_footer_columns'] ) ? $footer_cfg['pre_footer_columns'] : fw_get_db_settings_option( 'pre_footer_columns' );
if ( ! empty( $pre_columns['count'] ) ) {
	$pre_styling = isset( $footer_cfg['pre_footer_custom_styling'] ) ? $footer_cfg['pre_footer_custom_styling'] : fw_get_db_settings_option( 'pre_footer_custom_styling' );
	unysonplus_render_footer_section(
		array( 'pre_footer_columns' => $pre_columns, 'pre_footer_custom_styling' => $pre_styling ),
		'pre_footer'
	);
}

// Main Footer.
$main_columns = isset( $footer_cfg['main_footer_columns'] ) ? $footer_cfg['main_footer_columns'] : fw_get_db_settings_option( 'main_footer_columns' );
if ( ! empty( $main_columns['count'] ) ) {
	$main_styling = isset( $footer_cfg['main_footer_custom_styling'] ) ? $footer_cfg['main_footer_custom_styling'] : fw_get_db_settings_option( 'main_footer_custom_styling' );
	unysonplus_render_footer_section(
		array(
			'main_footer_columns'        => $main_columns,
			'main_footer_custom_styling' => $main_styling,
		),
		'main_footer'
	);
}

// Post-Footer — no Enable switch (content-driven, like Pre-Footer).
$post_columns = isset( $footer_cfg['post_footer_columns'] ) ? $footer_cfg['post_footer_columns'] : fw_get_db_settings_option( 'post_footer_columns' );
if ( ! empty( $post_columns['count'] ) ) {
	$post_styling = isset( $footer_cfg['post_footer_custom_styling'] ) ? $footer_cfg['post_footer_custom_styling'] : fw_get_db_settings_option( 'post_footer_custom_styling' );
	unysonplus_render_footer_section(
		array( 'post_footer_columns' => $post_columns, 'post_footer_custom_styling' => $post_styling ),
		'post_footer'
	);
}

// Copyright — keeps its Enable switch (you may want to hide it entirely).
$copyright = isset( $footer_cfg['copyright_settings'] ) ? $footer_cfg['copyright_settings'] : fw_get_db_settings_option( 'copyright_settings' );
if ( ! empty( $copyright['enabled'] ) && $copyright['enabled'] === 'yes' && ! empty( $copyright['yes'] ) ) {
	unysonplus_render_footer_section( $copyright['yes'], 'copyright' );
}

$rendered = trim( ob_get_clean() );

if ( $rendered !== '' ) {
	echo $rendered;
} elseif ( function_exists( 'unysonplus_render_footer_fallback' ) ) {
	unysonplus_render_footer_fallback();
}
