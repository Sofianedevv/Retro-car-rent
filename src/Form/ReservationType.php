<?php

namespace App\Form;


use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
        ->add('startDate', DateTimeType::class, [
            'label' => 'Date de location',
            'attr' => [
                'placeholder' => 'Choisissez une date',
                'class'=>'form-input rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 w-full text-black',
                
            ],
             'input'  => 'datetime_immutable'
        ])
        ->add('endDate', DateTimeType::class, [
            'label' => 'Date de location',
            'attr' => [
                'placeholder' => 'Choisissez une date',
               
                'input'  => 'datetime_immutable',
                'class'=>'form-input rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 w-full text-black',
            ],
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'vehicle' => [],
        ]);
    }
}
