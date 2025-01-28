<?php

namespace App\Controller\Vehicle;

use App\Repository\CarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Flasher\Prime\FlasherInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Repository\VehicleRepository;
use App\Repository\FavoriteRepository;
use App\Repository\CategoryRepository;
use App\Repository\VehicleOptionRepository;
use App\Repository\ReviewRepository;

use App\Service\Vehicle\VehicleService;



class VehicleController extends AbstractController
{
    private VehicleService $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

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
    public function show_cars(
        Request $request,
        VehicleRepository $vehicleRepository,
        FavoriteRepository $favoriteRepository,
        PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search');
        $filters = [
            'brand' => $request->query->all('brand'),
            'minPrice' => $request->query->get('minPrice'),
            'maxPrice' => $request->query->get('maxPrice'),
            'minYear' => $request->query->get('minYear'),
            'maxYear' => $request->query->get('maxYear'),
            'transmission' => $request->query->get('transmission'),
            'fuelType' => $request->query->all('fuelType'),
            'availability' => $request->query->get('availability'),
            'nbSeats' => $request->query->all('nbSeats'),
            'nbDoors' => $request->query->all('nbDoors'),
            'minMileage' => $request->query->get('minMileage'),
            'maxMileage' => $request->query->get('maxMileage'),
        ];
        
        if ($search) {
            $cars = $vehicleRepository->findCarsBySearch($search);
        } else {
            $cars = $vehicleRepository->findCarsByFilters($filters);
        }
        $carsPerPage = $paginator->paginate(
            $cars,
            $request->query->getInt('page', 1),
            10
        );

        $brands = $vehicleRepository->findAllCarBrands();
        $transmissions = ['Automatique', 'Manuelle'];
        $fuelTypes = ['Essence', 'Diesel', 'Électrique', 'Hybride'];
        $nbSeatsOptions = [2, 4, 5, 7, 8, 9];
        $nbDoorsOptions = [2, 3, 4, 5];
        $years = range(2010, 1900, -1);

        $isFavorite = array_fill_keys(
            array_map(function($car) { return $car->getId(); }, $carsPerPage->getItems()),
            false
        );

        $user = $this->getUser();
        if ($user) {
            $favorite = $favoriteRepository->findOneBy(['client' => $user]);
            if ($favorite) {
                $favoriteVehicles = $favorite->getVehicles();
                foreach ($carsPerPage->getItems() as $car) {
                    $isFavorite[$car->getId()] = $favoriteVehicles->contains($car);
                }
            }
        }



        return $this->render('vehicle/_display_car.html.twig', [
            'cars' => $carsPerPage,
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
    public function show_motorcycle(
        Request $request,
        VehicleRepository $vehicleRepository,
        FavoriteRepository $favoriteRepository,
        PaginatorInterface $paginator): Response
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


        $motorcyclePerPage = $paginator->paginate(
            $motorcycles,
            $request->query->getInt('page', 1),
            10
        );
        $brands = $vehicleRepository->findAllMotorcycleBrands();
        $types = ['Sport', 'Cruiser', 'Trail', 'Roadster'];
        $years = range(2010, 1900, -1);

        $isFavorite = array_fill_keys(
            array_map(function($motorcycle) { return $motorcycle->getId(); }, $motorcyclePerPage->getItems()),
            false
        );

        $user = $this->getUser();
        if ($user) {
            $favorite = $favoriteRepository->findOneBy(['client' => $user]);
            if ($favorite) {
                $favoriteVehicles = $favorite->getVehicles();
                foreach ($motorcyclePerPage as $motorcycle) {
                    $isFavorite[$motorcycle->getId()] = $favoriteVehicles->contains($motorcycle);
                }
            }
        }
        

        return $this->render('vehicle/_display_motorcycle.html.twig', [
            'motorcycles' => $motorcyclePerPage,
            'isFavorite' => $isFavorite,
            'brands' => $brands,
            'types' => $types,
            'years' => $years,
            'filters' => $filters,
            'search' => $search
        ]);
    }

    #[Route('/nos-van', name: 'app_van')]
    public function show_vans(
        Request $request,
        VehicleRepository $vehicleRepository,
        FavoriteRepository $favoriteRepository,
        PaginatorInterface $paginator): Response
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

        $vanPerPage = $paginator->paginate(
            $vans,
            $request->query->getInt('page', 1),
            10
        );


        $brands = $vehicleRepository->findAllVanBrands();
        $years = range(2010, 1900, -1);
        $nbSeatsOptions = [2, 3, 5, 6, 7, 8, 9];
        $nbDoorsOptions = [2, 3, 4, 5];

        $isFavorite = array_fill_keys(
            array_map(function($van) { return $van->getId(); }, $vanPerPage->getItems()),
            false
        );

        $user = $this->getUser();
        if ($user) {
            $favorite = $favoriteRepository->findOneBy(['client' => $user]);
            if ($favorite) {
                $favoriteVehicles = $favorite->getVehicles();
                foreach ($vanPerPage as $van) {
                    $isFavorite[$van->getId()] = $favoriteVehicles->contains($van);
                }
            }
        }

        return $this->render('vehicle/_display_van.html.twig', [
            'vans' => $vanPerPage,
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
    public function showDetails(
        int $vehicleId, 
        VehicleRepository $vehicleRepository,
        CategoryRepository $categoryRepository, 
        VehicleOptionRepository $vehicleOptionRepository, 
        ReviewRepository $reviewRepository,
        VehicleService $vehicleService,
        FlasherInterface $flasher
    ): Response
    {
        $user = $this->getUser();
        $vehicle = $vehicleRepository->find($vehicleId);
        $categories = $categoryRepository->findCategoriesByVehicle($vehicle);
        $options = $vehicleOptionRepository->findOptionsByVehicle($vehicle);
        $reviews = $reviewRepository->findBy(['vehicle' => $vehicle], ['createdAt' => 'DESC']);
        $averageRating = $reviewRepository->getAverageRating($vehicle);
        $type = $vehicleService->getVehicleType($vehicle);

        if(!$vehicle) {
           $flasher->addError( 'Ce véhicule n\'existe pas.');
            return $this->redirectToRoute('app_collections');
        }
        ;


        
        return $this->render('vehicle/_display_details.html.twig', [
            'vehicle' => $vehicle,
            'type' => $type,
            'categories'=> $categories,
            'options' => $options,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
        ]);
    }

    #[Route('/vehicules-disponible/{startDateTime}/{endDateTime}', name: 'app_available')]
    public function showAvailableVehicles(
        string $startDateTime, 
        string $endDateTime,
        VehicleRepository $vehicleRepository, 
        SessionInterface $session,
        FlasherInterface $flasher, 
        PaginatorInterface $paginator,
        Request $request,
    ): Response {
        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $startDateTime);
        $endDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $endDateTime);
    
        if (!$startDate || !$endDate) {
            $flasher->error('Le format des dates et heures est invalide.');
            return $this->redirectToRoute('app_available', [
                'startDateTime' => $startDate ? $startDate->format('Y-m-d H:i:s') : '',
                'endDateTime' => $endDate ? $endDate->format('Y-m-d H:i:s') : '',
            ]);
        }
    
        $availableVehiclesIds = $session->get('available_vehicles_ids', []);
        
        if (empty($availableVehiclesIds)) {
            $flasher->info('Aucun véhicule disponible pour les dates et heures sélectionnées.');
            return $this->redirectToRoute('app_available', [
                'startDateTime' => $startDate->format('Y-m-d H:i:s'),
                'endDateTime' => $endDate->format('Y-m-d H:i:s'),
            ]);
        }
    
        $vehicles = $vehicleRepository->findBy(['id' => $availableVehiclesIds]);

        $pagination = $paginator->paginate(
            $vehicles,
            $request->query->getInt('page', 1),
            20
        );
    
        $cars = [];
        $motorcycles = [];
        $vans = [];
    
        foreach ($pagination->getItems() as $vehicle) {
            if ($vehicle instanceof \App\Entity\Car) {
                $cars[] = $vehicle;
            } elseif ($vehicle instanceof \App\Entity\Motorcycle) {
                $motorcycles[] = $vehicle;
            } elseif ($vehicle instanceof \App\Entity\Van) {
                $vans[] = $vehicle;
            }
        }
    
        return $this->render('vehicle/_available_vehicle.html.twig', [
            'cars' => $cars,
            'motorcycles' => $motorcycles,
            'vans' => $vans,
            'startDateTime' => $startDate->format('Y-m-d H:i:s'),
            'endDateTime' => $endDate->format('Y-m-d H:i:s'),
            'pagination' => $pagination,
        ]);
    }
    
}
