<?php

namespace App\Form;

use App\Entity\MotorcycleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotorcycleTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'mt-1 block w-full px-4 py-3 bg-[#F5F5F0] border-2 border-[#8B4513]/20 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors duration-300'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-[#8B4513]']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'mt-1 block w-full px-4 py-3 bg-[#F5F5F0] border-2 border-[#8B4513]/20 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors duration-300',
                    'rows' => 3
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-[#8B4513]']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MotorcycleType::class,
        ]);
    }
} 