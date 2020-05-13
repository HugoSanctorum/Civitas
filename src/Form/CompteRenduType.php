<?php

namespace App\Form;

use App\Entity\CompteRendu;
use App\Entity\Probleme;
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

    public function __construct(ProblemeRepository $problemeRepository,TokenStorageInterface $tokenStorageInterface)
    {
        $this->problemeRepository = $problemeRepository;
        $this->personne = $tokenStorageInterface->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Probleme')
            ->add('urlDocument')
        ;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']
        );;
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm(); //récupération du formulaire
        $form->add('Probleme',EntityType::class,[
                "class" => Probleme::class,
                "choices" =>$this->problemeRepository->findAllUnResolvedProblemeByPersonne($this->personne),
                'choice_label' => 'Label',

            ]);
        $form->add('urlDocument', FileType::class, [
            'label' => 'Document',
            'mapped' => false,
            'multiple' => false,
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/png',
                        'image/jpg',
                        'application/pdf',
                        'application/x-pdf'
                        ],
                    'mimeTypesMessage' => 'Please upload a valid png/jpg document',
                ])
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompteRendu::class,
        ]);
    }
}
