<?php

namespace App\Form;

use App\Entity\Payment;
use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Vehicle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
        ->add('rangeDate', TextType::class, [
            'label' => 'Date de location',
            'attr' => [
                'placeholder' => 'Choisissez une date',
                'data-provider' => 'flatpickr', 
                'class'=>'flatpickr form-input rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 w-full text-black',
            ]
        ])
        ->add('totalPrice', NumberType::class, [

            'data' =>  $options['vehicle']->getPrice(),
            'attr' => [
                'class' => 'text-black',
                'data-vehicle-price'=> $options['vehicle']->getPrice(),
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
