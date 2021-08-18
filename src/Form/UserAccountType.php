<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class UserAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, [
                'required' => true,
                'label' => 'Nom d\'utilisateur',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer un e-mail',
                    ]),
                ],
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('phoneNumber', TelType::class, [
                'required' => false,
                'label' => 'Numéro de téléphone',
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
            'data_class' => User::class,
        ]);
    }
}
