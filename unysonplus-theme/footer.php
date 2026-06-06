		</div> <!-- #content -->
		<?php if ( ! ( function_exists( 'unysonplus_should_hide_site_footer' ) && unysonplus_should_hide_site_footer() ) ) : ?>
			<?php do_action( 'unysonplus_before_footer' ); ?>
			<?php
			$footer_classes = array( 'footer' );

			if ( function_exists( 'fw_get_db_settings_option' ) ) {
				$f_bg_image = fw_get_db_settings_option( 'footer_bg_image' );
				if ( ! empty( $f_bg_image['url'] ) ) {
					$footer_classes[] = 'footer--has-bg-image';
				}
				$f_css_class = fw_get_db_settings_option( 'footer_css_class' );
				if ( ! empty( $f_css_class ) ) {
					$footer_classes[] = sanitize_html_class( $f_css_class );
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
