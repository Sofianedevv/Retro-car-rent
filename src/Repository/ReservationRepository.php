<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeImmutable;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

public function findOverlappingReservations(DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin) {
        
    return $this->createQueryBuilder('r')
        ->where('r.startDate < :endDate AND r.endDate > :startDate')
        ->setParameter('startDate', $dateDebut)
        ->setParameter('endDate', $dateFin)
        ->getQuery()
        ->getResult();
}
}
