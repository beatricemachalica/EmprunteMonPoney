<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Activity;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placehorlder' => 'Rechercher',
                    'class' => 'w-100',
                ]
            ])
            ->add('categories', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Category::class,
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'w-100',
                ]
            ])
            ->add('activities', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Activity::class,
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'w-100',
                ]
            ])
            ->add('min', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix minimum',
                    'class' => 'w-100',
                ]
            ])
            ->add('max', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix maximum',
                    'class' => 'w-100',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // configureOptions allow us to configure those form options
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
            // csrf is not needed in this case
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
