<?php

namespace App\Repository;

use App\Entity\Intervenir;
use App\Entity\Personne;
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
    private $historiqueStatutRepository;

    public function __construct(ManagerRegistry $registry, HistoriqueStatutRepository $historiqueStatutRepository
    )
    {
        parent::__construct($registry, Intervenir::class);
        $this->historiqueStatutRepository = $historiqueStatutRepository;
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
            ->join('i.TypeIntervention', 'ti')
            ->andWhere("ti.nom = 'Signaleur'")
            ->setParameter('probleme', $probleme)
            ->getQuery()
            ->getOneOrNullResult();

    }
    public function findTechnicienByProbleme($probleme){
        return $this->createQueryBuilder('i')
            ->where('i.Probleme = :probleme')
            ->join('i.TypeIntervention', 'ti')
            ->andWhere("ti.nom = 'Technicien'")
            ->setParameter('probleme', $probleme)
            ->getQuery()
            ->getResult();
    }
    public function findAllInterventionByPersonneAndUnresolvedProbleme($probleme, $personne){
        $historiqueStatuts = $this->historiqueStatutRepository->findLatestHistoriqueStatutByProblem();
        $idHistoriqueStatut = [];
        foreach ($historiqueStatuts as $historiqueStatut){
            array_push($idHistoriqueStatut,$historiqueStatut['id']);
        }
        return $this->createQueryBuilder('i')
            ->join('i.Probleme','p')
            ->join('i.TypeIntervention', 't' )
            ->Join('p.HistoriqueStatuts','h')
            ->join('h.Statut','s')
            ->where("s.nom != 'Résolu'")
            ->andWhere("s.nom != 'Archivé'")
            ->andWhere('h.id IN (:historiqueStatut)')
            ->andWhere('i.Personne = :personne')
            ->andWhere("t.nom = 'Technicien'")
            ->andWhere('i.Probleme = :probleme')
            ->setParameter('probleme' , $probleme)
            ->setParameter('personne', $personne)
            ->setParameter('historiqueStatut', $idHistoriqueStatut)
            ->orderBy('p.titre')
            ->getQuery()
            ->getResult();

    }
    public function isInterventionBelongToThisPersonne(Intervenir $intervenir,Personne $personne){
        $sql = $this->createQueryBuilder('i')
            ->join('i.Personne','pe')
            ->where('i.Personne = :personne')
            ->andWhere('i.id = :intervenir')
            ->setParameter('intervenir', $intervenir->getId())
            ->setParameter('personne', $personne)
            ->getQuery()
            ->getResult();

        if($sql) return true;
        else return false;
    }
}
