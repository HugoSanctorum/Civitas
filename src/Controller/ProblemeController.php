<?php

namespace App\Controller;

use App\Entity\Probleme;
use App\Entity\Statut;
use App\Entity\HistoriqueStatut;
use App\Entity\Image;
use App\Entity\Intervenir;
use App\Entity\Personne;
use App\Form\ProblemeType;
use App\Form\ProblemeSearchType;
use App\Form\RedirectProblemeType;
use App\Repository\CategorieRepository;
use App\Repository\CommuneRepository;
use App\Repository\ImageRepository;
use App\Repository\PersonneRepository;
use App\Repository\PrioriteRepository;
use App\Repository\ProblemeRepository;
use App\Repository\StatutRepository;
use App\Services\Geocoder\GeocoderService;
use App\Services\Mailer\MailerService;
use App\Services\Personne\PermissionChecker;
use App\Services\Probleme\ProblemeService;
use App\Services\Probleme\ProblemeSearchInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpParser\Node\Stmt\Label;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Date;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/probleme")
 */
class ProblemeController extends AbstractController
{

    private $personne;
    private $permissionChecker;
    private $problemeService;

    public function __construct(
        TokenStorageInterface $tokenStorageInterface,ProblemeService $problemeService, PermissionChecker $permissionChecker )
    {
        $this->personne = $tokenStorageInterface->getToken()->getUser();
        $this->permissionChecker = $permissionChecker;
        $this->problemeService = $problemeService;
    }

    /**
     * @Route("/{page}", name="probleme_index", methods={"GET", "POST"}, defaults={"page": 1},requirements={"page"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function index(
        Request $request,
        SessionInterface $session,
        ProblemeRepository $problemeRepository,
        CategorieRepository $categorieRepository,
        ProblemeSearchInterface $problemeSearchInterface,
        int $page = 1
    ): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_PROBLEME"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                $form = $this->createForm(ProblemeSearchType::class);
                $form->handleRequest($request);

                $problemeSearchInterface->searchInApp($request->query->all());

                if ($form->isSubmitted() && $form->isValid()) {
                    $tab = $request->request->get("probleme_search");

                    if (array_key_exists("nom", $tab))
                        $session->set("search_nom_probleme", $tab["nom"]);
                    else
                        $session->remove("search_nom_probleme");

                    if (array_key_exists("categories", $tab))
                        $session->set("search_categories", $tab["categories"]);
                    else
                        $session->remove("search_categories");

                    if (array_key_exists("statuts", $tab))
                        $session->set("search_statuts", $tab["statuts"]);
                    else
                        $session->remove("search_statuts");

                    if (array_key_exists("element", $tab))
                        $session->set("search_element", $tab["element"]);
                    else
                        $session->remove("search_element");

                    if (array_key_exists("orderby", $tab))
                        $session->set("search_orderby", $tab["orderby"]);
                    else
                        $session->remove("search_orderby");
                }

                $active_nom = $session->get('search_nom_probleme') ? $session->get('search_nom_probleme') : "";
                $active_categories = $session->get('search_categories') ? $session->get('search_categories') : [];
                $active_statuts = $session->get('search_statuts') ? $session->get('search_statuts') : [];
                $active_element = $session->get('search_element') ? $session->get('search_element') : 20;
                $active_orderby = $session->get('search_orderby') ? $session->get('search_orderby') : "priorite";

                $problemes = $problemeRepository->findPaginateByCategoryAndName($page, $active_element, $active_categories, $active_statuts, $active_nom, $active_orderby);
                $nbr_page = ceil(count($problemeRepository->findAllByCategoryAndName($page, $active_element, $active_categories, $active_statuts, $active_nom)) / $active_element);

                return $this->render('probleme/index.html.twig', [
                    'problemes' => $problemes,
                    'nbr_page' => $nbr_page,
                    'active_page' => $page,
                    'categories' => $categorieRepository->findAll(),
                    'active_nom' => $active_nom,
                    'active_categories' => $active_categories,
                    'active_statuts' => $active_statuts,
                    'active_element' => $active_element,
                    'active_orderby' => $active_orderby,
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    /**
     * @Route("/new", name="probleme_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        GeocoderService $geocoderService,
        SessionInterface $session,
        ProblemeService $problemeService,
        CommuneRepository $communeRepository,
        LoggerInterface $logger
    ): Response
    {

        $probleme = new Probleme();


        $form = $this->createForm(ProblemeType::class, $probleme);
        $form->handleRequest($request);
        $imageArray = []; // 1,2,3,4

        $lng = $request->query->get('lng');
        $lat = $request->query->get('lat');

        if ($form->isSubmitted() && $form->isValid()) {
            $loc = $session->get("adresse");

            $tabImageToProblemes = [];
            for ($i = 1; $i <= 4; $i++) {
                $imageToProbleme = $form['Image' . $i]->getData();
                array_push($tabImageToProblemes, $imageToProbleme);
            }

            if($loc){
                $probleme->setLocalisation($loc);
                $session->remove("adresse");
            }

            $nom_ville = $request->request->all()["probleme"]["nomVille"];

            if(!$nom_ville) $nom_ville = $geocoderService->getNomCommuneFromAdress($loc);

            $commune = $communeRepository->findCommuneByName($nom_ville);

            if(empty($commune)){
                return $this->render("erreurs/communeNotFound.html.twig", [
                    'commune' => $nom_ville
                ]);
            }
            else $probleme->setCommune($commune[0]);
                

            if ($this->personne != "anon.") {
                $problemeService->SetReference($probleme);
                $problemeService->CreateNewProblemeAuthentificated($probleme, $this->personne,);
                $problemeService->UploadImagesNewProbleme($tabImageToProblemes, $probleme);

            }else {
                $session->set('titre', $probleme->getTitre());
                $session->set('description', $probleme->getDescription());
                $session->set('localisation', $probleme->getLocalisation());
                $session->set('commune', $probleme->getCommune());
                $session->set('categorie', $probleme->getCategorie());
                $session->set('priorite', $probleme->getPriorite());
                $session->set('urlImages',$problemeService->GetUrlFromThosesImages($tabImageToProblemes));
                return $this->redirectToRoute('probleme_redirect', [
                    'titre' => $probleme->getTitre(),
                ]);
            }

            return new RedirectResponse("/probleme");
        }

        $lng && $lat ? $adresse = $geocoderService->getAdressFromCoordinate($lat, $lng) : $adresse = null;
        if($adresse)$session->set('adresse', $adresse);

        $adresse ? $render = "probleme/new.modal.html.twig" : $render = "probleme/new.html.twig";

        return $this->render($render, [
            'probleme' => $probleme,
            'form' => $form->createView(),
            'adresse' => $adresse 
        ]);
    }

    /**
     * @Route("/{id}/show", name="probleme_show", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function show(
        Probleme $probleme,
        ImageRepository $imageRepository,
        ProblemeRepository $problemeRepository
    ): Response
    {
        $request = $problemeRepository->findByProblemeByPersonne($probleme, $this->personne);
        if(!$request) {
            if (!$this->permissionChecker->isUserGranted(['GET_OTHER_PROBLEME'])) {
                $this->addFlash('fail', 'vous ne possedez pas les permissions necessaires pour visualiser ce problème.');
                return $this->redirectToRoute('home_index');
            }return $this->render('probleme/show.html.twig', [
                'probleme' => $probleme,
                'images' => $imageRepository->findbyProbleme($probleme)
            ]);
        }else {
            $canValidate = $this->permissionChecker->isUserGranted(["UPDATE_OTHER_HISTORIQUE_STATUT"]);
            $canUpdateStatut = $this->permissionChecker->isUserGranted(["UPDATE_OTHER_HISTORIQUE_STATUT"]);
            return $this->render('probleme/show.html.twig', [
                'probleme' => $probleme,
                'images' => $imageRepository->findbyProbleme($probleme),
                'canValidate' => $canValidate,
                'canUpdateStatut' => $canUpdateStatut
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="probleme_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Probleme $probleme,
        ImageRepository $imageRepository,
        ProblemeService $problemeService

    ): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["UPDATE_OTHER_PROBLEME"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                $images = $imageRepository->findbyProbleme($probleme);


                $form = $this->createForm(ProblemeType::class, $probleme);
                $form->handleRequest($request);
                $tabImageToProblemes = [];
                for ($i = 1; $i <= 4; $i++) {
                    $imageToProbleme = $form['Image' . $i]->getData();
                    array_push($tabImageToProblemes, $imageToProbleme);
                }
                if ($request->query->get('statut')) $render = 'probleme/edit.modal.html.twig';
                else $render = 'probleme/edit.html.twig';

                if ($form->isSubmitted() && $form->isValid()) {
                    $problemeService->UploadImagesNewProbleme($tabImageToProblemes, $probleme);
                    $this->getDoctrine()->getManager()->flush();

                    return $this->redirectToRoute('probleme_show', ['id' => $probleme->getId()]);
                }

                return $this->render($render, [
                    'probleme' => $probleme,
                    'adresse' => $probleme->getLocalisation(),
                    'form' => $form->createView(),
                    'images' => $images
                ]);
            }
        }
    }

    /**
     * @Route("/{id}", name="probleme_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Probleme $probleme,
        ProblemeService $problemeService
    ): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["DELETE_OTHER_PROBLEME"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                if ($this->isCsrfTokenValid('delete' . $probleme->getId(), $request->request->get('_token'))) {
                    $problemeService->DeleteProbleme($probleme);
                }
                $this->addFlash('success', 'le problème "' . $probleme->getTitre() . '" a bien été supprimé');
                return $this->redirectToRoute('probleme_index');
            }
        }
    }

    /**
     * @Route("/redirect/{titre}", name="probleme_redirect", methods={"GET","POST"})
     */
    public function redirectUserWhenAnonyme(
        Request $request,
        PersonneRepository $personneRepository,
        ProblemeService $problemeService,
        SessionInterface $session,
        CommuneRepository $communeRepository,
        CategorieRepository $categorieRepository,
        PrioriteRepository $prioriteRepository
    ): Response
    {
        $form = $this->createForm(RedirectProblemeType::class);
        $form->handleRequest($request);
        $probleme = new Probleme();

        $probleme->setTitre($session->get('titre'));
        $probleme->setDescription($session->get('description'));
        $probleme->setLocalisation($session->get('localisation'));
        $tabUrl = $session->get('urlImages');

        $commune = $communeRepository->findOneBy(['id'=> $session->get('commune')->getId()]);
        $categorie =$categorieRepository->findOneBy(['id' =>$session->get('categorie')->getId()]);
        $priorite = $prioriteRepository->findOneBy(['id' => $session->get('priorite')->getId()]);

        $probleme->setCommune($commune);
        $probleme->setCategorie($categorie);
        $probleme->setPriorite($priorite);
        $problemeService->SetReference($probleme);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $problemeService->PersistUrlWithProbleme($tabUrl, $probleme);
            $mail = $request->request->all()["redirect_probleme"]["mail"];
            $isExisting = $personneRepository->findOneBy(['mail'=>$mail]);
            if($isExisting){
                $problemeService->CreateNewProblemeMailExistingNonAuthentificated($probleme,$isExisting);
            }else{
                $problemeService->CreateNewProblemeMailNonExistingNonAuthentificated($probleme,$mail);
            }
            $entityManager->flush();
            $session->clear();

            return $this->redirectToRoute('probleme_index');
        }else{
/*             $problemeService->DeleteThosesImages($tabUrl);*/
        }
        return $this->render('probleme/assets/redirect.html.twig',[
            'form' => $form->createView(),
            'images' => null,
        ]);

    }
    /**
     *@Route("/validate/{id}", name="probleme_validate", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function validateProblem(
        Probleme $probleme,
        StatutRepository $statutRepository
    ) : Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["UPDATE_OTHER_HISTORIQUE_STATUT"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                if ($probleme->getHistoriqueStatuts()->last()->getStatut()->getNom() != "Nouveau") {
                    return $this->redirectToRoute('probleme_show', ["id" => $probleme->getId()]);
                }

                $entityManager = $this->getDoctrine()->getManager();
                $this->problemeService->CreateNewHistoriqueStatut($probleme,'Ouvert');
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('probleme_show', ["id" => $probleme->getId()]);
    }

    /**
     *@Route("/archive/{id}", name="probleme_archive", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function archiveProblem(
        Probleme $probleme,
        StatutRepository $statutRepository
    ) : Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["UPDATE_OTHER_HISTORIQUE_STATUT"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                if ($probleme->getHistoriqueStatuts()->last()->getStatut()->getNom() != "Résolu") {
                    return $this->redirectToRoute('probleme_show', ["id" => $probleme->getId()]);
                }
                $entityManager = $this->getDoctrine()->getManager();
                $this->problemeService->CreateNewHistoriqueStatut($probleme,'Archivé');
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('probleme_show', ["id" => $probleme->getId()]);
    }

    /**
     *@Route("/restore/{id}", name="probleme_restore", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function restoreProblem(
        Probleme $probleme,
        StatutRepository $statutRepository
    ) : Response
    {
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["UPDATE_OTHER_HISTORIQUE_STATUT"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return new RedirectResponse("/");
            } else {
                if ($probleme->getHistoriqueStatuts()->last()->getStatut()->getNom() != "Archivé") {
                    return $this->redirectToRoute('probleme_show', ["id" => $probleme->getId()]);
                }
                $entityManager = $this->getDoctrine()->getManager();
                $this->problemeService->CreateNewHistoriqueStatut($probleme,"Ouvert");
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('probleme_show', ["id" => $probleme->getId()]);
    }

    /**
     *@Route("/search/reset/{redirect}", name="probleme_search_reset", methods={"GET"})
     */
    public function problemeResetSearch(
        ProblemeSearchInterface $problemeSearchInterface,
        int $redirect = null
    ) : Response
    {
        $problemeSearchInterface->clearSession();

        switch ($redirect) {
            case 1:
                return $this->redirectToRoute('probleme_index');
            case 2:
                return $this->redirectToRoute('personne_signalements');
            case 3:
                return $this->redirectToRoute('personne_interventions');    
            default:
                return $this->redirectToRoute('home_index');
        }        
    }
}
