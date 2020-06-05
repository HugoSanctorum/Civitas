<?php


namespace App\Tests\Repository;

use App\Entity\Personne;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PersonneRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSearchByMail()
    {
        $personne = $this->entityManager
            ->getRepository(Personne::class)
            ->findOneBy(['mail' => 'hugo_duporge@ens.univ-artois.fr'])
        ;

        $this->assertSame("Duporge", $personne->getNom());
    }
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}