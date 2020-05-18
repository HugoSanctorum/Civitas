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
        $intervenir->setTypeIntervention($this->getReference("Signaleur"));
        $intervenir->setCreatedAt(new \DateTime('now'));
        $this->addReference('intervention',$intervenir);
        $manager->persist($intervenir);

        $intervenir2 = new Intervenir();
        $intervenir2->setPersonne($this->getReference("hugo_sanctorum@ens.univ-artois.fr"));
        $intervenir2->setProbleme($this->getReference("987654321"));
        $intervenir2->setTypeIntervention($this->getReference("Signaleur"));
        $intervenir2->setCreatedAt(new \DateTime('now'));
        $manager->persist($intervenir2);

        $intervenir3 = new Intervenir();
        $intervenir3->setPersonne($this->getReference("hugo_duporge@ens.univ-artois.fr"));
        $intervenir3->setProbleme($this->getReference("9189189189189"));
        $intervenir3->setTypeIntervention($this->getReference("Signaleur"));
        $intervenir3->setCreatedAt(new \DateTime('now'));
        $manager->persist($intervenir3);

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
            TypeInterventionFixtures::class,
        );
    }
}
