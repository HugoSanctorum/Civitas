<?php

namespace App\DataFixtures;

use App\Entity\HistoriqueAction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class HistoriqueActionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $historiqueAction = new HistoriqueAction();
        $historiqueAction->setAction("1;hugo_duporge@ens.univ-artois.fr;admin;GET_SELF_PROBLEME");
        $historiqueAction->setPersonne($this->getReference("hugo_duporge@ens.univ-artois.fr"));
        $manager->persist($historiqueAction);
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
            PersonneFixtures::class
        );
    }
}
