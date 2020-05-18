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
        $statutNew->setDescription("Le problème a été soumis à votre commune mais n'a pas encore été approuvé");
        $statutNew->setCouleur("red");
        $statutNew->setIcone("folder-plus");
        $this->addReference($statutNew->getNom(),$statutNew);
        $manager->persist($statutNew);

        $statutOuvert = new Statut();
        $statutOuvert->setNom("Ouvert");
        $statutOuvert->setDescription("Le problème a été approuvé par la commune mais aucun technicien ne travaille dessus actuellement");
        $statutOuvert->setCouleur("orange");
        $statutOuvert->setIcone("folder-open");
        $this->addReference($statutOuvert->getNom(),$statutOuvert);
        $manager->persist($statutOuvert);

        $statutAffecte = new Statut();
        $statutAffecte->setNom("Affecté");
        $statutAffecte->setDescription("Un technicien a été affecté au problème. Il s'en chargera bientôt !");
        $statutAffecte->setCouleur("blue");
        $statutAffecte->setIcone("hard-hat");
        $this->addReference($statutAffecte->getNom(),$statutAffecte);
        $manager->persist($statutAffecte);

        $statutTraitement = new Statut();
        $statutTraitement->setNom("En cours de traitement");
        $statutTraitement->setDescription("Le technicien travaille actuellement sur le problème");
        $statutTraitement->setCouleur("darkpurple");
        $statutTraitement->setIcone("wrench");
        $this->addReference($statutTraitement->getNom(),$statutTraitement);
        $manager->persist($statutTraitement);

        $statutResolu = new Statut();
        $statutResolu->setNom("Résolu");
        $statutResolu->setDescription("Le problème a été réglé !");
        $statutResolu->setCouleur("green");
        $statutResolu->setIcone("check-circle");
        $this->addReference($statutResolu->getNom(),$statutResolu);
        $manager->persist($statutResolu);

        $statutResolu = new Statut();
        $statutResolu->setNom("Archivé");
        $statutResolu->setCouleur("cadetblue");
        $statutResolu->setIcone("archive");
        $this->addReference($statutResolu->getNom(),$statutResolu);
        $manager->persist($statutResolu);


        $manager->flush();
    }
}
