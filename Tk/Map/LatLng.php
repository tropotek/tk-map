<?php
namespace Tk\Map;

/**
 * A LatLng is a point in geographical coordinates: latitude and longitude.
 *
 *    Latitude ranges between -90 and 90 degrees, inclusive. Values above or below this range will be clamped to the nearest value within this range. For example, specifying a latitude of 100 will set the value to 90.
 *    Longitude ranges between -180 and 180 degrees, inclusive. Values above or below this range will be wrapped such that they fall within the range [-180, 180). For example, 480, 840 and 1200 will all be wrapped to 120 degrees.
 *
 * Although the default map projection associates longitude with the x-coordinate of the map, and latitude with the y-coordinate, the latitude coordinate is always written first, followed by the longitude.
 * Notice that you cannot modify the coordinates of a LatLng. If you want to compute another point, you have to create a new one.
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2005 Michael Mifsud
 */
class LatLng
{
    
    /**
     * @var float
     */
    public $lat = 0.0;
    
    /**
     * @var float
     */
    public $lng = 0.0;
    
    
    
    /**
     *
     * Creates a LatLng object representing a geographic point. Latitude is specified in degrees within
     * the range [-90, 90]. Longitude is specified in degrees within the range [-180, 180].
     *
     * @param float $lat
     * @param float $lng
     */
    function __construct($lat, $lng)
    {
        $this->lat = floatval($lat);
        $this->lng = floatval($lng);
    }
    
    /**
     * Creates a LatLng object representing a geographic point. Latitude is specified in degrees within
     * the range [-90, 90]. Longitude is specified in degrees within the range [-180, 180].
     *
     * @param float $lat
     * @param float $lng
     * @return LatLng
     */
    static function create($lat, $lng)
    {
        return new self($lat, $lng);
    }
    
    /**
     * Create a map point object from a string
     *
     * The string format is: "[LongtitudeValue],[Latitude],[Elevation|zoom]"
     *
     * @param string $str
     * @return LatLng
     */
    static function createFromString($str)
    {
        list($lng, $lat, $el) = explode(',', $str, 3);
        return new self($lat, $lng);
    }


    /**
     * Calculates the distance between two latlng locations in km.
     *
     * @param LatLng $p1  The first lat lng point.
     * @param LatLng $p2  The second lat lng point.
     * @return int  The distance between the two points in km.
     */
    static function distanceBetweenPoints(LatLng $p1, LatLng $p2)
    {
        $R = 6371; // Radius of the Earth in km
        $dLat = ($p2->lat() - $p1->lat()) * \M_PI / 180;
        $dLon = ($p2->lng() - $p1->lng()) * \M_PI / 180;
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($p1->lat() * \M_PI / 180) * cos($p2->lat() * \M_PI / 180) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $d = $R * $c;
        return $d;
    }


    /**
     * Comparison function.
     *
     * @param LatLng $latlng
     * @return bool
     */
    public function equals(LatLng $latlng)
    {
        return ($this->lat == $latlng->lat && $this->lng == $latlng->lng);
    }

    /**
     * Returns the latitude in degrees.
     *
     * @return float
     */
    public function lat()
    {
        return $this->lat;
    }

    /**
     * Returns the longitude in degrees.
     *
     * @return float
     */
    public function lng()
    {
        return $this->lng;
    }

    /**
     * Converts to string representation.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf("{'lat': %s, 'lng':  %s}", $this->lat, $this->lng);
    }

}