/**
 * Typography tab — live specimen preview with device toggles.
 *
 * Renders H1–H6 + Body from the ACTUAL Typography option values as you edit them. A device toggle
 * (desktop / tablet / mobile) re-sizes ONLY the specimen text to what that viewport would show — the
 * theme's fluid clamp() is evaluated in JS at the device width (preferred = intercept + slope·W,
 * clamped to [min,max]), matching css-tokens.php exactly. The tag (H1…) and the px/rem readout stay
 * on the sides; the specimen text sits on the site's own canvas + ink (localized, contrast-guarded).
 * Sizing mirrors the theme:
 *   - Fluid Heading Scale ON  → heading = base(Body) × ratio^step (h1=6…h6=1), min from mobile ratio.
 *   - Fluid Heading Scale OFF → the per-heading override size, min from mobile_font_size_scale().
 */
(function ($) {
	'use strict';

	var SAMPLE = 'The quick brown fox jumps over the lazy dog.';
	var STEP   = { h1: 6, h2: 5, h3: 4, h4: 3, h5: 2, h6: 1 };
	var DEF_SIZE = { h1: 36, h2: 28, h3: 24, h4: 20, h5: 18, h6: 16 };
	var MIN_VW = 360, MAX_VW = 1280, ROOT = 16, FLOOR = 14;

	function debounce(fn, ms) { var t; return function () { clearTimeout(t); t = setTimeout(fn, ms); }; }
	function num(v, d) { var n = parseFloat(v); return isFinite(n) ? n : d; }

	function val($scope, tail) {
		var $e = $scope.find('[name$="' + tail + '"]').first();
		return $e.length ? String($e.val() == null ? '' : $e.val()).trim() : '';
	}
	function switchVal($scope, tail) {
		var $c = $scope.find('[name$="' + tail + '"]:checked');
		if ($c.length) { return String($c.val()); }
		var $h = $scope.find('[name$="' + tail + '"]').first();
		return $h.length ? String($h.val()) : '';
	}

	// When Fluid Heading Scale is ON, the per-heading SIZE fields are ignored (the scale drives sizing),
	// so dim + lock just the Size cell of each H1–H6 override (line-height / spacing / colour stay live),
	// making the mode obvious. Value is preserved (not disabled), so turning Fluid off restores it.
	function syncScaleMode($scope) {
		var on = switchVal($scope, '[type_scale_enable]') === 'yes';
		['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].forEach(function (role) {
			var $cell = $scope.find('[name$="[' + role + '][size]"]').closest('.fw-option-typography-v2-option-size');
			$cell.toggleClass('upw-typo-size-off', on).attr('title', on ? 'Driven by the Fluid Heading Scale — turn it off to set this size manually.' : null);
		});
	}

	// --- theme fluid helpers, mirrored (font-size-presets.php) --------------------------------
	var BODY_GROW = 1.15; // body grows gently on large screens (mirror of --unysonplus_body_fluid_grow)
	function mobileScale(px) {
		if (px >= 60) { return Math.max(FLOOR, Math.round(px * 0.60)); }
		if (px >= 32) { return Math.max(FLOOR, Math.round(px * 0.75)); }
		if (px >= 20) { return Math.max(FLOOR, Math.round(px * 0.85)); }
		if (px >= 16) { return Math.max(FLOOR, Math.round(px * 0.90)); }
		return Math.max(FLOOR, Math.round(px));
	}
	// Evaluate the theme's fluid clamp() at a given viewport width W (px) → the rendered px.
	function evalAt(maxPx, minPx, W) {
		if (maxPx <= 0) { return 0; }
		if (minPx <= 0 || minPx >= maxPx) { return maxPx; } // fixed (body, or min≥max)
		var slope = (maxPx - minPx) / (MAX_VW - MIN_VW);
		var intercept = minPx - slope * MIN_VW;
		var preferred = intercept + slope * W;
		return Math.max(minPx, Math.min(maxPx, preferred));
	}
	// The (max,min) px endpoints for a role, matching css-tokens.php.
	function endpoints(role, m) {
		if (role === 'body') { return { max: m.base * BODY_GROW, min: m.base }; } // grows gently on desktop
		if (m.scaleOn) {
			var max = m.base * Math.pow(m.ratio, STEP[role]);
			var min = m.base * Math.pow(m.ratioMobile, STEP[role]);
			min = Math.min(min, max);
			min = Math.max(min, Math.min(FLOOR, max));
			return { max: max, min: min };
		}
		var mx = num(val(m.$scope, '[' + role + '][size]'), DEF_SIZE[role]);
		return { max: mx, min: mobileScale(mx) };
	}

	var loaded = {};
	function ensureFont(family) {
		if (!family || loaded[family] || /^(arial|helvetica|georgia|times|serif|sans-serif|monospace|system-ui|inherit)$/i.test(family)) { return; }
		loaded[family] = true;
		$('<link rel="stylesheet">').attr('href',
			'https://fonts.googleapis.com/css?family=' + encodeURIComponent(family).replace(/%20/g, '+') + ':400,500,600,700&display=swap'
		).appendTo(document.head);
	}

	function collect($scope) {
		var bodyFam = val($scope, '[body][family]') || 'inherit';
		var headFam = val($scope, '[heading_font][family]') || bodyFam;
		var pv = window.upwTypoPrev || {};
		return {
			$scope: $scope,
			base: num(val($scope, '[body][size]'), 16),
			ratio: num(val($scope, '[type_scale_ratio]'), 1.25),
			ratioMobile: num(val($scope, '[type_scale_ratio_mobile]'), 1.2),
			scaleOn: switchVal($scope, '[type_scale_enable]') === 'yes',
			bodyFam: bodyFam, headFam: headFam,
			bg: pv.bg || '#ffffff', ink: pv.text || '#1d2327'
		};
	}

	// The device width the clamp is evaluated at (and the canvas is drawn at). Desktop follows the
	// available canvas width (your real screen), tablet/mobile are exact device references.
	function deviceWidth(dev, avail) {
		if (dev === 'mobile') { return 390; }
		if (dev === 'tablet') { return 768; }
		return Math.max(360, Math.min(1280, avail || 1000));
	}

	var ICON = {
		desktop: '<svg viewBox="0 0 20 20" width="15" height="15" aria-hidden="true"><rect x="2" y="3" width="16" height="11" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M7 17h6M10 14v3" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round"/></svg>',
		tablet:  '<svg viewBox="0 0 20 20" width="15" height="15" aria-hidden="true"><rect x="4" y="2" width="12" height="16" rx="1.8" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="10" cy="15.5" r="0.9" fill="currentColor"/></svg>',
		mobile:  '<svg viewBox="0 0 20 20" width="15" height="15" aria-hidden="true"><rect x="6" y="2" width="8" height="16" rx="1.8" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="10" cy="15.5" r="0.8" fill="currentColor"/></svg>'
	};

	function buildPanel() {
		var rows = '';
		['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'body'].forEach(function (r) {
			rows += '<div class="upw-typo-prev__row" data-role="' + r + '">' +
				'<span class="upw-typo-prev__tag">' + (r === 'body' ? 'p' : r) + '</span>' +
				'<span class="upw-typo-prev__canvas"><span class="upw-typo-prev__spec">' + SAMPLE + '</span></span>' +
				'<span class="upw-typo-prev__meta"></span></div>';
		});
		return $(
			'<div class="upw-typo-prev">' +
			'<div class="upw-typo-prev__head">' +
			'<span class="upw-typo-prev__title">Live preview</span>' +
			'<span class="upw-typo-prev__note">Per-device sizes — reflects your fonts, scale, line-height, letter-spacing &amp; colour.</span>' +
			'<span class="upw-typo-prev__devices">' +
			'<button type="button" class="upw-dev is-on" data-dev="desktop" title="Desktop">' + ICON.desktop + '</button>' +
			'<button type="button" class="upw-dev" data-dev="tablet" title="Tablet">' + ICON.tablet + '</button>' +
			'<button type="button" class="upw-dev" data-dev="mobile" title="Mobile">' + ICON.mobile + '</button>' +
			'</span></div>' +
			'<div class="upw-typo-prev__body">' + rows + '</div>' +
			'</div>'
		);
	}

	function render($scope, $panel) {
		var m = collect($scope);
		ensureFont(m.bodyFam); ensureFont(m.headFam);

		var dev = $panel.data('dev') || 'desktop';
		var avail = $panel.find('.upw-typo-prev__canvas').first().parent().width() || 900;
		var W = deviceWidth(dev, avail);

		// Canvas colours + the device width for the canvas column.
		$panel[0].style.setProperty('--upw-bg', m.bg);
		$panel[0].style.setProperty('--upw-ink', m.ink);
		$panel[0].style.setProperty('--upw-dev', (dev === 'desktop' ? '1fr' : W + 'px'));

		$panel.find('.upw-typo-prev__row').each(function () {
			var $row = $(this);
			var role = $row.data('role');
			var isBody = role === 'body';
			var fam = isBody ? m.bodyFam : (val($scope, '[' + role + '][family]') || m.headFam);
			ensureFont(fam);

			var ep = endpoints(role, m);
			var px = Math.round(evalAt(ep.max, ep.min, W));
			var lh = isBody ? num(val($scope, '[body][line-height]'), 1.6) : num(val($scope, '[' + role + '][line-height]'), 1.2);
			var ls = isBody ? num(val($scope, '[body][letter-spacing]'), 0) : num(val($scope, '[' + role + '][letter-spacing]'), 0);
			var color = isBody ? val($scope, '[body][color]') : val($scope, '[' + role + '][color]');
			var weight = isBody ? (parseInt(val($scope, '[body][variation]'), 10) || parseInt(val($scope, '[body][weight]'), 10) || 400) : 600;

			$row.find('.upw-typo-prev__spec').css({
				fontFamily: fam && fam !== 'inherit' ? ('"' + fam + '", sans-serif') : '',
				fontSize: px + 'px',
				lineHeight: lh || '',
				letterSpacing: ls ? (ls + 'px') : '',
				color: color || '', // empty inherits the canvas ink
				fontWeight: weight
			}).text(SAMPLE);

			$row.find('.upw-typo-prev__meta').text(px + 'px / ' + (Math.round((px / ROOT) * 100) / 100) + 'rem');
		});
	}

	function setDevice($scope, $panel, dev) {
		$panel.data('dev', dev);
		$panel.find('.upw-dev').removeClass('is-on').filter('[data-dev="' + dev + '"]').addClass('is-on');
		render($scope, $panel);
	}

	function tryBoot() {
		var $anchor = $('[name$="[body][size]"]').first();
		if (!$anchor.length) { return false; }
		var $scope = $anchor.closest('.fw-backend-option-type-multi');
		if (!$scope.length) { $scope = $anchor.closest('form'); }
		if (!$scope.length) { return false; }
		if ($scope.data('upwTypoPrev')) { return true; }
		$scope.data('upwTypoPrev', true);

		var $panel = buildPanel();
		$panel.data('dev', 'desktop');
		$scope.prepend($panel);

		$panel.on('click', '.upw-dev', function () { setDevice($scope, $panel, $(this).data('dev')); });

		var update = debounce(function () { render($scope, $panel); }, 200);
		$scope.on('change input', 'input, select', update);
		// Instant (non-debounced) Size-field dimming when the Fluid toggle flips.
		$scope.on('change', '[name$="[type_scale_enable]"]', function () { syncScaleMode($scope); });

		render($scope, $panel);
		syncScaleMode($scope);
		setTimeout(function () { render($scope, $panel); syncScaleMode($scope); }, 600);
		return true;
	}

	function boot() {
		if (tryBoot()) { return; }
		var pending = false;
		var mo = new MutationObserver(function () {
			if (pending) { return; }
			pending = true;
			setTimeout(function () { pending = false; tryBoot(); }, 120);
		});
		mo.observe(document.body, { childList: true, subtree: true });
	}

	$(function () { setTimeout(boot, 400); });
})(jQuery);
