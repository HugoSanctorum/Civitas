<?php

namespace App\Controller;

use App\Entity\Commune;
use App\Form\CommuneType;
use App\Repository\CommuneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Services\Personne\PermissionChecker;
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
    public function new(Request $request): Response
    {
        if(!$this->permissionChecker->isUserGranted(["POST_COMMUNE"])){
            return new RedirectResponse("/");
        }

        $commune = new Commune();
        $form = $this->createForm(CommuneType::class, $commune);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
     * @Route("/{id}", name="commune_show", methods={"GET"})
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
     * @Route("/{id}/edit", name="commune_edit", methods={"GET","POST"})
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
     * @Route("/{id}", name="commune_delete", methods={"DELETE"})
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
}
