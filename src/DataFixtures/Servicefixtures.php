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
        $services = ["Comptabilité", "Logistique", "Administratif", "Voierie", "Assainissement"];

        foreach ($services as $service){
            $entity = new Service();
            $entity->setNom($service);
            $this->addReference($entity->getNom(), $entity);
            $manager->persist($entity);
        }
        $manager->flush();
    }
    public function getOrder(){
        return 1;
    }
}