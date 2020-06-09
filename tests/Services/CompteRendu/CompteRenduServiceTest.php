<?php


namespace App\Tests\Services\CompteRendu;


use App\Services\CompteRendu\CompteRenduService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Probleme;
use App\Entity\CompteRendu;

class CompteRenduServiceTest extends WebTestCase
{
    private $compteRenduService;

    public function setUp(CompteRenduService $compteRenduService){
        $this->compteRenduService = $compteRenduService;
    }

    public function testPersistCompteRendu(){

    }
}