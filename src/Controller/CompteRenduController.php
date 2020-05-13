<?php

namespace App\Controller;

use App\Entity\CompteRendu;
use App\Entity\HistoriqueStatut;
use App\Form\CompteRenduType;
use App\Repository\CompteRenduRepository;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\ProblemeRepository;
use App\Repository\StatutRepository;
use App\Services\CompteRendu\DocumentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/compteRendu")
 */
class CompteRenduController extends AbstractController
{
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
     * @Route("/new", name="compte_rendu_new", methods={"GET","POST"})
     */
    public function new(StatutRepository $statutRepository,Request $request, DocumentService $documentService,ProblemeRepository $problemeRepository): Response
    {
        $compteRendu = new CompteRendu();
        $historiqueStatut = new HistoriqueStatut();
        $statut = $statutRepository->findOneBy(['nom' => 'En cours de traitement']);
        $form = $this->createForm(CompteRenduType::class, $compteRendu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $document = $form->get('urlDocument')->getData();
            $problemeid = $request->request->all()['compte_rendu']['Probleme'];
            $probleme = $problemeRepository->findOneBy(['id'=>$problemeid]);
            $historiqueStatut->setProbleme($probleme);
            $historiqueStatut->setStatut($statut);
            $historiqueStatut->setDate(new \DateTime('now'));
            $historiqueStatut->setDescription('En cours de traitement'); // description temporaire.
            $documentService->PersistCompteRendu($compteRendu,$probleme,$document);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($historiqueStatut);
            $entityManager->flush();

            return $this->redirectToRoute('compte_rendu_index');
        }

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
