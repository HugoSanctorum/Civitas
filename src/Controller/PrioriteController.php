<?php

namespace App\Controller;

use App\Entity\Priorite;
use App\Form\PrioriteType;
use App\Repository\PrioriteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/priorite")
 */
class PrioriteController extends AbstractController
{
    /**
     * @Route("/", name="priorite_index", methods={"GET"})
     */
    public function index(PrioriteRepository $prioriteRepository): Response
    {
        return $this->render('priorite/index.html.twig', [
            'priorites' => $prioriteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="priorite_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $priorite = new Priorite();
        $form = $this->createForm(PrioriteType::class, $priorite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($priorite);
            $entityManager->flush();

            return $this->redirectToRoute('priorite_index');
        }

        return $this->render('priorite/editPassword.html.twig', [
            'priorite' => $priorite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="priorite_show", methods={"GET"})
     */
    public function show(Priorite $priorite): Response
    {
        return $this->render('priorite/show.html.twig', [
            'priorite' => $priorite,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="priorite_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Priorite $priorite): Response
    {
        $form = $this->createForm(PrioriteType::class, $priorite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('priorite_index');
        }

        return $this->render('priorite/edit.html.twig', [
            'priorite' => $priorite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="priorite_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Priorite $priorite): Response
    {
        if ($this->isCsrfTokenValid('delete'.$priorite->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($priorite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('priorite_index');
    }
}
