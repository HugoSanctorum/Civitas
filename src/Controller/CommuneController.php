<?php

namespace App\Controller;

use App\Entity\Commune;
use App\Form\CommuneType;
use App\Repository\CommuneRepository;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\StatutRepository;
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
    public function new(Request $request, Geoquery $geoquery): Response
    {
        if(!$this->permissionChecker->isUserGranted(["POST_COMMUNE"])){
            return new RedirectResponse("/");
        }

        $commune = new Commune();
        $form = $this->createForm(CommuneType::class, $commune);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $array = $request->request->all()["commune"];
            $geoquery->populate($commune, $array["nom"], $array["code"]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commune);
            $entityManager->flush();

            return $this->redirectToRoute('commune_index');
        }

        return $this->render('commune/new.html.twig', [
            'commune' => $commune,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commune_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Commune $commune): Response
    {
        if(!$this->permissionChecker->isUserGrantedSelf(
            ["GET_SELF_COMMUNE"],
            $commune==$this->user->getCommune()
        )){
            return new RedirectResponse("/");
        }

        return $this->render('commune/show.html.twig', [
            'commune' => $commune,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="commune_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Commune $commune): Response
    {
        $form = $this->createForm(CommuneType::class, $commune);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commune_index');
        }

        return $this->render('commune/edit.html.twig', [
            'commune' => $commune,
            'form' => $form->createView(),
        ]);
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
        GeocoderService $geocoderService
    ): Response
    {
        if(!$this->permissionChecker->isUserGranted(["GET_OTHER_PROBLEME"])){
            return new RedirectResponse("/");
        }
        $commune = $this->user->getCommune();
        $problemes = $commune->getProblemes();
        $infos_problemes = [];

        foreach ($problemes as $probleme) {
            $hs = $historiqueStatutRepository->findLatestHistoriqueStatutForOneProblem($probleme);
            $statut = $statutRepository->findStatutById($hs[0]['statut_id'])->getNom();
            array_push($infos_problemes, [
                "id" => $probleme->getId(),
                "titre" => $probleme->getTitre(),
                "statut" => $statut,
                "marker_color" => $probleme->getCategorie()->getCouleur(),
                "marker_icone" => $probleme->getCategorie()->getIcone(),
                "coordonnees" => $geocoderService->getCoordinateFromAdress($probleme->getLocalisation())
            ]);
        }

        return $this->render('commune/manage.html.twig', [
            'commune' => $commune,
            'problemes' => $infos_problemes,
            'statuts' => $statutRepository->findAll()
        ]);
    }
}
