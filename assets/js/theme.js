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
 * This is the javascript that made the listeners passive.
 */
(function() {
  var supportsPassive = eventListenerOptionsSupported();  

  if (supportsPassive) {
    var addEvent = EventTarget.prototype.addEventListener;
    overwriteAddEvent(addEvent);
  }

  function overwriteAddEvent(superMethod) {
    var defaultOptions = {
      passive: true,
      capture: false
    };

    EventTarget.prototype.addEventListener = function(type, listener, options) {
      var usesListenerOptions = typeof options === 'object';
      var useCapture = usesListenerOptions ? options.capture : options;

      options = usesListenerOptions ? options : {};
      options.passive = options.passive !== undefined ? options.passive : defaultOptions.passive;
      options.capture = useCapture !== undefined ? useCapture : defaultOptions.capture;

      superMethod.call(this, type, listener, options);
    };
  }

  function eventListenerOptionsSupported() {
    var supported = false;
    try {
      var opts = Object.defineProperty({}, 'passive', {
        get: function() {
          supported = true;
        }
      });
      window.addEventListener("test", null, opts);
    } catch (e) {}

    return supported;
  }
})();