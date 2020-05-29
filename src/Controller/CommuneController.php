<?php

namespace App\Controller;

use App\Entity\Commune;
use App\Form\CommuneType;
use App\Repository\CommuneRepository;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\CategorieRepository;
use App\Repository\StatutRepository;
use App\Services\Commune\CommuneService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Services\Personne\PermissionChecker;
use App\Services\Geoquery\Geoquery;
use App\Services\Geocoder\GeocoderService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/commune")
 * @IsGranted("ROLE_USER")
 */
class CommuneController extends AbstractController
{
    private $permissionChecker;
    private $user;

    public function __construct(
        PermissionChecker $permissionChecker,
        TokenStorageInterface $tokenStorageInterface
    ){
        $this->permissionChecker = $permissionChecker;
        $this->user = $tokenStorageInterface->getToken()->getUser();
    }

    /**
     * @Route("/", name="commune_index", methods={"GET"})
     */
    public function index(CommuneRepository $communeRepository): Response
    {
        if(!$this->permissionChecker->isUserGranted(["GET_OTHER_COMMUNE"])){
            return new RedirectResponse("/");
        }
        return $this->render('commune/index.html.twig', [
            'communes' => $communeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="commune_new", methods={"GET","POST"})
     */
    public function new(Request $request, Geoquery $geoquery,CommuneService $communeService): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else{
            if(!$this->permissionChecker->isUserGranted(["POST_COMMUNE"])){
                $this->addFlash('fail','Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            }else{
                $commune = new Commune();
                $form = $this->createForm(CommuneType::class, $commune);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $array = $request->request->all()["commune"];
                    $geoquery->populate($commune, $array["nom"], $array["code"]);
                    $document = $form->get('imageBackground')->getData();
                    $communeService->SetBackground($commune,$document);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($commune);
                    $entityManager->flush();
                    return $this->redirectToRoute('commune_index');
                }

                return $this->render('commune/new.html.twig', [
                    'commune' => $commune,
                    'form' => $form->createView(),
                ]);}
        }

    }

    /**
     * @Route("/{id}", name="commune_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Commune $commune): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else{
            if(!$this->permissionChecker->isUserGranted(["GET_OTHER_COMMUNE"])){
                $this->addFlash('fail','Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            }
        }

        return $this->render('commune/show.html.twig', [
            'commune' => $commune,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="commune_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Commune $commune,CommuneService $communeService): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else{
            if(!$this->permissionChecker->isUserGranted(["UPDATE_OTHER_COMMUNE"])){
                $this->addFlash('fail','Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            }else{
                $form = $this->createForm(CommuneType::class, $commune);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $document = $form->get('imageBackground')->getData();
                    $communeService->SetBackground($commune,$document);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('commune_index');
                }
                return $this->render('commune/edit.html.twig', [
                    'commune' => $commune,
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/{id}", name="commune_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Commune $commune): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commune->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commune);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commune_index');
    }

    /**
     * @Route("/manage", name="commune_manage", methods={"GET"})
     */
    public function manage(
        CommuneRepository $communeRepository,
        HistoriqueStatutRepository $historiqueStatutRepository,
        StatutRepository $statutRepository,
        CategorieRepository $categorieRepository,
        GeocoderService $geocoderService
    ): Response
    {
        if(!$this->permissionChecker->isUserGranted(["GET_OTHER_PROBLEME"])){
            return new RedirectResponse("/");
        }
        $commune = $this->user->getCommune();
        $problemes = $commune->getProblemes();
        $infos_problemes = [];
        $categories = [];
        $statuts = [];

        foreach ($problemes as $probleme) {
            $hs = $historiqueStatutRepository->findLatestHistoriqueStatutForOneProblemExcludingArchived($probleme);
            $statut = $statutRepository->findStatutById($hs[0]['statut_id']);
            array_push($infos_problemes, [
                "id" => $probleme->getId(),
                "titre" => $probleme->getTitre(),
                "categorie" => $probleme->getCategorie()->getNom(),
                "statut" => $statut->getNom(),
                "marker_color" => $statut->getCouleur(),
                "marker_icone" => $statut->getIcone(),
                "coordonnees" => $geocoderService->getCoordinateFromAdress($probleme->getLocalisation())
            ]);
        }

        foreach($categorieRepository->findAll() as $categorie){
            array_push($categories, $categorie->getNom());
        }

        foreach($statutRepository->findAll() as $statut){
            array_push($statuts, $statut->getNom());
        }

        return $this->render('commune/manage.html.twig', [
            'commune' => $commune,
            'problemes' => $infos_problemes,
            'categories' => $categories,
            'statuts' => $statuts
        ]);
    }
}
