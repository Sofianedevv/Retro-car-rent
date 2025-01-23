<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
   
class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('brand', TextType::class, [
                'label' => 'Marque',
                'attr' => [
                    'placeholder' => 'Ex: Citroën, Peugeot, Renault...',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('model', TextType::class, [
                'label' => 'Modèle',
                'attr' => [
                    'placeholder' => 'Ex: DS, 2CV, 4L...',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('year', NumberType::class, [
                'label' => 'Année',
                'attr' => [
                    'placeholder' => 'Ex: 1960',
                    'min' => 1900,
                    'max' => date('Y'),
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('mileage', NumberType::class, [
                'label' => 'Kilométrage',
                'attr' => [
                    'placeholder' => 'Ex: 80000',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix par jour',
                'currency' => 'EUR',
                'attr' => [
                    'placeholder' => 'Ex: 150',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('nbSeats', NumberType::class, [
                'label' => 'Nombre de places',
                'attr' => [
                    'min' => 1,
                    'max' => 9,
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('nbDoors', NumberType::class, [
                'label' => 'Nombre de portes',
                'attr' => [
                    'min' => 2,
                    'max' => 5,
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('trunkSize', NumberType::class, [
                'label' => 'Volume du coffre (L)',
                'attr' => [
                    'placeholder' => 'Ex: 400',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('fuelType', ChoiceType::class, [
                'label' => 'Type de carburant',
                'choices' => [
                    'Essence' => 'essence',
                    'Diesel' => 'diesel',
                    'Électrique' => 'electrique',
                    'Hybride' => 'hybride'
                ],
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('transmission', ChoiceType::class, [
                'label' => 'Transmission',
                'choices' => [
                    'Manuelle' => 'manuelle',
                    'Automatique' => 'automatique'
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
            ])
            ->add('imageFiles', FileType::class, [
                'label' => 'Photos du véhicule',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ],
                'constraints' => [
                    new All([
                        'constraints' => [
                            new Image([
                                'maxSize' => '5M',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                ],
                                'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG ou PNG)',
                            ])
                        ]
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
} 