<?php

namespace App\Controller;

use App\Entity\HistoriqueStatut;
use App\Entity\Probleme;
use App\Entity\Statut;
use App\Form\HistoriqueStatutType;
use App\Repository\HistoriqueStatutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/historiqueStatut")
 */
class HistoriqueStatutController extends AbstractController
{
    /**
     * @Route("/", name="historique_statut_index", methods={"GET"})
     */
    public function index(HistoriqueStatutRepository $historiqueStatutRepository): Response
    {
        return $this->render('historique_statut/index.html.twig', [
            'historique_statuts' => $historiqueStatutRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="historique_statut_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        Probleme $probleme,
        Statut $statut
    ): Response
    {

        $historiqueStatut = new HistoriqueStatut();
        $form = $this->createForm(HistoriqueStatutType::class, $historiqueStatut, [
            "Probleme" => $probleme,
            "Statut" => $statut
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($historiqueStatut);
            $entityManager->flush();

            return $this->redirectToRoute('historique_statut_index');
        }

        return $this->render('historique_statut/new.html.twig', [
            'historique_statut' => $historiqueStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="historique_statut_show", methods={"GET"})
     */
    public function show(HistoriqueStatut $historiqueStatut): Response
    {
        return $this->render('historique_statut/show.html.twig', [
            'historique_statut' => $historiqueStatut,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="historique_statut_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, HistoriqueStatut $historiqueStatut): Response
    {
        $form = $this->createForm(HistoriqueStatutType::class, $historiqueStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('historique_statut_index');
        }

        return $this->render('historique_statut/edit.html.twig', [
            'historique_statut' => $historiqueStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="historique_statut_delete", methods={"DELETE"})
     */
    public function delete(Request $request, HistoriqueStatut $historiqueStatut): Response
    {
        if ($this->isCsrfTokenValid('delete'.$historiqueStatut->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($historiqueStatut);
            $entityManager->flush();
        }

        return $this->redirectToRoute('historique_statut_index');
    }
}
