<?php

namespace App\DataFixtures;

use App\Entity\Priorite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PrioriteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
    	$faible = new Priorite();
        $faible->setNom("Faible");
        $faible->setPoids(0);
        $this->addReference($faible->getNom(),$faible);
        $manager->persist($faible);
        $manager->flush();

        $important = new Priorite();
        $important->setNom("Important");
        $important->setPoids(64);
        $this->addReference($important->getNom(),$important);
        $manager->persist($important);
        $manager->flush();

        $urgent = new Priorite();
        $urgent->setNom("Urgent");
        $urgent->setPoids(128);
        $this->addReference($urgent->getNom(),$urgent);
        $manager->persist($urgent);
        $manager->flush();
    }
}
