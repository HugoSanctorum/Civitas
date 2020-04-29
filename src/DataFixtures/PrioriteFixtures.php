<?php

namespace App\DataFixtures;

use App\Entity\Priorite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PrioriteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $priorite = new Priorite();
        $priorite->setNom("Haute");
        $priorite->setPoids(1);
        $this->addReference($priorite->getNom(),$priorite);
        $manager->persist($priorite);
        $manager->flush();
    }
}
