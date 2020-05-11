<?php

namespace App\DataFixtures;

use App\Entity\Commune;
use App\Services\Geoquery\Geoquery;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommuneFixtures extends Fixture implements DependentFixtureInterface
{
    private $geoquery;

    public function __construct(Geoquery $geoquery){
        $this->geoquery = $geoquery;
    }

    public function load(ObjectManager $manager)
    {
        $commune = new Commune();
        $this->geoquery->populate($commune, "Lens", "62300");
        $this->addReference($commune->getNom(), $commune);
        $commune->addService($this->getReference("Comptabilité"));
        $manager->persist($commune);
        $manager->flush();

        $commune2 = new Commune();
        $this->geoquery->populate($commune2, "Lille", "59800");
        $this->addReference($commune2->getNom(), $commune2);
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
