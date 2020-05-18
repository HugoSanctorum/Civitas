<?php

namespace App\Controller;

use App\Repository\HistoriqueStatutRepository;
use App\Repository\StatutRepository;

use App\Services\Geocoder\GeocoderService;

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
        $contour = [];

    	if(is_string($user)) $centre = '17 Place Jean Jaurès, 62300 Lens, France';
    	else{
    		$commune = $user->getCommune();
            $centre = $commune->getCentre();
            $problemes = $commune->getProblemes();
            $contour = $commune->getContour();
    	}

        foreach ($problemes as $probleme) {
            $latest = $historiqueStatutRepository->findLatestHistoriqueStatutForOneProblemExcludingNewResolvedAndArchived($probleme);
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
        	"centre" => $centre,
            "contour" => $contour,
            "problemes" => $infos_problemes
        ]);
    }
}
