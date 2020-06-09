<?php

namespace App\DataFixtures;

use App\Entity\Statut;
use App\Entity\StatutIntervention;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatutInterventionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $datas = [
            [
                "nom" => "En attente de révision",
                "desc" => "L'intervention a été crée par un gestionnaire et est en attente de révision par un technicien"
            ],
            [
                "nom" => "Acceptée",
                "desc" => "L'intervention a été accepté par le technicien"
            ],
            [
                "nom" => "En traitement",
                "desc" =>  "L'intervention est en cours de traitement par le technicien"
            ],
            [
                "nom" => "Suspendue",
                "desc" => "Le technicien a suspendu le traitement de l'intervention"
            ],
            [
                "nom" => "Terminée",
                "desc" => "Le technicien a terminé l'intervention"
            ],
            [
                "nom" => "Refusée",
                "desc" => "Le technicien a refusé l'intervention"
            ],
            [
                "nom" => "Annulée",
                "desc" => "L'intervention a été annulé par le technicien, vous pouvez néanmoins la consulter"
            ]
        ];
        foreach ($datas as $data) {
            $statutIntervention = new StatutIntervention();
            $statutIntervention->setNom($data["nom"]);
            $statutIntervention->setDescription($data["desc"]);
            $this->addReference($statutIntervention->getNom(), $statutIntervention);
            $manager->persist($statutIntervention);
        }
        $manager->flush();
    }
}
