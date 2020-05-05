<?php
namespace App\Controller;

use App\Form\AddRoleToSomeoneType;
use App\Repository\PersonneRepository;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function __construct(PersonneRepository $personneRepository, RoleRepository $roleRepository)
    {
        $this->personneRepository = $personneRepository;
        $this->roleRepository = $roleRepository;
    }


    /**
     * @Route("/add", name="manageRole_add",methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $defaultData = ['message','test'];

        $form = $this->createForm(AddRoleToSomeoneType::class,$defaultData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personne = $this->personneRepository->findOneBy(['id' => $request->request->all()["add_role_to_someone"]["personne"]]);
            $role = $this->roleRepository->findOneBy(['id'=> $request->request->all()["add_role_to_someone"]["role"]]);
            $personne->addRole($role);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personne);
            $entityManager->flush();

            return $this->redirectToRoute('role_index');
        }

        return $this->render('manageRole/new.html.twig', [
            'personne' => 1,
            'form' => $form->createView(),
        ]);
    }
}