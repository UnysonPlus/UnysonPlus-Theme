<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * @var string $id
 * @var array  $option
 * @var array  $data
 */

$group = ! empty( $option['preset_group'] ) ? $option['preset_group'] : '';
$groups = function_exists( 'unysonplus_settings_preset_groups' ) ? unysonplus_settings_preset_groups() : array();
$conf   = ( $group !== '' && ! empty( $groups[ $group ] ) ) ? $groups[ $group ] : null;

$attr = $option['attr'];
unset( $attr['value'] );
$hidden_name = isset( $attr['name'] ) ? $attr['name'] : $id;

// Data the JS needs: the group key, a nonce, the AJAX url, and the current saved
// values (for the Export download).
$nonce   = wp_create_nonce( 'unysonplus_settings_preset' );
$ajaxurl = admin_url( 'admin-ajax.php' );
$current = ( $group !== '' && function_exists( 'unysonplus_settings_preset_current_json' ) )
	? unysonplus_settings_preset_current_json( $group )
	: '{}';
?>
<div <?php echo fw_attr_to_html( $attr ); ?>
	data-preset-group="<?php echo esc_attr( $group ); ?>"
	data-preset-nonce="<?php echo esc_attr( $nonce ); ?>"
	data-preset-ajaxurl="<?php echo esc_url( $ajaxurl ); ?>"
	data-preset-current="<?php echo esc_attr( $current ); ?>">

	<input type="hidden" name="<?php echo esc_attr( $hidden_name ); ?>" value="" />

	<?php if ( $conf === null ) : ?>
		<p class="fw-preset-loader-empty"><?php esc_html_e( 'No presets are registered for this section.', 'unysonplus' ); ?></p>
	<?php else : ?>

		<div class="fw-preset-loader-cards">
			<?php foreach ( $conf['presets'] as $key => $preset ) :
				$label = isset( $preset['label'] ) ? $preset['label'] : $key;
				$desc  = isset( $preset['desc'] ) ? $preset['desc'] : '';
				?>
				<button type="button" class="fw-preset-card<?php echo strpos( $key, 'lib_' ) === 0 ? ' fw-preset-card--installed' : ''; ?>" data-preset-key="<?php echo esc_attr( $key ); ?>"><?php if ( strpos( $key, 'lib_' ) === 0 ) : ?><span class="fw-preset-card__del" role="button" tabindex="0" title="<?php esc_attr_e( 'Remove installed preset', 'unysonplus' ); ?>" data-del-group="<?php echo esc_attr( $group ); ?>" data-del-slug="<?php echo esc_attr( preg_replace( '/^lib_/', '', $key ) ); ?>">&times;</span><?php endif; ?>
					<span class="fw-preset-card__name"><?php echo esc_html( $label ); ?></span>
					<?php if ( $desc !== '' ) : ?>
						<span class="fw-preset-card__desc"><?php echo esc_html( $desc ); ?></span>
					<?php endif; ?>
				</button>
			<?php endforeach; ?>

			<!-- Custom (uploaded JSON) card — becomes selectable once a valid file is loaded. -->
			<button type="button" class="fw-preset-card fw-preset-card--custom is-disabled" data-preset-key="__custom__" disabled>
				<span class="fw-preset-card__name"><?php esc_html_e( 'Custom (uploaded)', 'unysonplus' ); ?></span>
				<span class="fw-preset-card__desc fw-preset-card__custom-hint"><?php esc_html_e( 'Upload a .json file below to enable.', 'unysonplus' ); ?></span>
			</button>
		</div>

		<div class="fw-preset-loader-actions">
			<button type="button" class="button button-primary fw-preset-apply" disabled>
				<?php esc_html_e( 'Apply Preset', 'unysonplus' ); ?>
			</button>

			<label class="button fw-preset-upload-label">
				<?php esc_html_e( 'Upload JSON…', 'unysonplus' ); ?>
				<input type="file" class="fw-preset-upload" accept="application/json,.json" hidden />
			</label>

			<button type="button" class="button fw-preset-export">
				<?php esc_html_e( 'Export current', 'unysonplus' ); ?>
			</button>

			<?php // Browse Library — opens a modal of downloadable presets for this group.
			if ( function_exists( 'unysonplus_preset_library_localize' ) ) : ?>
				<button type="button" class="button fw-preset-browse">
					<?php esc_html_e( 'Browse Library', 'unysonplus' ); ?>
				</button>
			<?php endif; ?>

			<span class="fw-preset-loader-status" role="status" aria-live="polite"></span>
		</div>

		<p class="fw-preset-loader-note">
			<?php esc_html_e( 'Applying a preset saves these settings and refreshes this tab. Unsaved changes elsewhere on this page will be lost.', 'unysonplus' ); ?>
		</p>

	<?php endif; ?>
</div>
