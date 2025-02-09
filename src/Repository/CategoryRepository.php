<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Vehicle;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }


    public function findCategoriesByVehicle(Vehicle $vehicle): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.vehicles', 'v')
            ->where('v.id = :vehicleId')
            ->setParameter('vehicleId', $vehicle->getId())
            ->getQuery()
            ->getResult();
    }
}
