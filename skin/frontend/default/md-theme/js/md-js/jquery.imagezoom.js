

(function (jQuery) {

  jQuery.fn.zoom = function (action) {
    var touchStateKey = 'zoomTouchState';

    var mouseMoveHandler = function (e) {
      e.stopPropagation();
      
      var jQuerymouseZoom = jQuery(this).find('.jsZoomMouse');
      var jQueryimage = jQuery(this).find('img');

      loadHqImage(jQuery(this));

      jQueryimage.css({ visibility: 'visible' });
      resetTouch(jQuery(this));

      var offset = jQuery(this).offset();
      var x = e.pageX - offset.left;
      var y = e.pageY - offset.top;

      if (x < 0 || x > jQuery(this).width() ||
        y < 0 || y > jQuery(this).height()) {
        jQuerymouseZoom.hide();
        return;
      }

      jQuerymouseZoom.css({
        'top': y - (jQuerymouseZoom.height() / 2),
        'left': x - (jQuerymouseZoom.width() / 2),
        'background-position': (x / jQuery(this).width() * 100) + '% ' + (y / jQuery(this).height() * 100) + '%',
        'background-size': (jQuery(this).width() * 6) + 'px ' + (jQuery(this).height() * 6) + 'px'
      });
      
      // Make sure this is the only zoom shown on page (in case there are multiple pages)
      jQuery('.jsZoomMouse').hide();
      jQuerymouseZoom.show();
    };
    
    var touchStartHandler = function (e) {
      var jQuerycontainer = jQuery(this);
      var jQueryimage = jQuery(this).find('img');
      var jQuerymouseZoom = jQuery(this).find('.jsZoomMouse');
      var offset = jQuerycontainer.offset();
      var touchState = jQuerycontainer.data(touchStateKey);
      
      loadHqImage(jQuerycontainer);
      
      jQuerymouseZoom.hide();
      jQueryimage.css({ visibility: 'hidden' });
      
      if (e.originalEvent.touches.length == 2) {
        
        jQueryimage.css({ visibility: 'hidden' });

        touchState.startPinch = distance(
          toPoint(e.originalEvent.touches[0]),
          toPoint(e.originalEvent.touches[1])
          );

        touchState.startPan = toPoint(
          e.originalEvent.touches[0].pageX - offset.left,
          e.originalEvent.touches[0].pageY - offset.top
          );
        touchState.startZoom = touchState.currentZoom;
        
      } else if (e.originalEvent.touches.length == 1) {
        
        touchState.startPan = toPoint(
          e.originalEvent.touches[0].pageX - offset.left,
          e.originalEvent.touches[0].pageY - offset.top
          );
        touchState.startPosition = copyPoint(touchState.currentPosition);
        
      }
      
      applyTouch(jQuerycontainer);
    };
    
    var touchEndHandler = function (e) {
      var jQuerycontainer = jQuery(this);
      var offset = jQuerycontainer.offset();
      var touchState = jQuerycontainer.data(touchStateKey);
      
      // Prevent touch from simulating mouseclick/move
      if (e.cancelable) {
        e.preventDefault();
      }
      
      if (e.originalEvent.touches.length == 1) {
        
        touchState.startPan = toPoint(
          e.originalEvent.touches[0].pageX - offset.left,
          e.originalEvent.touches[0].pageY - offset.top
          );
        touchState.startPosition = copyPoint(touchState.currentPosition);
        
      }
      
      applyTouch(jQuerycontainer);
    };
    
    var touchMoveHandler = function (e) {
      var jQuerycontainer = jQuery(this);
      var jQuerymouseZoom = jQuery(this).find('.jsZoomMouse');
      var jQueryimage = jQuery(this).find('img');
      var touchState = jQuerycontainer.data(touchStateKey);
      var offset = jQuerycontainer.offset();
      var originalPosition = copyPoint(touchState.currentPosition);
      
      var currentPan = toPoint(
          e.originalEvent.touches[0].pageX - offset.left,
          e.originalEvent.touches[0].pageY - offset.top
          );

      if (e.originalEvent.touches.length == 2) {

        var preZoom = toPoint(
          (touchState.currentPosition.x + currentPan.x) / (jQuerycontainer.width() * touchState.currentZoom),
          (touchState.currentPosition.y + currentPan.y) / (jQuerycontainer.height() * touchState.currentZoom)
          );

        var dst = distance(toPoint(e.originalEvent.touches[0]), toPoint(e.originalEvent.touches[1]));

        touchState.currentZoom = touchState.startZoom * (dst / touchState.startPinch);
        if (touchState.currentZoom < 1) {
          touchState.currentZoom = 1;
        }
        if (touchState.currentZoom > 10) {
          touchState.currentZoom = 10;
        }

        touchState.currentPosition = toPoint(
          preZoom.x * jQuerycontainer.width() * touchState.currentZoom - currentPan.x,
          preZoom.y * jQuerycontainer.height() * touchState.currentZoom - currentPan.y
          );

      } else if (e.originalEvent.touches.length == 1) {

        touchState.currentPosition = toPoint(
          touchState.currentPosition.x = touchState.startPosition.x + (touchState.startPan.x - currentPan.x),
          touchState.currentPosition.y = touchState.startPosition.y + (touchState.startPan.y - currentPan.y)
          );
      }

      var zoomWidth = jQuerycontainer.width() * touchState.currentZoom;
      var zoomHeight = jQuerycontainer.height() * touchState.currentZoom;

      if (touchState.currentPosition.x < 0) { touchState.currentPosition.x = 0; }
      if (touchState.currentPosition.x + jQuerycontainer.width() > zoomWidth) { touchState.currentPosition.x = zoomWidth - jQuerycontainer.width(); }
      if (touchState.currentPosition.y < 0) { touchState.currentPosition.y = 0; }
      if (touchState.currentPosition.y + jQuerycontainer.height() > zoomHeight) { touchState.currentPosition.y = zoomHeight - jQuerycontainer.height(); }

      applyTouch(jQuerycontainer);

      if (touchState.currentZoom > 1) {
        e.stopPropagation();
        e.preventDefault();
      }

    };
    
    var resetTouch = function (jQuerycontainer) {
      var touchState = jQuerycontainer.data(touchStateKey);
      
      if (touchState) {
        touchState.startPinch = 0;
        touchState.startZoom = 1;
        touchState.startPan = toPoint(0,0);
        touchState.startPosition = toPoint(0,0);
        touchState.currentZoom = 1;
        touchState.currentPosition = toPoint(0,0);
        
        applyTouch(jQuerycontainer);
      }
    };
    
    var applyTouch = function (jQuerycontainer) {
      var touchState = jQuerycontainer.data(touchStateKey);
      
      if (touchState.currentZoom > 1) {
        jQuerycontainer.addClass('jsZoomTouchZoomed');
      } else {
        jQuerycontainer.removeClass('jsZoomTouchZoomed');
      }
      
      jQuerycontainer.css({
        'background-size': (touchState.currentZoom * 100) + '%',
        'background-position': -touchState.currentPosition.x + 'px ' + -touchState.currentPosition.y + 'px'
      });
    }
    
    var loadHqImage = function (jQuerytarget) {
      var jQuerymouseZoom = jQuerytarget.find('.jsZoomMouse');
      var jQueryimage = jQuerytarget.find('img');

      if (!jQuerytarget.hasClass('jsZoomHqLoaded')) {
        jQuerytarget.addClass('jsZoomHqLoaded');

        jQuerytarget.css({
          'background-image': 'url(' + jQueryimage.attr('src') + ')'
        });
        jQuerymouseZoom.css({
          'background-image': 'url(' + jQueryimage.attr('src') + ')'
        });

        var hqImage = jQuerytarget.attr('data-zoom');
        if (hqImage) {
          var img = new Image();
  
          img.onload = function () {
            jQuerytarget.css({
              'background-image': 'url(' + hqImage + ')'
            });
            jQuerymouseZoom.css({
              'background-image': 'url(' + hqImage + ')'
            });
            hqImageState = 'loaded';
          };
  
          img.src = jQuerytarget.attr('data-zoom');
        }
      }
    };
    
    var toPoint = function (obj1, obj2) {
      if (typeof obj1.pageX === 'number' && typeof obj1.pageY === 'number') {
        return { x: obj1.pageX, y: obj1.pageY };
      } else if (typeof obj1 === 'number' && typeof obj2 === 'number') {
        return { x: obj1, y: obj2 };
      }

      throw 'Could not convert to point. Unrecognized object: ' + obj1;
    };
    
    var copyPoint = function (point) {
      return { x: point.x, y: point.y }
    };
    
    /**
     * Calculates the distance between two points
     */
    var distance = function (point1, point2) {
      var x = point1.x - point2.x;
      if (x < 0) { x *= -1; }
      var y = point1.y - point2.y;
      if (y < 0) { y *= -1; }
      return Math.round(Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2)));
    };
    
    /**
     * Calculates the center point between two points
     */
    var center = function (point1, point2) {
      var x = point1.x - ((point1.x - point2.x) / 2);
      var y = point1.y - ((point1.y - point2.y) / 2);
      return [x, y];
    };
    
    
    
    if (action === 'reset') {
      this.each((idx, elem) => { resetTouch(jQuery(elem)); });
    } else {
      
      this.css({
        'position': 'relative',
        'background-repeat': 'no-repeat'
      });
      this.addClass('jsZoom');
      if ('ontouchstart' in document.documentElement) {
        this.addClass("jsZoomTouch");
      }
      this.find('img').css({'display': 'block'});
    
      this.append(jQuery('<div class="jsZoomMouse" style="display: none; position: absolute; height: 300px; width: 300px; z-index: 9999; border-radius: 200px; background-repeat: no-repeat;"/>'));
    
      this.each((idx, elem) => {
        jQuery(elem).data(touchStateKey, {
          startPinch: 0,
          startZoom: 1,
          startPan: toPoint(0,0),
          startPosition: toPoint(0,0),
          currentZoom: 1,
          currentPosition: toPoint(0,0)
        });
      });
    
      this.on('mousemove', mouseMoveHandler);
      this.on("touchstart", touchStartHandler);
      this.on("touchend", touchEndHandler);
      this.on("touchmove", touchMoveHandler);
      
      jQuery(document).on('mousemove', function () {
        jQuery('.jsZoomMouse').hide();
      });
      
    }
  }
})(jQuery);