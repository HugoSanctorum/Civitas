<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{

    public function testLogin(){
        $client = static::createClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');

        $client->request('GET','/login');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());

        $client->request('POST','/login', ['_csrf_token' => $csrfToken, 'mail' => 'hugo_duporge@ens.univ-artois.fr', 'password' => 'hugo']);
        $this->assertResponseRedirects('/','302');

        $client->request('POST','/login', ['_csrf_token' => $csrfToken, 'mail' => 'hugo_duporge@ens.univfffff-artois.fr', 'password' => 'hugo']);
        $this->assertResponseRedirects('/login','302');
    }
}