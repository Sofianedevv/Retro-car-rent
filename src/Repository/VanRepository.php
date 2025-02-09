<?php

namespace App\Repository;

use App\Entity\Van;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Van>
 */
class VanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Van::class);
    }

    public function findBestRated(int $limit = 4): array
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.reviews', 'r')
            ->groupBy('v.id')
            ->having('COUNT(r.id) > 0')
            ->orderBy('AVG(r.rating)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findNbSeats():array
    {
        $results = $this->createQueryBuilder('v')
            ->select('v.nbSeats')
            ->distinct()
            ->orderBy('v.nbSeats')
            ->getQuery()
            ->getResult();
        return array_column($results, 'nbSeats');
    }

    public function findNbDoors():array
    {
        $results = $this->createQueryBuilder('v')
            ->select('v.nbDoors')
            ->distinct()
            ->orderBy('v.nbDoors')
            ->getQuery()
            ->getResult();

        return array_column( $results,  'nbDoors');
   
    }
}
