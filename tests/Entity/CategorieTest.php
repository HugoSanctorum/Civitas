<?php

namespace App\Tests\Entity;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
class CategorieTest extends KernelTestCase
{
    public function getEntity() : Categorie {
        $categorie= (new Categorie())
            ->setNom('dégradation')
            ->setCouleur("red")
            ->setIcone('skull');
        return $categorie;
    }

    public function assertHasErrors(Categorie $categorie,int $number=0){
        self::bootKernel();
        $error = self::$container->get('validator')->validate($categorie);
        $this->assertCount($number,$error);
    }

    public function testValidEntity() {
       $this->assertHasErrors($this->getEntity(),0);
    }
}