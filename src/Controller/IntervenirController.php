<?php

namespace App\Controller;

use App\Entity\HistoriqueStatut;
use App\Entity\HistoriqueStatutIntervention;
use App\Entity\Intervenir;
use App\Entity\Probleme;
use App\Form\IntervenirType;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\HistoriqueStatutInterventionRepository;
use App\Repository\IntervenirRepository;
use App\Repository\PersonneRepository;
use App\Repository\ProblemeRepository;
use App\Repository\StatutInterventionRepository;
use App\Repository\StatutRepository;
use App\Services\Mailer\MailerService;
use App\Services\Personne\PermissionChecker;
use App\Services\Probleme\ProblemeService;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/intervenir")
 */
class IntervenirController extends AbstractController
{
    private $tokenStorage;
    private $authorizationChecker;
    private $statutRepository;
    private $problemeRepository;
    private $intervenirRepository;
    private $personneRepository;
    private $permissionChecker;
    private $problemeService;
    private $personne;
    private $statutInterventionRepository;
    private $historiqueStatutInterventionRepository;


    public function __construct(TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        StatutRepository $statutRepository,
        ProblemeRepository $problemeRepository,
        PersonneRepository $personneRepository,
        IntervenirRepository $intervenirRepository,
        PermissionChecker $permissionChecker,
        ProblemeService $problemeService,
        TokenStorageInterface $tokenStorageInterface,
        StatutInterventionRepository $statutInterventionRepository,
        HistoriqueStatutInterventionRepository $historiqueStatutInterventionRepository
    )
    {
        $this->tokenStorage = $tokenStorage; // le token utilisateur
        $this->authorizationChecker = $authorizationChecker; // le service de controle d'utilisateur
        $this->statutRepository =$statutRepository;
        $this->problemeRepository = $problemeRepository;
        $this->intervenirRepository = $intervenirRepository;
        $this->personneRepository = $personneRepository;
        $this->permissionChecker = $permissionChecker;
        $this->problemeService = $problemeService;
        $this->personne = $tokenStorageInterface->getToken()->getUser();
        $this->statutInterventionRepository = $statutInterventionRepository;
        $this->historiqueStatutInterventionRepository = $historiqueStatutInterventionRepository;
    }
    /**
     * @Route("/", name="intervenir_index", methods={"GET"})
     */
    public function index(IntervenirRepository $intervenirRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('fail', 'Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        } else {
            if (!$this->permissionChecker->isUserGranted(["GET_OTHER_INTERVENTION"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                return $this->render('intervenir/index.html.twig', [
                    'intervenirs' => $intervenirRepository->findAll(),
                ]);
            }
        }
    }

    /**
     * @Route("/new/{id}", name="intervenir_new", methods={"GET","POST"}, defaults={"id": null}, requirements={"id"="\d+"})
     */
    public function new(
        MailerService $mailerService,
        Request $request,
        Probleme $probleme = null
    ): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('fail', 'Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if (!$this->permissionChecker->isUserGranted(["POST_INTERVENTION"])) {
                $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                return $this->redirectToRoute('home_index');
            } else {
                $intervenir = new Intervenir();
                $form = $this->createForm(IntervenirType::class, $intervenir, ["Probleme" => $probleme]);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($intervenir);
                    if (!$probleme){
                        $probleme = $this->problemeRepository->findOneBy(['id' => $request->request->all()['intervenir']['Probleme']]);
                    }
                    $this->problemeService->CreateNewHistoriqueStatut($probleme,'Affecté');
                    $technicien = $this->personneRepository->findOneBy(['id' => $request->request->all()['intervenir']['Personne']]);
                    $signaleurIntervention = $this->intervenirRepository->findSignaleurByProbleme($probleme);
                    if ($signaleurIntervention) {
                        $signaleur = $signaleurIntervention->getPersonne();
                        $mailerService->sendMailToTechnicienAffectedProbleme($technicien, $probleme);
                        $mailerService->sendMailToSignaleurAffectedProbleme($signaleur, $probleme);

                        $historiqueStatutIntervention = new HistoriqueStatutIntervention();
                        $historiqueStatutIntervention->setIntervenir($intervenir);
                        $historiqueStatutIntervention->setDate(new \DateTime());
                        $statutIntervention = $this->statutInterventionRepository->findOneBy(["nom" => "En attente de révision"]);
                        $historiqueStatutIntervention->setStatutIntervention($statutIntervention);
                        $entityManager->persist($historiqueStatutIntervention);
                    }
                    $entityManager->flush();
                    return $this->redirectToRoute('probleme_show', ['id' => $probleme->getId()]);
                }

                return $this->render('intervenir/new.html.twig', [
                    'intervenir' => $intervenir,
                    'form' => $form->createView(),
                ]);
            }
        }
    }


    /**
     * @Route("/{id}", name="intervenir_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Intervenir $intervenir): Response
    {
        $request = $this->intervenirRepository->isInterventionBelongToThisPersonne($intervenir, $this->personne);
        if(!$this->isGranted('ROLE_USER')){
            $this->addFlash('fail','Veuillez vous connectez pour acceder à cette page.');
            return $this->redirectToRoute('app_login');
        }else {
            if ($request == false) {
                if($this->permissionChecker->isUserGranted(['GET_OTHER_INTERVENTION'])){
                    return $this->render('intervenir/show.html.twig', [
                        'intervenir' => $intervenir,
                    ]);
                }else {
                    $this->addFlash('fail', 'Cette intervention ne vous concerne pas.');
                    return $this->redirectToRoute("home_index");
                }
            } else {
                if (!$this->permissionChecker->isUserGranted(["GET_SELF_INTERVENTION"])) {
                    $this->addFlash('fail', 'Vous ne possedez pas les permissions necessaires.');
                    return $this->redirectToRoute('home_index');
                } else {
                    $query = $this->historiqueStatutInterventionRepository->getLatestByIntervention($intervenir);
                    if($query) $latest_historique = $query;
                    else $latest_historique = null;
                    return $this->render('intervenir/show.html.twig', [
                        'intervenir' => $intervenir,
                        'latest_historique' => $latest_historique
                    ]);
                }
            }
        }
    }

    /**
     * @Route("/{id}/edit", name="intervenir_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Intervenir $intervenir): Response
    {
        $form = $this->createForm(IntervenirType::class, $intervenir);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('intervenir_index');
        }

        return $this->render('intervenir/edit.html.twig', [
            'intervenir' => $intervenir,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/accept/{id}/{choice}", name="intervenir_accept", methods={"GET"}, requirements={"id": "\d+", "choice": "\d+"})
     *
     * Accepte l'intervention et la refuse si $choice = false
     */
    public function acceptIntervenir(
        Intervenir $intervenir,
        bool $choice
    ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $hsi = new HistoriqueStatutIntervention();

        if(!$choice){
            $this->problemeService->CreateNewHistoriqueStatut($intervenir->getProbleme(), 'Ouvert', "L'intervention a été refusé par le technicien");
            $entityManager->remove($intervenir);
            $entityManager->flush();
            return $this->redirectToRoute('panel_technicien');
        }

        $statut = $this->statutInterventionRepository->findOneBy(['nom' => "Acceptée"]);
        $hsi->setStatutIntervention($statut);
        $hsi->setIntervenir($intervenir);
        $hsi->setDate(new \DateTime());
        $entityManager->persist($hsi);
        $entityManager->flush();

        return $this->redirectToRoute('panel_technicien');
    }

    /**
     * @Route("/run/{id}", name="intervenir_run", methods={"GET"}, requirements={"id": "\d+"})
     *
     * Démarre l'intervention et la reprend si elle était suspendue
     */
    public function runIntervenir(
        Intervenir $intervenir
    ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $hsi = new HistoriqueStatutIntervention();

        $hstatut = $this->historiqueStatutInterventionRepository->getLatestByIntervention($intervenir);
        if (!($hstatut->getStatutIntervention()->getNom() == "Acceptée" || $hstatut->getStatutIntervention()->getNom() == "Suspendue")){
            return $this->redirectToRoute('panel_technicien');
        }

        $statut = $this->statutInterventionRepository->findOneBy(['nom' => "En traitement"]);
        $hsi->setStatutIntervention($statut);   

        $this->problemeService->CreateNewHistoriqueStatut($intervenir->getProbleme(), 'En cours de traitement');

        $hsi->setIntervenir($intervenir);
        $hsi->setDate(new \DateTime());
        $entityManager->persist($hsi);
        $entityManager->flush();

        return $this->redirectToRoute('panel_technicien');
    }

    /**
     * @Route("/suspend/{id}", name="intervenir_suspend", methods={"GET"}, requirements={"id": "\d+"})
     *
     * Suspend l'intervention
     */
    public function suspendIntervenir(
        Intervenir $intervenir
    ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $hsi = new HistoriqueStatutIntervention();

        $hstatut = $this->historiqueStatutInterventionRepository->getLatestByIntervention($intervenir);
        if (!$hstatut->getStatutIntervention()->getNom() == "En traitement"){
            return $this->redirectToRoute('panel_technicien');
        }

        $statut = $this->statutInterventionRepository->findOneBy(['nom' => "Suspendue"]);
        $hsi->setStatutIntervention($statut);   

        $this->problemeService->CreateNewHistoriqueStatut($intervenir->getProbleme(), 'Affecté');

        $hsi->setIntervenir($intervenir);
        $hsi->setDate(new \DateTime());
        $entityManager->persist($hsi);
        $entityManager->flush();

        return $this->redirectToRoute('panel_technicien');
    }

    /**
     * @Route("/abort/{id}", name="intervenir_abort", methods={"GET"}, requirements={"id": "\d+"})
     *
     * Annule l'intervention
     */
    public function abortIntervenir(
        Intervenir $intervenir
    ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $hsi = new HistoriqueStatutIntervention();

        $statut = $this->statutInterventionRepository->findOneBy(['nom' => "Annulée"]);
        $hsi->setStatutIntervention($statut);   

        $this->problemeService->CreateNewHistoriqueStatut($intervenir->getProbleme(), 'Affecté', "La dernière intervention a été annulé par le technicien");

        $hsi->setIntervenir($intervenir);
        $hsi->setDate(new \DateTime());
        $entityManager->persist($hsi);
        $entityManager->flush();

        return $this->redirectToRoute('panel_technicien');
    }

    /**
     * @Route("/resolve/{id}", name="intervenir_resolve", methods={"GET"}, requirements={"id": "\d+"})
     *
     * Résoud l'intervention
     */
    public function resolveIntervenir(
        Intervenir $intervenir
    ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $hsi = new HistoriqueStatutIntervention();

        $statut = $this->statutInterventionRepository->findOneBy(['nom' => "Terminée"]);
        $hsi->setStatutIntervention($statut);

        $hsi->setIntervenir($intervenir);
        $hsi->setDate(new \DateTime());
        $entityManager->persist($hsi);
        $entityManager->flush();

        return $this->redirectToRoute('panel_technicien');
    }

    /**
    * @Route("/{id}", name="intervenir_delete", methods={"DELETE"}, requirements={"id": "\d+"})
    */
    public function delete(Request $request, Intervenir $intervenir): Response
    {
       if ($this->isCsrfTokenValid('delete'.$intervenir->getId(), $request->request->get('_token'))) {
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->remove($intervenir);
           $entityManager->flush();
       }

       return $this->redirectToRoute('intervenir_index');
    }

}