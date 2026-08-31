<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Theme bridge — publishes the UnysonPlus design system to WordPress as theme.json data.
 *
 * WHY: the static theme.json declares its palette as `var(--color-primary)`. WordPress
 * cannot resolve a CSS custom property to a value, so Global Styles, the core block
 * colour pickers and contrast tooling all see an opaque token instead of a colour, and
 * the real design system (Colour Presets, the font-size scale, Typography presets, the
 * spacing scale) is invisible to the block editor entirely.
 *
 * WHAT: this filter regenerates that data at runtime from the SAME sources the CSS
 * pipeline reads, so the two cannot drift:
 *
 *   colours     unysonplus_collect_theme_vars()        (semantic roles: primary, accent, …)
 *               unysonplus_color_preset_slug_map()     (named Colour Presets)
 *   font sizes  unysonplus_get_font_size_presets()     (+ the mobile scaler for fluid)
 *   families    unysonplus_typography_config()
 *   spacing     unysonplus_get_spacing_scale()
 *
 * NOTHING here changes front-end rendering. The CSS custom properties emitted by
 * theme-vars.php / css-tokens.php remain exactly as they are and stay what the theme's
 * own CSS consumes; this is a PARALLEL declaration of the same truth for WordPress's
 * benefit. Purely additive.
 *
 * Runs on `wp_theme_json_data_theme` (WP 6.1+), so no file is written and child themes
 * still layer on top normally.
 */

if ( ! function_exists( 'unysonplus_theme_json_is_color' ) ) :
	/**
	 * True when a value is a literal colour we can hand to WordPress. Rejects anything
	 * still expressed as a CSS custom property — passing `var(…)` through is precisely
	 * the bug this bridge exists to fix.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	function unysonplus_theme_json_is_color( $value ) {
		if ( ! is_string( $value ) ) { return false; }
		$value = trim( $value );
		if ( $value === '' || $value === 'transparent' ) { return false; }
		if ( stripos( $value, 'var(' ) !== false ) { return false; }
		return (bool) preg_match( '/^(#[0-9a-fA-F]{3,8}$|rgba?\(|hsla?\(|[a-zA-Z]+$)/', $value );
	}
endif;

if ( ! function_exists( 'unysonplus_theme_json_merge_presets' ) ) :
	/**
	 * Overlay generated presets onto whatever the theme.json file (or a child theme)
	 * already declares, matching on `slug`.
	 *
	 * Replacing a preset array wholesale silently DROPS any entry we do not happen to
	 * regenerate — e.g. the `heading` font family on a site that leaves the heading font
	 * unset, whose `var(--font-heading)` value resolves perfectly well at CSS runtime.
	 * Merging guarantees this bridge is purely additive: it can upgrade an entry or add
	 * one, never remove one.
	 *
	 * @param array $existing Presets already present, in file order.
	 * @param array $generated Presets built from Theme Settings.
	 * @return array
	 */
	function unysonplus_theme_json_merge_presets( $existing, $generated ) {
		if ( ! is_array( $existing ) )  { $existing  = array(); }
		if ( ! is_array( $generated ) ) { $generated = array(); }
		if ( empty( $existing ) )       { return $generated; }

		$by_slug = array();
		foreach ( $generated as $entry ) {
			if ( isset( $entry['slug'] ) ) { $by_slug[ $entry['slug'] ] = $entry; }
		}

		$out = array();
		foreach ( $existing as $entry ) {
			$slug = isset( $entry['slug'] ) ? $entry['slug'] : null;
			if ( $slug !== null && isset( $by_slug[ $slug ] ) ) {
				$out[] = $by_slug[ $slug ];   // upgraded (real value replaces var())
				unset( $by_slug[ $slug ] );
			} else {
				$out[] = $entry;              // preserved
			}
		}
		foreach ( $by_slug as $entry ) { $out[] = $entry; } // genuinely new

		return $out;
	}
endif;

if ( ! function_exists( 'unysonplus_theme_json_palette' ) ) :
	/**
	 * Colour palette: the semantic role colours first (they are the brand, and the most
	 * useful entries in the editor), then the named Colour Presets.
	 *
	 * Preset slugs come from unysonplus_color_preset_slug_map(), which derives them the
	 * same way css-tokens.php emits `--color-{slug}` — so a theme.json slug and its CSS
	 * variable always agree.
	 *
	 * @return array
	 */
	function unysonplus_theme_json_palette() {
		$palette = array();
		$seen    = array();

		// --- Semantic roles, resolved to real values -------------------------------
		$roles = array(
			'primary'    => array( '--color-primary', __( 'Primary', 'unysonplus' ) ),
			'accent'     => array( '--color-accent',  __( 'Accent', 'unysonplus' ) ),
			'text'       => array( '--color-text',    __( 'Text', 'unysonplus' ) ),
			'muted'      => array( '--color-muted',   __( 'Muted', 'unysonplus' ) ),
			'background' => array( '--color-bg',      __( 'Background', 'unysonplus' ) ),
		);

		$vars = function_exists( 'unysonplus_collect_theme_vars' ) ? unysonplus_collect_theme_vars() : array();
		if ( is_array( $vars ) ) {
			foreach ( $roles as $slug => $role ) {
				list( $var, $label ) = $role;
				if ( isset( $vars[ $var ] ) && unysonplus_theme_json_is_color( $vars[ $var ] ) ) {
					$palette[]     = array( 'slug' => $slug, 'name' => $label, 'color' => trim( $vars[ $var ] ) );
					$seen[ $slug ] = true;
				}
			}
		}

		// --- Named Colour Presets ---------------------------------------------------
		if ( function_exists( 'unysonplus_color_preset_slug_map' ) ) {
			$names = array();
			if ( function_exists( 'unysonplus_get_color_presets' ) ) {
				foreach ( unysonplus_get_color_presets() as $entry ) {
					// Same skip rule as unysonplus_color_preset_slug_map(), so the two stay aligned.
					if ( empty( $entry['name'] ) || empty( $entry['color'] ) ) { continue; }
					$slug = trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( $entry['name'] ) ), '-' );
					if ( $slug !== '' && ! isset( $names[ $slug ] ) ) { $names[ $slug ] = $entry['name']; }
				}
			}

			foreach ( unysonplus_color_preset_slug_map() as $slug => $hex ) {
				if ( isset( $seen[ $slug ] ) || ! unysonplus_theme_json_is_color( $hex ) ) { continue; }
				$palette[]     = array(
					'slug'  => $slug,
					'name'  => isset( $names[ $slug ] ) ? $names[ $slug ] : ucwords( str_replace( '-', ' ', $slug ) ),
					'color' => trim( $hex ),
				);
				$seen[ $slug ] = true;
			}
		}

		/** Filters the colour palette published to theme.json / the block editor. */
		return apply_filters( 'unysonplus_theme_json_palette', $palette );
	}
endif;

if ( ! function_exists( 'unysonplus_theme_json_font_sizes' ) ) :
	/**
	 * Font-size presets → core's font-size picker. Slug is the preset's utility class
	 * (`display-1`, `lead`, …) so an editor selection and the CSS utility agree.
	 *
	 * Fluid min is taken from unysonplus_mobile_font_size_scale(), the same tiered
	 * reducer the CSS pipeline uses, so editor and front end shrink identically.
	 *
	 * @return array
	 */
	function unysonplus_theme_json_font_sizes() {
		if ( ! function_exists( 'unysonplus_get_font_size_presets' ) ) { return array(); }

		$sizes = array();
		$seen  = array();
		foreach ( unysonplus_get_font_size_presets() as $preset ) {
			if ( empty( $preset['name'] ) ) { continue; }

			// Size is a unit-input { value, unit }; tolerate a bare legacy number.
			$raw = isset( $preset['size'] ) ? $preset['size'] : '';
			if ( is_array( $raw ) ) {
				$value = isset( $raw['value'] ) ? trim( (string) $raw['value'] ) : '';
				$unit  = isset( $raw['unit'] ) && $raw['unit'] !== '' ? (string) $raw['unit'] : 'px';
			} else {
				$value = trim( (string) $raw );
				$unit  = 'px';
			}
			if ( $value === '' || ! is_numeric( $value ) ) { continue; }

			$slug = ! empty( $preset['class'] )
				? sanitize_title( $preset['class'] )
				: sanitize_title( $preset['name'] );
			if ( $slug === '' || isset( $seen[ $slug ] ) ) { continue; }

			$entry = array(
				'slug' => $slug,
				'name' => $preset['name'],
				'size' => $value . $unit,
			);

			// Fluid only for px, and only when the mobile scaler actually reduces it.
			if ( $unit === 'px' && function_exists( 'unysonplus_mobile_font_size_scale' ) ) {
				$min = unysonplus_mobile_font_size_scale( $value, $slug );
				if ( $min > 0 && $min < floatval( $value ) ) {
					$entry['fluid'] = array( 'min' => $min . 'px', 'max' => $value . 'px' );
				}
			}

			$sizes[]       = $entry;
			$seen[ $slug ] = true;
		}

		/** Filters the font-size presets published to theme.json / the block editor. */
		return apply_filters( 'unysonplus_theme_json_font_sizes', $sizes );
	}
endif;

if ( ! function_exists( 'unysonplus_theme_json_font_families' ) ) :
	/**
	 * Heading + body families from the resolved Typography config. Slugs match the
	 * existing static theme.json (`heading`, `body`) so nothing downstream shifts.
	 *
	 * @return array
	 */
	function unysonplus_theme_json_font_families() {
		if ( ! function_exists( 'unysonplus_typography_config' ) || ! function_exists( 'fw_get_db_settings_option' ) ) {
			return array();
		}

		$cfg = unysonplus_typography_config( fw_get_db_settings_option( 'typography', array() ) );
		if ( ! is_array( $cfg ) ) { return array(); }

		$families = array();
		$map      = array(
			'body'    => array( isset( $cfg['body_family'] ) ? $cfg['body_family'] : '',       __( 'Body', 'unysonplus' ) ),
			'heading' => array( isset( $cfg['heading_family'] ) ? $cfg['heading_family'] : '', __( 'Heading', 'unysonplus' ) ),
		);

		foreach ( $map as $slug => $pair ) {
			list( $stack, $label ) = $pair;
			$stack = trim( (string) $stack );
			if ( $stack === '' || stripos( $stack, 'var(' ) !== false ) { continue; }
			$families[] = array( 'slug' => $slug, 'name' => $label, 'fontFamily' => $stack );
		}

		/** Filters the font families published to theme.json / the block editor. */
		return apply_filters( 'unysonplus_theme_json_font_families', $families );
	}
endif;

if ( ! function_exists( 'unysonplus_theme_json_spacing_sizes' ) ) :
	/**
	 * Spacing scale → core's spacing controls (padding / margin / blockGap presets).
	 *
	 * @return array
	 */
	function unysonplus_theme_json_spacing_sizes() {
		if ( ! function_exists( 'unysonplus_get_spacing_scale' ) ) { return array(); }

		$sizes = array();
		$seen  = array();
		foreach ( unysonplus_get_spacing_scale() as $entry ) {
			if ( ! is_array( $entry ) || ! isset( $entry['name'] ) || empty( $entry['size'] ) ) { continue; }

			$name = (string) $entry['name'];
			$size = trim( (string) $entry['size'] );
			if ( $size === '' || stripos( $size, 'var(' ) !== false ) { continue; }

			// Names may be plain steps ('0'..'5') or arbitrary tokens ('[40px]').
			$slug = sanitize_title( $name );
			if ( $slug === '' || isset( $seen[ $slug ] ) ) { continue; }

			$sizes[]       = array( 'slug' => $slug, 'name' => $name, 'size' => $size );
			$seen[ $slug ] = true;
		}

		/** Filters the spacing sizes published to theme.json / the block editor. */
		return apply_filters( 'unysonplus_theme_json_spacing_sizes', $sizes );
	}
endif;

if ( ! function_exists( 'unysonplus_theme_json_bridge' ) ) :
	/**
	 * Merge the generated design system into the theme's theme.json data.
	 *
	 * @param WP_Theme_JSON_Data $theme_json
	 * @return WP_Theme_JSON_Data
	 */
	function unysonplus_theme_json_bridge( $theme_json ) {
		if ( ! is_object( $theme_json ) || ! method_exists( $theme_json, 'update_with' ) ) {
			return $theme_json;
		}

		// theme.json is resolved more than once per request in some contexts; the
		// preset getters each hit options, so build the payload only once.
		static $data = null;

		if ( $data === null ) {
			// Everything is merged onto what the theme.json file already declares, so a
			// preset we do not regenerate is preserved rather than dropped.
			$current  = method_exists( $theme_json, 'get_data' ) ? $theme_json->get_data() : array();
			$existing = isset( $current['settings'] ) && is_array( $current['settings'] ) ? $current['settings'] : array();
			// NOTE: on this filter the incoming presets are keyed by ORIGIN
			// (`fontFamilies => [ 'theme' => [ … ] ]`), while update_with() expects a flat
			// list. Unwrap the theme origin — that is the layer a theme filter owns.
			$get = function ( $path ) use ( $existing ) {
				$node = $existing;
				foreach ( $path as $key ) {
					if ( ! is_array( $node ) || ! isset( $node[ $key ] ) ) { return array(); }
					$node = $node[ $key ];
				}
				if ( ! is_array( $node ) ) { return array(); }
				if ( isset( $node['theme'] ) && is_array( $node['theme'] ) ) { return $node['theme']; }
				return array_key_exists( 0, $node ) ? $node : array();
			};

			$settings = array();

			$palette = unysonplus_theme_json_merge_presets( $get( array( 'color', 'palette' ) ), unysonplus_theme_json_palette() );
			if ( ! empty( $palette ) ) {
				$settings['color']['palette'] = $palette;
			}

			$font_sizes = unysonplus_theme_json_merge_presets( $get( array( 'typography', 'fontSizes' ) ), unysonplus_theme_json_font_sizes() );
			if ( ! empty( $font_sizes ) ) {
				$settings['typography']['fontSizes'] = $font_sizes;
			}

			$families = unysonplus_theme_json_merge_presets( $get( array( 'typography', 'fontFamilies' ) ), unysonplus_theme_json_font_families() );
			if ( ! empty( $families ) ) {
				$settings['typography']['fontFamilies'] = $families;
			}

			$spacing = unysonplus_theme_json_merge_presets( $get( array( 'spacing', 'spacingSizes' ) ), unysonplus_theme_json_spacing_sizes() );
			if ( ! empty( $spacing ) ) {
				$settings['spacing']['spacingSizes'] = $spacing;
			}

			// Track the theme's own declared schema version. Forcing a different version
			// here changes how WordPress treats the DEFAULT presets (it is what silently
			// dropped core's default font families in testing), so it must not be raised
			// as a side effect of this bridge.
			$version = isset( $current['version'] ) ? (int) $current['version'] : 2;

			$data = empty( $settings )
				? array()
				: array( 'version' => $version, 'settings' => $settings );

			/** Filters the whole theme.json payload before it is merged. */
			$data = apply_filters( 'unysonplus_theme_json_data', $data );
		}

		return empty( $data ) ? $theme_json : $theme_json->update_with( $data );
	}
endif;

add_filter( 'wp_theme_json_data_theme', 'unysonplus_theme_json_bridge' );
