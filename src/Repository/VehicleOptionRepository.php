<?php

namespace App\Repository;

use App\Entity\VehicleOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Vehicle;

/**
 * @extends ServiceEntityRepository<VehicleOption>
 */
class VehicleOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleOption::class);
    }

//    /**
//     * @return VehicleOption[] Returns an array of VehicleOption objects
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

//    public function findOneBySomeField($value): ?VehicleOption
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findOptionsByVehicle(Vehicle $vehicle): array
{
    return $this->createQueryBuilder('v')
        ->join('v.vehicles', 'vehicle') 
        ->where('vehicle.id = :vehicleId')
        ->setParameter('vehicleId', $vehicle->getId())
        ->getQuery()
        ->getResult(); 
}
}
