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
        $commune->setCodePostal("62300");
        $commune->setRegion("Hauts-de-France");
        $this->addReference("Lens",$commune);
        $commune->addService($this->getReference("Comptabilité"));
        $manager->persist($commune);
        $manager->flush();

        $commune2 = new Commune();
        $commune2->setNom("Lille");
        $commune2->setCodePostal("59000");
        $commune2->setRegion("Nord");
        $this->addReference("Lille",$commune2);
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
