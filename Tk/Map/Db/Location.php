<?php
/*
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Tk\Map\Db;

/**
 * This interface must be implemented by any objects using the Map lib functions
 * 
 * @package Map\Db
 * @todo Check if extenting ModelInterface is necessary???
 */
interface Location extends \Tk\Db\ModelInterface
{

    /**
     * @return \Tk\Map\Marker
     */
    public function getMarker();

    /**
     * @return float
     */
    public function getZoom();


    /**
     * Find the latLng bounds of a list of
     * map items.
     *
     * @param $list
     * @return \Tk\Map\LatLng[] Return 2 LatLng points [SW, NE]
     */
    //public function findBounds($list);

    /**
     * @param \Tk\Map\LatLng $center
     * @param int $distanceKm = 20
     * @param \Tk\Db\Tool $tool
     * @return Location[]
     */
    //public function findWithinLocation($center, $distanceKm = 20, $tool = null);

    /**
     * @param \Tk\Map\LatLng $sw
     * @param \Tk\Map\LatLng $ne
     * @param \Tk\Db\Tool $tool
     * @return Location[]
     */
    //public function findWithinBoundry($sw, $ne, $tool = null);

}