/* Theme front-end behaviours — vanilla JS, no jQuery. */
(function () {
        'use strict';

        function ready(fn) {
                if (document.readyState !== 'loading') { fn(); }
                else { document.addEventListener('DOMContentLoaded', fn); }
        }

        /*----------------------------------------------------
        /* Back to top smooth scrolling
        /*--------------------------------------------------*/
        ready(function () {
                document.addEventListener('click', function (e) {
                        var link = e.target.closest('.toplink');
                        if (!link) { return; }
                        e.preventDefault();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                });
        });

        /*!
         * Anchor Scroll
         */
        document.addEventListener('click', function (event) {
                var link = event.target.closest('a[href^="#"]');
                if (!link) { return; }
                var hash = link.getAttribute('href');
                if (!hash || hash.length < 2) { return; }
                var target = null;
                try { target = document.querySelector(hash); } catch (e) { return; }
                if (target) {
                        event.preventDefault();
                        var top = target.getBoundingClientRect().top + window.pageYOffset - 190;
                        window.scrollTo({ top: top, behavior: 'smooth' });
                }
        });

        /* ==========================================
        scrollTop() >= 100
        Should be equal the the height of the header
        ========================================== */
        window.addEventListener('scroll', function () {
                var masthead = document.getElementById('masthead');
                if (!masthead) { return; }
                masthead.classList.toggle('sticky', window.pageYOffset >= 100);
        });

        /*!
         * Navigation hover
         */
        ready(function () {
                Array.prototype.forEach.call(document.querySelectorAll('.menu-item-has-children'), function (item) {
                        item.addEventListener('mouseenter', function () {
                                Array.prototype.forEach.call(item.querySelectorAll('.dropdown-menu'), function (m) { m.classList.add('show'); });
                        });
                        item.addEventListener('mouseleave', function () {
                                Array.prototype.forEach.call(item.querySelectorAll('.dropdown-menu'), function (m) { m.classList.remove('show'); });
                        });
                });
        });
})();


/*!
 * Make high-frequency SCROLL listeners passive (improves scroll performance and
 * silences Chrome's "non-passive event listener" / Lighthouse warning).
 *
 * IMPORTANT: this must NOT be applied to every event type. An earlier version
 * monkey-patched addEventListener to force `passive: true` on ALL listeners,
 * which silently broke any interaction that relies on preventDefault() inside a
 * pointer/mouse drag. The most visible casualty was the Unyson live-editor
 * column-resize handle (`fw-le-resize`): the drag tracked visually, but because
 * the pointermove listener was forced passive its preventDefault() was ignored,
 * so the new width was discarded on drop and the column snapped back to its
 * original size. Passive only matters for the scroll-blocking events below, so
 * the override is scoped to those — pointer/mouse drag listeners are untouched.
 */
(function() {
  if (!eventListenerOptionsSupported()) {
    return;
  }

  // The only events where a passive listener is beneficial AND where the
  // Lighthouse "passive listeners" audit looks. Everything else is left alone.
  var PASSIVE_EVENTS = { scroll: 1, wheel: 1, mousewheel: 1, touchstart: 1, touchmove: 1 };
  var superMethod = EventTarget.prototype.addEventListener;

  EventTarget.prototype.addEventListener = function(type, listener, options) {
    // Only auto-passive the safe scroll events, and only when the caller didn't
    // pass an explicit options object (so any deliberate passive:false is kept).
    if (PASSIVE_EVENTS[type] && (options === undefined || typeof options === 'boolean')) {
      options = { capture: !!options, passive: true };
    }
    return superMethod.call(this, type, listener, options);
  };

  function eventListenerOptionsSupported() {
    var supported = false;
    try {
      var opts = Object.defineProperty({}, 'passive', {
        get: function() {
          supported = true;
        }
      });
      window.addEventListener('test', null, opts);
      window.removeEventListener('test', null, opts);
    } catch (e) {}

    return supported;
  }
})();


/*!
 * Content protection (General → Base) — opt-in deterrents gated by body classes:
 *   body.up-nocontext → block the right-click / long-press context menu
 *   body.up-nocopy    → block copy / cut of page content
 * (Disabling text selection itself is pure CSS via body.up-noselect.) Form fields
 * stay usable so search / logins / comments still work. Deterrent only — content
 * remains accessible via View Source / Reader mode / DevTools.
 */
(function () {
  function ready(fn) {
    if (document.readyState !== 'loading') { fn(); }
    else { document.addEventListener('DOMContentLoaded', fn); }
  }
  ready(function () {
    var body = document.body;
    if (!body) { return; }
    var inField = function (el) {
      return !!(el && el.closest && el.closest('input, textarea, select, [contenteditable], [contenteditable="true"]'));
    };
    if (body.classList.contains('up-nocontext')) {
      document.addEventListener('contextmenu', function (e) {
        if (!inField(e.target)) { e.preventDefault(); }
      });
    }
    if (body.classList.contains('up-nocopy')) {
      var block = function (e) { if (!inField(e.target)) { e.preventDefault(); } };
      document.addEventListener('copy', block);
      document.addEventListener('cut', block);
    }
  });
})();