<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentDate = new \DateTime();
        
        $disablePastStartDate = $options['disable_past_start_date'] && $currentDate > $options['start_date'];

        $builder
        ->add('vehicle_type', ChoiceType::class, [
            'choices' => [
                'Tous les véhicules' => 'all',
                'Voiture' => 'car',
                'Van' => 'van',
                'Moto' => 'motorcycle',
            ],
            'label' => 'Type de véhicule',
            'attr' => [
                'class' => 'mt-3 w-full border-2 border-[#8B4513] bg-transparent rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#8B4513] focus:outline-none',
            ],
            
        ])
            ->add('start_date', DateType::class, [
                'widget' => 'single_text', 
                'label' => 'Date de début',
                'disabled' => $disablePastStartDate, 
                'attr' => [
                    'min' => $currentDate->format('Y-m-d'),  
                ],

            ])
            ->add('start_time', ChoiceType::class, [
                'label' => 'Heure de départ',
                'choices' => array_combine(
                    array_map(fn($hour) => $hour, [
                        "00:00", "00:30", "01:00", "01:30", "02:00", "02:30", "03:00", "03:30", "04:00", "04:30", "05:00", "05:30",
                        "06:00", "06:30", "07:00", "07:30", "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30",
                        "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30",
                        "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00", "22:30", "23:00", "23:30"
                    ]),
                    array_map(fn($hour) => $hour, [
                        "00:00", "00:30", "01:00", "01:30", "02:00", "02:30", "03:00", "03:30", "04:00", "04:30", "05:00", "05:30",
                        "06:00", "06:30", "07:00", "07:30", "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30",
                        "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30",
                        "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00", "22:30", "23:00", "23:30"
                    ]),
                ),
                'attr' => [
                    'class' => 'mt-3 w-full border-2 border-[#8B4513] bg-transparent rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#8B4513] focus:outline-none',
                ],
            ])
            ->add('end_date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'attr' => [
                    'min' => $currentDate->format('Y-m-d'),  
                ],

            ])
            ->add('end_time', ChoiceType::class, [
                'label' => 'Heure de Retour',
                'choices' => array_combine(
                    array_map(fn($hour) => $hour, [
                        "00:00", "00:30", "01:00" , "01:30", "02:00", "02:30", "03:00", "03:30", "04:00", "04:30", "05:00", "05:30",
                        "06:00", "06:30", "07:00", "07:30", "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30",
                        "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30",
                        "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00", "22:30", "23:00", "23:30"
                    ]),
                    array_map(fn($hour) => $hour, [
                        "00:00", "00:30", "01:00", "01:30", "02:00", "02:30", "03:00", "03:30", "04:00", "04:30", "05:00", "05:30",
                        "06:00", "06:30", "07:00", "07:30", "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30",
                        "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30",
                        "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00", "22:30", "23:00", "23:30"
                    ]),
                ),
                'attr' => [
                    'class' => 'mt-3 w-full border-2 border-[#8B4513] bg-transparent rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#8B4513] focus:outline-none',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, 
            'disable_past_start_date' => false,
            'disable_past_end_date' => false,
            'start_date' => new \DateTime(),  
            'end_date' => new \DateTime(), 
        ]);
    }
}
