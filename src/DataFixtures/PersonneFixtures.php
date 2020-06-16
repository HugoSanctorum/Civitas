<?php

namespace App\DataFixtures;

use App\Entity\Personne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class PersonneFixtures extends Fixture implements DependentFixtureInterface
{

    private $encoder;
    private $tokenGenerator;

    public function __construct(UserPasswordEncoderInterface $encoder,TokenGeneratorInterface $tokenGenerator)
    {
        $this->encoder = $encoder;

        $this->tokenGenerator = $tokenGenerator;
    }

    public function load(ObjectManager $manager)
    {
        $communes = [
            $this->getReference("Lens"),
            $this->getReference("Lille"),
            $this->getReference("Bruay-la-Buissière"),
            $this->getReference("Béthune"),
            $this->getReference("Liévin")
        ];

        $faker = Faker\Factory::create('fr_FR');

        for($i = 0; $i <= 7; $i++){
            $personne = new Personne();
            $personne->setPrenom($faker->firstName());
            $personne->setNom($faker->lastName());
            $personne->setMail($faker->freeEmail());
            $personne->setUsername($personne->getNom().'_'.$personne->getPrenom());
            $plainPassword = $personne->getPrenom();
            $encoded = $this->encoder->encodePassword($personne, $plainPassword);
            $personne->setPassword($encoded);
            $this->addReference('personne_'.$i, $personne);
            $personne->setCommune($communes[array_rand($communes)]);
            $personne->setCreatedAt(new \DateTime('now'));
            $personne->addRole($this->getReference('ROLE_USER'));
            if($i % 2 == 0 ){
                $tokenSub = $this->tokenGenerator->generateToken();
                $personne->setSubscribeToken($tokenSub);
            }
            if($i % 3 == 0){
                $tokenAct = $this->tokenGenerator->generateToken();
                $personne->setActivatedToken($tokenAct);
            }
            $manager->persist($personne);
        }

        $personne = new Personne();
        $tokenSub = $this->tokenGenerator->generateToken();
        $personne->setSubscribeToken($tokenSub);
        $personne->setPrenom("Hugo");
        $personne->setNom("Duporge");
        $personne->setMail("hugo_duporge@ens.univ-artois.fr");
        $personne->setUsername($personne->getNom().'_'.$personne->getPrenom());
        $personne->addRole($this->getReference("ROLE_ADMIN"));
        $plainPassword = "hugo";
        $encoded = $this->encoder->encodePassword($personne, $plainPassword);
        $personne->setPassword($encoded);
        $this->addReference($personne->getMail(),$personne);
        $personne->setCommune($this->getReference("Lens"));
        $personne->setCreatedAt(new \DateTime('now'));
        $manager->persist($personne);

        $personne2 = new Personne();
        $tokenSub = $this->tokenGenerator->generateToken();
        $personne2->setSubscribeToken($tokenSub);
        $personne2->setPrenom("Giovanna");
        $personne2->setNom("Giarusso");
        $personne2->setMail("GiovannaGiarusso@ens.univ-artois.fr");
        $personne2->setUsername($personne2->getNom().'_'.$personne2->getPrenom());
        $personne2->addRole($this->getReference("ROLE_GESTIONNAIRE"));
        $encoded = $this->encoder->encodePassword($personne2, $personne2->getPrenom());
        $personne2->setPassword($encoded);
        $this->addReference($personne2->getMail(),$personne2);
        $personne2->setCommune($this->getReference("Lens"));
        $personne2->setCreatedAt(new \DateTime('now'));
        $manager->persist($personne2);

        $personne3 = new Personne();
        $tokenSub = $this->tokenGenerator->generateToken();
        $personne3->setSubscribeToken($tokenSub);
        $personne3->setPrenom("Roger");
        $personne3->setNom("Dupont");
        $personne3->setMail("RogerDupont@ens.univ-artois.fr");
        $personne3->setUsername($personne3->getNom().'_'.$personne3->getPrenom());
        $personne3->addRole($this->getReference("ROLE_TECHNICIEN"));
        $encoded = $this->encoder->encodePassword($personne3, $personne3->getPrenom());
        $personne3->setPassword($encoded);
        $this->addReference($personne3->getMail(),$personne3);
        $personne3->setCommune($this->getReference("Lens"));
        $personne3->setCreatedAt(new \DateTime('now'));
        $manager->persist($personne3);



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
