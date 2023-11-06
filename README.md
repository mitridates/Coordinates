Coordinates
================

Some libs and dql function to get results within radius for Symfony. 
Thanks to [Hans Arnholm](https://gist.github.com/cypres) 
for libraries mentioned in [Credits](#credits)
 
### Require

[Doctrine](https://www.doctrine-project.org/) for DQL query.

### Get git files

In Symfony I use bundle namespace (App\Mybundle) 
and store this files unther src/Coordinates
    
     git clone git://github.com/mitridates/Coordinates.git

### If used in symfony bundle

add the custom DQL function

```php
        <?php
        namespace App\Mybundle;
        
        use App\Coordinates\DQL\DistanceHaversine;
        use Symfony\Component\HttpKernel\Bundle\Bundle;
        
        class Mybundle extends Bundle
        {
        
            public function boot()
            {
                $em = $this->container->get('doctrine.orm.entity_manager');
                $em->getConfiguration()->addCustomNumericFunction(
                    "DISTANCE_HAVERSINE", DistanceHaversine::class
                );
            }
        }
````    

In Symfony Repository add query to some Entity Repository

```php
    <?php
    
    namespace App\Mybundle\Repository;
    use App\Coordinates\lib\Boundary;
    use App\Coordinates\lib\Geoconstants;
    use Doctrine\ORM\EntityRepository;
    
    /**
     * Class MyentitywithlatlngcoordinatesRepository
     * @package App\Mybundle\Repository
     */
    class MyentitywithlatlngcoordinatesRepository extends EntityRepository
    { 
        /**
         * Get results within radius.
         * @param $lat
         * @param $lng
         * @param $radius
         * @param $unit
         * @return array;
         */
        public function findNearByLatLng($lat, $lng, $radius, $unit)
        {
            $alias = 'g';
            $qb = $this->createQueryBuilder($alias);
            $qb->select($alias);
    
            $boundary = Boundary::getBoundary($lat,$lng,$radius,$unit);
    
            /** HAVERSINE FORMULA **/
            $qb->addSelect('(:EarthRadius * DISTANCE_HAVERSINE(' . $alias . '.latitude, ' . $alias . '.longitude, :thatlat, :thatlng)) AS distance')
                ->setParameter('thatlat', $lat)
                ->setParameter('thatlng', $lng)
                ->having('distance <= :distance')
                ->setParameter('distance', $radius)
                ->setParameter('EarthRadius', Geoconstants::getEarthRadius($unit));
    
            $qb->where($alias . '.latitude >= :MinLatitude')
                ->andWhere($alias . '.latitude <= :MaxLatitude')
                ->andWhere($alias . '.longitude >= :MinLongitude')
                ->andWhere($alias . '.longitude <= :MaxLongitude')
                ->setParameter('MaxLatitude', $boundary['north'])
                ->setParameter('MinLatitude', $boundary['south'])
                ->setParameter('MaxLongitude', $boundary['east'])
                ->setParameter('MinLongitude', $boundary['west']);
    
            //ADD FILTER EXPRESSION
    
            //ADD ORDER BY
            $qb->orderBy('distance', 'ASC');
    
            //ADD MAX ROWS
    
            $query = $qb->getQuery();
            return $query->getResult();
        }
    
    } 
````

### Credits
-  [Hans Arnholm](https://gist.github.com/cypres) 
    -   [gPoint](https://gist.github.com/cypres/840476#file-gpoint-php) PHP class to convert Latitude & Longitude coordinates into UTM & Lambert Conic Conformal Northing/Easting coordinates.
    -   [GpointConverter](https://gist.github.com/cypres/840476#file-gpointconverter-class-php) PHP class to convert Latitude+Longitude coordinates into UTM and wise versa.

### License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.