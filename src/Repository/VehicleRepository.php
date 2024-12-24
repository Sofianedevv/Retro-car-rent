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
}
