<?php

namespace App\DataFixtures;

use App\Entity\Personne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PersonneFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $personne = new Personne();
        $personne->setPrenom("Hugo");
        $personne->setNom("Duporge");
        $personne->setMail("hugo_duporge@ens.univ-artois.fr");
        $personne->addRole($this->getReference("ADMIN"));
        $this->addReference($personne->getMail(),$personne);
        $personne->setCommune($this->getReference("Lens"));
        $personne->setCreatedAt(new \DateTime('now'));
        $personne->setPassword("test");
        $manager->persist($personne);
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
            RoleFixtures::class,
        );
    }
}
