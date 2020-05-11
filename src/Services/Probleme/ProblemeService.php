<?php


namespace App\Services\Probleme;

use App\Entity\Image;
use App\Entity\Intervenir;
use App\Entity\HistoriqueStatut;
use App\Entity\Personne;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\StatutRepository;
use App\Services\Mailer\MailerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class ProblemeService extends AbstractController
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

    public function UploadImagesNewProbleme($tabImageToProblemes,$probleme)
    {
        foreach ($tabImageToProblemes as $tabImageToProbleme) {
            $i=1;
            if ($tabImageToProbleme) {
                $originalFilename = pathinfo($tabImageToProbleme->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate(
                    'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' .
                    $tabImageToProbleme->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $tabImageToProbleme->move(
                        $this->getParameter('probleme_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger',
                        'Error on fileUpload :' . $e->getMessage());
                }
                    $imageArray[$i] = new Image();
                    $imageArray[$i]->setProbleme($probleme);
                    $imageArray[$i]->setURL($newFilename);
                    $this->entityManager->persist($imageArray[$i]);
                    $this->entityManager->flush();
                }
            }
        }
}