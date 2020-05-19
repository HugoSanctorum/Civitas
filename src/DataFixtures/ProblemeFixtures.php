<?php

namespace App\DataFixtures;

use App\Services\Probleme\ProblemeService;
use Faker;
use App\Entity\Priorite;
use App\Entity\Probleme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProblemeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $communes = [
            $this->getReference("Lens"),
            $this->getReference("Lille"),
            $this->getReference("Bruay-la-Buissière"),
            $this->getReference("Béthune"),
            $this->getReference("Liévin")
        ];

        $prefixes = ["rue", "impasse", "boulevard", "avenue", "allee", "place"];

        $rues = [];

        foreach($prefixes as $prefixe){
            foreach($communes as $commune){
                $request =  json_decode(file_get_contents("https://api-adresse.data.gouv.fr/search/?q=".$prefixe."&citycode=".$commune->getCodeInsee()."&type=street&limit=20"));
                foreach($request->features as $key => $value){
                    if($key == 'properties'){
                        array_push($rues, $value->properties->name);
                    }
                }
            }
        }
        dd($rues);
        $categories = [
            $this->getReference("Dégradation"),
            $this->getReference("Incendie"),
            $this->getReference("Innondation"),
            $this->getReference("Accident de voiture"),
            $this->getReference("Vandalisme")
        ];

        $priorites = [
            $this->getReference("Faible"),
            $this->getReference("Important"),
            $this->getReference("Urgent")
        ];

        for($i = 0; $i < 100; $i++){
            $probleme = new Probleme();
            $probleme->setCommune($communes[array_rand($communes)]);
            $probleme->setCategorie($categories[array_rand($categories)]);
            $probleme->setPriorite($priorites[array_rand($priorites)]);
            $probleme->setTitre($faker->sentence($nbWords = 3, $variableNbWords = true));
            $probleme->setDescription($faker->sentence($nbWords = 12, $variableNbWords = true));
            $probleme->setLocalisation($rues(array_rand($rues)));
            $probleme->setReference('probleme_'.$i);
            $this->addReference($probleme->getReference(),$probleme);
            $manager->persist($probleme);
        }

        $probleme = new Probleme();
        $probleme->setCommune($this->getReference("Lens"));
        $probleme->setCategorie($this->getReference("Dégradation"));
        $probleme->setPriorite($this->getReference("Faible"));
        $probleme->setTitre("tag à la bombe de peinture");
        $probleme->setDescription("mur de la mairie");
        $probleme->setLocalisation("16 Rue de l'Université, 62307 Lens");
        $probleme->setReference("123456789");
        $this->addReference($probleme->getReference(),$probleme);
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
