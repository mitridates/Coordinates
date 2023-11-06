<?php
namespace App\Coordinates\lib;
/**
 * Constants to calculate distances
 */
abstract class Geoconstants
{

    final public const EARTH_RADIUS_KM = 6372;//6372.797;
    final public const EARTH_RADIUS_MILES = 3963.0;
    final public const EARTH_RADIUS_NAUTIC_MILES = 3963.0;
     //Based in average earth diameter
    final public const KM_IN_DEGREE = 111.13384;// 1 degree = 111.13384 km
    final public const MILES_IN_DEGREE = 69.05482;//1 degree = 69.05482 miles
    final public const MILES_NAUTIQUES_IN_DEGREE = 59.97662; // 1 degree = 59.97662 Nautical miles

    /**
     * Get km/miles in a degree
     * @param string $unit
     * @return float km/miles in a degree
     */
    public static function getUnitsindegree($unit){
          return match ($unit) {
              'K' => self::KM_IN_DEGREE,
              'M' => self::MILES_IN_DEGREE,
              'MN' => self::MILES_NAUTIQUES_IN_DEGREE,
              default => self::KM_IN_DEGREE,
          };
    }

    /**
     * EarthRadius
     * @param string $unit
     * @return float Earth radius in km/miles/Nautical miles
     */
    public static function getEarthRadius($unit){
          return match (strtoupper($unit)) {
              'K' => self::EARTH_RADIUS_KM,
              'M' => self::EARTH_RADIUS_MILES,
              'MN' => self::EARTH_RADIUS_NAUTIC_MILES,
              default => self::EARTH_RADIUS_KM,
          };
    }
}