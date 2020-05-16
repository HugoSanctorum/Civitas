<?php
namespace App\Controller;

use App\Entity\Personne;
use App\Form\AddRoleToSomeoneType;
use App\Form\RemoveRoleToSomeoneType;
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
     * @Route("/", name="", methods={"GET"})
     */
    public function index(Personne $personne): Response
    {
        return $this->render('probleme/index.html.twig', [
            'problemes' => $problemeRepository->findAll(),
        ]);
    }


    /**
     * @Route("/add", name="manageRole_add",methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(AddRoleToSomeoneType::class);
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
    /**
     * @Route("/{id}/edit", name="manageRole_edit",methods={"GET","POST"})
     */
    public function edit(Request $request,Personne $personne): Response
    {
        $form = $this->createForm(AddRoleToSomeoneType::class,$personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $this->roleRepository->findOneBy(['id'=> $request->request->all()["add_role_to_someone"]["role"]]);
            $personne->addRole($role);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personne);
            $entityManager->flush();

            return $this->redirectToRoute('role_index');
        }

        return $this->render('manageRole/editPassword.html.twig', [
            'personne' => 1,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/remove", name="manageRole_remove",methods={"GET","POST"})
     */
    public function remove(Request $request,Personne $personne): Response
    {
        $form = $this->createForm(RemoveRoleToSomeoneType::class,$personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $this->roleRepository->findOneBy(['id'=> $request->request->all()["remove_role_to_someone"]["role"]]);
            $personne->removeRole($role);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personne);
            $entityManager->flush();

            return $this->redirectToRoute('role_index');
        }

        return $this->render('manageRole/remove.html.twig', [
            'personne' => 1,
            'form' => $form->createView(),
        ]);
    }
}