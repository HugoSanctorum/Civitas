<?php

namespace App\Controller;

use App\Form\PasswdType;
use App\Form\ResetPasswordMailType;
use App\Form\ResetPasswordType;
use App\Repository\PersonneRepository;
use App\Services\Mailer\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Encoder\EncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{

    private $personneRepository;
    private $tokenGenerator;
    private $mailerService;
    private $encoder;

    public function __construct(
        PersonneRepository $personneRepository,
        TokenGeneratorInterface $tokenGenerator,
        UserPasswordEncoderInterface $encoder,
        MailerService $mailerService
    )
    {
        $this->personneRepository = $personneRepository;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailerService = $mailerService;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home_index', [], 301);
        }

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

    /**
     * @Route("unsubscribe/{subscribeToken}", name="unsubscribe")
     */
    public function Unsubscribe($subscribeToken){
        $personne = $this->personneRepository->findOneBy(['subscribeToken' => $subscribeToken]);
        if($personne === null) {
            $this->addFlash('fail', 'l\'url saisie est invalide.');
            return $this->redirectToRoute('home_index');
        }else{
            $this->addFlash("success",'vous êtes bien désabonné des mails.');
            $personne->setSubscribeToken(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($personne);
            $em->flush();
            return $this->redirectToRoute('home_index');
        }
    }
    /**
     * @Route("/reset_password", name="reset_password")
     */
    public function resetPassword(Request $request){
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ResetPasswordMailType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mail=($request->request->all()['reset_password_mail']['email']);
            $personne = $this->personneRepository->findOneBy(['mail' => $mail]);
            if(!$personne){
                $this->addFlash('fail','Ce mail n\'est associé à aucun compte');
                return $this->redirectToRoute('reset_password');
            }else{
                $token = $this->tokenGenerator->generateToken();
                $personne->setResetPasswordToken($token);
                $personne->setResetPasswordToken(null);
                $em->persist($personne);
                $em->flush();
                $this->mailerService->sendMailResetPassword($personne,$token);
                $this->addFlash('success','Un email de réinitialisation de mot de passe a été envoyé.');
                return $this->redirectToRoute('home_index');
            }
        }
        return $this->render('resetPassword/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/reset_passwordConfirmed/{resetPasswordToken}", name="reset_passwordConfirmed",  )
     */
    public function resetPasswordConfirmedMail(Request $request, String $resetPasswordToken){
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        $personne = $this->personneRepository->findOneBy(['resetPasswordToken' => $resetPasswordToken]);

        if(!$personne){
            $this->addFlash('fail','cet URL est invalide');
            return $this->redirectToRoute('home_index');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $request->request->all()['reset_password']['password'];
            $password2 = $request->request->all()['reset_password']['password2'];
            if($password != $password2){
                $this->addFlash('fail','les deux mots de passe ne sont pas équivalent');
                return $this->redirectToRoute('reset_passwordConfirmed', ['resetPasswordToken' => $resetPasswordToken]);
            }else{
                $encoded = $this->encoder->encodePassword($personne, $password);
                $personne->setPassword($encoded);
                $em->persist($personne);
                $em->flush();
                $this->addFlash('success','votre mot de passe a bien été modifié.');
                $this->mailerService->sendMailPasswordChanged($personne);
                return $this->redirectToRoute('home_index');
            }
        }
        return $this->render('resetPassword/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
