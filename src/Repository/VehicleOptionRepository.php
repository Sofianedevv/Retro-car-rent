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
