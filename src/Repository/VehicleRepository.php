<?php

namespace App\Repository;

use App\Entity\Vehicle;
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
    // Find all vehicles
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

        if (!empty($filters['brand'])) {
            $qb->andWhere('c.brand = :brand')
               ->setParameter('brand', $filters['brand']);
        }

        if (!empty($filters['minPrice'])) {
            $qb->andWhere('c.price >= :minPrice')
               ->setParameter('minPrice', $filters['minPrice']);
        }

        if (!empty($filters['maxPrice'])) {
            $qb->andWhere('c.price <= :maxPrice')
               ->setParameter('maxPrice', $filters['maxPrice']);
        }

        if (!empty($filters['transmission'])) {
            $qb->andWhere('c.transmission = :transmission')
               ->setParameter('transmission', $filters['transmission']);
        }

        if (!empty($filters['fuelType'])) {
            $qb->andWhere('c.fuelType = :fuelType')
               ->setParameter('fuelType', $filters['fuelType']);
        }

        if (!empty($filters['availability'])) {
            $qb->andWhere('c.availability = :availability')
               ->setParameter('availability', true);
        }

        if (!empty($filters['nbSeats'])) {
            $qb->andWhere('c.nbSeats = :nbSeats')
               ->setParameter('nbSeats', $filters['nbSeats']);
        }

        if (!empty($filters['nbDoors'])) {
            $qb->andWhere('c.nbDoors = :nbDoors')
               ->setParameter('nbDoors', $filters['nbDoors']);
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
        return $this->createQueryBuilder('v')
            ->select('DISTINCT c.brand')
            ->from('App\Entity\Car', 'c')
            ->orderBy('c.brand', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function findMotorcyclesByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('m')
            ->from('App\Entity\Motorcycle', 'm');

        if (!empty($filters['brand'])) {
            $qb->andWhere('m.brand = :brand')
               ->setParameter('brand', $filters['brand']);
        }

        if (!empty($filters['minPrice'])) {
            $qb->andWhere('m.price >= :minPrice')
               ->setParameter('minPrice', $filters['minPrice']);
        }

        if (!empty($filters['maxPrice'])) {
            $qb->andWhere('m.price <= :maxPrice')
               ->setParameter('maxPrice', $filters['maxPrice']);
        }

        if (!empty($filters['type'])) {
            $qb->andWhere('m.type = :type')
               ->setParameter('type', $filters['type']);
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

        if (!empty($filters['availability'])) {
            $qb->andWhere('m.availability = :availability')
               ->setParameter('availability', true);
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllMotorcycleBrands(): array
    {
        return $this->createQueryBuilder('v')
            ->select('DISTINCT m.brand')
            ->from('App\Entity\Motorcycle', 'm')
            ->orderBy('m.brand', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function findVansByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('van')
            ->from('App\Entity\Van', 'van');

        if (!empty($filters['brand'])) {
            $qb->andWhere('van.brand = :brand')
               ->setParameter('brand', $filters['brand']);
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

        if (!empty($filters['availability'])) {
            $qb->andWhere('van.availability = :availability')
               ->setParameter('availability', true);
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllVanBrands(): array
    {
        return $this->createQueryBuilder('v')
            ->select('DISTINCT van.brand')
            ->from('App\Entity\Van', 'van')
            ->orderBy('van.brand', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }
}
