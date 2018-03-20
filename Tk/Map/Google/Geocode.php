<?php
namespace Tk\Map\Google;

/**
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2005 Michael Mifsud
 */
class Geocode
{
    
    /**
     * @var string
     */
    protected $url = 'http://maps.google.com.au/maps/api/geocode/json?sensor=false';
    
    /**
     * @var string
     */
    protected $key = '';


    /**
     * Create
     *
     * @param string $key
     * @param null $url
     */
    public function __construct($key = '', $url = null)
    {
        $this->key = $key;
        if ($url)
            $this->url = $url;
    }


    /**
     * Create
     *
     * @param string $gKey
     * @param null $url
     * @return Geocode
     */
    static function create($gKey = '', $url = null)
    {
        return new self($gKey, $url);
    }


    /**
     * Add a key if you have one.
     *
     * @param string $gKey
     */
    public function setGkey($gKey)
    {
        $this->key = $gKey;
    }


    /**
     * Search google using a get request to get map coordinates...
     *
     * @param string $search
     * @return \stdClass
     */
    public function performRequest($search)
    {
        $url = \Tk\Url::create($this->url)->set('address', $search);
        //$url = sprintf('%s?q=%s&output=%s&key=%s&oe=utf-8', self::$url, urlencode($search), $output, $this->gKey);
        if ($this->key) {
            $url->set('key', $this->key);
        }
        $ch = curl_init();
        curl_setopt($ch, \CURLOPT_URL, $url->toString());
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, 1);
        if (\Tk\Config::getInstance()->exists('system.site.proxy')) {
            curl_setopt($ch, \CURLOPT_PROXY, \Tk\Config::getInstance()->get('system.site.proxy'));
        }
        $response = json_decode(curl_exec($ch), true);
        return $response;
    }
    
    /**
     * Search google for an address and return a placemark object
     *
     * @param string $search
     * @return \Map\Placemark[]
     */
    public function lookup($search)
    {
        $response = $this->performRequest($search);
        if ($response['status'] != 'OK' || !is_array($response['results'])) {
            return array();
        }

        $placemarks = array();
        foreach ($response['results'] as $loc) {
            $placemarks[] = new \Map\Placemark(new \Map\LatLng($loc['geometry']['location']['lat'], $loc['geometry']['location']['lng']), $loc['formatted_address']);
        }
        return $placemarks;
    }
    
    /**
     * Return the most accurate result found
     *
     * @param string $search
     * @return  \Map\Placemark
     */
    public function lookupClosest($search)
    {
        $arr = $this->lookup($search);
        if (count($arr)) {
            return $arr[0];
        }
        return null;
    }
    
    
}