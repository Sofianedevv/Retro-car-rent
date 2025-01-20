<?php

namespace App\Repository;

use App\Entity\MotorcycleType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MotorcycleType>
 *
 * @method MotorcycleType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MotorcycleType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MotorcycleType[]    findAll()
 * @method MotorcycleType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotorcycleTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MotorcycleType::class);
    }

    public function save(MotorcycleType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MotorcycleType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 