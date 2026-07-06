<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

/**
 * Header / Footer presets — runtime resolution + admin wiring.
 *
 * A preset is a post of type up_header / up_footer (see inc/post-types.php) whose
 * meta box reuses the global header/footer slot UI, so its saved meta is
 * structurally identical to the global Theme Settings header/footer options.
 *
 * Which preset applies to the current request is resolved by
 * unysonplus_resolve_layout( 'header_preset' | 'footer_preset' ) — see
 * inc/includes/layout.php — through this cascade:
 *   1. per-content meta (the Header & Footer meta box, injected below)
 *   2. site-wide default preset (General → Pages settings)
 *   3. '' = use the global "Default" header/footer (Theme Settings)
 *
 * The render templates (template-parts/header-builder.php and footer-builder.php)
 * consume the resolved config via unysonplus_get_active_header_config() /
 * unysonplus_get_active_footer_config(). With zero presets created and nothing
 * selected, those return the global settings — i.e. byte-identical to before.
 */


/* ============================================================
 * Option-id maps
 * ============================================================ */

if ( ! function_exists( 'unysonplus_preset_option_ids' ) ) :
/**
 * The leaf option ids that make up a preset of the given kind. These match the
 * global Theme Settings ids exactly, so the same render code consumes both.
 *
 * @param string $kind 'header' | 'footer'
 * @return string[]
 */
function unysonplus_preset_option_ids( $kind ) {
	if ( $kind === 'footer' ) {
		return array(
			'pre_footer_columns',
			'pre_footer_custom_styling',
			'main_footer_columns',
			'main_footer_custom_styling',
			'post_footer_columns',
			'post_footer_custom_styling',
			'copyright_settings',
		);
	}
	return array(
		'header_layout',     // chrome: mode, container, heights, breakpoint, bg, behavior
		'header_topbar',     // top bar: bg/text + left/center/right columns
		'header_main',       // main row: left/center/right columns
		'header_bottombar',  // bottom bar: bg/text + left/center/right columns
	);
}
endif;


/* ============================================================
 * Preset resolution + active config
 * ============================================================ */

if ( ! function_exists( 'unysonplus_get_active_preset_id' ) ) :
/**
 * Resolve the preset post id that applies to the current request, or 0 when the
 * global "Default" header/footer should be used. Validates that the id points at
 * a published post of the matching preset type (a trashed / wrong-type id falls
 * back to 0 — never a fatal, never an empty header/footer).
 *
 * @param string $kind 'header' | 'footer'
 * @return int
 */
function unysonplus_get_active_preset_id( $kind ) {
	$key = $kind . '_preset'; // header_preset | footer_preset

	// Precedence: per-page override > Theme Builder Template > site-wide default > ''
	// (slots). When no Template applies, steps (1)+(3) reproduce the original
	// unysonplus_resolve_layout() result, so behavior is unchanged on sites with no
	// Templates.
	$id = 0;

	// (1) Explicit per-page pick wins (only singular content carries the picker).
	if ( is_singular() && function_exists( 'fw_get_db_post_option' ) ) {
		$qid = (int) get_queried_object_id();
		if ( $qid ) {
			$id = (int) fw_get_db_post_option( $qid, $key );
		}
	}

	// (2) A Theme Builder Template matched to this request by its conditions.
	if ( $id <= 0 && class_exists( 'FW_Theme_Builder_Resolver' ) ) {
		$id = ( $kind === 'footer' )
			? (int) FW_Theme_Builder_Resolver::footer_id()
			: (int) FW_Theme_Builder_Resolver::header_id();
	}

	// (3) Site-wide default (and the '' baseline) — the existing cascade. With no
	// per-page meta on this request it yields the site-wide default.
	if ( $id <= 0 ) {
		$id = function_exists( 'unysonplus_resolve_layout' )
			? (int) unysonplus_resolve_layout( $key, '' )
			: 0;
	}

	if ( $id <= 0 ) {
		return 0;
	}

	$cpt = ( $kind === 'footer' ) ? 'up_footer' : 'up_header';
	if ( get_post_type( $id ) !== $cpt || get_post_status( $id ) !== 'publish' ) {
		return 0;
	}
	return $id;
}
endif;


if ( ! function_exists( 'unysonplus_get_active_header_config' ) ) :
/**
 * Resolved header config for this request, keyed by the four header option ids
 * (header_layout / header_topbar / header_main / header_bottombar). When a preset
 * applies, ALL ids are read from that preset (so the header reads as one coherent
 * design, not a mix of preset + global); otherwise all ids come from the global
 * Theme Settings. Request-cached by preset id. Mirrors
 * unysonplus_get_active_footer_config().
 *
 * @return array<string,mixed>
 */
function unysonplus_get_active_header_config() {
	static $cache = array();

	$pid = unysonplus_get_active_preset_id( 'header' );
	if ( array_key_exists( $pid, $cache ) ) {
		return $cache[ $pid ];
	}

	$out = array();
	foreach ( unysonplus_preset_option_ids( 'header' ) as $oid ) {
		if ( $pid && function_exists( 'fw_get_db_post_option' ) ) {
			$out[ $oid ] = fw_get_db_post_option( $pid, $oid );
		} elseif ( function_exists( 'fw_get_db_settings_option' ) ) {
			$out[ $oid ] = fw_get_db_settings_option( $oid );
		} else {
			$out[ $oid ] = null;
		}
	}

	$cache[ $pid ] = $out;
	return $out;
}
endif;


if ( ! function_exists( 'unysonplus_get_active_footer_config' ) ) :
/**
 * Resolved footer config for this request, keyed by the five footer option ids.
 * When a preset applies, ALL ids are read from that preset (so the footer reads
 * as one coherent design, not a mix of preset + global). Otherwise all ids come
 * from the global Theme Settings. Request-cached by preset id.
 *
 * @return array<string,mixed>
 */
function unysonplus_get_active_footer_config() {
	static $cache = array();

	$pid = unysonplus_get_active_preset_id( 'footer' );
	if ( array_key_exists( $pid, $cache ) ) {
		return $cache[ $pid ];
	}

	$out = array();
	foreach ( unysonplus_preset_option_ids( 'footer' ) as $oid ) {
		if ( $pid && function_exists( 'fw_get_db_post_option' ) ) {
			$out[ $oid ] = fw_get_db_post_option( $pid, $oid );
		} elseif ( function_exists( 'fw_get_db_settings_option' ) ) {
			$out[ $oid ] = fw_get_db_settings_option( $oid );
		} else {
			$out[ $oid ] = null;
		}
	}

	$cache[ $pid ] = $out;
	return $out;
}
endif;


/* ============================================================
 * Migration: legacy single-blob header_layout → four keys
 *
 * Older builds stored the whole header (chrome + topbar + main + bottombar) in
 * one `header_layout` multi, with the top/bottom bars as multi-pickers
 * (`{enabled, yes:{…}}`). The header is now split into header_layout (chrome) +
 * header_topbar / header_main / header_bottombar, and the bars have no Enable
 * switch (a row renders when a column has content). This lifts the old shape into
 * the new keys for BOTH the global Theme Settings and any up_header preset
 * post-meta, preserving the visual outcome: a bar that was explicitly disabled
 * keeps its content withheld (we don't migrate its columns), so it stays hidden.
 * ============================================================ */

if ( ! function_exists( 'unysonplus_split_header_layout_blob' ) ) :
/**
 * Pure transform. Given a legacy header_layout array, return
 * [ 'header_layout'=>chrome, 'header_topbar'=>…, 'header_main'=>…,
 *   'header_bottombar'=>… ], or null when there is nothing to migrate.
 *
 * @param array $hl
 * @return array<string,array>|null
 */
function unysonplus_split_header_layout_blob( $hl ) {
	if ( ! is_array( $hl ) ) { return null; }

	$has_legacy = array_key_exists( 'topbar_settings', $hl )
		|| array_key_exists( 'bottombar_settings', $hl )
		|| array_key_exists( 'main_left', $hl )
		|| array_key_exists( 'main_center', $hl )
		|| array_key_exists( 'main_right', $hl );
	if ( ! $has_legacy ) { return null; }

	// A bar's content migrates only when it was explicitly enabled — preserving
	// the old "disabled" outcome (an empty new-shape key simply doesn't render).
	$lift_bar = function ( $bar ) {
		if ( ! is_array( $bar ) ) { return array(); }
		$enabled = isset( $bar['enabled'] ) && $bar['enabled'] === 'yes';
		if ( ! $enabled ) { return array(); }
		return ( ! empty( $bar['yes'] ) && is_array( $bar['yes'] ) ) ? $bar['yes'] : array();
	};

	$topbar    = $lift_bar( isset( $hl['topbar_settings'] ) ? $hl['topbar_settings'] : array() );
	$bottombar = $lift_bar( isset( $hl['bottombar_settings'] ) ? $hl['bottombar_settings'] : array() );

	$main = array();
	foreach ( array( 'main_left', 'main_center', 'main_right' ) as $k ) {
		if ( isset( $hl[ $k ] ) ) { $main[ $k ] = $hl[ $k ]; }
	}

	// Chrome = everything else (strip the relocated keys).
	$chrome = $hl;
	unset( $chrome['topbar_settings'], $chrome['bottombar_settings'], $chrome['main_left'], $chrome['main_center'], $chrome['main_right'] );

	return array(
		'header_layout'    => $chrome,
		'header_topbar'    => $topbar,
		'header_main'      => $main,
		'header_bottombar' => $bottombar,
	);
}
endif;

if ( ! function_exists( 'unysonplus_migrate_header_layout' ) ) :
/**
 * Run the split on the global settings (self-terminating: once header_layout has
 * no legacy keys, there is nothing to do) and, once, on every up_header preset.
 */
function unysonplus_migrate_header_layout() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}

	// --- Global Theme Settings ---
	$split = unysonplus_split_header_layout_blob( fw_get_db_settings_option( 'header_layout', array() ) );
	if ( $split !== null ) {
		foreach ( array( 'header_topbar', 'header_main', 'header_bottombar' ) as $k ) {
			$existing = fw_get_db_settings_option( $k, array() );
			if ( ( ! is_array( $existing ) || empty( $existing ) ) && ! empty( $split[ $k ] ) ) {
				fw_set_db_settings_option( $k, $split[ $k ] );
			}
		}
		fw_set_db_settings_option( 'header_layout', $split['header_layout'] );
	}

	// ---- header_mode → inline multi-picker shape (Header → Layout) ----
	// Additive to the blob split above; runs BEFORE the split early-return so it's
	// not skipped on installs where the split already completed. Idempotent — the
	// helper no-ops once header_mode is already an array.
	$mp_global = unysonplus_header_mode_to_multipicker( fw_get_db_settings_option( 'header_layout', array() ) );
	if ( $mp_global !== null ) {
		fw_set_db_settings_option( 'header_layout', $mp_global );
	}
	if ( get_option( 'unysonplus_header_mode_mp_done' ) !== 'yes'
	     && function_exists( 'fw_get_db_post_option' ) && function_exists( 'fw_set_db_post_option' )
	     && post_type_exists( 'up_header' ) ) {
		$mp_ids = get_posts( array( 'post_type' => 'up_header', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids', 'suppress_filters' => false ) );
		foreach ( $mp_ids as $mp_pid ) {
			$mp = unysonplus_header_mode_to_multipicker( fw_get_db_post_option( $mp_pid, 'header_layout' ) );
			if ( $mp !== null ) {
				fw_set_db_post_option( $mp_pid, 'header_layout', $mp );
			}
		}
		update_option( 'unysonplus_header_mode_mp_done', 'yes', false );
	}

	// --- up_header preset post-meta (one-time scan) ---
	if ( get_option( 'unysonplus_header_split_presets_done' ) === 'yes' ) {
		return;
	}
	if ( function_exists( 'fw_get_db_post_option' ) && function_exists( 'fw_set_db_post_option' ) && post_type_exists( 'up_header' ) ) {
		$ids = get_posts( array(
			'post_type'        => 'up_header',
			'post_status'      => 'any',
			'numberposts'      => -1,
			'fields'           => 'ids',
			'suppress_filters' => false,
		) );
		foreach ( $ids as $pid ) {
			$split = unysonplus_split_header_layout_blob( fw_get_db_post_option( $pid, 'header_layout' ) );
			if ( $split === null ) { continue; }
			foreach ( array( 'header_topbar', 'header_main', 'header_bottombar' ) as $k ) {
				$existing = fw_get_db_post_option( $pid, $k );
				if ( ( ! is_array( $existing ) || empty( $existing ) ) && ! empty( $split[ $k ] ) ) {
					fw_set_db_post_option( $pid, $k, $split[ $k ] );
				}
			}
			fw_set_db_post_option( $pid, 'header_layout', $split['header_layout'] );
		}
	}
	update_option( 'unysonplus_header_split_presets_done', 'yes', false );
}
endif;

if ( ! function_exists( 'unysonplus_header_mode_to_multipicker' ) ) :
/**
 * Lift a legacy `header_layout` array (a scalar `header_mode` + a flat
 * `vertical_width`) into the inline multi-picker shape:
 *
 *   [ 'header_mode' => [ 'mode' => 'top',
 *                        'vertical-left'  => [ 'vertical_width' => … ],
 *                        'vertical-right' => [ 'vertical_width' => … ] ], … ]
 *
 * Returns the migrated array, or null when there's nothing to lift (header_mode
 * already an array, not an array input, or no legacy values present) so callers
 * can skip the DB write.
 *
 * @param mixed $layout
 * @return array|null
 */
function unysonplus_header_mode_to_multipicker( $layout ) {
	if ( ! is_array( $layout ) ) {
		return null;
	}
	$hm = isset( $layout['header_mode'] ) ? $layout['header_mode'] : null;
	if ( is_array( $hm ) ) {
		return null; // already in the multi-picker shape
	}

	$has_vw     = isset( $layout['vertical_width'] ) && $layout['vertical_width'] !== '' && $layout['vertical_width'] !== null;
	$has_legacy = ( is_string( $hm ) && $hm !== '' ) || $has_vw;
	if ( ! $has_legacy ) {
		return null; // nothing legacy to lift (fresh install → option default applies)
	}

	$mode = ( is_string( $hm ) && $hm !== '' ) ? $hm : 'top';
	$new  = array( 'mode' => $mode );
	if ( $has_vw ) {
		$new['vertical-left']  = array( 'vertical_width' => $layout['vertical_width'] );
		$new['vertical-right'] = array( 'vertical_width' => $layout['vertical_width'] );
	}
	$layout['header_mode'] = $new;
	unset( $layout['vertical_width'] );
	return $layout;
}
endif;

if ( ! function_exists( 'unysonplus_migrate_header_preset_to_toggles' ) ) :
/**
 * Schema v4: the header CHROME style `header_layout.header_preset` (a short-lived
 * image-picker: bordered/elevated) becomes the border/shadow toggles:
 *   bordered → header_border = 'yes';  elevated → header_shadow = 'yes'.
 * Then `header_preset` is dropped (self-terminating). Runs on global settings + every
 * up_header preset meta. Idempotent + guarded.
 *
 * NOTE: this `header_preset` (a sub-key of the `header_layout` group) is DISTINCT from
 * the per-content `header_preset` SELECT that picks an up_header CPT — do NOT touch that.
 */
function unysonplus_migrate_header_preset_to_toggles() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}

	$apply = function ( $layout ) {
		if ( ! is_array( $layout ) || ! array_key_exists( 'header_preset', $layout ) ) {
			return null; // nothing to migrate (already done / never set)
		}
		$preset = $layout['header_preset'];
		if ( 'bordered' === $preset && empty( $layout['header_border'] ) ) { $layout['header_border'] = 'yes'; }
		if ( 'elevated' === $preset && empty( $layout['header_shadow'] ) ) { $layout['header_shadow'] = 'yes'; }
		unset( $layout['header_preset'] );
		return $layout;
	};

	// Global settings.
	$g = $apply( fw_get_db_settings_option( 'header_layout', array() ) );
	if ( null !== $g ) {
		fw_set_db_settings_option( 'header_layout', $g );
	}

	// up_header preset post-meta (one-time scan, done-flag gated).
	if ( get_option( 'unysonplus_header_preset_toggles_done' ) === 'yes' ) {
		return;
	}
	if ( function_exists( 'fw_get_db_post_option' ) && function_exists( 'fw_set_db_post_option' ) && post_type_exists( 'up_header' ) ) {
		$ids = get_posts( array( 'post_type' => 'up_header', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids', 'suppress_filters' => false ) );
		foreach ( $ids as $pid ) {
			$m = $apply( fw_get_db_post_option( $pid, 'header_layout' ) );
			if ( null !== $m ) {
				fw_set_db_post_option( $pid, 'header_layout', $m );
			}
		}
	}
	update_option( 'unysonplus_header_preset_toggles_done', 'yes', false );
}
endif;

if ( ! function_exists( 'unysonplus_migrate_header_design_to_multipicker' ) ) :
/**
 * Schema v5: the Top mode's `header_design` became an inline multi-picker, so a stored
 * scalar (e.g. 'pill') must be wrapped as [ 'design' => 'pill' ] wherever it sits under
 * header_layout → header_mode → <mode> → header_design. Idempotent + guarded; runs on
 * global settings + every up_header preset meta. Prevents the multi-picker editor-load
 * "illegal string offset" when a legacy scalar reaches the option UI.
 */
function unysonplus_migrate_header_design_to_multipicker() {
	if ( ! function_exists( 'fw_get_db_settings_option' ) || ! function_exists( 'fw_set_db_settings_option' ) ) {
		return;
	}

	$apply = function ( $layout ) {
		if ( ! is_array( $layout ) || ! isset( $layout['header_mode'] ) || ! is_array( $layout['header_mode'] ) ) {
			return null;
		}
		$changed = false;
		foreach ( $layout['header_mode'] as $mk => &$mv ) {
			if ( 'mode' === $mk || ! is_array( $mv ) ) {
				continue;
			}
			if ( isset( $mv['header_design'] ) && is_string( $mv['header_design'] ) && '' !== $mv['header_design'] ) {
				$mv['header_design'] = array( 'design' => $mv['header_design'] );
				$changed = true;
			}
		}
		unset( $mv );
		return $changed ? $layout : null;
	};

	$g = $apply( fw_get_db_settings_option( 'header_layout', array() ) );
	if ( null !== $g ) {
		fw_set_db_settings_option( 'header_layout', $g );
	}

	if ( get_option( 'unysonplus_header_design_mp_done' ) === 'yes' ) {
		return;
	}
	if ( function_exists( 'fw_get_db_post_option' ) && function_exists( 'fw_set_db_post_option' ) && post_type_exists( 'up_header' ) ) {
		foreach ( get_posts( array( 'post_type' => 'up_header', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids', 'suppress_filters' => false ) ) as $pid ) {
			$m = $apply( fw_get_db_post_option( $pid, 'header_layout' ) );
			if ( null !== $m ) {
				fw_set_db_post_option( $pid, 'header_layout', $m );
			}
		}
	}
	update_option( 'unysonplus_header_design_mp_done', 'yes', false );
}
endif;

// Invoked by the central schema-migration runner (inc/includes/migrations.php),
// not hooked directly. Kept idempotent so a re-run is a safe no-op.


/* ============================================================
 * Render-mode resolution (slots vs builder)
 *
 * Additive layer over the *_config() helpers above: tells the render templates
 * whether the active preset is a page-builder preset (Header & Footer Builder
 * extension) or the slot/Default header. Keeping this separate from the
 * *_config() functions preserves the live slot path with zero risk.
 * ============================================================ */

if ( ! function_exists( 'unysonplus_get_active_header_render' ) ) :
/**
 * @return array ['mode'=>'builder','post_id'=>int,'type'=>string,'behavior'=>string]
 *               | ['mode'=>'slots','config'=>array]
 */
function unysonplus_get_active_header_render() {
	$pid = unysonplus_get_active_preset_id( 'header' );

	if ( $pid
	     && function_exists( 'fw_ext_hfbuilder_render' )
	     && function_exists( 'fw_ext_page_builder_is_builder_post' )
	     && fw_ext_page_builder_is_builder_post( $pid ) ) {

		$type     = function_exists( 'fw_get_db_post_option' ) ? fw_get_db_post_option( $pid, 'hf_type' ) : '';
		$behavior = function_exists( 'fw_get_db_post_option' ) ? fw_get_db_post_option( $pid, 'hf_behavior' ) : '';

		return array(
			'mode'     => 'builder',
			'post_id'  => $pid,
			'type'     => $type ? $type : 'standard-top',
			'behavior' => $behavior ? $behavior : 'static',
		);
	}

	return array( 'mode' => 'slots', 'config' => unysonplus_get_active_header_config() );
}
endif;

if ( ! function_exists( 'unysonplus_get_active_footer_render' ) ) :
/**
 * @return array ['mode'=>'builder','post_id'=>int] | ['mode'=>'slots']
 */
function unysonplus_get_active_footer_render() {
	$pid = unysonplus_get_active_preset_id( 'footer' );

	if ( $pid
	     && function_exists( 'fw_ext_hfbuilder_render' )
	     && function_exists( 'fw_ext_page_builder_is_builder_post' )
	     && fw_ext_page_builder_is_builder_post( $pid ) ) {
		return array( 'mode' => 'builder', 'post_id' => $pid );
	}

	return array( 'mode' => 'slots' );
}
endif;


/* ============================================================
 * Admin choice builders
 * ============================================================ */

if ( ! function_exists( 'unysonplus_preset_choices' ) ) :
/**
 * id => title list of published presets of the given CPT, for select fields.
 *
 * @param string $cpt 'up_header' | 'up_footer'
 * @return array<string,string>
 */
function unysonplus_preset_choices( $cpt ) {
	$out = array();
	// Choices are an admin-only concern (select rendering / save validation).
	if ( ! is_admin() || ! post_type_exists( $cpt ) ) {
		return $out;
	}
	$posts = get_posts( array(
		'post_type'        => $cpt,
		'post_status'      => 'publish',
		'numberposts'      => -1,
		'orderby'          => 'title',
		'order'            => 'ASC',
		'suppress_filters' => false,
	) );
	foreach ( $posts as $p ) {
		$out[ (string) $p->ID ] = ( $p->post_title !== '' )
			? $p->post_title
			: sprintf( __( '(no title) #%d', 'unysonplus' ), $p->ID );
	}
	return $out;
}
endif;


if ( ! function_exists( 'unysonplus_builder_post_choices' ) ) :
/**
 * '' + id => label list of published page-builder posts, for the Builder Section
 * element. Only posts actually built with the page builder are listed. Excludes
 * the preset CPTs to prevent recursion. Returns just the placeholder when the
 * page-builder extension is inactive.
 *
 * @return array<string,string>
 */
function unysonplus_builder_post_choices() {
	$out = array( '' => __( '— Select a layout —', 'unysonplus' ) );

	// Choices are only needed while rendering / saving the meta box (admin).
	// Avoid the post query on front-end option-default parsing.
	if ( ! is_admin() || ! function_exists( 'fw_ext_page_builder_is_builder_post' ) ) {
		return $out;
	}

	$posts = get_posts( array(
		'post_type'        => array( 'page', 'post' ),
		'post_status'      => 'publish',
		'numberposts'      => 200,
		'orderby'          => 'title',
		'order'            => 'ASC',
		'suppress_filters' => false,
	) );

	foreach ( $posts as $p ) {
		if ( in_array( $p->post_type, array( 'up_header', 'up_footer' ), true ) ) {
			continue;
		}
		if ( fw_ext_page_builder_is_builder_post( $p->ID ) ) {
			$title         = ( $p->post_title !== '' ) ? $p->post_title : sprintf( __( '(no title) #%d', 'unysonplus' ), $p->ID );
			$out[ (string) $p->ID ] = sprintf( '%s (%s)', $title, $p->post_type );
		}
	}

	return $out;
}
endif;


/* ============================================================
 * Per-content preset pickers (all singular post types)
 *
 * Injected through the framework's `fw_post_options` filter, which fires for
 * every post type's meta-box build AND save. One box covers pages, posts and any
 * custom post type. The preset CPTs themselves (and attachments) are skipped.
 * Saved leaf ids are header_preset / footer_preset, read at render time via the
 * resolver in inc/includes/layout.php.
 * ============================================================ */

if ( ! function_exists( 'unysonplus_inject_preset_pickers' ) ) :
function unysonplus_inject_preset_pickers( $options, $post_type ) {
	if ( in_array( $post_type, array( 'up_header', 'up_footer', 'attachment' ), true ) ) {
		return $options;
	}
	$pt = get_post_type_object( $post_type );
	if ( ! $pt || empty( $pt->show_ui ) ) {
		return $options;
	}

	// Choices are only needed while rendering / saving the meta box (admin). The
	// select option type validates the submitted value against its choices, so
	// they must be present on save too — which is always an admin request.
	$header_choices = array( '' => __( 'Default (global header)', 'unysonplus' ) );
	$footer_choices = array( '' => __( 'Default (global footer)', 'unysonplus' ) );
	if ( is_admin() ) {
		$header_choices += unysonplus_preset_choices( 'up_header' );
		$footer_choices += unysonplus_preset_choices( 'up_footer' );
	}

	$options['unysonplus_header_footer'] = array(
		'title'    => __( 'Header & Footer', 'unysonplus' ),
		'type'     => 'box',
		'context'  => 'side',
		'priority' => 'low',
		'options'  => array(
			'group_header_footer' => array(
				'type'    => 'group',
				'options' => array(
					'header_preset' => array(
						'label'   => __( 'Header Preset', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '',
						'choices' => $header_choices,
						'desc'    => __( 'Use a different header on this content. Default uses the site header.', 'unysonplus' ),
					),
					'footer_preset' => array(
						'label'   => __( 'Footer Preset', 'unysonplus' ),
						'type'    => 'select',
						'value'   => '',
						'choices' => $footer_choices,
						'desc'    => __( 'Use a different footer on this content. Default uses the site footer.', 'unysonplus' ),
					),
				),
			),
		),
	);

	return $options;
}
endif;
add_filter( 'fw_post_options', 'unysonplus_inject_preset_pickers', 20, 2 );


/* ============================================================
 * Builder Section element (escape hatch)
 *
 * Renders a saved page-builder layout (a builder page/post) inside a header or
 * footer slot via do_shortcode(), so a fully bespoke layout can live in the slot
 * system without a rebuild. Shared by the header and footer element renderers.
 * Guards against recursion (never renders a preset CPT) and degrades to the raw
 * post content when the page-builder extension is unavailable.
 * ============================================================ */

if ( ! function_exists( 'unysonplus_render_builder_section' ) ) :
function unysonplus_render_builder_section( $settings ) {
	$pid = ! empty( $settings['builder_post_id'] ) ? (int) $settings['builder_post_id'] : 0;
	if ( ! $pid ) {
		return;
	}
	if ( in_array( get_post_type( $pid ), array( 'up_header', 'up_footer' ), true ) ) {
		return; // never render a header/footer preset here.
	}

	if ( function_exists( 'fw_ext_page_builder_get_post_content' )
	     && function_exists( 'fw_ext_page_builder_is_builder_post' )
	     && fw_ext_page_builder_is_builder_post( $pid ) ) {
		echo '<div class="up-builder-section">' . do_shortcode( fw_ext_page_builder_get_post_content( $pid ) ) . '</div>';
		return;
	}

	$post = get_post( $pid );
	if ( $post && $post->post_status === 'publish' ) {
		echo '<div class="up-builder-section">' . do_shortcode( wp_kses_post( $post->post_content ) ) . '</div>';
	}
}
endif;
