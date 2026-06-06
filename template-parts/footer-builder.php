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

$pre_footer  = fw_get_db_settings_option( 'pre_footer_settings' );
$post_footer = fw_get_db_settings_option( 'post_footer_settings' );
$copyright   = fw_get_db_settings_option( 'copyright_settings' );

$pre_footer_enabled  = ! empty( $pre_footer['enabled'] )  && $pre_footer['enabled']  === 'yes';
$post_footer_enabled = ! empty( $post_footer['enabled'] ) && $post_footer['enabled'] === 'yes';
$copyright_enabled   = ! empty( $copyright['enabled'] )   && $copyright['enabled']   === 'yes';

ob_start();

if ( $pre_footer_enabled && ! empty( $pre_footer['yes'] ) ) {
	unysonplus_render_footer_section( $pre_footer['yes'], 'pre_footer' );
}

$main_columns = fw_get_db_settings_option( 'main_footer_columns' );
if ( ! empty( $main_columns['count'] ) ) {
	unysonplus_render_footer_section(
		array(
			'main_footer_columns'        => $main_columns,
			'main_footer_custom_styling' => fw_get_db_settings_option( 'main_footer_custom_styling' ),
		),
		'main_footer'
	);
}

if ( $post_footer_enabled && ! empty( $post_footer['yes'] ) ) {
	unysonplus_render_footer_section( $post_footer['yes'], 'post_footer' );
}

if ( $copyright_enabled && ! empty( $copyright['yes'] ) ) {
	unysonplus_render_footer_section( $copyright['yes'], 'copyright' );
}

$rendered = trim( ob_get_clean() );

if ( $rendered !== '' ) {
	echo $rendered;
} elseif ( function_exists( 'unysonplus_render_footer_fallback' ) ) {
	unysonplus_render_footer_fallback();
}
