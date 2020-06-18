<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use App\Services\Personne\PermissionChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/role")
 */
class RoleController extends AbstractController
{
    /**
     * @var RoleRepository
     */
    private $roleRepository;
    private $permissionRepository;
    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * RoleController constructor.
     */
    public function __construct(RoleRepository $roleRepository,PermissionChecker $permissionChecker, PermissionRepository $permissionRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * @Route("/", name="role_index", methods={"GET"})
     */
    public function index(RoleRepository $roleRepository): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_ROLE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                return $this->render('role/index.html.twig', [
                    'roles' => $roleRepository->findAllRoleExceptAdmin(),
                ]);
            }
        }
    }

    /**
     * @Route("/new", name="role_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["POST_ROLE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                $role = new Role();
                $form = $this->createForm(RoleType::class, $role);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $roleNom = $this->roleRepository->findOneBy(["role" => $request->request->all()["role"]["role"]]);
                    if ($roleNom) {
                        $this->addFlash('fail', 'Ce nom de rôle est déjà utilisé');
                        return $this->redirectToRoute('role_new');
                    } else {
                        foreach ($request->request->all()["role"]["Permissions"] as $permission) {
                            $data = $this->permissionRepository->findOneBy(['id' => (int)$permission]);
                            $data->addRole($role);

                        }
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($role);
                        $entityManager->flush();
                    }

                    return $this->redirectToRoute('role_index');
                }

                return $this->render('role/new.html.twig', [
                    'role' => $role,
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/{id}", name="role_show", methods={"GET"})
     */
    public function show(Role $role): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_ROLE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                return $this->render('role/show.html.twig', [
                    'role' => $role,
                ]);
            }
        }
    }
    /**
     * @Route("/display/{roleNom}", name="role_display", methods={"GET"})
     */
    public function display(string $roleNom): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_ROLE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                $role = $this->roleRepository->findOneBy(["role" => $roleNom]);
                return $this->render('role/show.html.twig', [
                    'role' => $role,
                ]);
            }
        }
    }
    /**
     * @Route("/{id}/edit", name="role_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Role $role): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["UPDATE_OTHER_ROLE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                $form = $this->createForm(RoleType::class, $role);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    dd($request->request->all());
                    foreach($role->getPermissions() as $permission){
                        $role->removePermissions($permission);
                    }
                    foreach($request->request->all()['role']['Permissions'] as $permission){
                        $role->addPermissions($permission);
                    }
                    $this->getDoctrine()->getManager()->flush();

                    return $this->redirectToRoute('role_index');
                }

                return $this->render('role/edit.html.twig', [
                    'role' => $role,
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/{id}", name="role_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Role $role): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["DELETE_OTHER_ROLE"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                if ($this->isCsrfTokenValid('delete' . $role->getId(), $request->request->get('_token'))) {
                    if($role->getRole() == "ROLE_ADMIN" || $role->getRole() == "ROLE_TECHNICIEN" || $role->getRole() == "ROLE_GESTIONNAIRE" || $role->getRole() == "ROLE_USER"){
                        $this->addFlash('fail','Impossible de supprimer ce rôle');
                        return $this->redirectToRoute('home_index');
                    }else{
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->remove($role);
                        $entityManager->flush();
                    }
                }
            }
        }
        return $this->redirectToRoute('role_index');
    }
}
