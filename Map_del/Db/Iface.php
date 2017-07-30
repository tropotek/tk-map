<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Map\Db;

/**
 * This interface must be implemented by any objects widhing to use the Map Table renderer and other classes
 * 
 * @package Map\Db
 */
interface Iface
{




    /**
     *
     * @return float
     */
    public function getMapLat();

    /**
     *
     * @return float
     */
    public function getMapLng();

    /**
     *
     * @return int
     */
    public function getMapZoom();


}