<?php

namespace App\Form;

use App\Entity\Payment;
use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Vehicle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
        ->add('vehicle', HiddenType::class, [
            'data' => $options['vehicle']->getId(),
            'attr' => [

                'data-vehicle-id'=> $options['vehicle']->getId(),
            ],
            'mapped' => false

        ])

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
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'vehicle' => null
        ]);
    }
}
