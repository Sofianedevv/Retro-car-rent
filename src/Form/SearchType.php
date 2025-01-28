<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentDate = new \DateTime();
        
        $disablePastStartDate = $options['disable_past_start_date'] && $currentDate > $options['start_date'];

        $builder
            ->add('start_date', DateType::class, [
                'widget' => 'single_text', 
                'label' => 'Date de début',
                'disabled' => $disablePastStartDate, 
                'attr' => [
                    'min' => $currentDate->format('Y-m-d'),  
                ],

            ])
            ->add('start_time', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de début',
            ])
            ->add('end_date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'attr' => [
                    'min' => $currentDate->format('Y-m-d'),  
                ],

            ])
            ->add('end_time', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de fin',
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
