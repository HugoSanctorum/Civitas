<?php

namespace App\Controller;

use App\Entity\HistoriqueStatut;
use App\Entity\Intervenir;
use App\Form\IntervenirType;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\IntervenirRepository;
use App\Repository\PersonneRepository;
use App\Repository\ProblemeRepository;
use App\Repository\StatutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker,
                                StatutRepository $statutRepository, ProblemeRepository $problemeRepository, PersonneRepository $personneRepository,IntervenirRepository $intervenirRepository)
    {
        $this->tokenStorage = $tokenStorage; // le token utilisateur
        $this->authorizationChecker = $authorizationChecker; // le service de controle d'utilisateur
        $this->statutRepository =$statutRepository;
        $this->problemeRepository = $problemeRepository;
        $this->intervenirRepository = $intervenirRepository;
        $this->personneRepository = $personneRepository;
    }
    /**
     * @Route("/", name="intervenir_index", methods={"GET"})
     */
    public function index(IntervenirRepository $intervenirRepository): Response
    {
        return $this->render('intervenir/index.html.twig', [
            'intervenirs' => $intervenirRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="intervenir_new", methods={"GET","POST"})
     */
    public function new(\Swift_Mailer $mailer,Request $request): Response
    {


        $intervenir = new Intervenir();
        $statut = $this->statutRepository->findOneBy(['nom' => 'Affecté']);
        $historiqueStatut = new HistoriqueStatut();
        $form = $this->createForm(IntervenirType::class, $intervenir);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($intervenir);
            $technicien = $this->personneRepository->findOneBy(['id' =>$request->request->all()['intervenir']['Personne']]);

            $probleme = $this->problemeRepository->findOneBy(['id' =>$request->request->all()['intervenir']['Probleme']]);
            $historiqueStatut->setProbleme($probleme);
            $historiqueStatut->setStatut($statut);
            $historiqueStatut->setDate(new \DateTime('now'));
            $historiqueStatut->setDescription('Le probleme a été affecté');
            $test = $this->personneRepository->findOneBy(['id'=>1]);
            $signaleur = $this->intervenirRepository->findSignaleurByProbleme($probleme);
            $entityManager->persist($historiqueStatut);
            $entityManager->flush();
            $technicienMail = (new \Swift_Message('Probleme affecté'))
                ->setFrom('civitasnotification@gmail.com')
                ->setTo($technicien->getMail())
                ->addPart(
                    $this->renderView('email/notifNouvelleIntervention.html.twig',
                        [
                            "probleme" => $probleme,
                            "technicien" => $technicien
                        ]),
           'text/html'
                );
            $signaleurMail = (new \Swift_Message('Probleme affecté'))
                ->setFrom('civitasnotification@gmail.com')
                ->setTo($signaleur->getPersonne()->getMail())
                ->addPart(
                    $this->renderView('email/notifProblemeAffecte.html.twig',
                        [
                            "probleme" => $probleme,
                            "signaleur" => $signaleur
                        ]),
                    'text/html'
                );

            $mailer->send($technicienMail);
            $mailer->send($signaleurMail);

            return $this->redirectToRoute('intervenir_index');
        }

        return $this->render('intervenir/new.html.twig', [
            'intervenir' => $intervenir,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="intervenir_show", methods={"GET"})
     */
    public function show(Intervenir $intervenir): Response
    {
        return $this->render('intervenir/show.html.twig', [
            'intervenir' => $intervenir,
        ]);
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
     * @Route("/{id}", name="intervenir_delete", methods={"DELETE"})
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
