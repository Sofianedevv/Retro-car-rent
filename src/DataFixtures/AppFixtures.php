<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $loader = new NativeLoader();
        
        // Chargement des fixtures dans l'ordre
        $objectSet = $loader->loadFiles([
            __DIR__ . '/../../fixtures/category.yaml',
            __DIR__ . '/../../fixtures/vehicleOption.yaml',
            __DIR__ . '/../../fixtures/vehicle.yaml',
            __DIR__ . '/../../fixtures/user.yaml',
            __DIR__ . '/../../fixtures/review.yaml',
            __DIR__ . '/../../fixtures/reservation.yaml'
        ]);

        foreach ($objectSet->getObjects() as $object) {
            $manager->persist($object);
        }

        $manager->flush();
    }
} 