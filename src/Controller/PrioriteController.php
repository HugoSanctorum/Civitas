<?php

namespace App\Controller;

use App\Entity\Priorite;
use App\Form\PrioriteType;
use App\Repository\PrioriteRepository;
use App\Services\Personne\PermissionChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/priorite")
 */
class PrioriteController extends AbstractController
{
    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * PrioriteController constructor.
     */
    public function __construct(PermissionChecker $permissionChecker)
    {
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * @Route("/", name="priorite_index", methods={"GET"})
     */
    public function index(PrioriteRepository $prioriteRepository): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_PRIORITE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                return $this->render('priorite/index.html.twig', [
                    'priorites' => $prioriteRepository->findAll(),
                ]);
            }
        }
    }

    /**
     * @Route("/new", name="priorite_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        PrioriteRepository $prioriteRepository
    ): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["POST_PRIORITE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                $priorite = new Priorite();
                $form = $this->createForm(PrioriteType::class, $priorite);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $done = false;
                    $poids = $priorite->getPoids();
                    while(!$done){
                        $doublon_poids = $prioriteRepository->findOneBy(['poids' => $poids++]);
                        if($doublon_poids) $doublon_poids->setPoids($doublon_poids->getPoids()+1);
                        else $done = true;
                    }
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($priorite);
                    $entityManager->flush();

                    return $this->redirectToRoute('priorite_index');
                }

                return $this->render('priorite/new.html.twig', [
                    'priorite' => $priorite,
                    'priorites' => $prioriteRepository->findBy([], ['poids' => 'ASC']),
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/{id}", name="priorite_show", methods={"GET"})
     */
    public function show(Priorite $priorite): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_PRIORITE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                return $this->render('priorite/show.html.twig', [
                    'priorite' => $priorite,
                ]);
            }
        }
    }

    /**
     * @Route("/{id}/edit", name="priorite_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Priorite $priorite,
        PrioriteRepository $prioriteRepository
    ): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["UPDATE_OTHER_PRIORITE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                $form = $this->createForm(PrioriteType::class, $priorite);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $done = false;
                    $poids = $priorite->getPoids();
                    while(!$done){
                        $doublon_poids = $prioriteRepository->findOneBy(['poids' => $poids++]);
                        if($doublon_poids && $doublon_poids != $priorite) $doublon_poids->setPoids($doublon_poids->getPoids()+1);
                        else $done = true;
                    }
                    $this->getDoctrine()->getManager()->flush();
                    
                    return $this->redirectToRoute('priorite_index');
                }

                return $this->render('priorite/edit.html.twig', [
                    'priorite' => $priorite,
                    'priorites' => $prioriteRepository->findBy([], ['poids' => 'ASC']),
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/{id}", name="priorite_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Priorite $priorite): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["DELETE_OTHER_PRIORITE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                if ($this->isCsrfTokenValid('delete' . $priorite->getId(), $request->request->get('_token'))) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($priorite);
                    $entityManager->flush();
                }
            }
        }

        return $this->redirectToRoute('priorite_index');
    }
}
