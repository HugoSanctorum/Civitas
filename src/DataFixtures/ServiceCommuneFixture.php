<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Commune;
use App\Entity\Service;

class ServiceCommuneFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $commune = new Commune();
        $commune->setNom("Lens");
        $commune->setCodePostal("62300");
        $commune->setRegion("Hauts-de-France");

        $service = new Service();
        $service->setNom("Comptabilité");

        $commune->addService($service);
        $manager->persist($commune);
        $manager->persist($service);


        $manager->flush();
    }

}
