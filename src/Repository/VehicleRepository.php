<?php

namespace App\Repository;

use App\Entity\Vehicle;
use App\Entity\Car;
use App\Entity\Motorcycle;
use App\Entity\Van;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicle>
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

//    /**
//     * @return Vehicle[] Returns an array of Vehicle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Vehicle
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

//Find 5 vehicles
public function findCars(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v INSTANCE OF App\Entity\Car')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
    public function findMotorcycles(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v INSTANCE OF App\Entity\Motorcycle')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
    public function findVan(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v INSTANCE OF App\Entity\Van')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findAllCars(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v INSTANCE OF App\Entity\Car')
            ->getQuery()
            ->getResult();
    }
    public function findAllMotorcycles(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v INSTANCE OF App\Entity\Motorcycle')
            ->getQuery()
            ->getResult();
    }
    public function findAllVan(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v INSTANCE OF App\Entity\Van')
            ->getQuery()
            ->getResult();
    }

    public function findCarsByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('c')
            ->from('App\Entity\Car', 'c');

        if (!empty($filters['brand']) && is_array($filters['brand'])) {
            $qb->andWhere('c.brand IN (:brands)')
               ->setParameter('brands', $filters['brand']);
        }

        if (!empty($filters['minPrice'])) {
            $qb->andWhere('c.price >= :minPrice')
               ->setParameter('minPrice', $filters['minPrice']);
        }

        if (!empty($filters['maxPrice'])) {
            $qb->andWhere('c.price <= :maxPrice')
               ->setParameter('maxPrice', $filters['maxPrice']);
        }

        if (!empty($filters['transmission']) && is_array($filters['transmission'])) {
            $qb->andWhere('c.transmission IN (:transmissions)')
               ->setParameter('transmissions', $filters['transmission']);
        }

        if (!empty($filters['fuelType']) && is_array($filters['fuelType'])) {
            $qb->andWhere('c.fuelType IN (:fuelTypes)')
               ->setParameter('fuelTypes', $filters['fuelType']);
        }

        if (!empty($filters['nbSeats']) && is_array($filters['nbSeats'])) {
            $qb->andWhere('c.nbSeats IN (:nbSeatsOptions)')
               ->setParameter('nbSeatsOptions', $filters['nbSeats']);
        }

        if (!empty($filters['nbDoors']) && is_array($filters['nbDoors'])) {
            $qb->andWhere('c.nbDoors IN (:nbDoorsOptions)')
               ->setParameter('nbDoorsOptions', $filters['nbDoors']);
        }

        if (!empty($filters['minYear'])) {
            $qb->andWhere('c.year >= :minYear')
               ->setParameter('minYear', $filters['minYear']);
        }

        if (!empty($filters['maxYear'])) {
            $qb->andWhere('c.year <= :maxYear')
               ->setParameter('maxYear', $filters['maxYear']);
        }

        if (!empty($filters['minMileage'])) {
            $qb->andWhere('c.mileage >= :minMileage')
               ->setParameter('minMileage', $filters['minMileage']);
        }

        if (!empty($filters['maxMileage'])) {
            $qb->andWhere('c.mileage <= :maxMileage')
               ->setParameter('maxMileage', $filters['maxMileage']);
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllCarBrands(): array
    {
        $results = $this->createQueryBuilder('v')
            ->select('c.brand')
            ->from('App\Entity\Car', 'c')
            ->distinct()
            ->orderBy('c.brand', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'brand');
    }

    public function findMotorcyclesByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('m')
            ->from('App\Entity\Motorcycle', 'm');

        if (!empty($filters['brand']) && is_array($filters['brand'])) {
            $qb->andWhere('m.brand IN (:brands)')
               ->setParameter('brands', $filters['brand']);
        }

        if (!empty($filters['minPrice'])) {
            $qb->andWhere('m.price >= :minPrice')
               ->setParameter('minPrice', $filters['minPrice']);
        }

        if (!empty($filters['maxPrice'])) {
            $qb->andWhere('m.price <= :maxPrice')
               ->setParameter('maxPrice', $filters['maxPrice']);
        }

        if (!empty($filters['type']) && is_array($filters['type'])) {
            $qb->andWhere('m.type IN (:types)')
               ->setParameter('types', $filters['type']);
        }

        if (!empty($filters['minEngineCapacity'])) {
            $qb->andWhere('m.engineCapacity >= :minEngineCapacity')
               ->setParameter('minEngineCapacity', $filters['minEngineCapacity']);
        }

        if (!empty($filters['maxEngineCapacity'])) {
            $qb->andWhere('m.engineCapacity <= :maxEngineCapacity')
               ->setParameter('maxEngineCapacity', $filters['maxEngineCapacity']);
        }

        if (!empty($filters['minYear'])) {
            $qb->andWhere('m.year >= :minYear')
               ->setParameter('minYear', $filters['minYear']);
        }

        if (!empty($filters['maxYear'])) {
            $qb->andWhere('m.year <= :maxYear')
               ->setParameter('maxYear', $filters['maxYear']);
        }

        if (!empty($filters['minMileage'])) {
            $qb->andWhere('m.mileage >= :minMileage')
               ->setParameter('minMileage', $filters['minMileage']);
        }

        if (!empty($filters['maxMileage'])) {
            $qb->andWhere('m.mileage <= :maxMileage')
               ->setParameter('maxMileage', $filters['maxMileage']);
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllMotorcycleBrands(): array
    {
        $results = $this->createQueryBuilder('v')
            ->select('m.brand')
            ->from('App\Entity\Motorcycle', 'm')
            ->distinct()
            ->orderBy('m.brand', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'brand');
    }

    public function findVansByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('van')
            ->from('App\Entity\Van', 'van');

        if (!empty($filters['brand']) && is_array($filters['brand'])) {
            $qb->andWhere('van.brand IN (:brands)')
               ->setParameter('brands', $filters['brand']);
        }

        if (!empty($filters['minPrice'])) {
            $qb->andWhere('van.price >= :minPrice')
               ->setParameter('minPrice', $filters['minPrice']);
        }

        if (!empty($filters['maxPrice'])) {
            $qb->andWhere('van.price <= :maxPrice')
               ->setParameter('maxPrice', $filters['maxPrice']);
        }

        if (!empty($filters['minCargoVolume'])) {
            $qb->andWhere('van.cargoVolume >= :minCargoVolume')
               ->setParameter('minCargoVolume', $filters['minCargoVolume']);
        }

        if (!empty($filters['maxCargoVolume'])) {
            $qb->andWhere('van.cargoVolume <= :maxCargoVolume')
               ->setParameter('maxCargoVolume', $filters['maxCargoVolume']);
        }

        if (!empty($filters['minYear'])) {
            $qb->andWhere('van.year >= :minYear')
               ->setParameter('minYear', $filters['minYear']);
        }

        if (!empty($filters['maxYear'])) {
            $qb->andWhere('van.year <= :maxYear')
               ->setParameter('maxYear', $filters['maxYear']);
        }

        if (!empty($filters['minMileage'])) {
            $qb->andWhere('van.mileage >= :minMileage')
               ->setParameter('minMileage', $filters['minMileage']);
        }

        if (!empty($filters['maxMileage'])) {
            $qb->andWhere('van.mileage <= :maxMileage')
               ->setParameter('maxMileage', $filters['maxMileage']);
        }

        if (!empty($filters['nbSeats']) && is_array($filters['nbSeats'])) {
            $qb->andWhere('van.nbSeats IN (:nbSeatsOptions)')
               ->setParameter('nbSeatsOptions', $filters['nbSeats']);
        }

        if (!empty($filters['nbDoors']) && is_array($filters['nbDoors'])) {
            $qb->andWhere('van.nbDoors IN (:nbDoorsOptions)')
               ->setParameter('nbDoorsOptions', $filters['nbDoors']);
        }

        return $qb->getQuery()->getResult();
    }

    

    public function findAllVanBrands(): array
    {
        $results = $this->createQueryBuilder('v')
            ->select('van.brand')
            ->from('App\Entity\Van', 'van')
            ->distinct()
            ->orderBy('van.brand', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'brand');
    }
    public function findAllVehicleByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('v');

            if(!empty($filters['brand']) && is_array($filters['brand'])) {
                $qb->andWhere('v.brand IN (:brands)')
                   ->setParameter('brands', $filters['brand']);
            }

            if (!empty($filters['minPrice'])) {
                $qb->andWhere('v.price >= :minPrice')
                   ->setParameter('minPrice', $filters['minPrice']);
            }
        
            if (!empty($filters['maxPrice'])) {
                $qb->andWhere('v.price <= :maxPrice')
                   ->setParameter('maxPrice', $filters['maxPrice']);
            }

            if (!empty($filters['fuelType']) && is_array($filters['fuelType'])) {
                $qb->andWhere('v.fuelType IN (:fuelTypes)')
                   ->setParameter('fuelTypes', $filters['fuelType']);
            }

            if (!empty($filters['minYear'])) {
                $qb->andWhere('v.year >= :minYear')
                   ->setParameter('minYear', $filters['minYear']);
            }
        
            if (!empty($filters['maxYear'])) {
                $qb->andWhere('v.year <= :maxYear')
                   ->setParameter('maxYear', $filters['maxYear']);
            }

            if (!empty($filters['minMileage'])) {
                $qb->andWhere('v.mileage >= :minMileage')
                   ->setParameter('minMileage', $filters['minMileage']);
            }
        
            if (!empty($filters['maxMileage'])) {
                $qb->andWhere('v.mileage <= :maxMileage')
                   ->setParameter('maxMileage', $filters['maxMileage']);
            }


            return $qb->getQuery()->getResult();
    }

    public function findAllVehicleBrands(): array
    {
       $results = $this->createQueryBuilder('v')
            ->select('v.brand')
            ->distinct()
            ->getQuery()
            ->getScalarResult();
        return array_column($results, 'brand');
    }

    public function findVehicleBySearch (string $search): array
    {
        return $this->createQueryBuilder('v')
            ->select('v')
            ->where('v.brand LIKE :search')
            ->orWhere('v.model LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()
            ->getResult();
    }


    public function findVansBySearch(string $search): array
    {
        return $this->createQueryBuilder('v')
            ->select('van')
            ->from('App\Entity\Van', 'van')
            ->where('van.brand LIKE :search')
            ->orWhere('van.model LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()
            ->getResult();
    }

    public function findCarsBySearch(string $search): array
    {
        return $this->createQueryBuilder('v')
            ->select('c')
            ->from('App\Entity\Car', 'c')
            ->where('c.brand LIKE :search')
            ->orWhere('c.model LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()
            ->getResult();
    }

    public function findMotorcyclesBySearch(string $search): array
    {
        return $this->createQueryBuilder('v')
            ->select('m')
            ->from('App\Entity\Motorcycle', 'm')
            ->where('m.brand LIKE :search')
            ->orWhere('m.model LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()
            ->getResult();
    }

    public function findBestRated(int $limit = 5): array
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.reviews', 'r')
            ->groupBy('v.id')
            ->orderBy('AVG(r.rating)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findSimilar(Vehicle $vehicle, int $limit = 4): array
    {
        $categories = $vehicle->getCategories();
        $categoryIds = array_map(function($category) {
            return $category->getId();
        }, $categories->toArray());

        $type = match (true) {
            $vehicle instanceof Car => 'car',
            $vehicle instanceof Motorcycle => 'motorcycle',
            $vehicle instanceof Van => 'van',
            default => throw new \InvalidArgumentException('Type de véhicule inconnu')
        };

        $qb = $this->createQueryBuilder('v')
            ->leftJoin('v.categories', 'c')
            ->leftJoin('v.reviews', 'r')
            ->where('v.id != :currentId')
            ->andWhere('v INSTANCE OF :type')
            ->setParameter('currentId', $vehicle->getId())
            ->setParameter('type', $type);

        if (!empty($categoryIds)) {
            $qb->andWhere('c.id IN (:categories)')
               ->setParameter('categories', $categoryIds);
        }

        $qb->groupBy('v.id')
           ->addSelect('AVG(r.rating) as HIDDEN avgRating')
           ->addSelect('COUNT(r.id) as HIDDEN reviewCount')
           ->orderBy('avgRating', 'DESC')
           ->addOrderBy('reviewCount', 'DESC')
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

 
    public function findAllFuelTypes(): array 
    {
      $results = $this->createQueryBuilder('v')
            ->select('v.fuelType')
            ->distinct()
            ->getQuery()
            ->getResult();
        return array_column($results, 'fuelType');
    }

    

    public function findYears(): array 
    {
        return $this->createQueryBuilder('v')
            ->select('v.year')
            ->distinct()
            ->orderBy('v.year', 'DESC')
            ->getQuery()
            ->getResult();
    }



}
