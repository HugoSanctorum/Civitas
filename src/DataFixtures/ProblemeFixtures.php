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
        $this->addReference($probleme->getReference(),$probleme);
        $manager->persist($probleme);

        $probleme2 = new Probleme();
        $probleme2->setCommune($this->getReference("Lille"));
        $probleme2->setCategorie($this->getReference("Dégradation"));
        $probleme2->setPriorite($this->getReference("Haute"));
        $probleme2->setTitre("poubelle renversée");
        $probleme2->setDescription("Déchets à terre ");
        $probleme2->setDateDeDeclaration(new \DateTime('now'));
        $probleme2->setLocalisation("Mairie de Lille");
        $probleme2->setReference("987654321");
        $this->addReference($probleme2->getReference(),$probleme2);
        $manager->persist($probleme2);

        $probleme3 = new Probleme();
        $probleme3->setCommune($this->getReference("Lille"));
        $probleme3->setCategorie($this->getReference("Dégradation"));
        $probleme3->setPriorite($this->getReference("Haute"));
        $probleme3->setTitre("Accident de voiture");
        $probleme3->setDescription("Déchets à terre ");
        $probleme3->setDateDeDeclaration(new \DateTime('now'));
        $probleme3->setLocalisation(" 12 Rue de la paix");
        $probleme3->setReference("9189189189189");
        $this->addReference($probleme3->getReference(),$probleme3);
        $manager->persist($probleme3);

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
