<?php
namespace Tk\Map;

/**
 * A map object. This should contain all the information needed to display a map
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2005 Michael Mifsud
 */
class Map
{
    
    /**
     * @var int
     */
    private $mapId = 0;
    
    /**
     * @var int
     */
    public $width = 600;
    
    /**
     * @var int
     */
    public $height = 400;
    
    /**
     * @var int
     */
    public $zoom = 6;

    /**
     * @var LatLng
     */
    public $center = null;
    
    /**
     * @var array|Marker[]
     */
    protected $markerList = array();
    
    
    
    /**
     * __construct
     *
     * @param int $width
     * @param int $height
     * @param LatLng $center
     */
    function __construct($width = 600, $height = 400, $center = null)
    {
        $this->width = $width;
        $this->height = $height;
        $this->center = $center;
        if (!$this->center) {
            $this->center = LatLng::create(-23.694031,133.87699);
        }
        $this->createMapId();
    }

    /**
     * Create a marker using a point
     *
     * @param int $width
     * @param int $height
     * @param LatLng $center
     * @return Map
     */
    static function create($width = 600, $height = 400, $center = null)
    {
        return new static($width, $height, $center);
    }
    
    /**
     * Get the unique map id
     *
     * @return int
     */
    function getMapId()
    {
        if ($this->mapId === null) {
            return $this->createMapId();
        }
        return $this->mapId;
    }
    
    /**
     * create the unique map id
     *
     * @return int
     */
    private function createMapId()
    {
        static $mapId = 0;
        $this->mapId = ++$mapId;
        return $this->mapId;
    }
    
    /**
     * Add a marker to the list
     *
     * @param Marker $marker
     * @return Map
     */
    function addMarker($marker)
    {
        $this->markerList[] = $marker;
        return $this;
    }

    /**
     * Get the marker List
     *
     * @return array|Marker[]
     */
    function getMarkerList()
    {
        return $this->markerList;
    }
    
    /**
     * Set the marker List
     *
     * @param array $list
     * @return Map
     */
    function setMarkerList($list)
    {
        $this->markerList = $list;
        return $this;
    }
    
    /**
     * Clear the marker list
     *
     * @return Map
     */
    function clearMarkerList()
    {
        $this->markerList = array();
        return $this;
    }
    
    
}