<?php

namespace App\Form;

use App\Entity\Intervenir;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\ProblemeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntervenirType extends AbstractType{

    private $historiqueStatutRepository;
    private $problemeStatutRepository;

    public function __construct(HistoriqueStatutRepository $historiqueStatutRepository,ProblemeRepository $problemeRepository)
    {
        $this->historiqueStatutRepository = $historiqueStatutRepository;
        $this->problemeStatutRepository = $problemeRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $problemes=$this->problemeStatutRepository->findAllUnresolvedProblem();
        $builder
            ->add('createdAt')
            ->add('Personne')
            ->add('Probleme', ChoiceType::class,[
                "choices"=>$problemes,
                'choice_label' => 'Label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Intervenir::class,
        ]);
    }
}
