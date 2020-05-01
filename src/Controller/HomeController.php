<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Geocoder\Query\GeocodeQuery;
use Http\Adapter\Guzzle6\Client as HttpClient;
use Geocoder\Provider\Mapbox\Mapbox;
use Geocoder\StatefulGeocoder;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index" )
     */
    public function index(TokenStorageInterface $tokenStorageInterface){

    	$user = $tokenStorageInterface->getToken()->getUser();
    	if(is_string($user)) $mairie = '17 Place Jean Jaurès, 62300 Lens, France'; //Querry par IP A FAIRE
    	else{
    		$mairie = $user->getCommune()->getMairie();
    	}

    	$httpClient = new HttpClient();
    	$provider = new Mapbox($httpClient, 'pk.eyJ1IjoiYW5hY29tYiIsImEiOiJjazltdG82d2EwMnp5M21scGc1cWdtOGM3In0.WbSl0RvM9KcZkU3C4EDrug');
    	$geocoder = new StatefulGeocoder($provider, 'fr');

    	$result = $geocoder->geocodeQuery(GeocodeQuery::create($mairie));
    	$coordonnees = ["latitude" => $result->first()->getCoordinates()->getLatitude(), "longitude" => $result->first()->getCoordinates()->getLongitude()];

        return $this->render('home/index.html.twig', [
        	"coordonnees" => $coordonnees
        ]);
    }
}
