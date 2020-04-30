<?php

namespace App\Form\Type;

use App\Repository\HistoriqueStatutRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UnresolvedProblemType extends AbstractType
{
    private $historiqueStatutRepository;

    public function __construct(HistoriqueStatutRepository $historiqueStatutRepository)
    {
        $this->historiqueStatutRepository = $historiqueStatutRepository;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $historiqueStatuts = $this->historiqueStatutRepository->findUnresolvedProblem();
        dd($historiqueStatuts);
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