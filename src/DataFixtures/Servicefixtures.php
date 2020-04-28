<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class Servicefixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $service = new Service();
        $service->setNom("Comptabilité");
        $this->addReference($service->getNom(),$service);
        $manager->persist($service);
        $manager->flush();
    }
    public function getOrder(){
        return 1;
    }
}
