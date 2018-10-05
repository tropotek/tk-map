<?php
namespace Tk\Map\Google;

/**
 * Render a map using google maps
 *
 * @not: Consider adding the map to an Iframe to avoid page stalling when map server down.
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2005 Michael Mifsud
 */
class View extends \Dom\Renderer\Renderer
{
    // ROADMAP, SATELLITE, TERRAIN, HYBRID
    const TYPE_ROADMAP = 'ROADMAP';
    const TYPE_SATELLITE = 'SATELLITE';
    const TYPE_TERRAIN = 'TERRAIN';
    const TYPE_HYBRID = 'HYBRID';
    
    /**
     * @var null|\Tk\Uri
     */
    //private $url = 'https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places,geometry';
    
    /**
     * The google map key
     * @var string
     */
    private $key = '';

    /**
     * @var \Tk\Map\Map
     */
    private $map = null;

    /**
     * @var string
     */
    private $postInit = '';

    /**
     * @var string
     */
    private $type = self::TYPE_HYBRID;


    /**
     * Construct a Google map window
     *
     * @param \Tk\Map\Map $map
     * @param bool $hidden
     */
    function __construct(\Tk\Map\Map $map, $hidden = false, $key = '', $url = null)
    {
        $this->map = $map;
        $this->hidden = $hidden;
        //$this->key = $key;
//        if ($url)
//            $this->url = \Tk\Uri::create($url);
    }


    /**
     * Create an instance of this object
     *
     * @param \Tk\Map\Map $map
     * @param bool $hidden
     * @param string $key
     * @param null|string|\Tk\Uri $url
     * @return View
     */
    static function createView(\Tk\Map\Map $map, $hidden = false, $key = '', $url = null)
    {
        return new self($map, $hidden, $key, $url);
    }


    /**
     * Add a key if you have one.
     *
     *
     * @param string $key
     * @return View
     */
    public function setGkey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Set the map type from one of the constants:
     *  o TYPE_ROADMAP      = 'ROADMAP';
     *  o TYPE_SATELLITE    = 'SATELLITE';
     *  o TYPE_TERRAIN      = 'TERRAIN';
     *  o TYPE_HYBRID       = 'HYBRID';
     *
     * @param $type
     * @return $this
     */
    public function setMapType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get a unique map id for this instance of the renderer
     *
     * @return string
     */
    function getMapId()
    {
        return md5(serialize($this->map)) . '_' . $this->map->getMapId();
    }
    
    function getMapElementId()
    {
        return 'GMapCanvas_' . $this->getMapId();
    }

    /**
     * setPostInit
     *
     * @param $js
     */
    function setPostInit($js)
    {
        $this->postInit = $js;
    }

    /**
     * Render the widget
     *
     * @return \Dom\Template
     */
    function show()
    {
        $template = $this->getTemplate();
        //(-16.925397, 145.775178); // Cairns
        $center = 'var center = new GLatLng(0, 0);';
        if ($this->map->center) {
            $center = sprintf('var center =  new google.maps.LatLng(%s, %s);', $this->map->center->lat, $this->map->center->lng);
        }

        $zoom = 'var zoom = ' . $this->map->zoom . ';';

        $markers = 'var markers = [];';
        if (count($this->map->getMarkerList())) {
            /* @var $m \Tk\Map\Marker */
            foreach ($this->map->getMarkerList() as $i => $m) {
                $url = '';
                if ($m->icon) {
                    $url = $m->icon->toString();
                }
                $markers .= sprintf("\n    markers[$i] = { latlng: new google.maps.LatLng(%s, %s), title: '%s', html: '%s' }",
                    $m->latlng->lat,
                    $m->latlng->lng,
                    str_replace(array("\r","\n"), ' ', addslashes(htmlentities($m->title))),
                    str_replace(array("\r","\n"), ' ', addslashes($m->html))
                );
            }
        }

        $template->setAttr('canvas', 'id', 'GMapCanvas_' . $this->getMapId());
        $template->setAttr('canvas', 'style', 'width:' . $this->map->width . 'px; height:' . $this->map->height . 'px;');

        // Google Maps Start
//        if ($this->key) {
//            $this->url->set('key', $this->key);
//        }
//        $template->appendJsUrl($this->url);
        \App\Ui\Js::includeGoogleMaps($template, array('libraries' => 'places,geometry'));


        $js = <<<JS
jQuery(function($) {
    function initialize() {
        $center
        $zoom
        $markers

      var mapEl = document.getElementById('GMapCanvas_{$this->getMapId()}');
      var myOptions = {
          //scrollwheel: false,
          zoom: zoom,
          center: center,
          mapTypeId: google.maps.MapTypeId.{$this->type}  // ROADMAP, SATELLITE, TERRAIN, HYBRID
        };
      var map = new google.maps.Map(mapEl, myOptions);
      mapEl.map = map;
      mapEl.markers = markers;

      //map.setZoom(zoom);
      if (markers.length > 0) {
        for (var i = 0; i < markers.length; i++) {
          var marker = new google.maps.Marker({
            position: markers[i].latlng,
            map: map,
            title: markers[i].title
          });
          if (markers[i].html) {
            var infowindow = new google.maps.InfoWindow({
              content: markers[i].html
            });
            google.maps.event.addListener(marker, 'click', function() {
              infowindow.open(map,marker);
            });
          }
        }
      }
    };
    //google.maps.event.addDomListener(window, 'load', initialize);
    initialize();
});
JS;
        $template->appendJs($js);

        return $template;
    }


    /**
     * makeTemplate
     *
     * @return \Dom\Template
     * @throws \Dom\Exception
     */
    public function __makeTemplate()
    {
        $xmlStr = <<<HTML
<?xml version="1.0"?>
<div class="GMapCanvas tk-gmap-canvas" id="GMapCanvas_id" var="canvas"></div>
HTML;

        $template = \Dom\Template::load($xmlStr);
        return $template;
    }

}