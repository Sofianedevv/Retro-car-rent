<?php

namespace App\Repository;

use App\Entity\ReservationVehicleOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReservationVehicleOption>
 */
class ReservationVehicleOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationVehicleOption::class);
    }
}
