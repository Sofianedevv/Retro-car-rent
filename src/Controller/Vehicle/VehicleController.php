<?php

namespace App\Controller\Vehicle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\VehicleRepository;
use App\Entity\Car;

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
    public function show_cars(VehicleRepository $vehicleRepository): Response
    {
        $cars = $vehicleRepository->findAllCars();
        return $this->render('vehicle/_display_car.html.twig', [
            'cars' => $cars
        ]);
    }
    #[Route('/nos-motos', name: 'app_motorcycle')]
    public function show_motorcycle(VehicleRepository $vehicleRepository): Response
    {
        $motorcycles = $vehicleRepository->findAllMotorcycles();
     
        return $this->render('vehicle/_display_motorcycle.html.twig', [
            'motorcycles' => $motorcycles
        ]);
    }
    #[Route('/nos-van', name: 'app_van')]
    public function show_vans(VehicleRepository $vehicleRepository): Response
    {
        $vans = $vehicleRepository->findAllVan();
        return $this->render('vehicle/_display_van.html.twig', [
            'vans' => $vans,
        ]);
    }
}
