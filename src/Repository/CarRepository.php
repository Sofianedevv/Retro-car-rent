<?php

namespace App\Repository;

use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function findBestRated(int $limit = 4): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.reviews', 'r') 
            ->having('COUNT(r.id) > 0')
            ->orderBy('AVG(r.rating)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
//    public function findCarsByFiltersWithPagination(array $filters, int $page = 1, int $limit = 6): Paginator
//    {
//        $queryBuilder = $this->createQueryBuilder('v');
//
//        // Appliquez les filtres
//        if (!empty($filters['brand'])) {
//            $queryBuilder->andWhere('v.brand = :brand')
//                ->setParameter('brand', $filters['brand']);
//        }
//        if (!empty($filters['minPrice'])) {
//            $queryBuilder->andWhere('v.price >= :minPrice')
//                ->setParameter('minPrice', $filters['minPrice']);
//        }
//        if (!empty($filters['maxPrice'])) {
//            $queryBuilder->andWhere('v.price <= :maxPrice')
//                ->setParameter('maxPrice', $filters['maxPrice']);
//        }
//        // Ajoutez d'autres filtres ici...
//
//        // Pagination
//        $queryBuilder->setFirstResult(($page - 1) * $limit)
//            ->setMaxResults($limit);
//
//        return new Paginator($queryBuilder);
//    }

        public function findNbSeats():array
        {
            $results = $this->createQueryBuilder('c')
                ->select('c.nbSeats')
                ->distinct()
                ->orderBy('c.nbSeats')
                ->getQuery()
                ->getResult();
            return array_column($results, 'nbSeats');
        }

        public function findNbDoors():array
        {
            $results = $this->createQueryBuilder('c')
                ->select('c.nbDoors')
                ->distinct()
                ->orderBy('c.nbDoors')
                ->getQuery()
                ->getResult();

            return array_column( $results,  'nbDoors');

            
        }
        public function findAllTransmissions():array 
        {
            $results = $this->createQueryBuilder('c')
                ->select('c.transmission')
                ->distinct()
                ->getQuery()
                ->getResult();
            return array_column( $results, 'transmission');

        }
    


}
