<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', CKEditorType::class, [
                'required' => true,
                'label' => 'Description de l\'annonce',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ajoutez votre description de l\'annonce ici',
                ]
            ])
            // upload pictures, but will not be linked with DB (mapped=false)
            ->add('photo', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new All([
                        new Image([
                            'maxSize' => '8M',
                            'maxSizeMessage' => 'La taille de l\'image est trop grande. 
                            La taille maximale autorisée est de {{ limit }} {{ suffix }}.',
                        ]),
                        new File([
                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/jpg',
                                'image/webp',
                            ],
                            'mimeTypesMessage' => 'Format invalide ({{ type }}). 
                            Les formats autorisés sont : {{ types }}.',
                        ])
                    ])
                ],
            ])
            ->add('price', MoneyType::class, [
                'required' => false,
                'currency' => false,
                'label' => 'Prix souhaité par mois',
                'attr' => [
                    'class' => 'form-control'
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
            'data_class' => Post::class
        ]);
    }
}
