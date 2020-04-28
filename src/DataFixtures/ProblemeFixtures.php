<?php

namespace App\DataFixtures;

use App\Entity\Priorite;
use App\Entity\Probleme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProblemeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $probleme = new Probleme();
        $probleme->setCommune($this->getReference("Lens"));
        $probleme->setCategorie($this->getReference("Dégradation"));
        $probleme->setPriorite($this->getReference("Haute"));
        $probleme->setTitre("tag à la bombe de peinture");
        $probleme->setDescription("Nique l'état bouh sur le mur de la mairie oulahaha cest pas bien");
        $probleme->setDateDeDeclaration(new \DateTime('now'));
        $probleme->setLocalisation("Mairie de Lens");
        $probleme->setReference("123456789");
        $this->addReference("123456789",$probleme);
        $manager->persist($probleme);
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
            CommuneFixtures::class,
            CategorieFixtures::class,
            PrioriteFixtures::class
        );
    }
}
