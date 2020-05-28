<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Form\ProblemeSearchType;
use App\Form\ProfileType;
use App\Form\PasswdType;
use App\Repository\CategorieRepository;
use App\Repository\CommuneRepository;
use App\Repository\PersonneRepository;
use App\Repository\ProblemeRepository;
use App\Repository\RoleRepository;
use App\Services\Mailer\MailerService;
use App\Services\Personne\PermissionChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    private $communeRepository;
    private $permissionChecker;
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository,PermissionChecker $permissionChecker, CommuneRepository $communeRepository, PersonneRepository $personneRepository, UserPasswordEncoderInterface $encoder,TokenStorageInterface $tokenStorageInterface, TokenGeneratorInterface $tokenGenerator)
    {
        $this->encoder = $encoder;
        $this->personne = $tokenStorageInterface->getToken()->getUser();
        $this->tokenGenerator = $tokenGenerator;
        $this->personneRepository = $personneRepository;
        $this->communeRepository = $communeRepository;
        $this->permissionChecker = $permissionChecker;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @Route("/mes_signalements/{page}", name="personne_signalements", methods={"GET","POST"}, defaults={"page": 1}, requirements={"page"="\d+"})
     */
    public function mesSignalements(
        Request $request,
        SessionInterface $session,
        ProblemeRepository $problemeRepository,
        CategorieRepository $categorieRepository,
        int $page = 1): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else{
            if(!$this->permissionChecker->isUserGranted(["GET_SELF_PROBLEME"])){
                $this->addFlash('fail','Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            }
        }
        $form = $this->createForm(ProblemeSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tab = $request->request->get("probleme_search");

            if(array_key_exists("nom", $tab))
                $session->set("search_nom_probleme", $tab["nom"]);
            else
                $session->remove("search_nom_probleme");

            if(array_key_exists("categories", $tab))
                $session->set("search_categories", $tab["categories"]);
            else
                $session->remove("search_categories");

            if(array_key_exists("statuts", $tab))
                $session->set("search_statuts", $tab["statuts"]);
            else
                $session->remove("search_statuts");

            if(array_key_exists("element", $tab))
                $session->set("search_element", $tab["element"]);
            else
                $session->remove("search_element");
        }

        $active_nom = $session->get('search_nom_probleme') ? $session->get('search_nom_probleme') : "";
        $active_categories = $session->get('search_categories') ? $session->get('search_categories') : [];
        $active_statuts = $session->get('search_statuts') ? $session->get('search_statuts') : [];
        $active_element = $session->get('search_element') ? $session->get('search_element') : 20;

        $problemes = $problemeRepository->findPaginateByCategoryAndName($page, $active_element, $active_categories, $active_statuts, $active_nom, "Signaleur");

        $nbr_page = ceil(count($problemeRepository->findAllByCategoryAndName($page, $active_element, $active_categories, $active_statuts, $active_nom, "Signaleur"))/$active_element);

        return $this->render('personne/mesSignalements.html.twig', [
            'problemes' => $problemes,
            'nbr_page' => $nbr_page,
            'active_page' => $page,
            'categories' => $categorieRepository->findAll(),
            'active_nom' => $active_nom,
            'active_categories' => $active_categories,
            'active_statuts' => $active_statuts,
            'active_element' => $active_element,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mes_interventions/{page}", name="personne_interventions", methods={"GET","POST"}, defaults={"page": 1}, requirements={"page"="\d+"})
     */

    public function mesInterventions(
        Request $request,
        SessionInterface $session,
        ProblemeRepository $problemeRepository,
        CategorieRepository $categorieRepository,
        int $page = 1): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else{
            if(!$this->permissionChecker->isUserGranted(["GET_INTERVENED_PROBLEME"])){
                $this->addFlash('fail','Vous ne possedez pas les permissions necessaires');
                return new RedirectResponse("/");
            }
        }

        $form = $this->createForm(ProblemeSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tab = $request->request->get("probleme_search");

            if(array_key_exists("nom", $tab))
                $session->set("search_nom_probleme", $tab["nom"]);
            else
                $session->remove("search_nom_probleme");

            if(array_key_exists("categories", $tab))
                $session->set("search_categories", $tab["categories"]);
            else
                $session->remove("search_categories");

            if(array_key_exists("statuts", $tab))
                $session->set("search_statuts", $tab["statuts"]);
            else
                $session->remove("search_statuts");

            if(array_key_exists("element", $tab))
                $session->set("search_element", $tab["element"]);
            else
                $session->remove("search_element");
        }

        $active_nom = $session->get('search_nom_probleme') ? $session->get('search_nom_probleme') : "";
        $active_categories = $session->get('search_categories') ? $session->get('search_categories') : [];
        $active_statuts = $session->get('search_statuts') ? $session->get('search_statuts') : [];
        $active_element = $session->get('search_element') ? $session->get('search_element') : 20;

        $problemes = $problemeRepository->findPaginateByCategoryAndName($page, $active_element, $active_categories, $active_statuts, $active_nom, "Technicien");

        $nbr_page = ceil(count($problemeRepository->findAllByCategoryAndName($page, $active_element, $active_categories, $active_statuts, $active_nom, "Technicien"))/$active_element);

        return $this->render('personne/mesInterventions.html.twig', [
            'problemes' => $problemes,
            'nbr_page' => $nbr_page,
            'active_page' => $page,
            'categories' => $categorieRepository->findAll(),
            'active_nom' => $active_nom,
            'active_categories' => $active_categories,
            'active_statuts' => $active_statuts,
            'active_element' => $active_element,
            'form' => $form->createView(),
        ]);
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
            $communeId = $request->request->all()['personne']['Commune'];
            $commune = $this->communeRepository->findOneBy(['id' => $communeId]);
            $personneMail = $this->personneRepository->findOneBy(['mail' => $request->request->all()["personne"]["mail"]]);
            if (!$personneMail) {
                $nom = $request->request->all()['personne']['nom'];
                $prenom = $request->request->all()['personne']['prenom'];
                $personne->setNom($nom);
                $personne->setPrenom($prenom);
                $personne->setUsername($nom . '_' . $prenom);
                $personne->addRole($this->roleRepository->findOneBy(['role' => 'ROLE_USER']));

                $personne->setCommune($commune);

                $plainPassword = $request->request->all()['personne']['password'];
                $encoded = $this->encoder->encodePassword($personne, $plainPassword);
                $personne->setPassword($encoded);

                $activatedToken = $this->tokenGenerator->generateToken();
                $personne->setActivatedToken($activatedToken);

                $subscribeToken = $this->tokenGenerator->generateToken();
                $personne->setSubscribeToken($subscribeToken);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($personne);
                $mailerService->sendMailActivatedAccount($personne, $activatedToken);
                $entityManager->flush();
                return $this->redirectToRoute('personne_index');
            } elseif (!$personneMail->getPassword()) {
                $nom = $request->request->all()['personne']['nom'];
                $prenom = $request->request->all()['personne']['prenom'];
                $personneMail->setNom($nom);
                $personneMail->setPrenom($prenom);

                $personneMail->setCommune($commune);

                $personneMail->setUsername($nom . '_' . $prenom);
                $plainPassword = $request->request->all()['personne']['password'];
                $encoded = $this->encoder->encodePassword($personneMail, $plainPassword);
                $personneMail->setPassword($encoded);
                $personneMail->addRole($this->roleRepository->findOneBy(['role' => 'ROLE_USER']));

                $activatedToken = $this->tokenGenerator->generateToken();
                $personneMail->setActivatedToken($activatedToken);

                $subscribeToken = $this->tokenGenerator->generateToken();
                $personneMail->setSubscribeToken($subscribeToken);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($personneMail);

                $mailerService->sendMailActivatedAccount($personneMail, $activatedToken);

                $entityManager->flush();
                return $this->redirectToRoute('personne_index');
            } else {
                $this->addFlash("fail", "Cette adresse mail est déjà utilisé.");
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
