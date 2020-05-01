<?php

namespace App\DataFixtures;

use App\Entity\Statut;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatutFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $statut = new Statut();
        $statut->setNom("Nouvelle Demande");
        $this->addReference($statut->getNom(),$statut);
        $manager->persist($statut);

        $statutResolu = new Statut();
        $statutResolu->setNom("Résolu");
        $this->addReference($statutResolu->getNom(),$statutResolu);
        $manager->persist($statutResolu);
        $manager->flush();
    }
}
