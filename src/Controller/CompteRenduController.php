<?php

namespace App\Controller;

use App\Entity\CompteRendu;
use App\Entity\HistoriqueStatut;
use App\Form\ChoiceProblemType;
use App\Form\CompteRenduType;
use App\Repository\CompteRenduRepository;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\PersonneRepository;
use App\Repository\ProblemeRepository;
use App\Repository\StatutRepository;
use App\Services\Commune\CommuneService;
use App\Services\CompteRendu\CompteRenduService;
use App\Services\CompteRendu\DocumentService;
use App\Services\Personne\PermissionChecker;
use App\Services\UploadDocumentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * @Route("/compteRendu")
 * @IsGranted("ROLE_USER")
 */
class CompteRenduController extends AbstractController
{
    private $problemeRepository;
    private $compteRenduRepository;
    private $personne;
    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    public function __construct(TokenStorageInterface $tokenStorageInterface, ProblemeRepository $problemeRepository, PermissionChecker $permissionChecker, CompteRenduRepository $compteRenduRepository)
    {
        $this->problemeRepository = $problemeRepository;
        $this->permissionChecker = $permissionChecker;
        $this->compteRenduRepository = $compteRenduRepository;
        $this->personne = $tokenStorageInterface->getToken()->getUser();
    }

    /**
     * @Route("/", name="compte_rendu_index", methods={"GET"})
     */
    public function index(CompteRenduRepository $compteRenduRepository): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_COMPTE_RENDU"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                return $this->render('compte_rendu/index.html.twig', [
                    'compte_rendus' => $compteRenduRepository->findAll(),
                ]);
            }
        }
    }

    /**
     * @Route("/nouveau", name="compte_rendu_nouveau", methods={"GET","POST"})
     */
    public function nouveau(Request $request, SessionInterface $session): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["POST_COMPTE_RENDU"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                $form = $this->createForm(ChoiceProblemType::class);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $problemeId = (int)$request->request->all()['choice_problem']["Probleme"];
                    $probleme = $this->problemeRepository->find($problemeId);
                    $session->set('Probleme', $probleme);

                    return $this->redirectToRoute('compte_rendu_new');
                }
                return $this->render('compte_rendu/choiceProblemNew.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/new", name="compte_rendu_new", methods={"GET","POST"})
     */
    public function new(
        StatutRepository $statutRepository,
        Request $request,
        ProblemeRepository $problemeRepository,
        SessionInterface $session,
        CompteRenduService $compteRenduService
    ): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["POST_COMPTE_RENDU"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                $probleme = $session->get('Probleme');
                if ($probleme == null) {
                    $this->addFlash('fail', 'Aucun problème n\'a été selectionné');
                    return $this->redirectToRoute('compte_rendu_nouveau');
                }
                $compteRendu = new CompteRendu();
                $pb = $problemeRepository->findOneBy(['id' => $probleme->getId()]);
                $compteRendu->setProbleme($pb);
                $form = $this->createForm(CompteRenduType::class, $compteRendu);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $document = $form->get('urlDocument')->getData();
                    $compteRenduService->PersistCompteRendu($compteRendu, $pb, $document);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($compteRendu);
                    $entityManager->flush();

                    return $this->redirectToRoute('mes_compte-rendus');
                }

                return $this->render('compte_rendu/new.html.twig', [
                    'compte_rendu' => $compteRendu,
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/{id}", name="compte_rendu_show", methods={"GET"},  requirements={"id"="\d+"})
     */
    public function show(CompteRendu $compteRendu): Response
    {
        $request = $this->compteRenduRepository->getOneCompteRenduByTechnicien($compteRendu, $this->personne);
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if(!$request) {
                if ($this->permissionChecker->isUserGranted(["GET_OTHER_COMPTE_RENDU"])) {
                    return $this->render('compte_rendu/show.html.twig', [
                        'compte_rendu' => $compteRendu,
                    ]);
                }else{
                    $this->addFlash('fail', 'Ce compte rendu ne vous appartient pas.');
                    return $this->redirectToRoute("home_index");
                }
            }else {
                if (!$this->permissionChecker->isUserGranted(["GET_SELF_COMPTE_RENDU"])) {
                    $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                    return new RedirectResponse("/");
                }else {
                    return $this->render('compte_rendu/show.html.twig', [
                        'compte_rendu' => $compteRendu,
                    ]);
                }
            }
        }
    }

    /**
     * @Route("/{id}/edit", name="compte_rendu_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, CompteRendu $compteRendu): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["UPDATE_COMPTE_RENDU"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                $form = $this->createForm(CompteRenduType::class, $compteRendu);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $this->getDoctrine()->getManager()->flush();

                    return $this->redirectToRoute('compte_rendu_index');
                }

                return $this->render('compte_rendu/edit.html.twig', [
                    'compte_rendu' => $compteRendu,
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/{id}", name="compte_rendu_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, CompteRendu $compteRendu): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('fail', 'Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        } else {
            if (!$this->permissionChecker->isUserGranted(["DELETE_COMPTE_RENDU"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                if ($this->isCsrfTokenValid('delete' . $compteRendu->getId(), $request->request->get('_token'))) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($compteRendu);
                    $entityManager->flush();
                }

                return $this->redirectToRoute('compte_rendu_index');
            }
        }
    }

    /**
     * @Route("/mes_compte-rendus", name="mes_compte-rendus", methods={"GET","POST"})
     */
    public function mesCompteRendus()
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('fail', 'Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        } else {
            if (!$this->permissionChecker->isUserGranted(["GET_SELF_COMPTE_RENDU"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
            }else {
                $compteRendus = $this->compteRenduRepository->getAllCompteRenduByTechnicien($this->personne);
                return $this->render('compte_rendu/mes_compte-rendu.html.twig', [
                    "compte_rendus" => $compteRendus
                ]);
            }

        }
        return $this->redirectToRoute('compte_rendu_index');
    }
}
