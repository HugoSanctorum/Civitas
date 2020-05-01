<?php

namespace App\DataFixtures;

use App\Entity\Commune;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommuneFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $commune = new Commune();
        $commune->setNom("Lens");
        $commune->setMairie("17 Place Jean Jaurès, 62300 Lens, France");
        $commune->setCodePostal("62300");
        $commune->setRegion("Hauts-de-France");
        $this->addReference($commune->getNom(),$commune);
        $commune->addService($this->getReference("Comptabilité"));
        $manager->persist($commune);
        $manager->flush();

        $commune2 = new Commune();
        $commune2->setNom("Lille");
        $commune2->setCodePostal("59000");
        $commune2->setMairie("Hôtel de Ville place Augustin-Laurent, 59033 Lille, France");
        $commune2->setRegion("Nord");
        $this->addReference($commune2->getNom(),$commune2);
        $commune2->addService($this->getReference("Comptabilité"));
        $manager->persist($commune2);
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
            Servicefixtures::class,
        );
    }
}
