<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Theme bridge — typography tokens (h1-h6 + body) as CSS custom properties.
 *
 * On the FRONT END these are compiled into the generated CSS file by
 * inc/includes/hf-custom-css.php (no inline <style>). In the ADMIN they are still
 * emitted inline on admin_head so the page-builder editor preview stays live.
 *
 * Font Size + Color preset tokens are emitted by the plugin in a separate
 * <style id="unysonplus-presets"> block (see unysonplus/framework/includes/css-tokens.php).
 */

if ( ! function_exists( 'unysonplus_typography_presets' ) ) :
	/**
	 * Curated font-pairing presets (Theme Settings → General → Typography).
	 * Each pairs a heading + body family (verified in the plugin's Google-font list;
	 * '' = the system stack, no web font), a weight/letter-spacing for headings, and
	 * a px size scale h1→h6. Selecting one drives --font-heading / --font-body + the
	 * heading sizes; 'custom' uses the fields below instead. Mirrors how the Color
	 * Presets work, so the two systems feel the same.
	 *
	 * @return array<string,array>
	 */
	function unysonplus_typography_presets() {
		$sans  = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
		$serif = 'Georgia, Cambria, "Times New Roman", Times, serif';
		$sys   = 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
		$D = array( 40, 32, 28, 24, 20, 16 );  // default scale (px)
		$S = array( 48, 36, 28, 22, 20, 16 );  // display scale (bigger h1)
		return array(
			'system'    => array( 'label' => 'System', 'heading' => '', 'body' => '', 'heading_fallback' => $sys, 'body_fallback' => $sys, 'heading_weight' => 600, 'heading_ls' => '', 'heading_lh' => 1.2, 'sizes' => $D ),
			'modern'    => array( 'label' => 'Modern', 'heading' => 'Poppins', 'body' => 'Inter', 'heading_fallback' => $sans, 'body_fallback' => $sans, 'heading_weight' => 600, 'heading_ls' => '-0.01em', 'heading_lh' => 1.15, 'sizes' => $D ),
			'geometric' => array( 'label' => 'Geometric', 'heading' => 'Montserrat', 'body' => 'Work Sans', 'heading_fallback' => $sans, 'body_fallback' => $sans, 'heading_weight' => 700, 'heading_ls' => '-0.005em', 'heading_lh' => 1.15, 'sizes' => $D ),
			'editorial' => array( 'label' => 'Editorial', 'heading' => 'Playfair Display', 'body' => 'Inter', 'heading_fallback' => $serif, 'body_fallback' => $sans, 'heading_weight' => 700, 'heading_ls' => '', 'heading_lh' => 1.1, 'sizes' => $S ),
			'classic'   => array( 'label' => 'Classic', 'heading' => 'Merriweather', 'body' => 'Lato', 'heading_fallback' => $serif, 'body_fallback' => $sans, 'heading_weight' => 700, 'heading_ls' => '', 'heading_lh' => 1.2, 'sizes' => $D ),
			'elegant'   => array( 'label' => 'Elegant', 'heading' => 'Cormorant Garamond', 'body' => 'Nunito', 'heading_fallback' => $serif, 'body_fallback' => $sans, 'heading_weight' => 600, 'heading_ls' => '', 'heading_lh' => 1.1, 'sizes' => $S ),
			'bold'      => array( 'label' => 'Bold', 'heading' => 'Oswald', 'body' => 'Roboto', 'heading_fallback' => $sans, 'body_fallback' => $sans, 'heading_weight' => 600, 'heading_ls' => '0.01em', 'heading_lh' => 1.15, 'sizes' => $S ),
		);
	}
endif;

if ( ! function_exists( 'unysonplus_typography_config' ) ) :
	/**
	 * Resolve the effective typography from the saved `typography` options. When a
	 * preset (!= custom) is chosen it drives the heading/body families + heading
	 * sizes/weight; 'custom' uses heading_font + body + the per-heading overrides.
	 * `google` lists the family NAMES to load from Google Fonts.
	 *
	 * @param array $typography
	 * @return array
	 */
	function unysonplus_typography_config( $typography ) {
		$typography = is_array( $typography ) ? $typography : array();
		$sans = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
		$cfg  = array( 'heading_family' => '', 'body_family' => '', 'per' => array(), 'body' => array(), 'google' => array() );

		// Heading Font → --font-heading; Body → --font-body; each h1–h6 → per-heading
		// tokens. A Typography Preset (preset-loader) simply fills these fields, so
		// there is no separate preset branch here — the values ARE the source of truth.
		$hf = isset( $typography['heading_font']['family'] ) ? trim( (string) $typography['heading_font']['family'] ) : '';
		if ( $hf !== '' ) { $cfg['heading_family'] = "'" . $hf . "', " . $sans; $cfg['google'][] = $hf; }
		$bf = isset( $typography['body']['family'] ) ? trim( (string) $typography['body']['family'] ) : '';
		if ( $bf !== '' ) { $cfg['body_family'] = "'" . $bf . "', " . $sans; $cfg['google'][] = $bf; }
		foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $t ) {
			if ( ! empty( $typography[ $t ] ) ) {
				$cfg['per'][ $t ] = $typography[ $t ];
				if ( ! empty( $typography[ $t ]['family'] ) ) { $cfg['google'][] = $typography[ $t ]['family']; }
			}
		}
		if ( ! empty( $typography['body'] ) ) { $cfg['body'] = $typography['body']; }
		$cfg['google'] = array_values( array_unique( array_filter( $cfg['google'] ) ) );
		return $cfg;
	}
endif;

if ( ! function_exists( 'unysonplus_css_tokens_css' ) ) :
	/**
	 * Build the typography token CSS (minified `:root{}` + mobile media query),
	 * with no <style> wrapper. Returns '' when there are no tokens.
	 *
	 * @return string
	 */
	function unysonplus_css_tokens_css() {
		if ( ! function_exists( 'fw_get_db_settings_option' ) ) { return ''; }
		$typography = fw_get_db_settings_option( 'typography', array() );
		$cfg = unysonplus_typography_config( $typography );

		$tokens = array();
		if ( $cfg['heading_family'] !== '' ) { $tokens['--font-heading'] = $cfg['heading_family']; }
		if ( $cfg['body_family'] !== '' )    { $tokens['--font-body']    = $cfg['body_family']; }

		foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $t ) {
			if ( ! empty( $cfg['per'][ $t ] ) ) {
				$tokens = array_merge( $tokens, unysonplus_typography_to_vars( $cfg['per'][ $t ], $t ) );
			}
		}
		if ( ! empty( $cfg['body'] ) ) {
			$tokens = array_merge( $tokens, unysonplus_typography_to_vars( $cfg['body'], 'body' ) );
		}

		if ( empty( $tokens ) ) { return ''; }

		// Mobile overrides for typography font-size tokens (tiered scaling, shared with plugin)
		$mobile_overrides = array();
		if ( function_exists( 'unysonplus_mobile_font_size_scale' ) ) {
			foreach ( $tokens as $name => $value ) {
				if ( ! preg_match( '/^--(h[1-6]|body)-font-size$/', $name, $tag ) ) { continue; }
				if ( ! preg_match( '/^(\d+(?:\.\d+)?)px$/', $value, $m ) ) { continue; }
				$desktop_px = floatval( $m[1] );
				$mobile_px  = unysonplus_mobile_font_size_scale( $desktop_px, $tag[1] );
				if ( $mobile_px != $desktop_px ) {
					$mobile_overrides[ $name ] = $mobile_px . 'px';
				}
			}
		}

		$css = ':root{';
		foreach ( $tokens as $name => $value ) {
			$css .= $name . ':' . $value . ';';
		}
		$css .= '}';
		if ( ! empty( $mobile_overrides ) ) {
			$css .= '@media (max-width:767.98px){:root{';
			foreach ( $mobile_overrides as $name => $value ) { $css .= $name . ':' . $value . ';'; }
			$css .= '}}';
		}
		return $css;
	}
endif;

if ( ! function_exists( 'unysonplus_emit_css_tokens' ) ) :
	/** Inline emitter — admin only (front end uses the generated file). */
	function unysonplus_emit_css_tokens() {
		$css = unysonplus_css_tokens_css();
		if ( $css === '' ) { return; }
		echo '<style id="unysonplus-tokens">' . $css . '</style>'; // phpcs:ignore — CSS, values sanitized upstream
	}
endif;

if ( ! function_exists( 'unysonplus_typography_to_vars' ) ) :
	function unysonplus_typography_to_vars( array $font, string $prefix ) : array {
		$out = array();

		if ( ! empty( $font['family'] ) ) {
			$out[ "--{$prefix}-font-family" ] = "'" . $font['family'] . "'";
		}
		if ( isset( $font['size'] ) && $font['size'] !== '' ) {
			$out[ "--{$prefix}-font-size" ] = is_numeric( $font['size'] ) ? $font['size'] . 'px' : $font['size'];
		}
		if ( isset( $font['line-height'] ) && $font['line-height'] !== '' ) {
			$out[ "--{$prefix}-line-height" ] = $font['line-height'];
		}
		if ( isset( $font['letter-spacing'] ) && $font['letter-spacing'] !== '' ) {
			$out[ "--{$prefix}-letter-spacing" ] = is_numeric( $font['letter-spacing'] ) ? $font['letter-spacing'] . 'px' : $font['letter-spacing'];
		}
		// Skip pure-black — it's the legacy default of these (formerly inert) controls;
		// emitting it would force black headings over the theme's inherited text colour.
		if ( ! empty( $font['color'] ) && ! in_array( strtolower( (string) $font['color'] ), array( '#000000', '#000' ), true ) ) {
			$out[ "--{$prefix}-color" ] = $font['color'];
		}

		// Weight / style: emit ONLY explicit, non-neutral values. A "regular"/400 weight
		// and "normal" style are treated as no-override, so an untouched heading keeps the
		// theme/UA default (Bootstrap 500) instead of the legacy stored default.
		$v      = ! empty( $font['variation'] ) ? (string) $font['variation'] : '';
		$style  = '';
		$weight = 0;
		if ( $v !== '' ) {
			if ( stripos( $v, 'italic' ) !== false ) { $style = 'italic'; }
			$weight = intval( $v );
		} else {
			if ( ! empty( $font['style'] ) && $font['style'] !== 'normal' ) { $style = (string) $font['style']; }
			if ( ! empty( $font['weight'] ) ) { $weight = intval( $font['weight'] ); }
		}
		if ( $style === 'italic' )              { $out[ "--{$prefix}-font-style" ]  = 'italic'; }
		if ( $weight > 0 && $weight !== 400 )   { $out[ "--{$prefix}-font-weight" ] = $weight; }

		return $out;
	}
endif;
