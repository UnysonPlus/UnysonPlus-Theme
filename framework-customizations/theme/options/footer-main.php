<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

require dirname( __FILE__ ) . '/footer-common.php';

// Main footer: default to 3 columns, all empty. Editors add elements as needed.
$main_footer_empty_cols = function ( $c, $n ) { return []; };

$options = $footer_columns_picker( 'main_footer', 5, '3', $main_footer_empty_cols )
         + $section_settings( 'main_footer' );
