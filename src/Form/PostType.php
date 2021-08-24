<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Equid;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', CKEditorType::class, [
                'required' => true,
                'label' => 'Description de l\'annonce',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            // ->add('category', EntityType::class, [
            //     'class' => Category::class,
            //     'required' => true,
            //     'label' => 'Catégorie de l\'annonce',
            //     'attr' => [
            //         'class' => 'form-control'
            //     ]
            // ])

            ->add('equid', EntityType::class, [
                'class' => Equid::class,
                // 'query_builder' => function (EntityRepository $er) {
                //     return $er->qbGetHorsesByUser($options);
                // },
                'choices' => $options['horses'],
                'required' => true,
                'mapped' => true,
                'label' => 'Cheval',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            // upload pictures, but will not be linked with DB (mapped=false)
            ->add('photo', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control-file mt-3'
                ]
            ])
            ->add('price', MoneyType::class, [
                'required' => false,
                'currency' => false,
                'label' => 'Prix souhaité par mois',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'horses' => null,
        ]);
    }
}
