<?php

namespace App\Controller;

use App\Repository\HistoriqueStatutRepository;
use App\Repository\StatutRepository;
use App\Repository\IntervenirRepository;

use App\Services\Geocoder\GeocoderService;

use App\Services\Personne\PermissionChecker;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HomeController extends AbstractController
{

    private $permissionChecker;

    /**
     * HomeController constructor.
     */
    public function __construct(PermissionChecker $permissionChecker)
    {
        $this->permissionChecker = $permissionChecker;
    }


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
            $parameters["commune"] = $user->getCommune();
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
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            $user = $tokenStorageInterface->getToken()->getUser();
            $problemes = [];
            $infos_problemes = [];
            $contour = [];

            if (is_string($user)) $centre = '17 Place Jean Jaurès, 62300 Lens, France';
            else {
                $commune = $user->getCommune();
                $centre = $commune->getCentre();
                $problemes = $commune->getProblemes();
                $contour = $commune->getContour();
            }

            foreach ($problemes as $probleme) {
                $latest = $historiqueStatutRepository->findLatestHistoriqueStatutForOneProblemExcludingNewResolvedAndArchived($probleme);
                if ($latest) {
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
    }

    /**
     * @Route("/panel_gestionnaire", name="panel_gestionnaire")
     * @IsGranted("ROLE_USER")
     */
    public function pannel_gestionnaire()
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_PANEL_GESTIONNAIRE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            }else {
                return $this->render('home/panel/gestionnaire.html.twig');
            }
        }
    }

    /**
     * @Route("/panel_technicien", name="panel_technicien")
     * @IsGranted("ROLE_USER")
     */
    public function pannel_technicien(
        TokenStorageInterface $tokenStorageInterface,
        IntervenirRepository $intervenirRepository
    )
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_PANEL_TECHNICIEN"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                $personne = $tokenStorageInterface->getToken()->getUser();
                return $this->render('home/panel/technicien.html.twig', [
                    "new_interventions" => $intervenirRepository->getNewIntervenirByTechnician($personne),
                    "in_progress_interventions" => $intervenirRepository->getInProgressIntervenirByTechnician($personne),
                    "affected_interventions" => $intervenirRepository->getAffectedIntervenirByTechnician($personne),
                    "others_interventions" => $intervenirRepository->getOthersIntervenirByTechnician($personne)
                ]);
            }
        }
    }

    /**
     * @Route("/demoapi", name="demo_api")
     * @IsGranted("ROLE_USER")
     */
    public function demo_api()
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_PROBLEME"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            }else {
                return $this->render('home/demo_api.html.twig');
            }
        }
    }
}
