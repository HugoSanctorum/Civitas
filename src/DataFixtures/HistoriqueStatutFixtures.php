<?php

namespace App\DataFixtures;

use App\Entity\HistoriqueStatut;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class HistoriqueStatutFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $historiqueStatut = new HistoriqueStatut();
        $historiqueStatut->setProbleme($this->getReference("123456789"));
        $historiqueStatut->setStatut($this->getReference("Nouvelle Demande"));
        $historiqueStatut->setDate(new \DateTime('now'));
        $historiqueStatut->setDescription("Le problème va être traité");
        $manager->persist($historiqueStatut);

        $historiqueStatut2 = new HistoriqueStatut();
        $historiqueStatut2->setProbleme($this->getReference("987654321"));
        $historiqueStatut2->setStatut($this->getReference("Résolu"));
        $historiqueStatut2->setDate(new \DateTime('now'));
        $historiqueStatut2->setDescription("Le problème est résolu");
        $manager->persist($historiqueStatut2);
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
            ProblemeFixtures::class,
            StatutFixtures::class,
        );
    }
}
