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
        $this->addReference($statutNew->getNom(),$statutNew);
        $manager->persist($statutNew);

        $statutOuvert = new Statut();
        $statutOuvert->setNom("Ouvert");
        $this->addReference($statutOuvert->getNom(),$statutOuvert);
        $manager->persist($statutOuvert);

        $statutAffecte = new Statut();
        $statutAffecte->setNom("Affecté");
        $this->addReference($statutAffecte->getNom(),$statutAffecte);
        $manager->persist($statutAffecte);

        $statutTraitement = new Statut();
        $statutTraitement->setNom("En cours de traitement");
        $this->addReference($statutTraitement->getNom(),$statutTraitement);
        $manager->persist($statutTraitement);

        $statutTraite = new Statut();
        $statutTraite->setNom("Traité");
        $this->addReference($statutTraite->getNom(),$statutTraite);
        $manager->persist($statutTraite);


        $statutResolu = new Statut();
        $statutResolu->setNom("Résolu");
        $this->addReference($statutResolu->getNom(),$statutResolu);
        $manager->persist($statutResolu);

        $manager->flush();
    }
}
