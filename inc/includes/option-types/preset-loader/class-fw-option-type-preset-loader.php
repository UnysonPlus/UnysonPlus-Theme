<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

/**
 * `preset-loader` option type — a per-tab "Quick start" control.
 *
 * Renders a card grid of predefined presets + a custom-JSON uploader + an "Export
 * current" download. Choosing a preset and clicking Apply POSTs to the AJAX handler
 * (inc/includes/settings-presets.php), which writes the preset into the tab's saved
 * options and returns success; the JS then reloads the settings page so every widget
 * re-renders from the DB. See settings-presets.php for the preset registry.
 *
 * Option config:
 *   'type'         => 'preset-loader',
 *   'preset_group' => 'header_menu',   // a key in unysonplus_settings_preset_groups()
 *
 * The option itself stores nothing meaningful (its actions run through AJAX), so its
 * value is a harmless string.
 */
if ( ! class_exists( 'FW_Option_Type_Preset_Loader' ) ) :

class FW_Option_Type_Preset_Loader extends FW_Option_Type {

	public function get_type() {
		return 'preset-loader';
	}

	/** Shrink-to-fit is wrong here; the card grid wants the full option width. */
	public function _get_backend_width_type() {
		return 'full';
	}

	/**
	 * @internal
	 */
	protected function _enqueue_static( $id, $option, $data ) {
		$uri = get_template_directory_uri() . '/inc/includes/option-types/' . $this->get_type() . '/static';

		wp_enqueue_style(
			'fw-option-' . $this->get_type(),
			$uri . '/css/styles.css',
			array(),
			'1.0.0'
		);
		wp_enqueue_script(
			'fw-option-' . $this->get_type(),
			$uri . '/js/scripts.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);
	}

	/**
	 * @internal
	 */
	protected function _render( $id, $option, $data ) {
		return fw_render_view( dirname( __FILE__ ) . '/view.php', array(
			'id'     => $id,
			'option' => $option,
			'data'   => $data,
		) );
	}

	/**
	 * @internal
	 */
	protected function _get_value_from_input( $option, $input_value ) {
		// Nothing to persist — apply happens via AJAX. Keep whatever came back
		// (or the default) so the form round-trips cleanly.
		return is_string( $input_value ) ? $input_value : $option['value'];
	}

	/**
	 * @internal
	 */
	protected function _get_defaults() {
		return array(
			'value'        => '',
			'preset_group' => '',
		);
	}
}

FW_Option_Type::register( 'FW_Option_Type_Preset_Loader' );

endif; // class_exists guard
