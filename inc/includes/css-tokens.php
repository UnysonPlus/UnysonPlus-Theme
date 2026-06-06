<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Theme bridge — emits <style id="unysonplus-tokens">…</style> in <head> with
 * h1-h6 + body typography tokens.
 *
 * Font Size + Color preset tokens are emitted by the plugin in a separate
 * <style id="unysonplus-presets"> block (see unysonplus/framework/includes/css-tokens.php).
 */

if ( ! function_exists( 'unysonplus_emit_css_tokens' ) ) :
	function unysonplus_emit_css_tokens() {
		$typography = fw_get_db_settings_option( 'typography', array() );
		if ( empty( $typography ) ) { return; }

		$tokens  = array();
		$targets = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'body' );

		foreach ( $targets as $t ) {
			if ( empty( $typography[ $t ] ) ) { continue; }
			$tokens = array_merge( $tokens, unysonplus_typography_to_vars( $typography[ $t ], $t ) );
		}

		if ( empty( $tokens ) ) { return; }

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

		$pretty = ( defined( 'WP_DEBUG' ) && WP_DEBUG );

		if ( $pretty ) {
			echo "\n<style id=\"unysonplus-tokens\">\n:root {\n";
			foreach ( $tokens as $name => $value ) {
				echo "\t{$name}: {$value};\n";
			}
			echo "}\n";
			if ( ! empty( $mobile_overrides ) ) {
				echo "@media (max-width: 767.98px) {\n\t:root {\n";
				foreach ( $mobile_overrides as $name => $value ) { echo "\t\t{$name}: {$value};\n"; }
				echo "\t}\n}\n";
			}
			echo "</style>\n";
		} else {
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
			echo '<style id="unysonplus-tokens">' . $css . '</style>';
		}
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
		if ( ! empty( $font['color'] ) ) {
			$out[ "--{$prefix}-color" ] = $font['color'];
		}

		if ( ! empty( $font['variation'] ) ) {
			$v = $font['variation'];
			$out[ "--{$prefix}-font-style" ] = ( stripos( $v, 'italic' ) !== false ) ? 'italic' : 'normal';
			$weight = intval( $v );
			$out[ "--{$prefix}-font-weight" ] = ( $weight === 0 ) ? 400 : $weight;
		} elseif ( isset( $font['style'] ) || isset( $font['weight'] ) ) {
			if ( ! empty( $font['style'] ) )  { $out[ "--{$prefix}-font-style" ]  = $font['style']; }
			if ( ! empty( $font['weight'] ) ) { $out[ "--{$prefix}-font-weight" ] = $font['weight']; }
		}

		return $out;
	}
endif;
