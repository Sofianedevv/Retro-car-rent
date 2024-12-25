<?php

namespace App\Repository;

use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

//    /**
//     * @return Car[] Returns an array of Car objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Car
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


    /**
     * Récupère les X voitures les plus reviewées
     *
     * @param int $limit Nombre de voitures à récupérer
     * @return Car[] Retourne un tableau des voitures
     */
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
