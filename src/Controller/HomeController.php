<?php

namespace App\Controller;

use App\Repository\HistoriqueStatutRepository;
use App\Repository\StatutRepository;

use App\Service\Geocoder\GeocoderService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index" )
     */
    public function index(
        TokenStorageInterface $tokenStorageInterface,
        HistoriqueStatutRepository $historiqueStatutRepository,
        StatutRepository $statutRepository,
        GeocoderService $geocoderService
    ){

    	$user = $tokenStorageInterface->getToken()->getUser();
        $problemes = [];
        $infos_problemes = [];

    	if(is_string($user)) $mairie = '17 Place Jean Jaurès, 62300 Lens, France';
    	else{
    		$commune = $user->getCommune();
            $mairie = $commune->getMairie();
            $problemes = $commune->getProblemes();
    	}

    	$coordonnees_mairie = $geocoderService->getCoordinateFromAdress($mairie);

        foreach ($problemes as $probleme) {
            $latest = $historiqueStatutRepository->findLatestHistoriqueStatutForOneProblemExcludingNewAndResolved($probleme);
            if($latest){
                $coordonnees_problemes = $geocoderService->getCoordinateFromAdress($probleme->getLocalisation());
                array_push($infos_problemes, [
                    "id" => $probleme->getId(),
                    "titre" => $probleme->getTitre(),
                    "statut" => $statutRepository->findStatutById(intval($latest[0]['statut_id']))->getNom(),
                    "marker_color" => $probleme->getCategorie()->getCouleur(),
                    "marker_icone" => $probleme->getCategorie()->getIcone(),
                    "coordonnees" => $coordonnees_problemes
                ]);
            }
        }
        return $this->render('home/index.html.twig', [
        	"coordonnees_mairie" => $coordonnees_mairie,
            "problemes" => $infos_problemes
        ]);
    }
}
