<?php

namespace App\Form;

use App\Entity\Intervenir;
use App\Entity\Personne;
use App\Entity\Probleme;
use App\Repository\HistoriqueStatutRepository;
use App\Repository\PersonneRepository;
use App\Repository\ProblemeRepository;
use App\Repository\TypeInterventionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntervenirType extends AbstractType{

    private $historiqueStatutRepository;
    private $problemeStatutRepository;
    private $personneRepository;
    private $typeInterventionRepository;

    public function __construct(
        HistoriqueStatutRepository $historiqueStatutRepository,
        ProblemeRepository $problemeRepository,
        PersonneRepository $personneRepository,
        TypeInterventionRepository $typeInterventionRepository
    )
    {
        $this->historiqueStatutRepository = $historiqueStatutRepository;
        $this->problemeStatutRepository = $problemeRepository;
        $this->personneRepository = $personneRepository;
        $this->typeInterventionRepository = $typeInterventionRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Personne', EntityType::class,[
                "class" => Personne::class,
                "choices"=>$this->personneRepository->getPersonneByPermission('CAN_DO_INTERVENTION'),
                'choice_label' => 'Label',

            ])
            ->add('Probleme', EntityType::class,[
                "class" => Probleme::class,
                "choices"=>$this->problemeStatutRepository->findAllUnresolvedProblem(),
                'choice_label' => 'Label',
            ])
        ;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm(); //récupération du formulaire
        $entity = $event->getData();

        $form->remove('createAt');
        $entity->setCreatedAt(New \DateTime('now'));
        $entity->setTypeIntervention($this->typeInterventionRepository->findOneBy(['nom' => 'Technicien']));
    }
        public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Intervenir::class,
        ]);
    }
}
