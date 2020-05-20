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
use App\Services\CompteRendu\DocumentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/compteRendu")
 * @IsGranted("ROLE_USER")
 */
class CompteRenduController extends AbstractController
{
    private $problemeRepository;

    public function __construct(ProblemeRepository $problemeRepository)
    {
        $this->problemeRepository = $problemeRepository;
    }

    /**
     * @Route("/", name="compte_rendu_index", methods={"GET"})
     */
    public function index(CompteRenduRepository $compteRenduRepository): Response
    {
        return $this->render('compte_rendu/index.html.twig', [
            'compte_rendus' => $compteRenduRepository->findAll(),
        ]);
    }

    /**
     * @Route("/nouveau", name="compte_rendu_nouveau", methods={"GET","POST"})
     */
    public function nouveau(Request $request, SessionInterface $session): Response
    {

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

    /**
     * @Route("/new", name="compte_rendu_new", methods={"GET","POST"})
     */
    public function new(
        StatutRepository $statutRepository,
        Request $request,
        DocumentService $documentService,
        ProblemeRepository $problemeRepository,
        SessionInterface $session
    ): Response
    {
        $probleme = $session->get('Probleme');
        if($probleme == null) {
            $this->addFlash('fail','Aucun problème n\'a été selectionné');
            return $this->redirectToRoute('compte_rendu_nouveau');
        }
        $compteRendu = new CompteRendu();
        $compteRendu->setProbleme($probleme);

        $form = $this->createForm(CompteRenduType::class, $compteRendu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $document = $form->get('urlDocument')->getData();
            $documentService->PersistCompteRendu($compteRendu, $probleme, $document);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('compte_rendu_index');
        }
        $session->clear();
        return $this->render('compte_rendu/new.html.twig', [
            'compte_rendu' => $compteRendu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="compte_rendu_show", methods={"GET"})
     */
    public function show(CompteRendu $compteRendu): Response
    {
        return $this->render('compte_rendu/show.html.twig', [
            'compte_rendu' => $compteRendu,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="compte_rendu_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CompteRendu $compteRendu): Response
    {
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

    /**
     * @Route("/{id}", name="compte_rendu_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CompteRendu $compteRendu): Response
    {
        if ($this->isCsrfTokenValid('delete'.$compteRendu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($compteRendu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('compte_rendu_index');
    }

}
