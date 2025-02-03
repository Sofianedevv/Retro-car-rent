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

//    /**
//     * @return Motorcycle[] Returns an array of Motorcycle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Motorcycle
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findBestRated(int $limit = 4): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.reviews', 'r')
            ->groupBy('m.id')
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
