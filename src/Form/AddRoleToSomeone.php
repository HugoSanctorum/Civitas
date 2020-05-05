<?php


namespace App\Form;


use App\Repository\HistoriqueStatutRepository;
use App\Repository\ProblemeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddRoleToSomeone extends AbstractType
{
    public function __construct()
    {

    }
        public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('Personne')
            ->add('Role')
        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm(); //récupération du formulaire
        $entity = $event->getData();


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}