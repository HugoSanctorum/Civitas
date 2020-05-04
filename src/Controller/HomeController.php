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
        $problemes = [];
        $coordonnees_problemes = [];

    	if(is_string($user)) $mairie = '17 Place Jean Jaurès, 62300 Lens, France'; //Querry par IP A FAIRE
    	else{
    		$commune = $user->getCommune();
            $mairie = $commune->getMairie();
            $problemes = $commune->getProblemes();
    	}

    	$httpClient = new HttpClient();
    	$provider = new Mapbox($httpClient, 'pk.eyJ1IjoiYW5hY29tYiIsImEiOiJjazltdG82d2EwMnp5M21scGc1cWdtOGM3In0.WbSl0RvM9KcZkU3C4EDrug');
    	$geocoder = new StatefulGeocoder($provider, 'fr');

    	$result = $geocoder->geocodeQuery(GeocodeQuery::create($mairie));
    	$coordonnees = [
            "latitude" => $result->first()->getCoordinates()->getLatitude(),
            "longitude" => $result->first()->getCoordinates()->getLongitude()
        ];

        foreach ($problemes as $probleme) {
            $result = $geocoder->geocodeQuery(GeocodeQuery::create($probleme->getLocalisation( )))->first();
            array_push($coordonnees_problemes, [
                "id" => $probleme->getId(),
                "titre" =>$probleme->getTitre(),
                "coordonnees" => [
                    $result->getCoordinates()->getLatitude(),
                    $result->getCoordinates()->getLongitude()
                ]
            ]);
        }

        return $this->render('home/index.html.twig', [
        	"coordonnees" => $coordonnees,
            "problemes" => $coordonnees_problemes
        ]);
    }
}
