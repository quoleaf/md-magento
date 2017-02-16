/*!
 * Mobile plugin for jQuery Magnify (http://thdoan.github.io/magnify/)
 *
 * jQuery Magnify by T. H. Doan is licensed under the MIT License.
 * Read a copy of the license in the LICENSE file or at
 * http://choosealicense.com/licenses/mit
 */

(function(jQuery) {
  // Ensure this is loaded after jquery.magnify.js
  if (!jQuery.fn.magnify) return;
  // Add required CSS
  jQuery('<style>' +
    '.lens-mobile {' +
      'position:fixed;' +
      'top:100px;' +
      'left:0;' +
      'width:100%;' +
      'height:100%;' +
      'background:#ccc;' +
      'display:none;' +
      'overflow:scroll;' +
      '-webkit-overflow-scrolling:touch;' +
    '}' +
    '.magnify-mobile>.close {' +
      'position:fixed;' +
      'top:155px;' +
      'right:10px;' +
      'width:32px;' +
      'height:32px;' +
      'background:#333;' +
      'border-radius:16px;' +
      'color:#fff;' +
      'display:inline-block;' +
      'font:normal bold 20px sans-serif;' +
      'line-height:32px;' +
      'opacity:0.8;' +
      'text-align:center;' +
    '}' +
    '@media only screen and (min-device-width:320px) and (max-device-width:773px) {' +
      '/* Assume QHD (1440 x 2560) is highest mobile resolution */' +
      '.lens-mobile { display:block; }' +
    '}' +
    '</style>').appendTo('head');
  // Ensure .magnify is rendered
  jQuery(window).load(function() {
    jQuery('body').append('<div class="magnify-mobile"><div class="lens-mobile"></div></div>');
    var jQuerylensMobile = jQuery('.lens-mobile');
    // Only enable mobile zoom on smartphones
    if (jQuerylensMobile.is(':visible') && !!('ontouchstart' in window || (window.DocumentTouch && document instanceof DocumentTouch) || navigator.msMaxTouchPoints)) {
      var jQuerymagnify = jQuery('.magnify'),
        jQuerymagnifyMobile = jQuery('.magnify-mobile');
      // Disable desktop magnifying lens
      jQuerymagnify.off();
      // Initiate mobile zoom
      // NOTE: Fixed elements inside a scrolling div have issues in iOS, so we
      // need to insert the close icon at the same level as the lens
      jQuerymagnifyMobile.hide().append('<i class="close">&times;</i>');
      // Hook up event handlers
      jQuerymagnifyMobile.children('.close').on('touchstart', function() {
        jQuerymagnifyMobile.toggle();
      });
      jQuerymagnify.children('img').on({
        touchend: function() {
          // Only execute on tap
          if (jQuery(this).data('drag')) return;
          var oScrollOffset = jQuery(this).data('scrollOffset');
          jQuerymagnifyMobile.toggle();
          // Zoom into touch point
          jQuerylensMobile.scrollLeft(oScrollOffset.x);
          jQuerylensMobile.scrollTop(oScrollOffset.y);
        },
        touchmove: function() {
          // Set drag state
          jQuery(this).data('drag', true);
        },
        touchstart: function(e) {
          // Render zoom image
          // NOTE: In iOS background-image is url(...), not url("...")
          jQuerylensMobile.html('<img src="' + jQuery(this).prev('.magnify-lens').css('background-image').replace(/url\(["']?|["']?\)/g, '') + '" alt="">');
          // Determine zoom position
          var jQuerymagnifyImage = jQuery(this),
            oZoomSize = jQuerymagnifyImage.data('zoomSize'),
            nX = e.originalEvent.touches[0].pageX - jQuerymagnifyImage.offset().left,
            nXPct = nX / jQuerymagnifyImage.width(),
            nY = e.originalEvent.touches[0].pageY - jQuerymagnifyImage.offset().top,
            nYPct = nY / jQuerymagnifyImage.height();
          // Store scroll offsets
          jQuerymagnifyImage.data('scrollOffset', {
            x: (oZoomSize.width*nXPct)-(window.innerWidth/2),
            y: (oZoomSize.height*nYPct)-(window.innerHeight/2)
          });
          // Reset drag state
          jQuery(this).data('drag', false);
        }
      });
    }
  });
}(jQuery));
