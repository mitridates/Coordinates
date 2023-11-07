Coordinates
================

How to use the HAVERSINE formula in Symfony with dql.

The query get results within radius (lat , lng, radius, unit)
 
### Require

[Symfony](https://www.symfony.com/) This context.
[Doctrine](https://www.doctrine-project.org/) for DQL query.

### Get git files

Symfony example, used under src/Coordinates
    
     git clone git://github.com/mitridates/Coordinates.git

### Add DQL function

In a classic symfony bundle add this to  App\Mybundle\Mybundle.php

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

In some entity repository add query

```php
    <?php
    
    namespace App\Mybundle\Repository;
    use App\Coordinates\Boundary;
    use App\Coordinates\Geoconstants;use Doctrine\ORM\EntityRepository;
    
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

### License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.