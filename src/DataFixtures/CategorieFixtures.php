<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = array(
            array(
                "nom" => "Dégradation",
                "couleur" => "red",
                "icone" => "skull",
            ),
            array(
                "nom" => "Incendie",
                "couleur" => "darkred",
                "icone" => "fire",
            ),
            array(
                "nom" => "Innondation",
                "couleur" => "blue",
                "icone" => "water",
            ),
            array(
                "nom" => "Accident de voiture",
                "couleur" => "orange",
                "icone" => "car-crash",
            ),
            array(
                "nom" => "Nuisance sonore",
                "couleur" => "darkgreen",
                "icone" => "volume-up",
            ),
            array(
                "nom" => "Vandalisme",
                "couleur" => "purple",
                "icone" => "angry",
            )
        );

        foreach ($categories as $category){
            $categorie = new Categorie();
            $categorie->setNom($category["nom"]);
            $categorie->setCouleur($category["couleur"]);
            $categorie->seticone($category["icone"]);
            $manager->persist($categorie);
            $this->addReference($categorie->getNom(),$categorie);
        }
        $manager->flush();
    }
}
