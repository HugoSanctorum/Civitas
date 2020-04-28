<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categorie = new Categorie();
        $categorie->setNom("Dégradation");
        $this->addReference("Dégradation",$categorie);
        $manager->persist($categorie);
        $manager->flush();
    }
}
