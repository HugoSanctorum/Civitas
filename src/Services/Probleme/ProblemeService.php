<?php


namespace App\Services\Probleme;

use App\Entity\Intervenir;
use App\Entity\HistoriqueStatut;
use App\Entity\Personne;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\StatutRepository;
use App\Services\Mailer\MailerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class ProblemeService
{
    private $entityManager;
    private $statutRepository;
    private $mailerService;
    private $personne;

    public function __construct(EntityManagerInterface $entityManager, StatutRepository $statutRepository, MailerService $mailerService, TokenStorageInterface $tokenStorageInterface)
    {
        $this->entityManager = $entityManager;
        $this->statutRepository = $statutRepository;
        $this->mailerService = $mailerService;
        $this->personne = $tokenStorageInterface->getToken()->getUser();
    }

    public function CreateNewProblemeAuthentificated($probleme,$personne)
    {
        $this->entityManager->persist($probleme);

        $intervenir = new Intervenir();

        $intervenir->setProbleme($probleme);
        $intervenir->setPersonne($personne);
        $intervenir->setCreatedAt(new \DateTime('now'));
        $intervenir->setDescription('Signaleur');

        $this->CreateNewHistoriqueStatut($probleme);
        $this->entityManager->persist($intervenir);
        $this->entityManager->flush();

        $this->mailerService->sendMailToSignaleurNewProbleme($this->personne,$probleme);
    }

    public function CreateNewIntervenirNonAuthentificated($probleme,$mail){

        $intervenir = new Intervenir();
        $personne = new Personne();

        $personne->setMail($mail);
        $personne->setCreatedAt(new \DateTime('now'));

        $intervenir->setProbleme($probleme);
        $intervenir->setPersonne($personne);
        $intervenir->setCreatedAt(new \DateTime('now'));
        $intervenir->setDescription('Signaleur');
        $this->CreateNewHistoriqueStatut($probleme);

        $this->entityManager->persist($intervenir);
        $this->entityManager->persist($personne);
        $this->entityManager->flush();
        $this->mailerService->sendMailToSignaleurNewProbleme($personne,$probleme);

    }

    public function CreateNewHistoriqueStatut($probleme){
        $statut = $this->statutRepository->findOneBy(['nom' => 'Nouveau']);
        $historiqueStatut = new HistoriqueStatut();

        $historiqueStatut->setProbleme($probleme);
        $historiqueStatut->setStatut($statut);
        $historiqueStatut->setDate(new \DateTime('now'));
        $historiqueStatut->setDescription('Le problème a été créé');
        $this->entityManager->persist($historiqueStatut);

    }

    public function UploadImageNewProbleme(ArrayCollection $imageToProblemes)
    {
        foreach ($imageToProblemes as $imageToProbleme) {
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
    }
}