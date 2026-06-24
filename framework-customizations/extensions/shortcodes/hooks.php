<?php if (!defined('FW')) die('Forbidden');



/** @internal */


function _filter_disable_shortcodes($to_disable)

{

	// NOTE: do NOT disable 'team_member' here — that was leftover boilerplate from
	// the original Unyson sample theme (team_member was its throwaway "example" of
	// disabling a shortcode). It hid the Team Member element from the builder
	// entirely. The header/footer builder still disables it inside header/footer
	// editing only, which is correct.

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if(!is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )) {

		$to_disable[] = 'contact_form_7';

	}

//	$to_disable[] = 'some_other_shortcode';

	return $to_disable;

}

// add_filter('fw_ext_shortcodes_disable_shortcodes', '_filter_disable_shortcodes');