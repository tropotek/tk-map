<?php
namespace Tk\Map;

/**
 * A map marker object
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
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
    public $icon = null;

    
    
    /**
     * __construct
     *
     * @param LatLng $latlng
     * @param string $html
     * @param \Tk\Uri|string $icon
     */
    function __construct(LatLng $latlng, $html = '', $icon = null)
    {
        parent::__construct($latlng);
        $this->icon = \Tk\Uri::create($this->getConfig()->getOrgVendor() . '/tk-map/js/icons/mm_20_yellow.png');
        $this->html = $html;
        if ($icon) {
            $this->icon = $icon;
        }
    }
    
    /**
     * Create a marker using a point
     *
     * @param LatLng $latlng
     * @param string $html
     * @param \Tk\Uri|string $icon
     * @return Marker
     */
    static function createMarker(LatLng $latlng, $html = '', $icon = null)
    {
        $marker = new static($latlng, $html, $icon);
        return $marker;
    }
    
}