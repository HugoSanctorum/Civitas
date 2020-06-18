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
//            $this->getReference("Inondation"),
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
                "Commune" => "Lens",
                "Categorie" => "Dégradation",
                "Priorité" => "Urgent",
                "titre" => "Vitre brisée",
                "description" => "Vitre brisée sur le mur du magasin",
                "localisation" => "33 Rue Des Mouettes, 62300 Lens",
                "reference" => "probleme_3"
            ],
            [
                "Commune" => "Lens",
                "Categorie" => "Inondation",
                "Priorité" => "Urgent",
                "titre" => "Route inondée",
                "description" => "Route inondée",
                "localisation" => "146 Avenue Alfred Van Pelt, 62300 Lens",
                "reference" => "probleme_4"
            ],
            [
                "Commune" => "Lens",
                "Categorie" => "Dégradation",
                "Priorité" => "Important",
                "titre" => "Poteau electrique tombé",
                "description" => "Poteau electrique tombé",
                "localisation" => "7 Rue Gustave Eiffel, 62300 Lens",
                "reference" => "probleme_5"
            ],
            [
                "Commune" => "Lens",
                "Categorie" => "Nuisance sonore",
                "Priorité" => "Urgent",
                "titre" => "Voisin trop bruyant",
                "description" => "Pollution sonore quotidienne",
                "localisation" => "142 Rue Arthur Fauqueur, 62300 Lens",
                "reference" => "probleme_6"
            ],
            [
                "Commune" => "Lens",
                "Categorie" => "Incendie",
                "Priorité" => "Urgent",
                "titre" => "Incendie",
                "description" => "début d'incendie dans le parc",
                "localisation" => "13 Rue Marius Lateur, 62300 Lens",
                "reference" => "probleme_7"
            ],
            [
                "Commune" => "Lens",
                "Categorie" => "Vandalisme",
                "Priorité" => "Faible",
                "titre" => "Arret de bus détruit",
                "description" => "Arret de bus détruit",
                "localisation" => "3 Rue Du Pourquoi Pas, 62300 Lens",
                "reference" => "probleme_8"
            ],
            [
                "Commune" => "Lens",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Conteneur de vêtement brulé",
                "description" => "Conteneur de vêtement brulé à l'intérieur et à l'extérieur",
                "localisation" => "261 Avenue Alfred Maës, 62300 Lens",
                "reference" => "probleme_9"
            ],
            [
                "Commune" => "Lens",
                "Categorie" => "Vandalisme",
                "Priorité" => "Urgent",
                "titre" => "Maison cambriolée ",
                "description" => "Trâces d'effraction visible sur la maison (fenêtres brisées, serrure de porte cassée",
                "localisation" => "67 Rue D'artois, 62300 Lens",
                "reference" => "probleme_10"
            ],
            [
                "Commune" => "Lille",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Tag de peinture",
                "description" => "Tag sur un mur",
                "localisation" => "227 Boulevard De La Liberté, 59800 Lille",
                "reference" => "probleme_11"
            ],
            [
                "Commune" => "Lille",
                "Categorie" => "Accident de voiture",
                "Priorité" => "Important",
                "titre" => "Accident de voiture",
                "description" => "Collision entre une moto et une voiture",
                "localisation" => "11 Rue Guillaume Apollinaire, 59000 Lille",
                "reference" => "probleme_12"
            ],
            [
                "Commune" => "Lille",
                "Categorie" => "Dégradation",
                "Priorité" => "Urgent",
                "titre" => "Vitre brisée",
                "description" => "Vitre brisée sur le mur du magasin",
                "localisation" => "51 Rue Philippe-Laurent Roland, 59800 Lille",
                "reference" => "probleme_13"
            ],
            [
                "Commune" => "Lille",
                "Categorie" => "Inondation",
                "Priorité" => "Urgent",
                "titre" => "Route inonndée",
                "description" => "Route inondée",
                "localisation" => "169 Rue D'arras, 59000 Lille",
                "reference" => "probleme_14"
            ],
            [
                "Commune" => "Lille",
                "Categorie" => "Dégradation",
                "Priorité" => "Important",
                "titre" => "Poteau electrique tombé",
                "description" => "Poteau electrique tombé",
                "localisation" => "120 Rue D'isly, 59000 Lille",
                "reference" => "probleme_15"
            ],
            [
                "Commune" => "Lille",
                "Categorie" => "Nuisance sonore",
                "Priorité" => "Urgent",
                "titre" => "Voisin trop bruyant",
                "description" => "Pollution sonore quotidienne",
                "localisation" => "Cité Brehart, 59000 Lille",
                "reference" => "probleme_16"
            ],
            [
                "Commune" => "Lille",
                "Categorie" => "Incendie",
                "Priorité" => "Urgent",
                "titre" => "Incendie",
                "description" => "début d'incendie dans le parc",
                "localisation" => "4 Residence Sainte Marie, 59000 Lille",
                "reference" => "probleme_17"
            ],
            [
                "Commune" => "Lille",
                "Categorie" => "Vandalisme",
                "Priorité" => "Faible",
                "titre" => "Arret de bus détruit",
                "description" => "Arret de bus détruit",
                "localisation" => "Rue Du Chai, 59000 Lille",
                "reference" => "probleme_18"
            ],
            [
                "Commune" => "Lille",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Conteneur de vêtement brulé",
                "description" => "Conteneur de vêtement brulé à l'intérieur et à l'extérieur",
                "localisation" => "21b Rue Porret, 59800 Lille",
                "reference" => "probleme_19"
            ],
            [
                "Commune" => "Lille",
                "Categorie" => "Vandalisme",
                "Priorité" => "Urgent",
                "titre" => "Maison cambriolée ",
                "description" => "Trâces d'effraction visible sur la maison (fenêtres brisées, serrure de porte cassée",
                "localisation" => "73 Boulevard De La Moselle, 59000 Lille",
                "reference" => "probleme_20"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Tag de peinture",
                "description" => "Tag sur un mur",
                "localisation" => "29 Rue Danton, 62800 Liévin",
                "reference" => "probleme_21"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Accident de voiture",
                "Priorité" => "Important",
                "titre" => "Accident de voiture",
                "description" => "Collision entre une moto et une voiture",
                "localisation" => "12 Rue Clodion, 62800 Liévin",
                "reference" => "probleme_22"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Dégradation",
                "Priorité" => "Urgent",
                "titre" => "Vitre brisée",
                "description" => "Vitre brisée sur le mur du magasin",
                "localisation" => "79 Rue Montgolfier, 62800 Liévin",
                "reference" => "probleme_23"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Inondation",
                "Priorité" => "Urgent",
                "titre" => "Route inonndée",
                "description" => "Route inondée",
                "localisation" => "4 Avenue De La Résistance, 62800 Liévin",
                "reference" => "probleme_24"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Dégradation",
                "Priorité" => "Important",
                "titre" => "Poteau electrique tombé",
                "description" => "Poteau electrique tombé",
                "localisation" => "55 Rue René Cassin, 62800 Liévin",
                "reference" => "probleme_25"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Nuisance sonore",
                "Priorité" => "Urgent",
                "titre" => "Voisin trop bruyant",
                "description" => "Pollution sonore quotidienne",
                "localisation" => "5 Rue Hayez, 62800 Liévin",
                "reference" => "probleme_26"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Incendie",
                "Priorité" => "Urgent",
                "titre" => "Incendie",
                "description" => "début d'incendie dans le parc",
                "localisation" => "5 Rue Vincent Van Gogh, 62800 Liévin",
                "reference" => "probleme_27"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Vandalisme",
                "Priorité" => "Faible",
                "titre" => "Arret de bus détruit",
                "description" => "Arret de bus détruit",
                "localisation" => "20 Rue Edgard Sellier, 62800 Liévin",
                "reference" => "probleme_28"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Conteneur de vêtement brulé",
                "description" => "Conteneur de vêtement brulé à l'intérieur et à l'extérieur",
                "localisation" => "175 Rue Des Marichelles, 62800 Liévin",
                "reference" => "probleme_29"
            ],
            [
                "Commune" => "Liévin",
                "Categorie" => "Vandalisme",
                "Priorité" => "Urgent",
                "titre" => "Maison cambriolée ",
                "description" => "Trâces d'effraction visible sur la maison (fenêtres brisées, serrure de porte cassée",
                "localisation" => "1 Rue Du Conseil De L'europe, 62800 Liévin",
                "reference" => "probleme_30"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Tag de peinture",
                "description" => "Tag sur un mur",
                "localisation" => "37 Rue Gaston Blot, 62700 Bruay-la-Buissière",
                "reference" => "probleme_31"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Accident de voiture",
                "Priorité" => "Important",
                "titre" => "Accident de voiture",
                "description" => "Collision entre une moto et une voiture",
                "localisation" => "39 Rue Pierre Bérégovoy, 62700 Bruay-la-Buissière",
                "reference" => "probleme_32"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Dégradation",
                "Priorité" => "Urgent",
                "titre" => "Vitre brisée",
                "description" => "Vitre brisée sur le mur du magasin",
                "localisation" => "323 Rue Augustin Caron, 62700 Bruay-la-Buissière",
                "reference" => "probleme_33"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Inondation",
                "Priorité" => "Urgent",
                "titre" => "Route inonndée",
                "description" => "Route inondée",
                "localisation" => "34 Rue Des Sports, 62700 Bruay-la-Buissière",
                "reference" => "probleme_34"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Dégradation",
                "Priorité" => "Important",
                "titre" => "Poteau electrique tombé",
                "description" => "Poteau electrique tombé",
                "localisation" => "113 Rue De L'esplanade, 62700 Bruay-la-Buissière",
                "reference" => "probleme_35"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Nuisance sonore",
                "Priorité" => "Urgent",
                "titre" => "Voisin trop bruyant",
                "description" => "Pollution sonore quotidienne",
                "localisation" => "107 Rue Des Martyrs, 62700 Bruay-la-Buissière",
                "reference" => "probleme_36"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Incendie",
                "Priorité" => "Urgent",
                "titre" => "Incendie",
                "description" => "début d'incendie dans le parc",
                "localisation" => "5 Rue Vincent Van Gogh, 62800 Liévin",
                "reference" => "probleme_37"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Vandalisme",
                "Priorité" => "Faible",
                "titre" => "Arret de bus détruit",
                "description" => "Arret de bus détruit",
                "localisation" => "177 Rue De Philippeville, 62700 Bruay-la-Buissière",
                "reference" => "probleme_38"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Conteneur de vêtement brulé",
                "description" => "Conteneur de vêtement brulé à l'intérieur et à l'extérieur",
                "localisation" => "2 Cite Léon Blum, 62700 Bruay-la-Buissière",
                "reference" => "probleme_39"
            ],
            [
                "Commune" => "Bruay-la-Buissière",
                "Categorie" => "Vandalisme",
                "Priorité" => "Urgent",
                "titre" => "Maison cambriolée ",
                "description" => "Trâces d'effraction visible sur la maison (fenêtres brisées, serrure de porte cassée",
                "localisation" => "47 Rue D'alsace, 62700 Bruay-la-Buissière",
                "reference" => "probleme_40"
            ],
            [
                "Commune" => "Béthune",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Tag de peinture",
                "description" => "Tag sur un mur",
                "localisation" => "795 Rue Du Pré Des Sœurs, 62400 Béthune",
                "reference" => "probleme_41"
            ],
            [
                "Commune" => "Béthune",
                "Categorie" => "Accident de voiture",
                "Priorité" => "Important",
                "titre" => "Accident de voiture",
                "description" => "Collision entre une moto et une voiture",
                "localisation" => "296 Rue Des Bruyères, 62400 Béthune",
                "reference" => "probleme_42"
            ],
            [
                "Commune" => "Béthune",
                "Categorie" => "Dégradation",
                "Priorité" => "Urgent",
                "titre" => "Vitre brisée",
                "description" => "Vitre brisée sur le mur du magasin",
                "localisation" => "6 Boulevard Des États Unis, 62400 Béthune",
                "reference" => "probleme_43"
            ],
            [
                "Commune" => "Béthune",
                "Categorie" => "Inondation",
                "Priorité" => "Urgent",
                "titre" => "Route inonndée",
                "description" => "Route inondée",
                "localisation" => "210 Rue De Lille, 62400 Béthune",
                "reference" => "probleme_44"
            ],
            [
                "Commune" => "Béthune",
                "Categorie" => "Dégradation",
                "Priorité" => "Important",
                "titre" => "Poteau electrique tombé",
                "description" => "Poteau electrique tombé",
                "localisation" => "225 Rue Bernard Palissy, 62400 Béthune",
                "reference" => "probleme_45"
            ],
            [
                "Commune" => "Béthune",
                "Categorie" => "Nuisance sonore",
                "Priorité" => "Urgent",
                "titre" => "Voisin trop bruyant",
                "description" => "Pollution sonore quotidienne",
                "localisation" => "268 Rue Paul Doumer, 62400 Béthune",
                "reference" => "probleme_46"
            ],
            [
                "Commune" => "Béthune",
                "Categorie" => "Incendie",
                "Priorité" => "Urgent",
                "titre" => "Incendie",
                "description" => "début d'incendie dans le parc",
                "localisation" => "1 Rue De Saint-Pol, 62400 Béthune",
                "reference" => "probleme_47"
            ],
            [
                "Commune" => "Béthune",
                "Categorie" => "Vandalisme",
                "Priorité" => "Faible",
                "titre" => "Arret de bus détruit",
                "description" => "Arret de bus détruit",
                "localisation" => "33 Rue Du Moulin Masclef, 62400 Béthune",
                "reference" => "probleme_48"
            ],
            [
                "Commune" => "Béthune",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Conteneur de vêtement brulé",
                "description" => "Conteneur de vêtement brulé à l'intérieur et à l'extérieur",
                "localisation" => "288 Avenue De Bruay, 62400 Béthune",
                "reference" => "probleme_49"
            ],
            [
                "Commune" => "Béthune",
                "Categorie" => "Vandalisme",
                "Priorité" => "Urgent",
                "titre" => "Maison cambriolée ",
                "description" => "Trâces d'effraction visible sur la maison (fenêtres brisées, serrure de porte cassée",
                "localisation" => "404 Rue Du Faubourg D'arras, 62400 Béthune",
                "reference" => "probleme_50"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Tag de peinture",
                "description" => "Tag sur un mur",
                "localisation" => "Rue Du Mont Saint-Éloi, 62470 Calonne-Ricouart",
                "reference" => "probleme_51"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Accident de voiture",
                "Priorité" => "Important",
                "titre" => "Accident de voiture",
                "description" => "Collision entre une moto et une voiture",
                "localisation" => "55 Rue De Champagne, 62470 Calonne-Ricouart",
                "reference" => "probleme_52"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Dégradation",
                "Priorité" => "Urgent",
                "titre" => "Vitre brisée",
                "description" => "Vitre brisée sur le mur du magasin",
                "localisation" => "2 Boulevard De La Paix, 62470 Calonne-Ricouart",
                "reference" => "probleme_53"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Inondation",
                "Priorité" => "Urgent",
                "titre" => "Route inonndée",
                "description" => "Route inondée",
                "localisation" => "12 Rue Du Parc, 62470 Calonne-Ricouart",
                "reference" => "probleme_54"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Dégradation",
                "Priorité" => "Important",
                "titre" => "Poteau electrique tombé",
                "description" => "Poteau electrique tombé",
                "localisation" => "Rue De Brias, 62470 Calonne-Ricouart",
                "reference" => "probleme_55"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Nuisance sonore",
                "Priorité" => "Urgent",
                "titre" => "Voisin trop bruyant",
                "description" => "Pollution sonore quotidienne",
                "localisation" => "88 Rue André Mancey, 62470 Calonne-Ricouart",
                "reference" => "probleme_56"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Incendie",
                "Priorité" => "Urgent",
                "titre" => "Incendie",
                "description" => "début d'incendie dans le parc",
                "localisation" => "61 Rue De Katowice, 62470 Calonne-Ricouart",
                "reference" => "probleme_57"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Vandalisme",
                "Priorité" => "Faible",
                "titre" => "Arret de bus détruit",
                "description" => "Arret de bus détruit",
                "localisation" => "56 Rue De La Gare, 62470 Calonne-Ricouart",
                "reference" => "probleme_58"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Dégradation",
                "Priorité" => "Faible",
                "titre" => "Conteneur de vêtement brulé",
                "description" => "Conteneur de vêtement brulé à l'intérieur et à l'extérieur",
                "localisation" => "61 Rue De Katowice, 62470 Calonne-Ricouart",
                "reference" => "probleme_59"
            ],
            [
                "Commune" => "Calonne-Ricouart",
                "Categorie" => "Vandalisme",
                "Priorité" => "Urgent",
                "titre" => "Maison cambriolée ",
                "description" => "Trâces d'effraction visible sur la maison (fenêtres brisées, serrure de porte cassée",
                "localisation" => "5 Rue Du 11 Novembre 1918, 62470 Calonne-Ricouart",
                "reference" => "probleme_60"
            ]
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
