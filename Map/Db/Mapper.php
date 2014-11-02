<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Map\Db;

/**
 * The base mapper object that controls the mapping of columns to objects
 *
 * @package Map\Db
 */
abstract class Mapper extends \Tk\Db\Mapper
{


    /**
     * Find the latLng bounds of a list of
     * map items.
     *
     *
     * @param $list
     */
    public function findBounds($list)
    {

    }

    /**
     *
     *
     * @param \Map\LatLng $center
     * @param int $distanceKm = 20
     * @param \Tk\Db\Tool $tool
     * @return \Tk\Db\ArrayObject
     */
    public function findWithinLocation(\Map\LatLng $center, $distanceKm = 20, $tool = null)
    {
        return $this->selectMany('', $tool);
    }

    /**
     *
     *
     * @param \Map\LatLng $sw
     * @param \Map\LatLng $ne
     * @param \Tk\Db\Tool $tool
     * @return \Tk\Db\ArrayObject
     */
    public function findWithinBoundry(\Map\LatLng $sw, \Map\LatLng $ne, $tool = null)
    {
        return $this->selectMany('', $tool);
    }


}
