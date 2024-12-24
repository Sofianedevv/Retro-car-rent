<?php

namespace App\Form;

use App\Entity\Motorcycle;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotorcycleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('brand', TextType::class, [
                'label' => 'Marque',
                'attr' => [
                    'placeholder' => 'Ex: Harley-Davidson, Triumph...',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('model', TextType::class, [
                'label' => 'Modèle',
                'attr' => [
                    'placeholder' => 'Ex: Bonneville, Sportster...',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('year', NumberType::class, [
                'label' => 'Année',
                'attr' => [
                    'placeholder' => 'Ex: 1965',
                    'min' => 1900,
                    'max' => date('Y'),
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('mileage', NumberType::class, [
                'label' => 'Kilométrage',
                'attr' => [
                    'placeholder' => 'Ex: 45000',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix par jour',
                'currency' => 'EUR',
                'attr' => [
                    'placeholder' => 'Ex: 180',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('engineCapacity', NumberType::class, [
                'label' => 'Cylindrée (cc)',
                'attr' => [
                    'placeholder' => 'Ex: 1200',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Custom' => 'custom',
                    'Café Racer' => 'cafe_racer',
                    'Classique' => 'classic',
                    'Roadster' => 'roadster'
                ],
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('availability', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false,
                'attr' => [
                    'class' => 'h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500'
                ]
            ])
            ->add('defaultImage', TextType::class, [
                'label' => 'Image par défaut (URL)',
                'attr' => [
                    'placeholder' => 'http://exemple.com/image.jpg',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Catégories',
                'attr' => [
                    'class' => 'mt-2 grid grid-cols-2 gap-4'
                ],
                'label_attr' => [
                    'class' => 'text-sm font-medium text-gray-700'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Motorcycle::class,
        ]);
    }
} 