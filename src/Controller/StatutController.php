<?php

namespace App\Controller;

use App\Entity\Statut;
use App\Form\StatutType;
use App\Repository\StatutRepository;
use App\Services\Personne\PermissionChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Dumper\MoFileDumper;

/**
 * @Route("/statut")
 */
class StatutController extends AbstractController
{
    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * StatutController constructor.
     */
    public function __construct(PermissionChecker $permissionChecker)
    {
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * @Route("/", name="statut_index", methods={"GET"})
     */
    public function index(StatutRepository $statutRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('fail', 'Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        } else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_STATUT"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                return $this->render('statut/index.html.twig', [
                    'statuts' => $statutRepository->findAll(),
                ]);
            }
        }
    }

    /**
     * @Route("/new", name="statut_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('fail', 'Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        } else {
            if (!$this->permissionChecker->isUserGranted(["POST_STATUT"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                $statut = new Statut();
                $form = $this->createForm(StatutType::class, $statut);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($statut);
                    $entityManager->flush();

                    return $this->redirectToRoute('statut_index');
                }

                return $this->render('statut/new.html.twig', [
                    'statut' => $statut,
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/{id}", name="statut_show", methods={"GET"})
     */
    public function show(Statut $statut): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('fail', 'Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        } else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_STATUT"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                return $this->render('statut/show.html.twig', [
                    'statut' => $statut,
                ]);
            }
        }
    }

    /**
     * @Route("/{id}/edit", name="statut_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Statut $statut): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('fail', 'Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        } else {
            if (!$this->permissionChecker->isUserGranted(["UPDATE_OTHER_STATUT"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                $form = $this->createForm(StatutType::class, $statut);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $this->getDoctrine()->getManager()->flush();

                    return $this->redirectToRoute('statut_index');
                }

                return $this->render('statut/edit.html.twig', [
                    'statut' => $statut,
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/{id}", name="statut_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Statut $statut): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('fail', 'Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        } else {
            if (!$this->permissionChecker->isUserGranted(["DELETE_OTHER_STATUT"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                if ($this->isCsrfTokenValid('delete' . $statut->getId(), $request->request->get('_token'))) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($statut);
                    $entityManager->flush();
                }
            }
        }

        return $this->redirectToRoute('statut_index');
    }
}
