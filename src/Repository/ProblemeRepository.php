<?php

namespace App\Repository;

use App\Entity\HistoriqueAction;
use App\Entity\Personne;
use App\Entity\Probleme;
use App\Entity\Statut;
use App\Entity\Categorie;
use App\Entity\TypeIntervention;
use App\Repository\HistoriqueStatutRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method Probleme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Probleme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Probleme[]    findAll()
 * @method Probleme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProblemeRepository extends ServiceEntityRepository
{
    private $historiqueStatutRepository;
    /**
     * @var TokenStorageInterface
     */
     private $personne;


    public function __construct(ManagerRegistry $registry, TokenStorageInterface $tokenStorageInterface, HistoriqueStatutRepository $historiqueStatutRepository)
    {
        parent::__construct($registry, Probleme::class);
        $this->historiqueStatutRepository = $historiqueStatutRepository;
        $this->personne = $tokenStorageInterface->getToken()->getUser();
    }

    public function formatValues($query)
    {
        $str = "(";
        foreach ($query as $val) {
            if($val instanceof Categorie || $val instanceof Statut)
                $str .= $val->getId().", ";
            else
                $str .= $val.", ";
        }
        return substr($str, 0, -2).")";
    }

    public function getRequestPagination(
        array $categories,
        array $statuts,
        string $nom,
        string $orderBy = null,
        string $typeIntervention = null
    )
    {
        $em = $this->getEntityManager();


        $parameters = [];

        $parameters['join'] = '';
        $parameters['conditions'] = '';

        if (empty($categories)){
            $categories_raw = $em->getRepository(Categorie::class)->findAll();
            $parameters['categorie_ids'] = $this->formatValues($categories_raw);
        }else{
            $parameters['categorie_ids'] = $this->formatValues($categories);
        }

        if (empty($statuts)){
            $statuts_raw = $em->getRepository(Statut::class)->findAll();
            $parameters['statut_ids'] = $this->formatValues($statuts_raw);
        }else{
            $parameters['statut_ids'] = $this->formatValues($statuts);
        }

        if (empty($nom)){
            $parameters['nom'] = '%';
        }else{
            $parameters['nom'] = '%'.str_replace("\"", "'", $nom).'%';
        }

        //ORDER BY
        if ($orderBy){
            if ($orderBy == 'date_asc'){
                $parameters['orderBy'] = 'maxdate ';
            }
            else if($orderBy == 'date_desc'){
                $parameters['orderBy'] = 'maxdate DESC ';
            }else if($orderBy == 'categorie'){
                $parameters['orderBy'] = 'categorie.nom ';
                $parameters['join'] .= 'INNER JOIN categorie ON probleme.categorie_id = categorie.id ';
            }else if($orderBy == 'priorite'){
                $parameters['orderBy'] = 'priorite.poids DESC ';
                $parameters['join'] .= 'INNER JOIN priorite ON probleme.priorite_id = priorite.id ';
            }else{
                $parameters['orderBy'] = 'statut.nom ';
            }
        }else{
            $parameters['orderBy'] = 'maxdate ';
        }

        if($typeIntervention != null){
            $parameters['join'] .= " 
                INNER JOIN intervenir ON probleme.id = intervenir.probleme_id
                INNER JOIN personne ON intervenir.personne_id = personne.id 
                INNER JOIN type_intervention ti on intervenir.type_intervention_id = ti.id";

                $parameters['conditions'] = "
                AND personne.id = ".$this->personne->getId()."
                AND ti.nom = '".$typeIntervention."'";
        }

        $sql = '
            SELECT probleme.*
            FROM historique_statut AS t1 LEFT OUTER JOIN 
            (
                SELECT probleme_id, MAX(date) as maxdate
                FROM historique_statut
                GROUP BY probleme_id
            )AS t2 USING (probleme_id)
            INNER JOIN statut ON t1.statut_id = statut.id
            INNER JOIN probleme ON t1.probleme_id = probleme.id
            '.$parameters['join'].'
            WHERE t1.date = t2.maxdate
            AND statut.id IN '.$parameters['statut_ids'].'
            AND probleme.categorie_id IN '.$parameters['categorie_ids'].'
            AND titre LIKE "'.$parameters['nom'].'"
            '.$parameters['conditions'].'
            GROUP BY t1.probleme_id
            ORDER BY '.$parameters['orderBy']
        ;
        return $sql;
    }
    
    public function findPaginateByCategoryAndName(
        int $page,
        int $nbr_max_element,
        array $categories,
        array $statuts,
        string $nom,
        string $orderBy,
        string $typeIntervention = null
    )
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = $this->getRequestPagination($categories, $statuts, $nom, $orderBy, $typeIntervention);
        $sql .= 'LIMIT '.$nbr_max_element.' OFFSET '.($page-1) * $nbr_max_element;
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $problemes = [];
        foreach ($stmt->fetchAll() as $probleme) {
            array_push($problemes, $this->getEntityManager()->getRepository(Probleme::class)->findOneBy(['id' => $probleme['id']]));
        }
        return $problemes;
    }

    public function findAllByCategoryAndName(
        int $page,
        int $nbr_max_element,
        array $categories,
        array $statuts,
        string $nom,
        string $orderBy = null,
        string $typeIntervention = null
    )
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = $this->getRequestPagination($categories, $statuts, $nom, $orderBy, $typeIntervention);

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
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
    public function findByProblemeByPersonne($probleme,$personne){
        return $this->createQueryBuilder('p')
            ->join('p.Intervenirs','i')
            ->join ('i.TypeIntervention','t')
            ->where('i.Personne = :personne')
            ->andWhere("t.nom = 'Signaleur'")
            ->andWhere('p.id = :probleme')
            ->setParameter('probleme', $probleme)
            ->setParameter('personne', $personne)
            ->getQuery()
            ->getResult();
    }

    public function findAllProblemeByCategorie(Categorie $categorie){
        return $this->createQueryBuilder('p')
            ->join('p.Categorie','c')
            ->where('c.nom = :categorie')
            ->setParameter('categorie', $categorie->getNom())
            ->getQuery()
            ->getResult();

    }

}
