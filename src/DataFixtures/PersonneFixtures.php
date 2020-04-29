<?php

namespace App\DataFixtures;

use App\Entity\Personne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PersonneFixtures extends Fixture implements DependentFixtureInterface
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $personne = new Personne();
        $personne->setPrenom("Hugo");
        $personne->setNom("Duporge");
        $personne->setMail("hugo_duporge@ens.univ-artois.fr");
        $personne->setUsername($personne->getNom()."_".$personne->getPrenom());
        $personne->addRole($this->getReference("ROLE_ADMIN"));
        $personne->addRole($this->getReference("ROLE_GESTIONNAIRE"));
        $plainPassword = "hugo";
        $encoded = $this->encoder->encodePassword($personne, $plainPassword);
        $personne->setPassword($encoded);
        $this->addReference($personne->getMail(),$personne);
        $personne->setCommune($this->getReference("Lens"));
        $personne->setCreatedAt(new \DateTime('now'));
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
