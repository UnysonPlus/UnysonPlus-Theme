		</div> <!-- #content -->
		<?php if ( ! ( function_exists( 'unysonplus_should_hide_site_footer' ) && unysonplus_should_hide_site_footer() ) ) : ?>
			<?php do_action( 'unysonplus_before_footer' ); ?>
			<?php
			$footer_classes = array( 'footer' );

			if ( function_exists( 'fw_get_db_settings_option' ) ) {
				// Background Pro image (Footer → Layout → Background), or the legacy
				// footer_bg_image as a fallback for sites not yet migrated.
				$f_has_bg_image = false;
				$f_bg = fw_get_db_settings_option( 'footer_background' );
				if ( is_array( $f_bg ) && function_exists( 'fw_akg' ) && fw_akg( 'image/src/url', $f_bg ) ) {
					$f_has_bg_image = true;
				} else {
					$f_bg_image = fw_get_db_settings_option( 'footer_bg_image' );
					if ( ! empty( $f_bg_image['url'] ) ) { $f_has_bg_image = true; }
				}
				if ( $f_has_bg_image ) {
					$footer_classes[] = 'footer--has-bg-image';
				}
				$f_css_class = fw_get_db_settings_option( 'footer_css_class' );
				if ( ! empty( $f_css_class ) ) {
					$footer_classes[] = sanitize_html_class( $f_css_class );
				}
				// Footer border (Footer → Layout → Border): the shared width/style/colour
				// applies to the edges chosen in Border Sides. Default 'top' preserves the
				// previous top-only behavior. The border stays invisible until a width AND
				// colour are set (the CSS vars fall back to 0 / transparent).
				$f_sides = fw_get_db_settings_option( 'footer_border_sides' );
				$f_sides = function_exists( 'unysonplus_hf_normalize_sides' )
					? unysonplus_hf_normalize_sides( $f_sides )
					: ( in_array( $f_sides, array( 'top', 'bottom', 'both' ), true )
						? ( $f_sides === 'both' ? array( 'top', 'bottom' ) : array( $f_sides ) )
						: array( 'top' ) );
				$f_side_class = array( 'top' => 'footer--bt', 'right' => 'footer--br', 'bottom' => 'footer--bb', 'left' => 'footer--bl' );
				foreach ( $f_sides as $f_side ) {
					if ( isset( $f_side_class[ $f_side ] ) ) { $footer_classes[] = $f_side_class[ $f_side ]; }
				}
				// Border extent (Border Extent): Container / Custom move the border(s) onto
				// a centered, capped pseudo-element; Full (default) keeps them edge-to-edge.
				$f_bext = fw_get_db_settings_option( 'footer_border_top_extent' );
				if ( is_array( $f_bext ) && isset( $f_bext['mode'] ) && $f_bext['mode'] !== 'full' && $f_bext['mode'] !== '' ) {
					$footer_classes[] = 'footer--bcontained';
				}
			}
			?>
			<footer id="colophon" class="<?php echo esc_attr( implode( ' ', $footer_classes ) ); ?>" role="contentinfo">
				<?php get_template_part( 'template-parts/footer', 'builder' ); ?>
			</footer><!-- #colophon -->
			<?php do_action( 'unysonplus_after_footer' ); ?>
		<?php endif; ?>
	</div> <!-- #page -->
	<?php do_action( 'unysonplus_after' ); ?>
	<?php wp_footer(); ?>
	</body>
</html>
