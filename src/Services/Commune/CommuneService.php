<?php


namespace App\Services\Commune;


use App\Entity\Commune;
use App\Entity\Probleme;
use App\Services\UploadDocumentService;
use Doctrine\ORM\EntityManagerInterface;

class CommuneService
{
    private $documentService;
    private $entityManager;


    public function __construct(UploadDocumentService $documentService, EntityManagerInterface $entityManager){
        $this->documentService = $documentService;
        $this->entityManager = $entityManager;
    }

    public function SetBackground(Commune $commune, $imageBackground)
    {
        $commune->setImageBackground($this->documentService->UploadDocument($imageBackground, 'communeBackground_directory'));

    }
}