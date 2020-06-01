<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Statut;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProblemeSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'required' => false,
                'class' => Categorie::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                },
                'choice_label' => 'nom',
                'multiple' => true
            ])
            ->add('statuts', EntityType::class, [
                'required' => false,
                'class' => Statut::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s');
                },
                'choice_label' => 'nom',
                'multiple' => true
            ])
            ->add('element', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => [
                    '10' => 10,
                    '20' => 20,
                    '50' => 50,
                    '100' => 100,
                ]
            ])
            ->add('orderby', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => [
                    'Date ↑' => "date_asc",
                    'Date ↓' => "date_desc",
                    'Statut' => "statut",
                    'Priorité' => "priorite",
                    'Catégorie' => "categorie"
                ]
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
