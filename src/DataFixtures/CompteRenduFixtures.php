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
        for($i = 0; $i < 4; $i++){
            $compteRendu = new CompteRendu();
            $compteRendu->setUrlDocument('https://picsum.photos/1280');
            $compteRendu->setPersonne($this->getReference("personne_".random_int(0, 9)));
            $compteRendu->setProbleme($this->getReference("probleme_".random_int(0, 99)));
            $compteRendu->setIntervenir($this->getReference('intervention_'.$i));
            $compteRendu->setTitre('compte_rendu'.$i);
            $compteRendu->setDate(new \DateTime());
            $manager->persist($compteRendu);
        }
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
            ProblemeFixtures::class,
            PersonneFixtures::class
        );
    }
}
