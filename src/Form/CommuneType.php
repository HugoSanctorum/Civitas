<?php

namespace App\Form;

use App\Entity\Commune;
use App\Services\Geoquery\Geoquery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CommuneType extends AbstractType
{
    private $geoquery;

    public function __construct(Geoquery $geoquery){
        $this->geoquery = $geoquery;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('code', TextType::class, ["mapped" => false])
            ->add('Service')

        ;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']
        );;
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $entity = $event->getData();

        $form->add('imageBackground', FileType::class, [
            'label' => 'Image de fond',
            'mapped' => false,
            'multiple' => false,
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/png',
                        'image/jpg',
                        'image/jpeg',
                    ],
                    'mimeTypesMessage' => 'Choisissez une image valide (png, jpg, jpeg)',
                ])
            ],
        ]);
    }

        public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commune::class,
            'allow_extra_fields' => true
        ]);
    }
}
