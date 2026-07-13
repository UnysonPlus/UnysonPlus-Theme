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

// The three CONTENT sections (pre / main / post) are buffered into a `.footer__body`
// wrapper that carries the footer's vertical padding (--footer-pad-top/-bottom). The
// copyright is buffered SEPARATELY and rendered after that wrapper — still inside
// <footer> (so it stays in the contentinfo landmark and under the footer background),
// but OUTSIDE the padded body, so the footer's bottom padding no longer sits below it.
// The copyright becomes a flush bottom bar with only its own .footer-section padding.
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

$body = trim( ob_get_clean() );

// Copyright — keeps its Enable switch (you may want to hide it entirely). Buffered on
// its own so it can sit OUTSIDE the padded .footer__body (flush bottom bar).
ob_start();
$copyright = isset( $footer_cfg['copyright_settings'] ) ? $footer_cfg['copyright_settings'] : fw_get_db_settings_option( 'copyright_settings' );
if ( ! empty( $copyright['enabled'] ) && $copyright['enabled'] === 'yes' && ! empty( $copyright['yes'] ) ) {
	unysonplus_render_footer_section( $copyright['yes'], 'copyright' );
}
$copyright_html = trim( ob_get_clean() );

// Compose: content sections in the padded .footer__body, copyright flush after it.
$rendered = '';
if ( $body !== '' ) {
	$rendered .= '<div class="footer__body">' . $body . '</div>';
}
$rendered .= $copyright_html;

if ( trim( $rendered ) !== '' ) {
	echo $rendered; // phpcs:ignore — already-escaped footer-section HTML.
} elseif ( function_exists( 'unysonplus_render_footer_fallback' ) ) {
	unysonplus_render_footer_fallback();
}
