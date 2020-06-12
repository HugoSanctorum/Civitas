<?php

namespace App\Repository;

use App\Entity\HistoriqueStatutIntervention;
use App\Entity\Intervenir;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HistoriqueStatutIntervention|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoriqueStatutIntervention|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoriqueStatutIntervention[]    findAll()
 * @method HistoriqueStatutIntervention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriqueStatutInterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriqueStatutIntervention::class);
    }

    // /**
    //  * @return HistoriqueStatutIntervention[] Returns an array of HistoriqueStatutIntervention objects
    //  */
    
    public function getLatestByIntervention(Intervenir $intervenir)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT *
            FROM historique_statut_intervention
            WHERE intervenir_id = :intervenir and date = (
                SELECT MAX(date)
                FROM historique_statut_intervention
            )
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['intervenir' => $intervenir->getId()]);

        $result = $stmt->fetch();
        if($result) return $this->findOneBy(['id' => $result['id']]);
        else return null;
    }
    

    /*
    public function findOneBySomeField($value): ?HistoriqueStatutIntervention
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
