<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, ['label' => 'Marque'])
            ->add('model', TextType::class, ['label' => 'Modèle'])
            ->add('year', NumberType::class, ['label' => 'Année'])
            ->add('price', NumberType::class, ['label' => 'Prix'])
            ->add('fuelType', TextType::class, ['label' => 'Type de carburant'])
            ->add('mileage', NumberType::class, ['label' => 'Kilométrage'])
            ->add('nbSeats', NumberType::class, ['label' => 'Nombre de sièges'])
            ->add('nbDoors', NumberType::class, ['label' => 'Nombre de portes'])
            ->add('trunkSize', NumberType::class, ['label' => 'Taille du coffre'])
            ->add('transmission', TextType::class, ['label' => 'Transmission'])
            ->add('defaultImage', UrlType::class, ['label' => 'Image par défaut'])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Catégories'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
} 