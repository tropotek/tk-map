<?php
namespace Tk\Map;

/**
 * A LatLngBounds instance represents a rectangle in geographical coordinates, including one that crosses the 180 degrees longitudinal meridian.
 *
 *      -------* {NE}
 *      |     |
 *      |     |
 * {SW} *------
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2005 Michael Mifsud
 */
class Bounds
{
    
    /**
     * @var LatLng
     */
    public $sw = null;
    
    /**
     * @var LatLng
     */
    public $ne = null;
    
    
    
    /**
     * Constructs a rectangle from the points at its south-west and north-east corners.
     *
     * @param LatLng $sw
     * @param LatLng $ne
     */
    function __construct(LatLng $sw, LatLng $ne)
    {
        $this->sw = $sw;
        $this->ne = $ne;
    }

    /**
     * Create a map point object
     *
     * @param LatLng $sw
     * @param LatLng $ne
     * @return Bounds
     */
    static function create(LatLng $sw, LatLng $ne)
    {
        return new static($sw, $ne);
    }

    /**
     * Create a map point object
     *
     * @param String $str
     * @return Bounds
     * @throws \Tk\Exception
     */
    static function createFromString($str)
    {
        if (!preg_match('|^\(\(([0-9\.\-]+), ([0-9\.\-]+)\), \(([0-9\.\-]+), ([0-9\.\-]+)\)\)$|', $str, $regs)) {
            throw new \Tk\Exception('Malformed Bounds string');
        }
        $sw = LatLng::create($regs[1], $regs[2]);
        $ne = LatLng::create($regs[3], $regs[4]);
        return new self($sw, $ne);
    }

    /**
     * Get the center of the boundary object
     *
     * @return LatLng
     * @todo: Test this object
     */
    public function getCenter()
    {
        $west = $this->sw->lng();
        $east = $this->ne->lng();
        $north = $this->ne->lat();
        $south = $this->sw->lat();

        $lng = $west+(($east-$west)/2);
        $lat = $north+(($south-$north)/2);

        return LatLng::create($lat, $lng);
    }

    /**
     * Returns true if the given lat/lng is in this bounds.
     *
     * @param LatLng $latlng
     * @return void
     */
    public function contains(LatLng $latlng)
    {
        // TODO:
    }

    /**
     * Returns true if this bounds approximately equals the given bounds.
     *
     * @param Bounds $bounds
     * @return void
     */
    public function equals(Bounds $bounds)
    {
        // TODO:
    }

    /**
     * Returns true if this bounds shares any points with this bounds.
     *
     * @param Bounds $bounds
     * @return void
     */
    public function intersects(Bounds $bounds)
    {
        // TODO:
    }

    /**
     * Extends this bounds to contain the union of this and the given bounds.
     *
     * @param Bounds $bounds
     * @return void
     */
    public function union(Bounds $bounds)
    {
        // TODO:
    }

    /**
     * Extends this bounds to contain the given point.
     *
     * @param LatLng $latlng
     * @return void
     */
    public function extend(LatLng $latlng)
    {
        // TODO:
    }

    /**
     * Get the North East boundry point
     *
     * @return LatLng
     */
    public function getNorthEast()
    {
        return $this->ne;
    }

    /**
     * Get the South West boundry point
     *
     * @return LatLng
     */
    public function getSouthWest()
    {
        return $this->sw;
    }


    /**
     * Converts to string.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf("((%s, %s), (%s, %s))", $this->sw->lat(), $this->sw->lng(), $this->ne->lat(), $this->ne->lng());
    }


}