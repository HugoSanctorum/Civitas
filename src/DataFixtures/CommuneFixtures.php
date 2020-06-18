<?php

namespace App\DataFixtures;

use App\Entity\Commune;
use App\Services\Geoquery\Geoquery;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommuneFixtures extends Fixture implements DependentFixtureInterface
{
    private $geoquery;

    public function __construct(Geoquery $geoquery){
        $this->geoquery = $geoquery;
    }

    public function load(ObjectManager $manager)
    {
        $communes = [
                [
                    "nom" => "Lens",
                    "code" => "62300",
                    "Service" => "Comptabilité"
                ],
                [
                    "nom" => "Lille",
                    "code" => "59800",
                    "Service" => "Logistique"
                ],
                [
                    "nom" => "Bruay-la-Buissiere",
                    "code" => "62700",
                    "Service" => "Administratif"
                ],
                [
                    "nom" => "Bethune",
                    "code" => "62400",
                    "Service" => "Voierie"
                ],
                [
                    "nom" => "Lievin",
                    "code" => "62800",
                    "Service" => "Assainissement"
                ],
                [
                    "nom" => "Calonne-Ricouart",
                    "code" => "62470",
                    "Service" => "Entretien"
                ]
            ];

        foreach($communes as $commune){
            $entity = new Commune();
            $this->geoquery->populate($entity, $commune["nom"], $commune["code"]);
            $this->addReference($entity->getNom(), $entity);
            $entity->addService($this->getReference($commune["Service"]));
            $entity->setImageBackground("default/default_city_banner.jpg");
            $manager->persist($entity);
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
            Servicefixtures::class,
        );
    }
}
