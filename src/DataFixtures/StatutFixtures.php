<?php

namespace App\DataFixtures;

use App\Entity\Statut;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatutFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $statutNew = new Statut();
        $statutNew->setNom("Nouveau");
        $statutNew->setCouleur("red");
        $statutNew->setIcone("folder-plus");
        $this->addReference($statutNew->getNom(),$statutNew);
        $manager->persist($statutNew);

        $statutOuvert = new Statut();
        $statutOuvert->setNom("Ouvert");
        $statutOuvert->setCouleur("orange");
        $statutOuvert->setIcone("folder-open");
        $this->addReference($statutOuvert->getNom(),$statutOuvert);
        $manager->persist($statutOuvert);

        $statutAffecte = new Statut();
        $statutAffecte->setNom("Affecté");
        $statutAffecte->setCouleur("blue");
        $statutAffecte->setIcone("hard-hat");
        $this->addReference($statutAffecte->getNom(),$statutAffecte);
        $manager->persist($statutAffecte);

        $statutTraitement = new Statut();
        $statutTraitement->setNom("En cours de traitement");
        $statutTraitement->setCouleur("darkpurple");
        $statutTraitement->setIcone("wrench");
        $this->addReference($statutTraitement->getNom(),$statutTraitement);
        $manager->persist($statutTraitement);

        $statutResolu = new Statut();
        $statutResolu->setNom("Résolu");
        $statutResolu->setCouleur("green");
        $statutResolu->setIcone("check-circle");
        $this->addReference($statutResolu->getNom(),$statutResolu);
        $manager->persist($statutResolu);

        $manager->flush();
    }
}
