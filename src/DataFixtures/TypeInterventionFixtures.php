<?php

namespace App\DataFixtures;

use App\Entity\TypeIntervention;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeInterventionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $signaleur = new TypeIntervention();
        $signaleur->setNom("Signaleur");
        $this->addReference($signaleur->getNom(), $signaleur);
        $manager->persist($signaleur);

        $technicien = new TypeIntervention();
        $technicien->setNom("Technicien");
        $this->addReference($technicien->getNom(), $technicien);
        $manager->persist($technicien);

        $gestionnaire = new TypeIntervention();
        $gestionnaire->setNom("Gestionnaire");
        $this->addReference($gestionnaire->getNom(), $gestionnaire);
        $manager->persist($gestionnaire);

        $manager->flush();
    }
}
