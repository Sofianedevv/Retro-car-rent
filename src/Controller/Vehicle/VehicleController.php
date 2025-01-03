<?php

namespace App\Controller\Vehicle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


use App\Entity\Car;
use App\Entity\Van;
use App\Entity\Motorcycle;
use App\Entity\Vehicle;
use App\Entity\Favorite;
use App\Entity\Category;

use App\Repository\VehicleRepository;
use App\Repository\FavoriteRepository;
use App\Repository\CategoryRepository;
use App\Repository\VehicleOptionRepository;
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
    public function show_cars(Request $request, VehicleRepository $vehicleRepository, FavoriteRepository $favoriteRepository): Response
    {
        $search = $request->query->get('search');
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
    
        if ($search) {
            $cars = $vehicleRepository->findCarsBySearch($search);
        } else {
            $cars = $vehicleRepository->findCarsByFilters($filters);
        }

        $brands = $vehicleRepository->findAllCarBrands();
        $transmissions = ['Automatique', 'Manuelle'];
        $fuelTypes = ['Essence', 'Diesel', 'Électrique', 'Hybride'];
        $nbSeatsOptions = [2, 4, 5, 7, 8, 9];
        $nbDoorsOptions = [2, 3, 4, 5];
        $years = range(2010, 1900, -1);
    
        $isFavorite = [];
        $user = $this->getUser();
        if ($user) {
            $favorite = $favoriteRepository->findOneBy(['client' => $user]);
            if ($favorite) {
                $favoriteVehicles = $favorite->getVehicles();
                foreach ($cars as $car) {
                    $isFavorite[$car->getId()] = $favoriteVehicles->contains($car);
                }
            }
        }
    

    
        return $this->render('vehicle/_display_car.html.twig', [
            'cars' => $cars,
            'isFavorite' => $isFavorite, 
            'brands' => $brands,
            'transmissions' => $transmissions,
            'fuelTypes' => $fuelTypes,
            'nbSeatsOptions' => $nbSeatsOptions,
            'nbDoorsOptions' => $nbDoorsOptions,
            'years' => $years,
            'filters' => $filters,
            'search' => $search
        ]);
    }
    
    #[Route('/nos-motos', name: 'app_motorcycle')]
    public function show_motorcycle(Request $request, VehicleRepository $vehicleRepository, FavoriteRepository $favoriteRepository): Response
    {
        $search = $request->query->get('search');
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

        if ($search) {
            $motorcycles = $vehicleRepository->findMotorcyclesBySearch($search);
        } else {
            $motorcycles = $vehicleRepository->findMotorcyclesByFilters($filters);
        }

        $brands = $vehicleRepository->findAllMotorcycleBrands();
        $types = ['Sport', 'Cruiser', 'Trail', 'Roadster'];
        $years = range(2010, 1900, -1);

        $isFavorite = [];
        $user = $this->getUser();
        if ($user) {
            $favorite = $favoriteRepository->findOneBy(['client' => $user]);
            if ($favorite) {
                $favoriteVehicles = $favorite->getVehicles();
                foreach ($motorcycles as $motorcycle) {
                    $isFavorite[$motorcycle->getId()] = $favoriteVehicles->contains($motorcycle);
                }
            }
        }
        

        return $this->render('vehicle/_display_motorcycle.html.twig', [
            'motorcycles' => $motorcycles,
            'isFavorite' => $isFavorite,
            'brands' => $brands,
            'types' => $types,
            'years' => $years,
            'filters' => $filters,
            'search' => $search
        ]);
    }

    #[Route('/nos-van', name: 'app_van')]
    public function show_vans(Request $request, VehicleRepository $vehicleRepository, FavoriteRepository $favoriteRepository): Response
    {
        $search = $request->query->get('search');
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
            'nbSeats' => $request->query->get('nbSeats'),
            'nbDoors' => $request->query->get('nbDoors'),
        ];

        if ($search) {
            $vans = $vehicleRepository->findVansBySearch($search);
        } else {
            $vans = $vehicleRepository->findVansByFilters($filters);
        }

        $brands = $vehicleRepository->findAllVanBrands();
        $years = range(2010, 1900, -1);
        $nbSeatsOptions = [2, 3, 5, 6, 7, 8, 9];
        $nbDoorsOptions = [2, 3, 4, 5];

        $isFavorite = [];
        $user = $this->getUser();
        if ($user) {
            $favorite = $favoriteRepository->findOneBy(['client' => $user]);
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
            'brands' => $brands,
            'years' => $years,
            'nbSeatsOptions' => $nbSeatsOptions,
            'nbDoorsOptions' => $nbDoorsOptions,
            'filters' => $filters,
            'search' => $search
        ]);
    }

    #[Route('/details/{vehicleId}', name: 'app_vehicle_show_details')]
    public function showDetails(int $vehicleId, VehicleRepository $vehicleRepository,CategoryRepository $categoryRepository, VehicleOptionRepository $vehicleOptionRepository, FavoriteRepository $favoriteRepository): Response
    {
        $user = $this->getUser();

        $vehicle = $vehicleRepository->find($vehicleId);
        $categories = $categoryRepository->findCategoriesByVehicle($vehicle);
        $options = $vehicleOptionRepository->findOptionsByVehicle($vehicle);
        $type = $this->getVehicleType($vehicle);

        if(!$vehicle) {
            $this->addFlash('error', 'Ce véhicule n`\'existe pas.');
            return $this->redirectToRoute('app_collections');
        }
        
        return $this->render('vehicle/_display_details.html.twig', [
            'vehicle' => $vehicle,
            'type' => $type,
            'categories'=> $categories,
            'options' => $options
        ]);
       
    }
    private function getVehicleType(Vehicle $vehicle): string
    {
        if ($vehicle instanceof Car) {
            return 'Car';
        } elseif ($vehicle instanceof Van) {
            return 'Van';
        } elseif ($vehicle instanceof Motorcycle) {
            return 'Motorcycle';
        }

        return 'Type de véhicule inconnu';
    }
}