<?php 
$hide_footer_widgets = get_post_meta(get_the_ID(), 'hide-footer-widgets', true);
if(empty($hide_footer_widgets))	{
	$footer_widgets = function_exists('fw_get_db_settings_option') ? fw_get_db_settings_option('footer_widgets') : null;
	if( is_array($footer_widgets) && ! empty($footer_widgets['enabled']) && $footer_widgets['enabled'] == 'yes' ) :
		$footer_widgets = $footer_widgets['yes'];
		$column_count = $footer_widgets['style']['selected'];
		$class[]	= unysonplus_fw_container_class( $footer_widgets['container'] );
		$class[]	= '';
		$attr = array();
		if( !empty($class))			$attr['class'] = join(' ', $class);
		?>
		<div class="widgets">
			<div <?php echo unysonplus_attr_to_html($attr); ?>>
			<div class="fw-row">
				<?php
				switch ($column_count) {
					case 'col-md-12':
							$column 	 = array_fill(0, 1, 'col-md-12');
							break;
					case 'col-md-6':
							$column 	 = array_fill(0, 2, 'col-md-6');
							break;
					case 'col-md-6-a':
							$column[] = 'col-md-8';
							$column[] = 'col-md-4';
							break;
					case 'col-md-6-b':
							$column[] = 'col-md-4';
							$column[] = 'col-md-8';
							break;
					case 'col-md-4':
							$column 	 = array_fill(0, 3, 'col-md-4');
							break;
					case 'col-md-4-a':
							$column[] = 'col-md-3';
							$column[] = 'col-md-6';
							$column[] = 'col-md-3';
							break;
					case 'col-md-4-b':
							$column[] = 'col-md-3';
							$column[] = 'col-md-3';
							$column[] = 'col-md-6';
							break;
					case 'col-md-4-c':
							$column[] = 'col-md-4';
							$column[] = 'col-md-2';
							$column[] = 'col-md-6';
							break;
					case 'col-md-5':
							$column 	 = array_fill(0, 5, 'col-md');
							break;
					default:
							$column 	 = array_fill(0, 4, 'col-md-3');
							break;
				}  
				for( $i = 0; $i < count($column); $i++ ) : ?>
					<div class="<?php echo str_replace( 'col-md', 'fw-col-md', $column[$i] ) . ' widget-' . ( $i + 1 ); ?>">
							<?php dynamic_sidebar( 'footer-'. ( $i + 1 ) ); ?>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	</div>
	<?php endif; 
} ?>
