<?php

namespace App\Tests\Controller;

use App\Entity\Personne;
use App\Entity\Probleme;
use App\Repository\ProblemeRepository;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;


class ProblemeControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $client = null;
    private $csrfToken = null;

    public function setUp()
    {
        $this->client = static::createClient();

        $this->csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

    }
    public function testNewProbleme(){

        $this->login();
        $this->client->request('POST','/probleme/new', [
            'probleme[titre]' => 'titre probleme test',
            'probleme[description]' => 'jjjj lll',
            'probleme[localisation]' => 'Route de Béthune',
            'probleme[Categorie]' => 1,
            'probleme[Priorite]' => 1,
            'probleme[nomVille]' => 'Lens',
            'probleme[_token]' => $this->csrfToken
        ]);
        $crawler = $this->client->request('GET','/probleme/new');
        $link = $crawler
            ->filter('div:contains("Save")')
            ->link()
        ;
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('submit')->form();

// set some values
        $form['probleme[titre]'] = 'titre';
        $form['probleme[description]'] = 'çtt';
        $form['probleme[localisation]'] = 'Route de Béthune';
        $form['probleme[Categorie]'] = 1;
        $form['probleme[Priorite]'] = 1;
        $form['probleme[nomVille]'] = 'Lens';
        $form['probleme[_token]'] = $this->csrfToken;

// submit the form
        $crawler = $this->client->submit($form);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $probleme = $this->entityManager
            ->getRepository(Probleme::class)
            ->findOneBy(['id' => 105]);


        $this->assertEquals('fzef',$probleme->getTitre());
    }
    private function login()
    {
        $session = self::$container->get('session');

        // somehow fetch the user (e.g. using the user repository)
        $user = $this->entityManager
            ->getRepository(Personne::class)
            ->findOneBy(['mail' => 'hugo_duporge@ens.univ-artois.fr'])
        ;
        $firewallName = 'main';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'main';

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        //$token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $token = new PostAuthenticationGuardToken($user, $firewallName,$user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}



