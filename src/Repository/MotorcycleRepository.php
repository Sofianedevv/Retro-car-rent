<?php

namespace App\Repository;

use App\Entity\Motorcycle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Motorcycle>
 */
class MotorcycleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motorcycle::class);
    }

    public function findBestRated(int $limit = 4): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.reviews', 'r')
            ->having('COUNT(r.id) > 0')
            ->orderBy('AVG(r.rating)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findAllEngineTypes(): array
    {
        $results = $this->createQueryBuilder('m')
            ->select('m.type')
            ->distinct()
            ->getQuery()
            ->getResult();
        return array_column($results, 'type');
    }
}
