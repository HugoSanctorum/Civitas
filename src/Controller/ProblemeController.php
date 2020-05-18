<?php

namespace App\Controller;

use App\Entity\Probleme;
use App\Entity\Statut;
use App\Entity\HistoriqueStatut;
use App\Entity\Image;
use App\Entity\Intervenir;
use App\Entity\Personne;
use App\Form\ProblemeType;
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
use App\Services\Probleme\ProblemeService;
use Doctrine\Common\Collections\ArrayCollection;
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

/**
 * @Route("/probleme")
 */
class ProblemeController extends AbstractController
{

    private $personne;

    public function __construct(
        TokenStorageInterface $tokenStorageInterface
    )
    {
        $this->personne = $tokenStorageInterface->getToken()->getUser();
    }

    /**
     * @Route("/", name="probleme_index", methods={"GET"})
     */
    public function index(ProblemeRepository $problemeRepository): Response
    {
        return $this->render('probleme/index.html.twig', [
            'problemes' => $problemeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="probleme_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        GeocoderService $geocoderService,
        SessionInterface $session,
        ProblemeService $problemeService,
        CommuneRepository $communeRepository
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
                $problemeService->CreateNewProblemeMailExisting($probleme, $this->personne);
                $problemeService->UploadImagesNewProbleme($tabImageToProblemes, $probleme);

            } else {
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
     * @Route("/{id}", name="probleme_show", methods={"GET"})
     */
    public function show(
        Probleme $probleme,
        ImageRepository $imageRepository
    ): Response
    {
        return $this->render('probleme/show.html.twig', [
            'probleme' => $probleme,
            'images' => $imageRepository->findbyProbleme($probleme)

        ]);
    }

    /**
     * @Route("/{id}/edit", name="probleme_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Probleme $probleme
    ): Response
    {
        $form = $this->createForm(ProblemeType::class, $probleme);
        $form->handleRequest($request);

        if($request->query->get('statut')) $render = 'probleme/edit.modal.html.twig';
        else $render = 'probleme/edit.html.twig';

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('probleme_show', ['id' => $probleme->getId()]);
        }

        return $this->render($render, [
            'probleme' => $probleme,
            'adresse' => $probleme->getLocalisation(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="probleme_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Probleme $probleme
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$probleme->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($probleme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('probleme_index');
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
        $probleme->setDateDeDeclaration(new \DateTime('now'));
        $problemeService->SetReference($probleme);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($probleme);
            $mail = $request->request->all()["redirect_probleme"]["mail"];
            $isExisting = $personneRepository->findOneBy(['mail'=>$mail]);
            if($isExisting){
                $problemeService->CreateNewProblemeMailExisting($probleme,$isExisting);
            }else{
                $problemeService->CreateNewIntervenirNonAuthentificated($probleme,$mail);
                $problemeService->PersistUrlWithProbleme($tabUrl, $probleme);
            }
            $entityManager->flush();

            return $this->redirectToRoute('probleme_index');
        }else{
/*             $problemeService->DeleteThosesImages($tabUrl);*/
        }
        return $this->render('probleme/redirect.html.twig',[
            'form' => $form->createView()
        ]);

    }
    /**
     *@Route("/validate/{id}/{statut}", name="probleme_validate", methods={"GET","POST"}, requirements={"id"="\d+", "statut"="\d+"})
     */
    public function validateProblem(
        Probleme $probleme,
        Statut $statut,
        StatutRepository $statutRepository
    ) : Response
    {
        if($probleme->getHistoriqueStatuts()->last()->getStatut()->getNom() != "Nouveau"){
            return $this->redirectToRoute('probleme_show', ["id" => $probleme->getId()]);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $hs = new HistoriqueStatut();
        $hs->setProbleme($probleme);
        $hs->setStatut($statutRepository->findOneBy(["nom" => "Ouvert"]));
        $hs->setDate(new \DateTime());

        $entityManager->persist($hs);
        $entityManager->flush();


        return $this->redirectToRoute('probleme_show', ["id" => $probleme->getId()]);
    }
}
