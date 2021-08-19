<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
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
            'message' => 'Merci de saisir votre email',
          ]),
        ],
        'label' => 'Email',
        'attr' => [
          'class' => 'form-control'
        ]
      ])
      ->add('message', TextareaType::class, [
        'attr' => [
          'rows' => 5,
          'class' => 'form-control'
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      // Configure your form options here
    ]);
  }
}
