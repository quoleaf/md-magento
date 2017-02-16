/*!
 * jQuery Magnify Plugin v1.6.18 by T. H. Doan (http://thdoan.github.io/magnify/)
 * Based on http://thecodeplayer.com/walkthrough/magnifying-glass-for-images-using-jquery-and-css3
 *
 * jQuery Magnify by T. H. Doan is licensed under the MIT License.
 * Read a copy of the license in the LICENSE file or at
 * http://choosealicense.com/licenses/mit
 */

(function(jQuery) {
  jQuery.fn.magnify = function(oOptions) {

    var jQuerythat = this, // Preserve scope
      oSettings = jQuery.extend({
        /* Default options */
        speed: 100,
        timeout: -1,
        afterLoad: function(){}
      }, oOptions),
      init = function(el) {
        // Initiate
        var jQueryimage = jQuery(el),
          jQueryanchor = jQueryimage.closest('a'),
          jQuerycontainer,
          jQuerylens,
          oContainerOffset,
          nContainerWidth,
          nContainerHeight,
          nImageWidth,
          nImageHeight,
          nLensWidth,
          nLensHeight,
          nMagnifiedWidth = 0,
          nMagnifiedHeight = 0,
          sImgSrc = jQueryimage.attr('data-magnify-src') || oSettings.src || jQueryanchor.attr('href') || '',
          hideLens = function() {
            if (jQuerylens.is(':visible')) jQuerylens.fadeOut(oSettings.speed, function() {
              jQuery('html').removeClass('magnifying').trigger('magnifyend'); // Reset overflow-x
            });
          };
        // Disable zooming if no valid zoom image source
        if (!sImgSrc) return;

        // Save any inline styles for resetting
        jQueryimage.data('originalStyle', jQueryimage.attr('style'));

        // Activate magnification:
        // 1. Try to get zoom image dimensions
        // 2. Proceed only if able to get zoom image dimensions OK

        // [1] Calculate the native (magnified) image dimensions. The zoomed
        // version is only shown after the native dimensions are available. To
        // get the actual dimensions we have to create this image object.
        var elImage = new Image();
        jQuery(elImage).on({
          load: function() {
            // [2] Got image dimensions OK

            var nX, nY;

            // Fix overlap bug at the edges during magnification
            jQueryimage.css('display', 'block');
            // Create container div if necessary
            if (!jQueryimage.parent('.magnify').length) {
              jQueryimage.wrap('<div class="magnify"></div>');
            }
            jQuerycontainer = jQueryimage.parent('.magnify');
            // Create the magnifying lens div if necessary
            if (jQueryimage.prev('.magnify-lens').length) {
              jQuerycontainer.children('.magnify-lens').css('background-image', 'url(\'' + sImgSrc + '\')');
            } else {
              jQueryimage.before('<div class="magnify-lens loading" style="background:url(\'' + sImgSrc + '\') no-repeat 0 0"></div>');
            }
            jQuerylens = jQuerycontainer.children('.magnify-lens');
            // Remove the "Loading..." text
            jQuerylens.removeClass('loading');
            // Cache offsets and dimensions for improved performance
            // NOTE: This code is inside the load() function, which is
            // important. The width and height of the object would return 0 if
            // accessed before the image is fully loaded.
            oContainerOffset = jQuerycontainer.offset();
            nContainerWidth = jQuerycontainer.width();
            nContainerHeight = jQuerycontainer.height();
            nImageWidth = jQueryimage.innerWidth(); // Correct width with padding
            nImageHeight = jQueryimage.innerHeight(); // Correct height with padding
            nLensWidth = jQuerylens.width();
            nLensHeight = jQuerylens.height();
            nMagnifiedWidth = elImage.width;
            nMagnifiedHeight = elImage.height;
            // Store dimensions for mobile plugin
            jQueryimage.data('zoomSize', {
              width: nMagnifiedWidth,
              height: nMagnifiedHeight
            });
            // Clean up
            elImage = null;
            // Execute callback
            oSettings.afterLoad();
            // Handle mouse movements
            jQuerycontainer.off().on({
              'mousemove touchmove': function(e) {
                e.preventDefault();
                // Reinitialize if image initially hidden
                if (!nContainerHeight) {
                  refresh();
                  return;
                }
                // x/y coordinates of the mouse pointer or touch point
                // This is the position of .magnify relative to the document.
                //
                // We deduct the positions of .magnify from the mouse or touch
                // positions relative to the document to get the mouse or touch
                // positions relative to the container (.magnify).
                nX = (e.pageX || e.originalEvent.touches[0].pageX) - oContainerOffset.left,
                nY = (e.pageY || e.originalEvent.touches[0].pageY) - oContainerOffset.top;
                // Toggle magnifying lens
                if (!jQuerylens.is(':animated')) {
                  if (nX<nContainerWidth && nY<nContainerHeight && nX>0 && nY>0) {
                    if (jQuerylens.is(':hidden')) {
                      jQuery('html').addClass('magnifying').trigger('magnifystart'); // Hide overflow-x while zooming
                      jQuerylens.fadeIn(oSettings.speed);
                    }
                  } else {
                    hideLens();
                  }
                }
                if (jQuerylens.is(':visible')) {
                  // Move the magnifying lens with the mouse
                  var nPosX = nX - nLensWidth/2,
                    nPosY = nY - nLensHeight/2;
                  if (nMagnifiedWidth && nMagnifiedHeight) {
                    // Change the background position of .magnify-lens according
                    // to the position of the mouse over the .magnify-image image.
                    // This allows us to get the ratio of the pixel under the
                    // mouse pointer with respect to the image and use that to
                    // position the large image inside the magnifying lens.
                    var nRatioX = Math.round(nX/nImageWidth*nMagnifiedWidth - nLensWidth/2)*-1,
                      nRatioY = Math.round(nY/nImageHeight*nMagnifiedHeight - nLensHeight/2)*-1,
                      sBgPos = nRatioX + 'px ' + nRatioY + 'px';
                  }
                  // Now the lens moves with the mouse. The logic is to deduct
                  // half of the lens's width and height from the mouse
                  // coordinates to place it with its center at the mouse
                  // coordinates. If you hover on the image now, you should see
                  // the magnifying lens in action.
                  jQuerylens.css({
                    top: Math.round(nPosY) + 'px',
                    left: Math.round(nPosX) + 'px',
                    backgroundPosition: sBgPos || ''
                  });
                }
              },
              'mouseenter': function() {
                // Need to update offsets here to support accordions
                oContainerOffset = jQuerycontainer.offset();
              },
              'mouseleave': hideLens
            });

            // Prevent magnifying lens from getting "stuck"
            if (oSettings.timeout>=0) {
              jQuerycontainer.on('touchend', function() {
                setTimeout(hideLens, oSettings.timeout);
              });
            }
            // Ensure lens is closed when tapping outside of it
            jQuery('body').not(jQuerycontainer).on('touchstart', hideLens);

            // Support image map click-throughs while zooming
            var sUsemap = jQueryimage.attr('usemap');
            if (sUsemap) {
              var jQuerymap = jQuery('map[name=' + sUsemap.slice(1) + ']');
              // Image map needs to be on the same DOM level as image source
              jQueryimage.after(jQuerymap);
              jQuerycontainer.click(function(e) {
                // Trigger click on image below lens at current cursor position
                if (e.clientX || e.clientY) {
                  jQuerylens.hide();
                  var elPoint = document.elementFromPoint(
                      e.clientX || e.originalEvent.touches[0].clientX,
                      e.clientY || e.originalEvent.touches[0].clientY
                    );
                  if (elPoint.nodeName==='AREA') {
                    elPoint.click();
                  } else {
                    // Workaround for buggy implementation of elementFromPoint()
                    // See https://bugzilla.mozilla.org/show_bug.cgi?id=1227469
                    jQuery('area', jQuerymap).each(function() {
                      var a = jQuery(this).attr('coords').split(',');
                      if (nX>=a[0] && nX<=a[2] && nY>=a[1] && nY<=a[3]) {
                        this.click();
                        return false;
                      }
                    });
                  }
                }
              });
            }

            if (jQueryanchor.length) {
              // Make parent anchor inline-block to have correct dimensions
              jQueryanchor.css('display', 'inline-block');
              // Disable parent anchor if it's sourcing the large image
              if (jQueryanchor.attr('href') && !(jQueryimage.attr('data-magnify-src') || oSettings.src)) {
                jQueryanchor.click(function(e) {
                  e.preventDefault();
                });
              }
            }

          },
          error: function() {
            // Clean up
            elImage = null;
          }
        });

        elImage.src = sImgSrc;
      },
      // Simple debounce
      nTimer = 0,
      refresh = function() {
        clearTimeout(nTimer);
        nTimer = setTimeout(function() {
          jQuerythat.destroy();
          jQuerythat.magnify(oSettings);
        }, 100);
      };

    /* Public methods */

    // Turn off zoom and reset to original state
    this.destroy = function() {
      this.each(function() {
        var jQuerythis = jQuery(this),
          jQuerylens = jQuerythis.prev('div.magnify-lens'),
          sStyle = jQuerythis.data('originalStyle');
        if (jQuerythis.parent('div.magnify').length && jQuerylens.length) {
          if (sStyle) jQuerythis.attr('style', sStyle);
          else jQuerythis.removeAttr('style');
          jQuerythis.unwrap();
          jQuerylens.remove();
        }
      });
      // Unregister event handler
      jQuery(window).off('resize', refresh);
      return jQuerythat;
    }

    // Handle window resizing
    jQuery(window).resize(refresh);

    return this.each(function() {
      // Initiate magnification powers
      init(this);
    });

  };
}(jQuery));
