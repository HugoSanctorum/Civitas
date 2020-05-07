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
        $personne->setUsername($personne->getNom().'_'.$personne->getPrenom());
        $personne->addRole($this->getReference("ROLE_ADMIN"));
        $personne->addRole($this->getReference("ROLE_GESTIONNAIRE"));
        $plainPassword = "hugo";
        $encoded = $this->encoder->encodePassword($personne, $plainPassword);
        $personne->setPassword($encoded);
        $this->addReference($personne->getMail(),$personne);
        $personne->setCommune($this->getReference("Lens"));
        $personne->setCreatedAt(new \DateTime('now'));
        $manager->persist($personne);

        $personne2 = new Personne();
        $personne2->setPrenom("Hugo");
        $personne2->setNom("Sanctorum");
        $personne2->setMail("hugo_sanctorum@ens.univ-artois.fr");
        $personne2->setUsername($personne2->getNom().'_'.$personne2->getPrenom());
        $personne2->addRole($this->getReference("ROLE_GESTIONNAIRE"));
        $plainPassword = "hugo";
        $encoded = $this->encoder->encodePassword($personne2, $plainPassword);
        $personne2->setPassword($encoded);
        $this->addReference($personne2->getMail(),$personne2);
        $personne2->setCommune($this->getReference("Lille"));
        $personne2->setCreatedAt(new \DateTime('now'));
        $manager->persist($personne2);

        for($i = 1; $i < 10; $i++){
            $user = new Personne();
            $user->setUsername('user'.$i);
            $user->setPrenom('user'.$i);
            $user->setNom('user'.$i);
            $user->setMail($user->getUsername().'@gmail.com');
            $plainPassword = $user->getUsername();
            $encoded = $this->encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);
            $user->setCreatedAt(new \DateTime('now'));
            $this->addReference($user->getUsername(), $user);
            $user->setCommune($this->getReference("Lens"));
            $user->setCreatedAt(new \DateTime('now'));
            $manager->persist($user);
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
            RoleFixtures::class,
        );
    }
}
