<?php
namespace App\Controller;

use App\Entity\Personne;
use App\Form\AddRoleToSomeoneType;
use App\Form\RemoveRoleToSomeoneType;
use App\Repository\PersonneRepository;
use App\Repository\RoleRepository;
use App\Services\Personne\PermissionChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manageRole")
 */
class ManageRoleController extends AbstractController
{
    private $personneRepository;
    private $roleRepository;
    private $permissionChecker;

    public function __construct(PermissionChecker $permissionChecker, PersonneRepository $personneRepository, RoleRepository $roleRepository)
    {
        $this->personneRepository = $personneRepository;
        $this->roleRepository = $roleRepository;
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * @Route("/", name="manageRole_index", methods={"GET"})
     */
    public function index(): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_ROLE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                return $this->render('manageRole/index.html.twig', [
                    'personnes' => $this->personneRepository->findAll(),
                ]);
            }
        }
    }


    /**
     * @Route("/addRole/", name="manageRole_add",methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_ROLE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                $form = $this->createForm(AddRoleToSomeoneType::class);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $personne = $this->personneRepository->findOneBy(['id' => $request->request->all()["add_role_to_someone"]["personne"]]);
                    $role = $this->roleRepository->findOneBy(['id' => $request->request->all()["add_role_to_someone"]["role"]]);
                    $personne->addRole($role);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($personne);
                    $entityManager->flush();

                    return $this->redirectToRoute('manageRole_index');
                }

                return $this->render('manageRole/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/{id}/remove", name="manageRole_remove",methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function remove(Request $request,Personne $personne): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["DELETE_OTHER_ROLE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                $form = $this->createForm(RemoveRoleToSomeoneType::class, $personne);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $role = $this->roleRepository->findOneBy(['id' => $request->request->all()["remove_role_to_someone"]["role"]]);
                    $personne->removeRole($role);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($personne);
                    $entityManager->flush();

                    return $this->redirectToRoute('manageRole_index');
                }

                return $this->render('manageRole/remove.html.twig', [
                    'personne' => $personne,
                    'form' => $form->createView(),
                ]);
            }
        }
    }
}