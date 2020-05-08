<?php

namespace App\Form;

use App\Entity\Personne;
use App\Entity\Probleme;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\File;

class ProblemeType extends AbstractType
{
    private $user;

    public function __construct(
        TokenStorageInterface $tokenStorageInterface
    ){
        $this->user = $tokenStorageInterface->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('date_de_declaration')
            ->add('localisation')
            ->add('reference')
            ->add('Commune')
            ->add('Categorie')
            ->add('Priorite');
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']
        );;

    }
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $entity = $event->getData();

        $form->remove('reference');
        $form->remove('date_de_declaration');
        $entity->setDateDeDeclaration(new \DateTime('now'));
        $entity->setReference(rand(1,2000));
        $form->add('Image1', FileType::class, [
            'label' => 'Image',
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '200M','mimeTypes' => ['image/png','image/jpg','image/jpeg',],
                    'mimeTypesMessage' => 'Please upload a valid png/jpg document',
                ])
            ],
        ]);
        $form->add('Image2', FileType::class, [
            'label' => 'Image',
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '200M','mimeTypes' => ['image/png','image/jpg','image/jpeg',],
                    'mimeTypesMessage' => 'Please upload a valid png/jpg document',
                ])
            ],
        ]);
        $form->add('Image3', FileType::class, [
            'label' => 'Image',
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '200M','mimeTypes' => ['image/png','image/jpg','image/jpeg',],
                    'mimeTypesMessage' => 'Please upload a valid png/jpg document',
                ])
            ],
        ]);
        $form->add('Image4', FileType::class, [
            'label' => 'Image',
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '200M','mimeTypes' => ['image/png','image/jpg','image/jpeg',],
                    'mimeTypesMessage' => 'Please upload a valid png/jpg document',
                ])
            ],
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Probleme::class
        ]);
    }
}
