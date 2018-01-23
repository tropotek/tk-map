/*
 * Plugin: Example
 * Version: 1.0
 * Date: 11/05/17
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 * @source http://stefangabos.ro/jquery/jquery-plugin-boilerplate-revisited/
 */

/**
 * TODO: Change every instance of "tkMap" to the name of your plugin!
 *
 * <code>
 *   $(document).ready(function() {
 *     // attach the plugin to an element
 *     $('#element').tkMap({'foo': 'bar'});
 *
 *     // call a public method
 *     $('#element').data('tkMap').foo_public_method();
 *
 *     // get the value of a property
 *     $('#element').data('tkMap').settings.foo;
 *   
 *   });
 * </code>
 */
// An optimised ready to use version of the above

;(function($) {
  var tkMap = function(element, options) {
    // plugin vars
    var defaults = {
      ajaxUrl: '',
      canvasTpl: '<div class="tk-table-map` map"></div>',
      mapOptions: {
        zoom: 1,
        maxZoom: 16,
        minZoom: 2,
        center: new google.maps.LatLng(15.029686, 91.127928),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        panControl: false,
        streetViewControl: false,
        mapTypeControl: false,
        zoomControlOptions: {style: google.maps.ZoomControlStyle.SMALL}
      }
    };

    var plugin = this;
    var $element = $(element);
    var $canvas = null;
    plugin.settings = {};

    var map = null;
    var markers = [];
    var infoWindow = new google.maps.InfoWindow({
      content: ""
    });

    // constructor method
    plugin.init = function() {
      plugin.settings = $.extend({}, defaults, options, $element.data());

      // replace the table with the map
      $canvas = $(plugin.settings.canvasTpl);
      $canvas.css({'height': '100%', 'width' : '100%'});
      $canvas.attr('class', $element.attr('class'));

      $element.parent().prepend($canvas);
      $element.hide();

      // Setup Map Window
      map = new google.maps.Map($canvas.get(0), plugin.settings.mapOptions);
      google.maps.event.addListener(map, 'click', function(event) { // Close open infoWindow
          infoWindow.close();
      });

      if (plugin.settings.ajaxUrl) {
        $.post(plugin.settings.ajaxUrl, {}, function (data) {
          if (!data.markerList) return;
          $.each(data.markerList, function (i) {
            addMarker(this);
            autoCenter();
          });
        }, 'json').fail(function (obj, type, message) {
          console.error('AJAX Error: ' + message);
        });
      }

    };  // END init()




      /**
       *
       *  Location Json Object: {
       *    "visible": true,
       *    "title": "Agnes Banks Equine Clinic",
       *    "html": "<h5>Agnes Banks Equine Clinic<\/h5>\n<p><p class=\"impSpecEqu\"><strong>Specialist Equipment:<\/strong> Endoscope<\/p><\/p>",
       *    "iconUrl": null,
       *    "latlng": {
       *        "lat": -33.61056137,
       *        "lng": 150.71524048
       *    },
       *    "address": "",
       *    "icon": "/~mifsudm/Unimelb/ems/vendor/ttek/tk-map/js/icons/mm_20_yellow.png"
       *  }
       *
       *
       */
    function addMarker(location) {
      if (!location) return;

      var url = 'http://labs.google.com/ridefinder/images/mm_20_yellow.png';
      var size = new google.maps.Size(12, 20);

      if (location.icon) {
        url = location.icon;
        // TODO: size ????
      }
      var icon = { url: url, size: size };

      var mkOptions = {
        map: map,
        position: new google.maps.LatLng(location.latlng.lat, location.latlng.lng),
        title: location.title,
        icon: icon,
        object: location
      };
      var mk = new google.maps.Marker(mkOptions);

      google.maps.event.addListener(mk, 'click', function() {
        infoWindow.setContent(this.object.html);
        infoWindow.open(map, this);
      });

      markers.push(mk);
    }


    function autoCenter() {
      //  Create a new viewpoint bound
      var bounds = new google.maps.LatLngBounds();
      //  Go through each...
      $.each(markers, function(index, marker) {
        bounds.extend(marker.position);
      });
      //  Fit these bounds to the map
      map.fitBounds(bounds);
    }

    // private methods
    //var foo_private_method = function() { };

    // public methods
    //plugin.foo_public_method = function() { };

    // call the "constructor" method
    plugin.init();
  };

  // add the plugin to the jQuery.fn object
  $.fn.tkMap = function(options) {
    return this.each(function() {
      if (undefined === $(this).data('tkMap')) {
        var plugin = new tkMap(this, options);
        $(this).data('tkMap', plugin);
      }
    });
  }

})(jQuery);

