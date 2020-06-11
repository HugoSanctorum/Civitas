<?php


namespace App\Services\Probleme;

use App\Entity\Image;
use App\Entity\Intervenir;
use App\Entity\HistoriqueStatut;
use App\Entity\Personne;
use App\Entity\Probleme;
use App\Repository\CompteRenduRepository;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\ImageRepository;
use App\Repository\IntervenirRepository;
use App\Repository\ProblemeRepository;
use App\Repository\StatutRepository;
use App\Repository\TypeInterventionRepository;
use App\Services\Mailer\MailerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Constraints\Date;


class ProblemeService extends AbstractController
{
    private $problemeRepository;
    private $typeInterventionRepository;
    private $statutRepository;
    private $entityManager;
    private $mailerService;
    private $personne;
    private $historiqueStatutRepository;
    private $internirRepository;
    private $imageRepository;
    private $compteRenduRepository;
    private $tokenGenerator;


    public function __construct(
        ProblemeRepository $problemeRepository,
        TypeInterventionRepository $typeInterventionRepository,
        StatutRepository $statutRepository,
        EntityManagerInterface $entityManager,
        MailerService $mailerService,
        TokenStorageInterface $tokenStorageInterface,
        HistoriqueStatutRepository $historiqueStatutRepository,
        IntervenirRepository $intervenirRepository,
        ImageRepository $imageRepository,
        CompteRenduRepository $compteRenduRepository,
        TokenGeneratorInterface $tokenGenerator
    )
    {
        $this->problemeRepository = $problemeRepository;
        $this->typeInterventionRepository = $typeInterventionRepository;
        $this->statutRepository = $statutRepository;
        $this->entityManager = $entityManager;
        $this->mailerService = $mailerService;
        $this->personne = $tokenStorageInterface->getToken()->getUser();
        $this->historiqueStatutRepository = $historiqueStatutRepository;
        $this->internirRepository = $intervenirRepository;
        $this->imageRepository =$imageRepository;
        $this->compteRenduRepository = $compteRenduRepository;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function CreateNewProblemeAuthentificated(Probleme $probleme, $personne){
        $this->entityManager->persist($probleme);

        $this->CreateNewIntervenir($probleme, $personne,'Signaleur');
        $this->CreateNewHistoriqueStatut($probleme, 'Nouveau');
        $this->mailerService->sendMailToSignaleurNewProbleme($personne, $probleme);

        $this->entityManager->flush();
    }

    public function CreateNewProblemeMailExistingNonAuthentificated(Probleme $probleme, $personne)
    {
        $this->entityManager->persist($probleme);

        $this->CreateNewIntervenir($probleme, $personne,'Signaleur');
        $this->CreateNewHistoriqueStatut($probleme,'Nouveau');
        $this->mailerService->sendMailToSignaleurNewProbleme($personne, $probleme);

        $this->entityManager->flush();
    }

    public function CreateNewProblemeMailNonExistingNonAuthentificated(Probleme $probleme, $mail)
    {
        $token = $this->tokenGenerator->generateToken();
        $this->entityManager->persist($probleme);
        $personne = new Personne();
        $personne->setMail($mail);
        $personne->setSubscribeToken($token);
        $personne->setCreatedAt(new \DateTime('now'));

        $this->CreateNewIntervenir($probleme, $personne, 'Signaleur');
        $this->CreateNewHistoriqueStatut($probleme,'Nouveau');

        $this->entityManager->persist($personne);
        $this->entityManager->flush();
        $this->mailerService->sendMailToSignaleurNewProbleme($personne, $probleme);


    }

    public function CreateNewHistoriqueStatut(Probleme $probleme, string $statut)
    {
        $statut = $this->statutRepository->findOneBy(['nom' => $statut]);
        $historiqueStatut = new HistoriqueStatut();

        $historiqueStatut->setProbleme($probleme);
        $historiqueStatut->setStatut($statut);
        $historiqueStatut->setDate(new \DateTime('now'));
        $this->entityManager->persist($historiqueStatut);

    }

    public function CreateNewIntervenir(Probleme $probleme, Personne $personne, $typeIntervenition){
        $intervention = new Intervenir();

        $intervention->setProbleme($probleme);
        $intervention->setPersonne($personne);
        $intervention->setTypeIntervention($this->typeInterventionRepository->findOneBy(['nom' => $typeIntervenition]));
        $intervention->setCreatedAt(new \DateTime('now'));
        $this->entityManager->persist($intervention);
    }

    public function UploadImagesNewProbleme($tabImageToProblemes, $probleme)
    {
        foreach ($tabImageToProblemes as $tabImageToProbleme) {
            $i = 1;
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

    public function GetUrlFromThosesImages($tabImages)
    {
        $url=[];
        foreach ($tabImages as $tabImage) {
            if ($tabImage) {
                $originalFilename = pathinfo($tabImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate(
                    'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' .
                    $tabImage->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $tabImage->move(
                        $this->getParameter('probleme_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger',
                        'Error on fileUpload :' . $e->getMessage());
                }
                array_push($url,$newFilename);
            }
        }
        return $url;
    }
    public function PersistUrlWithProbleme($tabUrl, $probleme){
        foreach($tabUrl as $Url){
            $image= new Image();
            $image->setProbleme($probleme);
            $image->setURL($Url);
            $this->entityManager->persist($image);
            $this->entityManager->flush();
        }
    }
    public function DeleteThosesImages($tabImage)
    {
        $path = $this->getParameter('kernel.public');
        foreach ($tabImage as $image) {
            $filesystem = new Filesystem();
            $filesystem->remove([null, $path, $image]);
        }
    }

    public function SetReference(Probleme $probleme){
        $date = new \DateTime();
        $anneeMois = $date->format('Ymd');
        $string = $this->problemeRepository->findMaxId();
        $id =((int)$string[1]);
        $cell = 26;
        $probleme->setReference($anneeMois.($id+$cell));
    }

    public function DeleteProbleme($probleme){
        $historiqueStatuts = $this->historiqueStatutRepository->findBy(["Probleme" => $probleme]);
        $intervenirs = $this->internirRepository->findBy(["Probleme" => $probleme]);
        $images = $this->imageRepository->findBy(["Probleme" => $probleme]);
        $compteRendus = $this->compteRenduRepository->findBy(["Probleme" => $probleme]);

        if($historiqueStatuts){
            foreach ($historiqueStatuts as $historiqueStatut) {
                $this->entityManager->remove($historiqueStatut);
                $this->entityManager->flush();
            }
        }
        if($intervenirs){
            foreach ($intervenirs as $intervenir) {
                $this->entityManager->remove($intervenir);
                $this->entityManager->flush();
            }
        }
        if($images){
            foreach ($images as $image) {
                $this->entityManager->remove($image);
                $this->entityManager->flush();

            }
        }
        if($compteRendus){
            foreach ($compteRendus as $compteRendu) {
                $this->entityManager->remove($compteRendu);
                $this->entityManager->flush();
            }
        }

        $this->entityManager->remove($probleme);
        $this->entityManager->flush();


    }

}