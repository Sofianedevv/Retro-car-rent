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
