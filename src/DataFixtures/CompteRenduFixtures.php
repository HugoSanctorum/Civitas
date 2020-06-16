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
        for($i = 1; $i <= 10; $i++){
            $compteRendu = new CompteRendu();
            $compteRendu->setUrlDocument('');
            $compteRendu->setPersonne($this->getReference("RogerDupont@ens.univ-artois.fr"));
            $compteRendu->setProbleme($this->getReference("probleme_".$i));
            $compteRendu->setIntervenir($this->getReference('intervention_1'));
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
