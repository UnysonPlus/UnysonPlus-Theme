<?php
/**
 * Template Name: Demo Options — fw_print()
 *
 * Dumps fw_print() of every leaf option in the Theme Settings "Demo Options"
 * showcase (framework-customizations/theme/options/demo-box.php, which nests
 * demo.php + demo-2.php) — one panel per Unyson+ option type, showing the shape
 * of its saved value.
 *
 * Assign it to a page via Page Attributes → Template ("Demo Options — fw_print()").
 * The values shown are the current Theme Settings values, or each option's
 * default when nothing has been saved. (The demo tab only appears in Theme
 * Settings when General → Misc "Show demo options" is on, but this page reads
 * the values either way.)
 *
 * @package Unysonplus
 */

get_header();
?>
<div class="fw-container" style="max-width:1080px;padding:2.5rem 0">
	<h1 class="entry-title"><?php the_title(); ?></h1>

	<?php
	if ( ! function_exists( 'fw' ) || ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_extract_only_options' ) ) {

		echo '<p><strong>' . esc_html__( 'The UnysonPlus plugin (Unyson+ framework) must be active to use this template.', 'unysonplus' ) . '</strong></p>';

	} else {

		// Pull the "Demo Options" container tree and flatten it to leaf options
		// (containers stripped), keyed by option id.
		$demo_tree    = fw()->theme->get_options( 'demo-box' );
		$leaf_options = fw_extract_only_options( is_array( $demo_tree ) ? $demo_tree : array() );

		if ( empty( $leaf_options ) ) {

			echo '<p>' . esc_html__( 'No demo options found (demo-box.php / demo.php / demo-2.php).', 'unysonplus' ) . '</p>';

		} else {

			echo '<p style="color:#50575e;margin:.25rem 0 1.5rem">'
				. sprintf(
					/* translators: %d: number of option types. */
					esc_html__( '%d option types — fw_print() of each current value.', 'unysonplus' ),
					count( $leaf_options )
				)
				. '</p>';

			foreach ( $leaf_options as $option_id => $option ) {
				$type  = isset( $option['type'] ) ? (string) $option['type'] : '(unknown)';
				$label = ( isset( $option['label'] ) && is_string( $option['label'] ) && $option['label'] !== '' )
					? $option['label']
					: $option_id;
				$value = fw_get_db_settings_option( $option_id );
				?>
				<section style="margin:0 0 1.5rem;border:1px solid #dcdcde;border-radius:8px;overflow:hidden">
					<header style="display:flex;justify-content:space-between;gap:1rem;align-items:baseline;background:#f6f7f7;padding:.55rem 1rem;border-bottom:1px solid #dcdcde">
						<span>
							<code style="font-weight:600"><?php echo esc_html( $option_id ); ?></code>
							<span style="color:#787c82">&mdash; <?php echo esc_html( wp_strip_all_tags( $label ) ); ?></span>
						</span>
						<span style="font-size:.85em;color:#787c82">type: <code><?php echo esc_html( $type ); ?></code></span>
					</header>
					<div style="padding:.4rem 1rem">
						<?php fw_print( $value ); ?>
					</div>
				</section>
				<?php
			}
		}
	}
	?>
</div>
<?php
get_footer();
