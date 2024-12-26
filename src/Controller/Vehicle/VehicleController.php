<?php

namespace App\Controller\Vehicle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\VehicleRepository;
use App\Entity\Car;
use App\Entity\Favorite;
use Doctrine\ORM\EntityManagerInterface;


class VehicleController extends AbstractController
{
    #[Route('/nos-vehicules', name: 'app_collections')]
    public function vehicules(VehicleRepository $vehicleRepository): Response
    {
        $cars = $vehicleRepository->findCars();
        $motorcycles = $vehicleRepository->findMotorcycles();
        $vans = $vehicleRepository->findVan();
        return $this->render('vehicle/collections.html.twig', [
            'cars' => $cars,
            'motorcycles' => $motorcycles,
            'vans' => $vans,
        ]);
    }
    #[Route('/nos-voitures', name: 'app_car')]
    public function show_cars(VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager): Response
    {
        $cars = $vehicleRepository->findAllCars();
        
        $isFavorite = [];
        
        $user = $this->getUser();
        if ($user) {
        
            $favorite = $entityManager->getRepository(Favorite::class)->findOneBy(['client' => $user]);
            if ($favorite) {
                $favoriteVehicles = $favorite->getVehicles();
                foreach ($cars as $car) {
                    $isFavorite[$car->getId()] = $favoriteVehicles->contains($car);
                }
            }
        }
            return $this->render('vehicle/_display_car.html.twig', [
            'cars' => $cars,
            'isFavorite' => $isFavorite
        ]);
    }
    #[Route('/nos-motos', name: 'app_motorcycle')]
    public function show_motorcycle(VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager): Response
    {
        $motorcycles = $vehicleRepository->findAllMotorcycles();
    
        $isFavorite = [];
        
        $user = $this->getUser();
        if ($user) {
            $favorite = $entityManager->getRepository(Favorite::class)->findOneBy(['client' => $user]);
            if ($favorite) {
                $favoriteVehicles = $favorite->getVehicles();
                foreach ($motorcycles as $motorcycle) {
                    $isFavorite[$motorcycle->getId()] = $favoriteVehicles->contains($motorcycle);
                }
            }
        }
    
        return $this->render('vehicle/_display_motorcycle.html.twig', [
            'motorcycles' => $motorcycles,
            'isFavorite' => $isFavorite
        ]);
    }
    
    #[Route('/nos-van', name: 'app_van')]
    public function show_vans(VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager): Response
    {
        $vans = $vehicleRepository->findAllVan();
    
        $isFavorite = [];
        
        $user = $this->getUser();
        if ($user) {
            $favorite = $entityManager->getRepository(Favorite::class)->findOneBy(['client' => $user]);
            if ($favorite) {
                $favoriteVehicles = $favorite->getVehicles();
                foreach ($vans as $van) {
                    $isFavorite[$van->getId()] = $favoriteVehicles->contains($van);
                }
            }
        }
    
        return $this->render('vehicle/_display_van.html.twig', [
            'vans' => $vans,
            'isFavorite' => $isFavorite,
        ]);
    }

    #[Route('/details/{vehicleId}', name: 'app_vehicle_show_details')]
    public function showDetails(): Response
    {
        return $this->render('vehicle/collections.html.twig', [

        ]);
       
    }
}
