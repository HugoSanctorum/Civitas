<?php

namespace App\Form;

use App\Entity\Probleme;
use App\Repository\ProblemeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChoiceProblemType extends AbstractType
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

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
