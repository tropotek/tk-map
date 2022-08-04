<?php
namespace Tk\Map;

use Tk\ConfigTrait;

/**
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2005 Michael Mifsud
 */
class Placemark
{
    use ConfigTrait;
    
    /**
     * @var LatLng
     */
    public $latlng = null;
    
    /**
     * @var string
     */
    public $address = '';
    

    /**
     *  Construct
     *
     * @param LatLng $latlng
     * @param string $address
     */
    public function __construct($latlng, $address = '')
    {
        $this->address = $address;
        $this->latlng = $latlng;
    }

    /**
     * Create a map point object
     *s
     *
     * @param LatLng $latlng
     * @param string $address
     * @return Placemark
     */
    static function createPlacemark($latlng, $address = '')
    {
        $obj = new self($latlng, $address);
        return $obj;
    }
    
}