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
    
    public function findAllPaginate(int $page, int $nbr_max_element)
    {
        return $this->createQueryBuilder('p')   
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->setFirstResult(($page-1) * $nbr_max_element)
            ->setMaxResults($nbr_max_element)
            ->getResult()
        ;
    }


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

    public function findAllUnResolvedProblemeByPersonne($personne){
        $historiqueStatuts = $this->historiqueStatutRepository->findLatestHistoriqueStatutByProblem();
        $idHistoriqueStatut = [];
        foreach ($historiqueStatuts as $historiqueStatut){
            array_push($idHistoriqueStatut,$historiqueStatut['id']);
        }
        return $this->createQueryBuilder('p')
            ->Join('p.HistoriqueStatuts','h')
            ->join('h.Statut','s')
            ->join('p.Intervenirs','i')
            ->join('i.TypeIntervention', 't' )
            ->where("s.nom != 'Résolu'")
            ->andWhere('h.id IN (:historiqueStatut)')
            ->andWhere('i.Personne = :personne')
            ->andWhere("t.nom = 'Technicien'")
            ->setParameter('personne', $personne)
            ->setParameter('historiqueStatut', $idHistoriqueStatut)
            ->orderBy('p.titre')
            ->getQuery()
            ->getResult();

    }

}
