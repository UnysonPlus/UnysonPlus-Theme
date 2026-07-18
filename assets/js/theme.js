/*----------------------------------------------------
/* Back to top smooth scrolling
/*--------------------------------------------------*/

jQuery(document).ready(function($) {
    jQuery('.toplink').click(function(){
        jQuery('html, body').animate({scrollTop:0}, 'slow');
        return false;
    });
});



/*!
 * Anchor Scroll
 */
jQuery(function ($) {
        $(document).on('click', 'a[href^="#"]', function (event) {
                var target = $($(this).attr('href'));
                if (target.length) {
                        event.preventDefault();
                        $('html, body').animate({
                                scrollTop: target.offset().top - 190 }, 800, 'linear');
                }
        });
});



/* ========================================== 
scrollTop() >= 300
Should be equal the the height of the header
========================================== */
jQuery(function ($) {
        $(window).scroll(function(){
                if ($(window).scrollTop() >= 100) {
                        $('#masthead').addClass('sticky');
                }
                else {
                        $('#masthead').removeClass('sticky');
                }
        });
});


/*!
 * Navigation hover
 */
jQuery(function ($) {
        $('.menu-item-has-children').hover(
                function(){$(this).find('.dropdown-menu').addClass('show')},
        function(){$(this).find('.dropdown-menu').removeClass('show')}
        );
});


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