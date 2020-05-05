<?php

namespace App\Form;

use App\Entity\Intervenir;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\ProblemeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            ->add('Personne')
            ->add('Probleme', ChoiceType::class,[
                "choices"=>$problemes,
                'choice_label' => 'Label',
            ])
        ;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']
        );;
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm(); //récupération du formulaire
        $entity = $event->getData();

        $form->remove('createAt');
        $entity->setCreatedAt(New \DateTime('now'));
    }
        public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Intervenir::class,
        ]);
    }
}
