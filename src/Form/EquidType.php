<?php

namespace App\Form;

use App\Entity\Equid;
use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EquidType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom du cheval',
                ]
            ])
            ->add('breed', TextType::class, [
                'required' => true,
                'label' => 'Race',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Selle français',
                ]
            ])
            ->add('size', IntegerType::class, [
                'required' => true,
                'label' => 'Taille au garrot (cm)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '155',
                ]
            ])
            ->add('sex', TextType::class, [
                'required' => true,
                'label' => 'Sexe',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Hongre',
                ]
            ])
            ->add('birthDate', DateType::class, [
                'required' => true,
                'label' => 'Date de naissance',
                'placeholder' => [
                    'year' => 'Année',
                    'month' => 'Mois',
                    'day' => 'Jours'
                ],
                'format' => 'ddMMyyyy',
                'widget' => 'choice',
                'years' => range(date('Y') - 30, date('Y')),
                'constraints' => [
                    new NotBlank()
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'label' => 'Ville',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Strasbourg',
                ]
            ])
            ->add('cp', IntegerType::class, [
                'required' => true,
                'label' => 'Code postal',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '67000',
                ]
            ])
            ->add('departement', TextType::class, [
                'required' => true,
                'label' => 'Département',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Bas-rhin',
                ]
            ])
            ->add('activities', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'label' => "Activité pratiquée :",
                    'class' => Activity::class,
                ],
                'by_reference' => false,
                'label' => false,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Equid::class,
            'activities' => null,
        ]);
    }
}
