<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Theme bridge — publishes the Section Styles preset library as BLOCK STYLE VARIATIONS.
 *
 * Section Styles (Theme Settings → Components → Section Styles) are the framework's named
 * band skins — Alt / Light / Dark and any the user adds. On the front end they render as
 * `.section--{slug}` rules generated into the presets CSS, and the Section shortcode picks
 * one via its Section Style option.
 *
 * None of that was reachable from the block editor: a core Group block had no way to wear a
 * site skin. This registers each preset as a block style variation, so the same named skins
 * appear in the Styles panel of a Group block and are applied by WordPress itself.
 *
 * Uses register_block_style()'s `style_data` (WP 6.6+) — a theme.json-like style object, so
 * WordPress generates the CSS and the variation is editable through Global Styles, rather
 * than the theme shipping a second hardcoded stylesheet. Values resolve through
 * unysonplus_resolve_preset_color(), the SAME helper the CSS pipeline uses, so a variation
 * and its `.section--{slug}` counterpart cannot drift apart.
 *
 * Registered on `init` because block styles must exist before the editor asks for them.
 */

if ( ! function_exists( 'unysonplus_section_style_to_block_style_data' ) ) :
	/**
	 * Turn one Section Style preset into a theme.json-like style object.
	 *
	 * Returns an empty array when the preset carries nothing renderable, so the caller can
	 * skip registering a variation that would do nothing.
	 *
	 * @param array $sp A Section Style preset (see unysonplus_default_section_style_presets).
	 * @return array
	 */
	function unysonplus_section_style_to_block_style_data( $sp ) {
		if ( ! is_array( $sp ) ) { return array(); }

		$color = function ( $v ) {
			return function_exists( 'unysonplus_resolve_preset_color' ) ? unysonplus_resolve_preset_color( $v ) : '';
		};
		// A bare number means px; anything already carrying a unit passes through.
		$len = function ( $v ) {
			if ( is_array( $v ) ) {
				$n = isset( $v['value'] ) ? trim( (string) $v['value'] ) : '';
				if ( $n === '' ) { return ''; }
				$u = ( isset( $v['unit'] ) && $v['unit'] !== '' ) ? (string) $v['unit'] : 'px';
				return $n . $u;
			}
			$v = trim( (string) $v );
			if ( $v === '' ) { return ''; }
			return is_numeric( $v ) ? $v . 'px' : $v;
		};

		$data = array();

		// --- Background (a background-pro value: background.color.value) ---------------
		$bg = '';
		if ( isset( $sp['background']['color']['value'] ) ) {
			$bg = $color( $sp['background']['color']['value'] );
		}
		if ( $bg !== '' ) { $data['color']['background'] = $bg; }

		// --- Text, and the nested elements the skin also recolours ---------------------
		$text = $color( isset( $sp['text_color'] ) ? $sp['text_color'] : '' );
		if ( $text !== '' ) { $data['color']['text'] = $text; }

		$heading = $color( isset( $sp['heading_color'] ) ? $sp['heading_color'] : '' );
		if ( $heading !== '' ) { $data['elements']['heading']['color']['text'] = $heading; }

		$link = $color( isset( $sp['link_color'] ) ? $sp['link_color'] : '' );
		if ( $link !== '' ) {
			$data['elements']['link']['color']['text'] = $link;
			// Without an explicit :hover WordPress leaves the theme's link hover in place,
			// which on a dark skin can land back on an unreadable colour.
			$data['elements']['link'][':hover']['color']['text'] = $link;
		}

		// --- Border. Only emitted when a style is chosen, matching the CSS pipeline
		// (None = no border). Border SIDES are deliberately not mapped: theme.json's border
		// shorthand is all-round, and a partial border is better left to `.section--{slug}`.
		$bd     = ( isset( $sp['border'] ) && is_array( $sp['border'] ) ) ? $sp['border'] : array();
		$bstyle = isset( $bd['style'] ) ? (string) $bd['style'] : '';
		if ( $bstyle !== '' ) {
			$data['border']['style'] = $bstyle;
			$bw = $len( isset( $bd['width'] ) ? $bd['width'] : '' );
			$data['border']['width'] = ( $bw !== '' ? $bw : '1px' );
			$bc = $color( isset( $bd['color'] ) ? $bd['color'] : '' );
			if ( $bc !== '' ) { $data['border']['color'] = $bc; }
		}
		$radius = $len( isset( $sp['border_radius'] ) ? $sp['border_radius'] : '' );
		if ( $radius !== '' ) { $data['border']['radius'] = $radius; }

		// --- Padding ('all' wins over the per-side values, as the spacing option does) ---
		$pad = ( isset( $sp['padding']['padding'] ) && is_array( $sp['padding']['padding'] ) )
			? $sp['padding']['padding']
			: array();
		if ( $pad ) {
			$all = $len( isset( $pad['all'] ) ? $pad['all'] : '' );
			if ( $all !== '' ) {
				$data['spacing']['padding'] = array( 'top' => $all, 'right' => $all, 'bottom' => $all, 'left' => $all );
			} else {
				foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
					$v = $len( isset( $pad[ $side ] ) ? $pad[ $side ] : '' );
					if ( $v !== '' ) { $data['spacing']['padding'][ $side ] = $v; }
				}
			}
		}

		return $data;
	}
endif;

if ( ! function_exists( 'unysonplus_register_section_block_styles' ) ) :
	/** Register every Section Style preset as a block style variation. */
	function unysonplus_register_section_block_styles() {
		if ( ! function_exists( 'register_block_style' ) ) { return; }
		if ( ! function_exists( 'unysonplus_get_section_style_presets' ) ) { return; }

		$presets = unysonplus_get_section_style_presets();
		if ( ! is_array( $presets ) || ! $presets ) { return; }

		$slug_map = function_exists( 'unysonplus_section_style_preset_slug_map' )
			? unysonplus_section_style_preset_slug_map()
			: array();

		/**
		 * Blocks that can wear a Section Style. Group is the block-editor equivalent of a
		 * section band; Columns and Cover are the other natural section-level containers.
		 */
		$blocks = apply_filters( 'unysonplus_section_style_block_types', array( 'core/group', 'core/columns' ) );

		foreach ( $presets as $sp ) {
			if ( ! is_array( $sp ) || empty( $sp['id'] ) ) { continue; }

			$id   = preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $sp['id'] );
			$slug = isset( $slug_map[ $id ] ) ? $slug_map[ $id ] : strtolower( $id );
			if ( $slug === '' ) { continue; }

			$style_data = unysonplus_section_style_to_block_style_data( $sp );
			if ( ! $style_data ) { continue; } // nothing renderable — don't offer an inert style

			$label = isset( $sp['style_name'] ) && $sp['style_name'] !== ''
				? (string) $sp['style_name']
				: ucfirst( str_replace( '-', ' ', $slug ) );

			register_block_style(
				$blocks,
				array(
					// `upw-` prefixed so a preset named e.g. "Dark" can never collide with a
					// core or third-party variation of the same name.
					'name'       => 'upw-' . $slug,
					'label'      => $label,
					'style_data' => $style_data,
				)
			);
		}
	}
endif;

add_action( 'init', 'unysonplus_register_section_block_styles' );
