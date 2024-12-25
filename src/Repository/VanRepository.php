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

//    /**
//     * @return Van[] Returns an array of Van objects
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

//    public function findOneBySomeField($value): ?Van
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findTopReviewedCars(int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->addSelect('COUNT(r.id) AS HIDDEN reviewCount') // Compte les reviews
            ->leftJoin('c.reviews', 'r') // Jointure avec les reviews
            ->groupBy('c.id') // Groupe par voiture
            ->orderBy('reviewCount', 'DESC') // Tri par nombre de reviews
            ->setMaxResults($limit) // Limite des résultats
            ->getQuery()
            ->getResult();
    }
}
