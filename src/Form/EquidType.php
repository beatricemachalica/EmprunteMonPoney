<?php

namespace App\Form;

use App\Entity\Equid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EquidType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('breed', TextType::class, [
                'required' => true,
                'label' => 'Race',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('size', IntegerType::class, [
                'required' => true,
                'label' => 'Taille',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('sex', TextType::class, [
                'required' => true,
                'label' => 'Sexe',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('birthDate', DateType::class, [
                'required' => true,
                'label' => 'Date de naissance',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'label' => 'Ville',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('cp', IntegerType::class, [
                'required' => true,
                'label' => 'Code postal',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('departement', TextType::class, [
                'required' => true,
                'label' => 'Département',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Equid::class,
        ]);
    }
}
