<?php

namespace App\Repository;

use App\Entity\HistoriqueAction;
use App\Repository\HistoriqueStatutRepository;
use App\Entity\Probleme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Probleme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Probleme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Probleme[]    findAll()
 * @method Probleme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProblemeRepository extends ServiceEntityRepository
{
    private $historiqueStatutRepository;

    public function __construct(ManagerRegistry $registry, HistoriqueStatutRepository $historiqueStatutRepository)
    {
        parent::__construct($registry, Probleme::class);
        $this->historiqueStatutRepository = $historiqueStatutRepository;
    }

    // /**
    //  * @return Probleme[] Returns an array of Probleme objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Probleme
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */





    public function findAllUnresolvedProblem()
    {

        $historiqueStatuts = $this->historiqueStatutRepository->findLatestHistoriqueStatutByProblem();
        $idHistoriqueStatut = [];
        foreach ($historiqueStatuts as $historiqueStatut){
            array_push($idHistoriqueStatut,$historiqueStatut['id']);
        }
         return $this->createQueryBuilder('p')
             ->Join('p.HistoriqueStatuts','h')
             ->join('h.Statut','s')
             ->where("s.nom != 'Résolu'")
             ->andWhere('h.id IN (:historiqueStatut)')
             ->setParameter('historiqueStatut', $idHistoriqueStatut)
             ->orderBy('p.titre')
             ->getQuery()
             ->getResult();

    }

    public function findMaxId(){
        return $this->createQueryBuilder('p')
            ->select('MAX(p.id)')
            ->getQuery()
            ->getOneOrNullResult();
    }

}
