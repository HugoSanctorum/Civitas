<?php

namespace App\Repository;

use App\Entity\CompteRendu;
use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompteRendu|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompteRendu|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompteRendu[]    findAll()
 * @method CompteRendu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompteRenduRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompteRendu::class);
    }

    // /**
    //  * @return CompteRendu[] Returns an array of CompteRendu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CompteRendu
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getOneCompteRenduByTechnicien(CompteRendu $compteRendu, Personne $personne)
    {
        return $this->createQueryBuilder('c')
            ->where('c.Personne = :personne')
            ->andWhere('c.id = :compteRendu')
            ->setParameter('compteRendu', $compteRendu)
            ->setParameter('personne', $personne)
            ->getQuery()
            ->getResult();
    }

    public function getAllCompteRenduByTechnicien(Personne $personne){
        return $this->createQueryBuilder('c')
            ->join('c.Probleme','p')
            ->join('p.HistoriqueStatuts','h')
            ->where('c.Personne = :personne')
            ->setParameter('personne', $personne)
            ->getQuery()
            ->getResult();
    }
}
