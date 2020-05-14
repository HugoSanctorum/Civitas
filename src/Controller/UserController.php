<?php

namespace App\Controller;

use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{


    private $personneRepository;


    public function __construct(
        PersonneRepository $personneRepository
    )
    {
        $this->personneRepository = $personneRepository;
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request,AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one


        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        return $this->render('security/home.html.twig');

    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token){
        $personne = $this->personneRepository->findOneBy(["activatedToken" => $token]);
        if($personne === null){
            $this->addFlash('fail','l\'url saisie est invalide.');
            return $this->redirectToRoute('home_index');
        }else{
            $this->addFlash("success",'votre compte a bien été activé.');
            $personne->setActivatedToken(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($personne);
            $em->flush();
            return $this->redirectToRoute('home_index');
        }
    }
}
