<?php

namespace App\Repository;

use App\Entity\StatutIntervention;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StatutIntervention|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatutIntervention|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatutIntervention[]    findAll()
 * @method StatutIntervention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutInterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutIntervention::class);
    }

    // /**
    //  * @return StatutIntervention[] Returns an array of StatutIntervention objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StatutIntervention
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
