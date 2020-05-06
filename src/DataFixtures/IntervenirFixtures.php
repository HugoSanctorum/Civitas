<?php

namespace App\DataFixtures;

use App\Entity\Intervenir;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class IntervenirFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $intervenir = new Intervenir();
        $intervenir->setPersonne($this->getReference("hugo_duporge@ens.univ-artois.fr"));
        $intervenir->setProbleme($this->getReference("123456789"));
        $intervenir->setDescription('Signaleur');
        $intervenir->setCreatedAt(new \DateTime('now'));
        $manager->persist($intervenir);
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
            PersonneFixtures::class,
            ProblemeFixtures::class,
        );
    }
}
