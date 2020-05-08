<?php

namespace App\Controller;

use App\Entity\HistoriqueStatut;
use App\Entity\Image;
use App\Entity\Intervenir;
use App\Entity\Personne;
use App\Entity\Probleme;
use App\Form\ProblemeType;
use App\Form\RedirectProblemeType;
use App\Repository\ImageRepository;
use App\Repository\ProblemeRepository;
use App\Repository\StatutRepository;
use App\Service\Geocoder\GeocoderService;
use App\Service\MailerService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/probleme")
 */
class ProblemeController extends AbstractController
{

    private $personne;


    public function __construct(
        TokenStorageInterface $tokenStorageInterface
    ){
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
        MailerService $mailerService,
        Request $request,
        StatutRepository $statutRepository,
        GeocoderService $geocoderService
    ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $probleme = new Probleme();
        $statut = $statutRepository->findOneBy(['nom' => 'Nouveau']);
        $historiqueStatut = new HistoriqueStatut();
        $intervenir = new Intervenir();
        $form = $this->createForm(ProblemeType::class, $probleme);
        $form->handleRequest($request);
        $imageArray = []; // 1,2,3,4

        $lng = $request->query->get('lng');
        $lat = $request->query->get('lat');

        $lng && $lat ? $adresse = $geocoderService->getAdressFromCoordinate($lat, $lng) : $adresse = null;

        if ($form->isSubmitted() && $form->isValid()) {

            for ($i = 1; $i <= 4; $i++) {
                $imageArray[$i] = new Image();
                $imageToProbleme = $form['Image' . $i]->getData();
                if ($imageToProbleme) {
                    $originalFilename = pathinfo($imageToProbleme->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = transliterator_transliterate(
                        'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                        $originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' .
                        $imageToProbleme->guessExtension();
                    // Move the file to the directory where brochures are stored
                    try {
                        $imageToProbleme->move(
                            $this->getParameter('probleme_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('danger',
                            'Error on fileUpload :' . $e->getMessage());
                        return $this->redirectToRoute('home');
                    }

                    if ($imageArray[$i] != null) {
                        $imageArray[$i]->setProbleme($probleme);
                        $imageArray[$i]->setURL($newFilename);
                        $entityManager->persist($imageArray[$i]);
                    }
                }
            }

            $historiqueStatut->setProbleme($probleme);
            $historiqueStatut->setStatut($statut);
            $historiqueStatut->setDate(new \DateTime('now'));
            $historiqueStatut->setDescription('Le problème a été créé');

            $intervenir->setProbleme($probleme);
            if($this->personne != "anon.") {
                $intervenir->setPersonne($this->personne);
            }else{
                return $this->redirectToRoute('probleme_redirect',[
                    "titre" => $probleme->getTitre(),
                    "description" => $probleme->getDescription(),
                    "localisation" => $probleme->getLocalisation(),
                    "commune" => $probleme->getCommune(),
                    "categorie" => $probleme->getCategorie(),
                    "priorite" => $probleme->getPriorite(),
                ]);
            }
            $intervenir->setCreatedAt(new \DateTime('now'));
            $intervenir->setDescription('Signaleur');

            $entityManager->persist($historiqueStatut);
            $entityManager->persist($probleme);
            $entityManager->persist($intervenir);

            $mailerService->sendMailToSignaleurNewProbleme($this->personne,$probleme);

            $entityManager->flush();

            return $this->redirectToRoute('probleme_index');
        }
        return $this->render('probleme/new.html.twig', [
            'probleme' => $probleme,
            'form' => $form->createView(),
            'adresse' => $adresse 
        ]);
    }

    /**
     * @Route("/{id}", name="probleme_show", methods={"GET"})
     */
    public function show(Probleme $probleme,ImageRepository $imageRepository): Response
    {

        return $this->render('probleme/show.html.twig', [
            'probleme' => $probleme,
            'images' => $imageRepository->findbyProbleme($probleme)

        ]);
    }

    /**
     * @Route("/{id}/edit", name="probleme_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Probleme $probleme): Response
    {
        $form = $this->createForm(ProblemeType::class, $probleme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('probleme_index');
        }

        return $this->render('probleme/edit.html.twig', [
            'probleme' => $probleme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="probleme_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Probleme $probleme): Response
    {
        if ($this->isCsrfTokenValid('delete'.$probleme->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($probleme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('probleme_index');
    }

    /**
     * @Route("/{titre}/redirect", name="probleme_redirect", methods={"GET","POST"})
     */
    public function redirectUserWhenAnonyme(Request $request): Response
    {

        $personne = new Personne();
        $form = $this->createForm(RedirectProblemeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personne->setMail($request->request->all()["redirect_probleme"]["mail"]);
            $personne->setCreatedAt(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personne);
            $entityManager->flush();
            return $this->redirectToRoute('probleme_index');
        }
        return $this->render('probleme/redirect.html.twig',[
            'form' => $form->createView()
        ]);

    }
}
