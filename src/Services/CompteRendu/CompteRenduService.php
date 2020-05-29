<?php


namespace App\Services\CompteRendu;


use App\Entity\CompteRendu;
use App\Entity\Probleme;
use App\Services\UploadDocumentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CompteRenduService
{
    private $personne;
    private $documentService;
    private $entityManager;


    public function __construct(TokenStorageInterface $tokenStorageInterface, UploadDocumentService $documentService, EntityManagerInterface $entityManager){
        $this->personne = $tokenStorageInterface->getToken()->getUser();
        $this->documentService = $documentService;
        $this->entityManager = $entityManager;
    }

    public function PersistCompteRendu(CompteRendu $compteRendu, Probleme $probleme, $document)
    {
        $compteRendu->setProbleme($probleme);
        $compteRendu->setPersonne($this->personne);
        $compteRendu->setUrlDocument($this->documentService->UploadDocument($document, 'document_directory'));
        $this->entityManager->persist($compteRendu);

    }
}