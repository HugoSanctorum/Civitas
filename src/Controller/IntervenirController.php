<?php

namespace App\Controller;

use App\Entity\Intervenir;
use App\Form\IntervenirType;
use App\Repository\IntervenirRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/intervenir")
 */
class IntervenirController extends AbstractController
{
    private $tokenStorage;
    private $authorizationChecker;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage; // le token utilisateur
        $this->authorizationChecker = $authorizationChecker; // le service de controle d'utilisateur
    }
    /**
     * @Route("/", name="intervenir_index", methods={"GET"})
     */
    public function index(IntervenirRepository $intervenirRepository): Response
    {
        return $this->render('intervenir/index.html.twig', [
            'intervenirs' => $intervenirRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="intervenir_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {


        $intervenir = new Intervenir();
        $form = $this->createForm(IntervenirType::class, $intervenir);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($intervenir);
            $entityManager->flush();

            return $this->redirectToRoute('intervenir_index');
        }

        return $this->render('intervenir/new.html.twig', [
            'intervenir' => $intervenir,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="intervenir_show", methods={"GET"})
     */
    public function show(Intervenir $intervenir): Response
    {
        return $this->render('intervenir/show.html.twig', [
            'intervenir' => $intervenir,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="intervenir_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Intervenir $intervenir): Response
    {
        $form = $this->createForm(IntervenirType::class, $intervenir);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('intervenir_index');
        }

        return $this->render('intervenir/edit.html.twig', [
            'intervenir' => $intervenir,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="intervenir_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Intervenir $intervenir): Response
    {
        if ($this->isCsrfTokenValid('delete'.$intervenir->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($intervenir);
            $entityManager->flush();
        }

        return $this->redirectToRoute('intervenir_index');
    }
}
