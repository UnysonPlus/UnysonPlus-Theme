<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

/**
 * The class declaration is guarded by class_exists() so this file is a no-op
 * when the Unyson+ plugin (which bundles an identical option-type since
 * framework v2.7.75) has already declared and registered the class. Without
 * the guard, both the plugin and theme attempting to declare the same class
 * caused a "Cannot redeclare class FW_Option_Type_FwMultiInline" fatal on
 * activation.
 */
if ( ! class_exists( 'FW_Option_Type_FwMultiInline' ) ) :

class FW_Option_Type_FwMultiInline extends FW_Option_Type
{
    public function get_type()
    {
        return 'fw-multi-inline';
    }

    /**
     * @internal
     */
    protected function _enqueue_static($id, $option, $data)
    {
        $uri = get_template_directory_uri() .'/inc/includes/option-types/'. $this->get_type() .'/static';

        wp_enqueue_style(
            'fw-option-'. $this->get_type(),
            $uri .'/css/styles.css'
        );

    }


	/**
	 * @internal
	 */
	public function _get_backend_width_type()
	{
		return 'auto';
	}
	/**
	 * @internal
	 */
	protected function _render($id, $option, $data)
	{
		return fw_render_view(dirname(__FILE__) . '/view.php', array(
			'id' => $id,
			'option' => $option,
			'data' => $data
		));
	}
	
	

	/**
	 * @internal
	 */
	protected function _get_value_from_input($option, $input_value)
	{
		
		
		if ( is_array( $input_value ) ) {
			
			$value = $input_value;

		} else {
			
			$value = $option['value'];
		}
		return $value;

	}

    /**
     * @internal
     */
    protected function _get_defaults()
    {

        return array(
            'value' => array(
				'firstoption' => 'select2'
			),
			
			'fw_multi_options' => array(
				'firstoption' => array(
					'type' => 'select',
					'title' => __('Title', 'fw'),
					'choices' => array(
						'select1' => __('select1', 'fw'),
						'select2' => __('select2', 'fw'),
						'select3' => __('select3', 'fw')
					),
				)			
			),
			'groupname'=>''
        );
    }
}

FW_Option_Type::register('FW_Option_Type_FwMultiInline');

endif; // class_exists guard