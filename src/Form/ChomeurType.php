<?php

namespace App\Form;

use App\Entity\Adresse;
use App\Entity\Chomeur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\{TextType,SubmitType};



class ChomeurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $requis = true;
        $builder
            ->add('nom', TextType::class, ['required' => $requis])
            ->add('courriel', TextType::class, ['required' => $requis])
            ->add('telephone', TextType::class, ['required' => $requis])
            ->add('soumettre', SubmitType::class, ['label' =>'Envoyer', 'attr' =>['class' => 'btn btn-info']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chomeur::class,
        ]);
    }
}
