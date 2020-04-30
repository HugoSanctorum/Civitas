<?php

namespace App\Form;

use App\Entity\Intervenir;
use App\Form\Type\UnresolvedProblemType;
use App\Repository\HistoriqueStatutRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntervenirType extends AbstractType{

    private $historiqueStatutRepository;

    public function __construct(HistoriqueStatutRepository $historiqueStatutRepository)
    {
        $this->historiqueStatutRepository = $historiqueStatutRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $historiqueStatuts = $this->historiqueStatutRepository->findUnresolvedProblem();
        $builder
            ->add('createdAt')
            ->add('Personne')
            ->add('Probleme', UnresolvedProblemType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Intervenir::class,
        ]);
    }
}
