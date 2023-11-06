<?php
namespace App\Coordinates\lib;

/**
 * Get boundaries for coordinates
 */
class Boundary
{

    /**
     * Get boundaries for coordinates
     * @param float $latitude
     * @param float $longitude
     * @param float $distance
     * @param string $unit  Km(K), miles(M), nautical miles(NM)
     * @return array limits
     */
    public static function getBoundary($latitude, $longitude, $distance, $unit){
        $latRange = ($distance / Geoconstants::getUnitsindegree($unit));
        $longRange = (($distance / Geoconstants::getUnitsindegree($unit)) / cos(deg2rad($latitude)));
          return  ['north'=>$latitude + $latRange, 'south'=>$latitude - $latRange, 'east'=>$longitude + $longRange, 'west'=>$longitude - $longRange];
    }
}