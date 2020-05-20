<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Form\ProfileType;
use App\Form\PasswdType;
use App\Repository\PersonneRepository;
use App\Services\Mailer\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class PersonneController extends AbstractController
{

    private $encoder;
    private $personne;
    private $tokenGenerator;
    private $personneRepository;

    public function __construct(PersonneRepository $personneRepository, UserPasswordEncoderInterface $encoder,TokenStorageInterface $tokenStorageInterface, TokenGeneratorInterface $tokenGenerator)
    {
        $this->encoder = $encoder;
        $this->personne = $tokenStorageInterface->getToken()->getUser();
        $this->tokenGenerator = $tokenGenerator;
        $this->personneRepository = $personneRepository;


    }
    /**
     * @Route("personne/", name="personne_index", methods={"GET"})
     */
    public function index(PersonneRepository $personneRepository): Response
    {
        return $this->render('personne/index.html.twig', [
            'personnes' => $personneRepository->findAll(),
        ]);
    }

    /**
     * @Route("/senregistrer", name="personne_new", methods={"GET","POST"})
     */
    public function new(MailerService $mailerService,Request $request): Response
    {
        $personne = new Personne();
        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personneMail = $this->personneRepository->findOneBy(['mail' => $request->request->all()["personne"]["mail"]]);
            if (!$personneMail) {
                $nom = $request->request->all()['personne']['nom'];
                $prenom = $request->request->all()['personne']['prenom'];
                $personne->setUsername($nom . '_' . $prenom);
                $plainPassword = $request->request->all()['personne']['password'];
                $encoded = $this->encoder->encodePassword($personne, $plainPassword);
                $activatedToken = $this->tokenGenerator->generateToken();
                $personne->setActivatedToken($activatedToken);
                $subscribeToken = $this->tokenGenerator->generateToken();
                $personne->setSubscribeToken($subscribeToken);
                $personne->setPassword($encoded);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($personne);
                $mailerService->sendMailActivatedAccount($personne, $activatedToken);
                $entityManager->flush();
                return $this->redirectToRoute('personne_index');
            }elseif (!$personneMail->getPassword()) {
                $nom = $request->request->all()['personne']['nom'];
                $prenom = $request->request->all()['personne']['prenom'];
                $personneMail->setUsername($nom . '_' . $prenom);
                $plainPassword = $request->request->all()['personne']['password'];
                $encoded = $this->encoder->encodePassword($personneMail, $plainPassword);
                $activatedToken = $this->tokenGenerator->generateToken();
                $personneMail->setActivatedToken($activatedToken);
                $subscribeToken = $this->tokenGenerator->generateToken();
                $personneMail->setSubscribeToken($subscribeToken);
                $personneMail->setPassword($encoded);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($personneMail);
                $mailerService->sendMailActivatedAccount($personneMail, $activatedToken);
                $entityManager->flush();
                return $this->redirectToRoute('personne_index');
            }else{
                $this->addFlash("fail","Cette adresse mail est déjà utilisé.");
                return $this->redirectToRoute('/senregistrer');
            }

        }
            return $this->render('personne/new.html.twig', [
                'personne' => $personne,
                'form' => $form->createView(),
            ]);

    }

    /**
     * @Route("personne/{id}", name="personne_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Personne $personne): Response
    {
        return $this->render('personne/show.html.twig', [
            'personne' => $personne,
        ]);
    }

    /**
     * @Route("personne/{id}/edit", name="personne_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Personne $personne): Response
    {
        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('personne_index');
        }

        return $this->render('personne/edit.html.twig', [
            'personne' => $personne,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("personne/{id}", name="personne_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Personne $personne): Response
    {
        if ($this->isCsrfTokenValid('delete'.$personne->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($personne);
            $entityManager->flush();
        }

        return $this->redirectToRoute('personne_index');
    }

    /**
     * @Route("/profile", name="personne_profile", methods={"GET","POST"})
     */
    public function profile(Request $request): Response
    {
        $form = $this->createForm(ProfileType::class, $this->personne);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if(key_exists('subscribeToken',$request->request->all()['profile'])){
                $subscribeToken = $this->tokenGenerator->generateToken();
                $this->personne->setSubscribeToken($subscribeToken);
            }else{
                $this->personne->setSubscribeToken(null);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($this->personne);
            $entityManager->flush();
            return $this->redirectToRoute('personne_index');
        }

        return $this->render('personne/new.html.twig', [
            'personne' => $this->personne,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ModifierMotDePasse", name="personne_editPassword", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function editPassword(Request $request): Response
    {
        $form = $this->createForm(PasswdType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $request->request->all()["passwd"]["oldPassword"];
            $newPassword = $request->request->all()["passwd"]["newPassword"];
            $newEncoded = $this->encoder->encodePassword($this->personne, $newPassword);

            if($this->encoder->isPasswordValid($this->personne,$oldPassword)){
                $this->personne->setPassword($newEncoded);
                $this->addFlash('success','Votre mot de passe a bien été modifié :)');
                $em = $this->getDoctrine()->getManager();
                $em->persist($this->personne);
                $em->flush();
                return $this->redirectToRoute('home_index');
            }else{
                $this->addFlash('fail','Mot de passe incorrect');
                return $this->redirectToRoute('personne_editPassword');
            }

        }
        return $this->render('password/editPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
