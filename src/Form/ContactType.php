<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ContactType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('pseudo', TextType::class, [
        'required' => true,
        'label' => 'Nom',
        'attr' => [
          'class' => 'form-control',
          'placeholder' => 'Jean',
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
          'class' => 'form-control',
          'placeholder' => 'Votre adresse mail',
        ]
      ])
      ->add('message', CKEditorType::class, [
        'required' => true,
        'label' => 'Message',
        'attr' => [
          'class' => 'form-control m-0',
          'rows' => 5,
          'placeholder' => 'Ajoutez votre message ici.'
        ]
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      // Configure your form options here
    ]);
  }
}
