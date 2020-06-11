<?php

namespace App\Form;

use App\Entity\Personne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataMapper\CheckboxListMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('mail');
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm(); //récupération du formulaire
        $entity = $event->getData();

        $form->add('Commune');
        if($entity) {
            if ($entity->getSubscribeToken() == null) {
                $form->add('subscribeToken', CheckboxType::class, [
                    'label' => 'Recevoir les notifications par mails',
                    'required' => false,
                ]);

            } else {
                $form->add('subscribeToken', CheckboxType::class, [
                    'label' => 'Recevoir les notifications par mails',
                    'required' => false,
                    'data' => true
                ]);
            }
        }

    }

        public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
            'allow_extra_fields' => true
        ]);
    }
}
