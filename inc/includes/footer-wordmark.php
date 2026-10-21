<?php if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }
/**
 * Footer → Layout → Watermark Text: an oversized wordmark (usually the site name, or the logo) set edge
 * to edge across the footer — the "giant footer text" style. Purely decorative: aria-hidden, never a
 * heading, so screen readers and search engines do not read the site name a second time.
 *
 * Placement (where template-parts/footer-builder.php prints it):
 *   before            above the footer content
 *   behind-body       behind the Pre / Main / Post-Footer (inside .footer__body)
 *   before-copyright  between the footer content and the copyright bar
 *   after             at the very bottom, after the copyright (the classic cropped band)
 *   behind-all        behind the whole footer, copyright included
 *
 * Sizing: CSS guesses the size from container units and the character count (no layout jump), then a
 * tiny script fits the real glyphs to the box once the web font has loaded, again on resize.
 *
 * Extension seam: `unysonplus_footer_wordmark_effects` adds choices to the Effect select (the theme
 * offers None and Reveal); the chosen effect is printed as `footer-wordmark--fx-{id}` + `data-fwm-fx`,
 * and `unysonplus_footer_wordmark_rendered` fires after the markup so an extension can load its assets.
 */

if ( ! function_exists( 'unysonplus_footer_wordmark_effect_choices' ) ) :
	/** Effect choices: the theme's own plus any an extension (the Animation Engine) adds. */
	function unysonplus_footer_wordmark_effect_choices() {
		$base = array(
			'none'   => __( 'None', 'unysonplus' ),
			'reveal' => __( 'Reveal on scroll (rises into place)', 'unysonplus' ),
		);
		$more = apply_filters( 'unysonplus_footer_wordmark_effects', array() );
		return is_array( $more ) ? array_merge( $base, $more ) : $base;
	}
endif;

if ( ! function_exists( 'unysonplus_footer_wordmark_effect_options' ) ) :
	/**
	 * Per-effect settings for the Effect picker: array( effect id => array( option id => option ) ).
	 * Each choice of the picker reveals its own group. The theme's own effects need none.
	 */
	function unysonplus_footer_wordmark_effect_options() {
		$opts = apply_filters( 'unysonplus_footer_wordmark_effect_options', array() );
		return is_array( $opts ) ? $opts : array();
	}
endif;

if ( ! function_exists( 'unysonplus_footer_wordmark_cfg' ) ) :
	/**
	 * The saved Watermark Text settings, normalised, or null when it is off.
	 *
	 * @return array|null
	 */
	function unysonplus_footer_wordmark_cfg() {
		static $cfg = false;
		if ( false !== $cfg ) {
			return $cfg;
		}
		$cfg = null;
		if ( ! function_exists( 'fw_get_db_settings_option' ) ) {
			return $cfg;
		}
		$opt = fw_get_db_settings_option( 'footer_wordmark' );
		if ( ! is_array( $opt ) || empty( $opt['enabled'] ) || 'yes' !== $opt['enabled'] ) {
			return $cfg;
		}
		$v    = ( isset( $opt['yes'] ) && is_array( $opt['yes'] ) ) ? $opt['yes'] : array();
		$pick = function ( $key, $allowed, $default ) use ( $v ) {
			return ( isset( $v[ $key ] ) && in_array( (string) $v[ $key ], $allowed, true ) ) ? (string) $v[ $key ] : $default;
		};
		$num  = function ( $key, $min, $max, $default ) use ( $v ) {
			return isset( $v[ $key ] ) && is_numeric( $v[ $key ] ) ? max( $min, min( $max, (float) $v[ $key ] ) ) : $default;
		};
		$col  = function ( $key ) use ( $v ) {
			return function_exists( 'unysonplus_preset_color_to_css' ) && isset( $v[ $key ] ) ? unysonplus_preset_color_to_css( $v[ $key ] ) : '';
		};

		// Placement — the two 2.6.53 values map onto the new ones.
		$placement = isset( $v['footer_wordmark_placement'] ) ? (string) $v['footer_wordmark_placement'] : 'after';
		$placement = array( 'below' => 'after', 'behind' => 'behind-all' )[ $placement ] ?? $placement;
		if ( ! in_array( $placement, array( 'before', 'behind-body', 'before-copyright', 'after', 'behind-all' ), true ) ) {
			$placement = 'after';
		}

		// Source: a multi-picker { source: text|image, text: { footer_wordmark_text }, image: { footer_wordmark_image } }.
		// The flat 2.6.55 keys are still read when present.
		$src_raw = isset( $v['footer_wordmark_source'] ) ? $v['footer_wordmark_source'] : 'text';
		$source  = is_array( $src_raw ) ? ( isset( $src_raw['source'] ) ? (string) $src_raw['source'] : 'text' ) : (string) $src_raw;
		$source  = in_array( $source, array( 'text', 'image' ), true ) ? $source : 'text';
		if ( is_array( $src_raw ) ) {
			if ( isset( $src_raw['text']['footer_wordmark_text'] ) ) { $v['footer_wordmark_text'] = $src_raw['text']['footer_wordmark_text']; }
			if ( isset( $src_raw['image']['footer_wordmark_image'] ) ) { $v['footer_wordmark_image'] = $src_raw['image']['footer_wordmark_image']; }
		}
		$image  = '';
		if ( 'image' === $source && ! empty( $v['footer_wordmark_image'] ) ) {
			$img   = $v['footer_wordmark_image'];
			$image = function_exists( 'unysonplus_upload_option_src' ) ? (string) unysonplus_upload_option_src( $img ) : ( is_array( $img ) && ! empty( $img['url'] ) ? $img['url'] : '' );
		}
		if ( 'image' === $source && '' === $image ) {
			$source = 'text';
		}
		$text = trim( wp_strip_all_tags( isset( $v['footer_wordmark_text'] ) ? (string) $v['footer_wordmark_text'] : '' ) );
		if ( '' === $text ) {
			$text = get_bloginfo( 'name' );
		}
		// The Site Title is stored HTML-encoded ("Fluff &amp; Bubbles"); esc_html() re-encodes on output.
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		if ( 'text' === $source && '' === trim( $text ) ) {
			return $cfg;
		}

		// Effect: a multi-picker { effect: id, <id>: { …its settings } } (a plain id string is accepted too).
		$effects  = unysonplus_footer_wordmark_effect_choices();
		$fx_raw   = isset( $v['footer_wordmark_effect'] ) ? $v['footer_wordmark_effect'] : 'none';
		$effect   = is_array( $fx_raw ) ? ( isset( $fx_raw['effect'] ) ? (string) $fx_raw['effect'] : 'none' ) : (string) $fx_raw;
		$effect   = isset( $effects[ $effect ] ) ? $effect : 'none';
		$fx_conf  = ( is_array( $fx_raw ) && isset( $fx_raw[ $effect ] ) && is_array( $fx_raw[ $effect ] ) ) ? $fx_raw[ $effect ] : array();

		$cfg = array(
			'source'     => $source,
			'image'      => $image,
			'text'       => $text,
			'style'      => $pick( 'footer_wordmark_style', array( 'fill', 'outline', 'fade', 'gradient', 'outline-fade' ), 'fade' ),
			'placement'  => $placement,
			'valign'     => $pick( 'footer_wordmark_valign', array( 'top', 'center', 'bottom' ), 'bottom' ),
			'width'      => $pick( 'footer_wordmark_width', array( 'full', 'container', 'custom' ), 'full' ),
			'max_width'  => $num( 'footer_wordmark_max_width', 200, 4000, 1200 ),
			'font'       => $pick( 'footer_wordmark_font', array( 'heading', 'body' ), 'heading' ),
			'weight'     => $pick( 'footer_wordmark_weight', array( '300', '400', '500', '600', '700', '800', '900' ), '800' ),
			'uppercase'  => isset( $v['footer_wordmark_uppercase'] ) ? 'yes' === $v['footer_wordmark_uppercase'] : true,
			'italic'     => isset( $v['footer_wordmark_italic'] ) && 'yes' === $v['footer_wordmark_italic'],
			'tracking'   => $num( 'footer_wordmark_tracking', -10, 30, -2 ) / 100,
			'opacity'    => $num( 'footer_wordmark_opacity', 2, 100, 10 ) / 100,
			'crop'       => $num( 'footer_wordmark_crop', 0, 60, 22 ) / 100,
			'stroke'     => $num( 'footer_wordmark_stroke', 0.5, 6, 1 ),
			'color'      => $col( 'footer_wordmark_color' ),
			'color2'     => $col( 'footer_wordmark_color_2' ),
			'angle'      => (int) $num( 'footer_wordmark_angle', 0, 360, 90 ),
			'hide'       => isset( $v['footer_wordmark_hide'] ) && is_array( $v['footer_wordmark_hide'] ) ? array_values( array_intersect( array_keys( array_filter( $v['footer_wordmark_hide'] ) ), array( 'hide-xs', 'hide-sm', 'hide-md' ) ) ) : array(),
			'effect'     => $effect,
			'effect_settings' => $fx_conf,
			'link'       => isset( $v['footer_wordmark_link'] ) ? esc_url_raw( trim( (string) $v['footer_wordmark_link'] ) ) : '',
		);
		return $cfg;
	}
endif;

if ( ! function_exists( 'unysonplus_footer_wordmark_html' ) ) :
	/**
	 * The wordmark markup for one placement, or '' when it is off or placed elsewhere.
	 *
	 * @param string $at The slot asking: before | behind-body | before-copyright | after | behind-all.
	 * @return string
	 */
	function unysonplus_footer_wordmark_html( $at ) {
		$c = unysonplus_footer_wordmark_cfg();
		if ( ! $c || $c['placement'] !== $at ) {
			return '';
		}
		$vars = '--fwm-opacity:' . $c['opacity'] . ';--fwm-crop:' . $c['crop'] . ';--fwm-weight:' . $c['weight']
			. ';--fwm-tracking:' . $c['tracking'] . 'em;--fwm-stroke:' . $c['stroke'] . 'px;--fwm-angle:' . $c['angle'] . 'deg;';
		if ( '' !== $c['color'] ) {
			$vars .= '--fwm-color:' . $c['color'] . ';';
		}
		if ( '' !== $c['color2'] ) {
			$vars .= '--fwm-color-2:' . $c['color2'] . ';';
		}
		if ( 'custom' === $c['width'] ) {
			$vars .= '--fwm-max:' . (int) $c['max_width'] . 'px;';
		}
		$classes = array_merge(
			array(
				'footer-wordmark',
				'footer-wordmark--' . $c['style'],
				'footer-wordmark--at-' . $c['placement'],
				'footer-wordmark--v-' . $c['valign'],
				'footer-wordmark--w-' . $c['width'],
				'footer-wordmark--font-' . $c['font'],
				'footer-wordmark--src-' . $c['source'],
				'footer-wordmark--fx-' . sanitize_html_class( $c['effect'] ),
			),
			$c['italic'] ? array( 'footer-wordmark--italic' ) : array(),
			$c['hide']
		);
		if ( 'image' === $c['source'] ) {
			$inner = '<img class="footer-wordmark__img" src="' . esc_url( $c['image'] ) . '" alt="" loading="lazy" decoding="async">';
		} else {
			$text   = $c['uppercase'] ? ( function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $c['text'] ) : strtoupper( $c['text'] ) ) : $c['text'];
			$chars  = max( 1, function_exists( 'mb_strlen' ) ? mb_strlen( $text ) : strlen( $text ) );
			$vars  .= '--fwm-chars:' . $chars . ';';
			$inner  = '<span class="footer-wordmark__text">' . esc_html( $text ) . '</span>';
		}
		$tag  = '' !== $c['link'] ? 'a' : 'div';
		$link = '' !== $c['link'] ? ' href="' . esc_url( $c['link'] ) . '" tabindex="-1"' : '';
		/**
		 * Whether the script fits the text to the box width. An effect that needs the natural width
		 * (e.g. a marquee that scrolls overflowing text) returns false for its id.
		 */
		$fit  = apply_filters( 'unysonplus_footer_wordmark_fit', true, $c['effect'], $c ) ? '' : ' data-fwm-fit="no"';
		$html = '<' . $tag . ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $link . ' style="' . esc_attr( $vars ) . '" data-fwm-fx="' . esc_attr( $c['effect'] ) . '"' . $fit . ' aria-hidden="true">'
			. $inner . '</' . $tag . '>';

		ob_start();
		/** Fires after the wordmark markup is built, so an extension can load assets for its effect. */
		do_action( 'unysonplus_footer_wordmark_rendered', $c );
		$html .= ob_get_clean();
		return $html . unysonplus_footer_wordmark_script();
	}
endif;

if ( ! function_exists( 'unysonplus_footer_wordmark_script' ) ) :
	/** The fit + reveal script, printed once. */
	function unysonplus_footer_wordmark_script() {
		static $done = false;
		if ( $done ) {
			return '';
		}
		$done = true;
		return <<<'HTML'
<script>
/* Footer Watermark Text: fit each text wordmark to its box once the real font has loaded (CSS already
   gave a close guess), and run the Reveal effect when the wordmark scrolls into view. */
( function () {
	document.querySelectorAll( '.footer-wordmark' ).forEach( function ( w ) {
		var t = w.querySelector( '.footer-wordmark__text' );
		if ( t && w.getAttribute( 'data-fwm-fit' ) !== 'no' ) {
			var fit = function () {
				var cs = getComputedStyle( w ), avail = w.clientWidth - parseFloat( cs.paddingLeft ) - parseFloat( cs.paddingRight );
				if ( avail <= 0 ) { return; }
				t.style.fontSize = '100px';
				var tw = t.getBoundingClientRect().width;
				if ( tw ) { t.style.fontSize = ( 100 * avail / tw ).toFixed( 2 ) + 'px'; }
			};
			fit();
			if ( document.fonts && document.fonts.ready ) { document.fonts.ready.then( fit ); }
			if ( window.ResizeObserver ) { new ResizeObserver( fit ).observe( w ); } else { window.addEventListener( 'resize', fit, { passive: true } ); }
		}
		if ( w.classList.contains( 'footer-wordmark--fx-reveal' ) ) {
			if ( ! window.IntersectionObserver || window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) { w.classList.add( 'is-in' ); return; }
			var io = new IntersectionObserver( function ( es ) { es.forEach( function ( e ) { if ( e.isIntersecting ) { w.classList.add( 'is-in' ); io.disconnect(); } } ); }, { threshold: 0.15 } );
			io.observe( w );
		}
	} );
} )();
</script>
HTML;
	}
endif;

if ( ! function_exists( 'unysonplus_render_footer_wordmark' ) ) :
	/** Back-compat (2.6.53 called this from footer.php): prints the wordmark for the bottom slots. */
	function unysonplus_render_footer_wordmark() {
		echo unysonplus_footer_wordmark_html( 'after' ) . unysonplus_footer_wordmark_html( 'behind-all' ); // phpcs:ignore — escaped above.
	}
endif;
