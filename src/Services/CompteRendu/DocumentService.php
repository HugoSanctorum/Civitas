<?php


namespace App\Services\CompteRendu;


use App\Entity\CompteRendu;
use App\Entity\Personne;
use App\Entity\Probleme;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DocumentService extends AbstractController
{
    private $personne;
    private $entityManager;

    public function __construct(
        TokenStorageInterface $tokenStorageInterface,
        EntityManagerInterface $entityManager)
    {
        $this->personne = $tokenStorageInterface->getToken()->getUser();
        $this->entityManager = $entityManager;
    }

    public function UploadDocument($document)
    {
        if ($document) {
            $originalFilename = pathinfo($document->getClientOriginalName(),
                PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = transliterator_transliterate(
                'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                $originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' .
                $document->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $document->move(
                    $this->getParameter('document_directory'),
                    $newFilename
                );
                return $newFilename;
            } catch (FileException $e) {
                return $e;
            }
        }
    }

        public function PersistCompteRendu(CompteRendu $compteRendu,Probleme $probleme, $document){
            $compteRendu->setProbleme($probleme);
            $compteRendu->setPersonne($this->personne);
            $compteRendu->setUrlDocument($this->UploadDocument($document));
            $this->entityManager->persist($compteRendu);


        }

}