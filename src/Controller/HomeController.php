<?php

namespace App\Controller;

use App\Repository\HistoriqueStatutRepository;
use App\Repository\StatutRepository;
use App\Repository\IntervenirRepository;

use App\Services\Geocoder\GeocoderService;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home_index" )
     */
    public function index(
        TokenStorageInterface $tokenStorageInterface
    ){
        $parameters = [];
        $user = $tokenStorageInterface->getToken()->getUser() != "anon." ? $tokenStorageInterface->getToken()->getUser() : null;

        $render = $user ? 'home/home_commune.html.twig' : 'home/home_general.html.twig';
        
        if($user){
            $parameters["commune"] = $user->getCommune()->getNom();
        }

        return $this->render($render, $parameters);
    }

    /**
     * @Route("/carte_probleme", name="home_carte" )
     */
    public function carte(
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
                    "nom_categorie" => $probleme->getCategorie()->getNom(),
                    "marker_color" => $probleme->getCategorie()->getCouleur(),
                    "marker_icone" => $probleme->getCategorie()->getIcone(),
                    "coordonnees" => $coordonnees_problemes
                ]);
            }
        }
        return $this->render('home/map_index.html.twig', [
        	"centre" => $centre,
            "contour" => $contour,
            "commune" => $commune->getNom(),
            "problemes" => $infos_problemes
        ]);
    }

    /**
     * @Route("/pannel_gestionnaire", name="pannel_gestionnaire")
     * @IsGranted("ROLE_USER")
     */
    public function pannel_gestionnaire()
    {
        return $this->render('home/pannel/gestionnaire.html.twig', [
            
        ]);
    }

    /**
     * @Route("/pannel_technicien", name="pannel_technicien")
     * @IsGranted("ROLE_USER")
     */
    public function pannel_technicien(
        TokenStorageInterface $tokenStorageInterface,
        IntervenirRepository $intervenirRepository
    )
    {
        $personne = $tokenStorageInterface->getToken()->getUser();
        return $this->render('home/pannel/technicien.html.twig', [
            "new_interventions" => $intervenirRepository->getNewIntervenirByTechnician($personne),
            //"in_progress_intervention" =>$intervenirRepository->getInProgressIntervenirByTechnician($personne),
            //"affected_intervention" => $intervenirRepository->getAffectedIntervenirByTechnician($personne) 
        ]);
    }
}
