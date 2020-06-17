<?php

namespace App\DataFixtures;

use App\Entity\CompteRendu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompteRenduFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
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
            PersonneFixtures::class
        );
    }
}
