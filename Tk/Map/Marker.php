<?php
namespace Tk\Map;

/**
 * A map marker object
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2005 Michael Mifsud
 */
class Marker extends Placemark
{
    
    /**
     * @var bool
     */
    public $visible = true;
    
    /**
     * @var string
     */
    public $title = '';
    
    /**
     * @var string
     */
    public $html = '';
    
    /**
     * @var \Tk\Uri
     */
    public $iconUrl = null;
    
    
    /**
     * __construct
     *
     * @param LatLng $latlng
     * @param string $html
     * @param \Tk\Uri|string $iconUrl
     */
    function __construct(LatLng $latlng, $html = '', $iconUrl = null)
    {
        parent::__construct($latlng);
        $this->html = $html;
        if ($iconUrl) {
            $this->icon = $iconUrl;
        }
    }
    
    /**
     * Create a marker using a point
     *
     * @param LatLng $latlng
     * @param string $html
     * @param \Tk\Uri|string $iconUrl
     * @return Marker
     */
    static function createMarker(LatLng $latlng, $html = '', $iconUrl = null)
    {
        $marker = new static($latlng, $html, $iconUrl);
        return $marker;
    }
    
}