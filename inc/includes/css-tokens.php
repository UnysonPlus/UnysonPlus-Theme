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

		// Type scale (Phase 2): build the modular scale from the saved settings —
		// base size = the Body size, ratios from the Type Scale controls. When the
		// "Fluid Heading Scale" switch is on, H1–H6 font-size is driven from the
		// scale steps (each already an accessibility-safe clamp); their line-height /
		// spacing / colour still come from the per-heading overrides.
		$scale_opts = array();
		if ( isset( $typography['body'] ) && is_array( $typography['body'] ) && isset( $typography['body']['size'] )
			&& function_exists( 'unysonplus_css_length' ) && function_exists( 'unysonplus_font_size_to_px' ) ) {
			$base_px = unysonplus_font_size_to_px( unysonplus_css_length( $typography['body']['size'] ) );
			if ( $base_px ) { $scale_opts['base_px'] = $base_px; }
		}
		if ( ! empty( $typography['type_scale_ratio'] ) )        { $r  = floatval( $typography['type_scale_ratio'] );        if ( $r  > 1 ) { $scale_opts['ratio']        = $r;  } }
		if ( ! empty( $typography['type_scale_ratio_mobile'] ) ) { $rm = floatval( $typography['type_scale_ratio_mobile'] ); if ( $rm > 1 ) { $scale_opts['ratio_mobile'] = $rm; } }

		$scale = function_exists( 'unysonplus_generate_type_scale' ) ? unysonplus_generate_type_scale( $scale_opts ) : array();

		if ( $scale && ! empty( $typography['type_scale_enable'] ) && 'yes' === $typography['type_scale_enable'] ) {
			$map = apply_filters( 'unysonplus_type_scale_heading_map', array( 'h1' => '6', 'h2' => '5', 'h3' => '4', 'h4' => '3', 'h5' => '2', 'h6' => '1' ) );
			foreach ( $map as $tag => $key ) {
				if ( isset( $scale[ $key ] ) ) { $tokens[ "--{$tag}-font-size" ] = $scale[ $key ]['value']; }
			}
		}

		// Fluid typography: rewrite each remaining px/rem/em heading/body font-size
		// token to an accessibility-safe clamp() that scales smoothly from a mobile
		// floor up to the authored size (replaces the old single-breakpoint step-down
		// that left tablets on desktop sizes). Any value already a clamp() — e.g. a
		// scale-driven heading above — is left as-is; body (min == max) stays fixed.
		if ( function_exists( 'unysonplus_fluid_font_clamp' ) && function_exists( 'unysonplus_mobile_font_size_scale' ) ) {
			foreach ( $tokens as $name => $value ) {
				if ( ! preg_match( '/^--(h[1-6]|body)-font-size$/', $name, $tag ) ) { continue; }
				if ( strpos( $value, 'clamp(' ) !== false ) { continue; }
				$desktop_px = function_exists( 'unysonplus_font_size_to_px' ) ? unysonplus_font_size_to_px( $value ) : null;
				if ( $desktop_px === null || $desktop_px <= 0 ) { continue; }
				if ( 'body' === $tag[1] ) {
					// BODY grows GENTLY on large screens (Google/Utopia model) rather than shrinking: the
					// authored size is the mobile floor (min), growing to size × grow on wide viewports (max).
					// Headings do the opposite (authored = desktop max, shrink on mobile). Grow is filterable;
					// default 1.15 (e.g. 16px → ~18px). rem + vw preferred value keeps browser-zoom accessible.
					$grow      = (float) apply_filters( 'unysonplus_body_fluid_grow', 1.15 );
					$max_px    = ( $grow > 1 ) ? round( $desktop_px * $grow, 2 ) : $desktop_px;
					$mobile_px = $desktop_px;                       // authored size = the mobile floor
					$clamp     = unysonplus_fluid_font_clamp( $max_px, $mobile_px );
				} else {
					$mobile_px = unysonplus_mobile_font_size_scale( $desktop_px, $tag[1] );
					$clamp     = unysonplus_fluid_font_clamp( $desktop_px, $mobile_px );
				}
				if ( $clamp !== null ) {
					$tokens[ $name ] = $clamp;
				} else {
					// No fluid range (small size at/below the mobile floor, min == max): emit it as REM
					// instead of the authored px, so the whole scale is rem-anchored and every size honours
					// the reader's browser font-size preference (the clamp() sizes above are already rem).
					$tokens[ $name ] = rtrim( rtrim( number_format( $desktop_px / 16, 4, '.', '' ), '0' ), '.' ) . 'rem';
				}
			}
		}

		// Type-scale tokens (Phase 1 engine): emit the modular scale as --fs-* and a
		// matching per-step --lh-* line-height, so headings, shortcodes and custom
		// CSS can reference a single coherent, fluid scale. Additive — does not
		// disturb the authored --hN-font-size tokens above.
		foreach ( $scale as $key => $step ) {
			$tokens[ '--fs-' . $key ] = $step['value'];
			if ( isset( $step['line_height'] ) ) {
				$tokens[ '--lh-' . $key ] = $step['line_height'];
			}
		}

		// Semantic role aliases (Phase 4): friendly names elements/shortcodes can use
		// instead of numeric steps. Var references keep a single source of truth — a
		// scale change flows through automatically. Filterable so a child theme can
		// remap a role to a different step.
		$roles = apply_filters( 'unysonplus_type_scale_roles', array(
			'display' => '6', 'lead' => '1', 'body' => '0', 'caption' => 'n1',
		) );
		foreach ( $roles as $role => $key ) {
			if ( isset( $scale[ $key ] ) ) {
				$tokens[ '--fs-' . $role ] = 'var(--fs-' . $key . ')';
				$tokens[ '--lh-' . $role ] = 'var(--lh-' . $key . ')';
			}
		}

		// Font-loading CLS (Phase 3): for a web font with known metric overrides,
		// emit a metric-matched fallback @font-face and splice it into the font
		// stack so the swap from fallback → web font does not shift layout. Modifies
		// --font-body / --font-heading in place; no-op for fonts without metrics.
		$fallback_faces = function_exists( 'unysonplus_font_fallback_css' ) ? unysonplus_font_fallback_css( $tokens ) : '';

		$css = $fallback_faces . ':root{';
		foreach ( $tokens as $name => $value ) {
			$css .= $name . ':' . $value . ';';
		}
		$css .= '}';
		return $css;
	}
endif;

if ( ! function_exists( 'unysonplus_font_fallback_metrics' ) ) :
	/**
	 * Metric-override data for building a zero-CLS fallback @font-face per web font.
	 * The overrides make a system font (the `fallback`) occupy the same space as the
	 * web font, so the swap does not shift layout (Cumulative Layout Shift). Values
	 * are the standard computed overrides for each family. Filterable — add
	 * { 'Family' => [...] } to cover more fonts.
	 *
	 * @return array<string,array<string,string>>
	 */
	function unysonplus_font_fallback_metrics() {
		return apply_filters( 'unysonplus_font_fallback_metrics', array(
			'Open Sans' => array(
				'fallback'          => 'Arial',
				'size-adjust'       => '103.2%',
				'ascent-override'   => '106.88%',
				'descent-override'  => '29.3%',
				'line-gap-override' => '0%',
			),
		) );
	}
endif;

if ( ! function_exists( 'unysonplus_font_fallback_css' ) ) :
	/**
	 * Build fallback @font-face rules for any registered web font in use, and splice
	 * the fallback family into the matching --font-* stack (right after the primary,
	 * before the system stack). Returns the @font-face CSS (or ''). $tokens is passed
	 * by reference and modified in place.
	 *
	 * @param array $tokens The CSS-variable map being assembled.
	 * @return string
	 */
	function unysonplus_font_fallback_css( array &$tokens ) {
		$metrics = unysonplus_font_fallback_metrics();
		if ( empty( $metrics ) ) { return ''; }

		$faces = '';
		$emitted = array();
		foreach ( array( '--font-body', '--font-heading' ) as $token ) {
			if ( empty( $tokens[ $token ] ) ) { continue; }
			if ( ! preg_match( "/^'([^']+)'/", $tokens[ $token ], $m ) ) { continue; }
			$family = $m[1];
			if ( ! isset( $metrics[ $family ] ) ) { continue; }

			$fb_name = $family . ' Fallback';
			if ( ! isset( $emitted[ $fb_name ] ) ) {
				$md     = $metrics[ $family ];
				$faces .= "@font-face{font-family:'" . $fb_name . "';src:local('" . $md['fallback'] . "');"
					. 'size-adjust:' . $md['size-adjust'] . ';ascent-override:' . $md['ascent-override'] . ';'
					. 'descent-override:' . $md['descent-override'] . ';line-gap-override:' . $md['line-gap-override'] . ';}';
				$emitted[ $fb_name ] = true;
			}
			// Insert the fallback family right after the primary quoted family.
			$tokens[ $token ] = preg_replace( "/^('[^']+',)/", "$1 '" . $fb_name . "',", $tokens[ $token ], 1 );
		}
		return $faces;
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
			// Size is now a unit-input ({value,unit}); unysonplus_css_length resolves that,
			// a JSON string, a legacy bare number (+px) and an already-typed length string.
			$size_css = function_exists( 'unysonplus_css_length' )
				? unysonplus_css_length( $font['size'] )
				: ( is_numeric( $font['size'] ) ? $font['size'] . 'px' : ( is_array( $font['size'] ) ? '' : (string) $font['size'] ) );
			if ( $size_css !== '' ) {
				$out[ "--{$prefix}-font-size" ] = $size_css;
			}
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
		// text-transform: a heading font uppercased purely by CSS (a display face like Syncopate) reproduces
		// its casing through this token, consumed by the hN rules that read var(--{prefix}-text-transform).
		if ( ! empty( $font['text-transform'] ) && in_array( strtolower( (string) $font['text-transform'] ), array( 'uppercase', 'lowercase', 'capitalize', 'none' ), true ) ) {
			$out[ "--{$prefix}-text-transform" ] = strtolower( (string) $font['text-transform'] );
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
