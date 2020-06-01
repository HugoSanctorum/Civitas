<?php

namespace App\Form;

use App\Entity\CompteRendu;
use App\Entity\Intervenir;
use App\Entity\Probleme;
use App\Repository\IntervenirRepository;
use App\Repository\ProblemeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\File;


class CompteRenduType extends AbstractType
{
    private $personne;
    private $problemeRepository;
    private $intervenirRepository;

    public function __construct(ProblemeRepository $problemeRepository,TokenStorageInterface $tokenStorageInterface, IntervenirRepository $intervenirRepository)
    {
        $this->problemeRepository = $problemeRepository;
        $this->intervenirRepository = $intervenirRepository;
        $this->personne = $tokenStorageInterface->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('urlDocument');

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']
        );;
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm(); //récupération du formulaire
        $form->remove('Probleme');
        $entity = $event->getData();
        $entity->setDate(new \DateTime());


        $form->add('urlDocument', FileType::class, [
            'label' => 'Document',
            'mapped' => false,
            'multiple' => false,
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/png',
                        'image/jpeg',
                        'application/pdf',
                        'application/x-pdf',
                        'application/docx'
                        ],
                    'mimeTypesMessage' => 'Choisissez un document valide (pdf, xpdf, doc, png, jpeg)',
                ])
            ],
        ]);
        $form->add('Intervenir', EntityType::class, [
            "class" => Intervenir::class,
            "choices" => $this->intervenirRepository->findAllInterventionByPersonneAndUnresolvedProbleme($entity->getProbleme(),$this->personne),
            "choice_label" => 'label'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompteRendu::class,
            'allow_extra_fields' => true
        ]);
    }
}
