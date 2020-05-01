<?php

namespace App\Form\Type;

use App\Repository\HistoriqueStatutRepository;
use App\Repository\ProblemeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UnresolvedProblemType extends AbstractType
{
    private $historiqueStatutRepository;
    private $problemeRepository;

    public function __construct(HistoriqueStatutRepository $historiqueStatutRepository, ProblemeRepository $problemeRepository)
    {
        $this->historiqueStatutRepository = $historiqueStatutRepository;
        $this->problemeRepository = $problemeRepository;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        //$historiqueStatuts = $this->historiqueStatutRepository->findUnresolvedProblem();

        $historiqueStatuts =$this->problemeRepository->findAllUnresolvedProblem();
        foreach($historiqueStatuts as $historiqueStatut){
            $data[] = $historiqueStatut;
        }


        $resolver->setDefaults([
            'choices' => $data,
            'multiple' => false,
            'expanded' => false,
        ]);
    }
    public function getParent(){
        return ChoiceType::class;
    }

}