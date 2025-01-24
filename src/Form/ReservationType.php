<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text',
            ])
            ->add('startTime', TimeType::class, [
                'label' => 'Heure de départ',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'time-interval'],
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de retour',
                'widget' => 'single_text',
            ])
            ->add('endTime', TimeType::class, [
                'label' => 'Heure de retour',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'time-interval'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}