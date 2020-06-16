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
//        $faker = Faker\Factory::create('fr_FR');
//
//        $communes = [
//            $this->getReference("Lens"),
//            $this->getReference("Lille"),
//            $this->getReference("Bruay-la-Buissière"),
//            $this->getReference("Béthune"),
//            $this->getReference("Liévin")
//        ];
//
//        $prefixes = ["rue", "impasse", "boulevard", "avenue", "allee", "place"];
//
//        $rues = [];
//
//        foreach($communes as $commune){
//            foreach($prefixes as $prefixe){
//                $request =  json_decode(file_get_contents("https://api-adresse.data.gouv.fr/search/?q=".$prefixe."&citycode=".$commune->getCodeInsee()."&type=street&limit=20"));
//                foreach($request->features as $value){
//                    array_push($rues, [
//                        "ville" => $value->properties->city,
//                        "rue" => $value->properties->label
//                    ]);
//                }
//            }
//        }
//        $categories = [
//            $this->getReference("Dégradation"),
//            $this->getReference("Incendie"),
//            $this->getReference("Innondation"),
//            $this->getReference("Accident de voiture"),
//            $this->getReference("Vandalisme")
//        ];
//
//        $priorites = [
//            $this->getReference("Faible"),
//            $this->getReference("Important"),
//            $this->getReference("Urgent")
//        ];
//
//        for($i = 1; $i <= 100; $i++){
//            $probleme = new Probleme();
//            $probleme->setCommune($communes[array_rand($communes)]);
//            $probleme->setCategorie($categories[array_rand($categories)]);
//            $probleme->setPriorite($priorites[array_rand($priorites)]);
//            $probleme->setTitre($faker->sentence($nbWords = 3, $variableNbWords = true));
//            $probleme->setDescription($faker->sentence($nbWords = 12, $variableNbWords = true));
//            do {
//                $tab = $rues[array_rand($rues)];
//            }while($probleme->getCommune()->getNom() != $tab["ville"]);
//            $probleme->setLocalisation($tab['rue']);
//            $probleme->setReference('probleme_'.$i);
//            $this->addReference($probleme->getReference(), $probleme);
//            $manager->persist($probleme);
//        }
//
//        $probleme = new Probleme();
//        $probleme->setCommune($this->getReference("Lens"));
//        $probleme->setCategorie($this->getReference("Dégradation"));
//        $probleme->setPriorite($this->getReference("Faible"));
//        $probleme->setTitre("tag à la bombe de peinture");
//        $probleme->setDescription("mur de la mairie");
//        $probleme->setLocalisation("16 Rue de l'Université, 62307 Lens");
//        $probleme->setReference("123456789");
//        $this->addReference($probleme->getReference(),$probleme);
//        $manager->persist($probleme);
        $datas = [
            [
                "Commune" => "Lens",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Tag de peinture",
                "description" => "Tag sur un mur",
                "localisation" => "233 Route De Béthune, 62300 Lens",
                "reference" => "probleme_1"
            ],
            [
                "Commune" => "Lens",
                "Categorie" => "Accident de voiture",
                "Priorité" => "Important",
                "titre" => "Accident de voiture",
                "description" => "Collision entre une moto et une voiture",
                "localisation" => "49 Chemin Tassette, 62300 Lens",
                "reference" => "probleme_2"
            ],
            [
                "Commune" => "Lille",
                "Categorie" => "Dégradation",
                "Priorité" => "Urgent",
                "titre" => "Vitre brisée",
                "description" => "Vitre brisée sur le mur du magasin",
                "localisation" => "4 Place Gentil Muiron, 59800 Lille",
                "reference" => "probleme_3"
            ],
            [
                "Commune" => "Lens",
                "Categorie" => "Innondation",
                "Priorité" => "Urgent",
                "titre" => "Route inonndée",
                "description" => "Route innondée",
                "localisation" => "Boulevard Du Maréchal Leclerc De Hauteclocque, 62800 Liévin",
                "reference" => "probleme_4"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Dégradation",
                "Priorité" => "Important",
                "titre" => "Poteau electrique tombé",
                "description" => "Poteau electrique tombé",
                "localisation" => "41 Rue François Courtin, 62800 Liévin",
                "reference" => "probleme_5"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Nuisance sonore",
                "Priorité" => "Urgent",
                "titre" => "Voisin trop bruyant",
                "description" => "Pollution sonore quotidienne",
                "localisation" => "5 Rue Henri Cadot, 62700 Bruay-la-Buissière",
                "reference" => "probleme_6"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Incendie",
                "Priorité" => "Urgent",
                "titre" => "Incendie",
                "description" => "début d'incendie dans le parc",
                "localisation" => "16 Rue De Caen, 62800 Liévin",
                "reference" => "probleme_7"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Vandalisme",
                "Priorité" => "Faible",
                "titre" => "Arret de bus détruit",
                "description" => "Arret de bus détruit",
                "localisation" => "Rue Télésphore Et Florent Caudron, 62700 Bruay-la-Buissière",
                "reference" => "probleme_8"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Conteneur de vêtement brulé",
                "description" => "Conteneur de vêtement brulé à l'intérieur et à l'extérieur",
                "localisation" => "3 Rue De Bruay, 62470 Calonne-Ricouart",
                "reference" => "probleme_9"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Vandalisme",
                "Priorité" => "Urgent",
                "titre" => "Maison cambriolée ",
                "description" => "Trâces d'effraction visible sur la maison (fenêtres brisées, serrure de porte cassée",
                "localisation" => "2 Rue D'hesdin, 62470 Calonne-Ricouart",
                "reference" => "probleme_10"
            ],
        ];

        foreach ($datas as $data){
            $probleme = new Probleme();
            $probleme->setCommune($this->getReference($data['Commune']));
            $probleme->setCategorie($this->getReference($data['Categorie']));
            $probleme->setPriorite($this->getReference($data['Priorité']));
            $probleme->setTitre($data['titre']);
            $probleme->setDescription($data['description']);
            $probleme->setLocalisation($data['localisation']);
            $probleme->setReference($data['reference']);
            $this->addReference($probleme->getReference(),$probleme);
            $manager->persist($probleme);
        }

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
