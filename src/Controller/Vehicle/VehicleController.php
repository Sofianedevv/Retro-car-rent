<?php

namespace App\Controller\Vehicle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function show_cars(Request $request, VehicleRepository $vehicleRepository): Response
    {
        $filters = [
            'brand' => $request->query->get('brand'),
            'minPrice' => $request->query->get('minPrice'),
            'maxPrice' => $request->query->get('maxPrice'),
            'minYear' => $request->query->get('minYear'),
            'maxYear' => $request->query->get('maxYear'),
            'transmission' => $request->query->get('transmission'),
            'fuelType' => $request->query->get('fuelType'),
            'availability' => $request->query->get('availability'),
            'nbSeats' => $request->query->get('nbSeats'),
            'nbDoors' => $request->query->get('nbDoors'),
            'minMileage' => $request->query->get('minMileage'),
            'maxMileage' => $request->query->get('maxMileage'),
        ];

        $cars = $vehicleRepository->findCarsByFilters($filters);
        $brands = $vehicleRepository->findAllCarBrands();
        $transmissions = ['Automatique', 'Manuelle'];
        $fuelTypes = ['Essence', 'Diesel', 'Électrique', 'Hybride'];
        $nbSeatsOptions = [2, 4, 5, 7, 8, 9];
        $nbDoorsOptions = [2, 3, 4, 5];
        $years = range(2010, 1900, -1);

        return $this->render('vehicle/_display_car.html.twig', [
            'cars' => $cars,
            'brands' => $brands,
            'transmissions' => $transmissions,
            'fuelTypes' => $fuelTypes,
            'nbSeatsOptions' => $nbSeatsOptions,
            'nbDoorsOptions' => $nbDoorsOptions,
            'years' => $years,
            'filters' => $filters
        ]);
    }
    #[Route('/nos-motos', name: 'app_motorcycle')]
    public function show_motorcycle(Request $request, VehicleRepository $vehicleRepository): Response
    {
        $filters = [
            'brand' => $request->query->get('brand'),
            'minPrice' => $request->query->get('minPrice'),
            'maxPrice' => $request->query->get('maxPrice'),
            'minYear' => $request->query->get('minYear'),
            'maxYear' => $request->query->get('maxYear'),
            'type' => $request->query->get('type'),
            'availability' => $request->query->get('availability'),
            'minEngineCapacity' => $request->query->get('minEngineCapacity'),
            'maxEngineCapacity' => $request->query->get('maxEngineCapacity'),
            'minMileage' => $request->query->get('minMileage'),
            'maxMileage' => $request->query->get('maxMileage'),
        ];

        $motorcycles = $vehicleRepository->findMotorcyclesByFilters($filters);
        $brands = $vehicleRepository->findAllMotorcycleBrands();
        $types = ['Sport', 'Cruiser', 'Trail', 'Roadster'];
        $years = range(2010, 1900, -1);

        return $this->render('vehicle/_display_motorcycle.html.twig', [
            'motorcycles' => $motorcycles,
            'brands' => $brands,
            'types' => $types,
            'years' => $years,
            'filters' => $filters
        ]);
    }
    #[Route('/nos-van', name: 'app_van')]
    public function show_vans(Request $request, VehicleRepository $vehicleRepository): Response
    {
        $filters = [
            'brand' => $request->query->get('brand'),
            'minPrice' => $request->query->get('minPrice'),
            'maxPrice' => $request->query->get('maxPrice'),
            'minYear' => $request->query->get('minYear'),
            'maxYear' => $request->query->get('maxYear'),
            'minCargoVolume' => $request->query->get('minCargoVolume'),
            'maxCargoVolume' => $request->query->get('maxCargoVolume'),
            'availability' => $request->query->get('availability'),
            'minMileage' => $request->query->get('minMileage'),
            'maxMileage' => $request->query->get('maxMileage'),
        ];

        $vans = $vehicleRepository->findVansByFilters($filters);
        $brands = $vehicleRepository->findAllVanBrands();
        $years = range(2010, 1900, -1);

        return $this->render('vehicle/_display_van.html.twig', [
            'vans' => $vans,
            'brands' => $brands,
            'years' => $years,
            'filters' => $filters
        ]);
    }
}
