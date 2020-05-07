<?php

namespace App\Repository;

use App\Entity\Intervenir;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Intervenir|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intervenir|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intervenir[]    findAll()
 * @method Intervenir[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntervenirRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intervenir::class);
    }

    // /**
    //  * @return Intervenir[] Returns an array of Intervenir objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Intervenir
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findSignaleurByProbleme($probleme){
        return $this->createQueryBuilder('i')
            ->where('i.Probleme = :probleme')
            ->andWhere("i.description = 'Signaleur'")
            ->setParameter('probleme', $probleme)
            ->getQuery()
            ->getOneOrNullResult();

    }
    public function findTechnicienByProbleme($probleme){
        return $this->createQueryBuilder('i')
            ->where('i.Probleme = :probleme')
            ->andWhere("i.description = 'Technicien'")
            ->setParameter('probleme', $probleme)
            ->getQuery()
            ->getResult();
    }
}
