<?php

namespace App\DataFixtures;

use App\Entity\HistoriqueStatut;
use App\Entity\Intervenir;
use App\Entity\Probleme;
use App\Entity\Statut;
use App\Repository\PersonneRepository;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class HistoriqueStatutFixtures extends Fixture implements DependentFixtureInterface
{
    private $cpt;
    private $offset;

    public function createHistoriqueStatut(string $statut, Probleme $probleme){
        $historiqueStatut = new HistoriqueStatut();
        $historiqueStatut->setProbleme($probleme);
        $date = new \DateTime();
        $date->add(new DateInterval('PT'.$this->offset.'H'));
        $historiqueStatut->setDate($date);
        $this->offset++;
        $historiqueStatut->setStatut($this->getReference($statut));
        return $historiqueStatut;
    }

    public function createIntervenir(Probleme $probleme){
        $inter = new Intervenir();
        $inter->setProbleme($probleme);
        $inter->setPersonne($this->getReference("personne_".random_int(0, 9)));
        $inter->setTypeIntervention($this->getReference("Technicien"));
        $inter->setCreatedAt(new \DateTime());
        $this->addReference('intervention_'.$this->cpt++, $inter);
        return $inter;
    }

    public function load(ObjectManager $manager)
    {
        $this->cpt = 0;

        $statuts = [
            $this->getReference("Nouveau"),
            $this->getReference("Ouvert"),
            $this->getReference("Affecté"),
            $this->getReference("En cours de traitement"),
            $this->getReference("Résolu")
        ];

        $problemes = [];
        for($i = 0; $i < 100; $i++){
            array_push($problemes, $this->getReference("probleme_".$i));
        }

        foreach ($problemes as $probleme) {
            $this->offset = 0;
            $statut = $statuts[array_rand($statuts)];

            $manager->persist($this->createHistoriqueStatut("Nouveau", $probleme));

            if ($statut->getNom() == "Ouvert") {
                $manager->persist($this->createHistoriqueStatut("Ouvert", $probleme));
            } else if ($statut->getNom() == "Affecté") {
                $manager->persist($this->createHistoriqueStatut("Ouvert", $probleme));
                $manager->persist($this->createHistoriqueStatut("Affecté", $probleme));
                $manager->persist($this->createIntervenir($probleme));
            } else if ($statut->getNom() == "En cours de traitement") {
                $manager->persist($this->createHistoriqueStatut("Ouvert", $probleme));
                $manager->persist($this->createHistoriqueStatut("Affecté", $probleme));
                $manager->persist($this->createHistoriqueStatut("En cours de traitement", $probleme));
                $manager->persist($this->createIntervenir($probleme));
            } else if ($statut->getNom() == "Résolu") {
                $manager->persist($this->createHistoriqueStatut("Ouvert", $probleme));
                $manager->persist($this->createHistoriqueStatut("Affecté", $probleme));
                $manager->persist($this->createHistoriqueStatut("En cours de traitement", $probleme));
                $manager->persist($this->createHistoriqueStatut("Résolu", $probleme));
                $manager->persist($this->createIntervenir($probleme));
            }
        }
        $manager->persist($this->createHistoriqueStatut("Nouveau", $this->getReference('123456789')));
        $manager->flush();
    }
    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return class-string[]
     */
    public function getDependencies()
    {
        return array(
            PersonneFixtures::class,
            ProblemeFixtures::class,
            StatutFixtures::class
        );
    }
}
