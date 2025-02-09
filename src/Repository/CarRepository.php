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


    public function findNbSeats(): array
    {
        $results = $this->createQueryBuilder('c')
            ->select('c.nbSeats')
            ->distinct()
            ->orderBy('c.nbSeats')
            ->getQuery()
            ->getResult();
        return array_column($results, 'nbSeats');
    }

    public function findNbDoors(): array
    {
        $results = $this->createQueryBuilder('c')
            ->select('c.nbDoors')
            ->distinct()
            ->orderBy('c.nbDoors')
            ->getQuery()
            ->getResult();

        return array_column($results, 'nbDoors');


    }

    public function findAllTransmissions(): array
    {
        $results = $this->createQueryBuilder('c')
            ->select('c.transmission')
            ->distinct()
            ->getQuery()
            ->getResult();
        return array_column($results, 'transmission');

    }


}
