<?php

namespace App\Tests\Controller;

use App\Entity\Personne;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;


class PersonneControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $client = null;


    public function setUp()
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testSuccessProfileAuthenticated(){
        $this->login();
        $this->client->request('GET','/profile');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
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



