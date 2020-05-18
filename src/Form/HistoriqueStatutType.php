<?php

namespace App\Form;

use App\Entity\HistoriqueStatut;
use App\Entity\Probleme;
use App\Entity\Statut;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class HistoriqueStatutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date')
            ->add('description')
            ->add('Statut')
            ->add('Probleme')
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                [$this, 'onPreSetData']
            )
        ;
    }
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm(); //récupération du formulaire
        $entity = $event->getData();

        $form->remove('date');
        $entity->setDate(new \DateTime('now'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HistoriqueStatut::class,
        ]);
    }
}
