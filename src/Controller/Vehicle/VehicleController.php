<?php

namespace App\Controller\Vehicle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Flasher\Prime\FlasherInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Repository\VehicleRepository;
use App\Repository\FavoriteRepository;
use App\Repository\CategoryRepository;
use App\Repository\ReservationRepository;
use App\Repository\VehicleOptionRepository;
use App\Repository\ReviewRepository;
use App\Repository\CarRepository;


use App\Service\Vehicle\VehicleService;
use App\Service\Vehicle\VehicleFiltersService;



class VehicleController extends AbstractController
{

    #[Route('/nos-vehicules', name: 'app_collections')]
    public function vehicules(VehicleRepository $vehicleRepository, CarRepository $carRepository): Response
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
        PaginatorInterface $paginator,
        CarRepository $carRepository,
        VehicleFiltersService $vehicleFiltersService
        ): Response
    {
        $search = $request->query->get('search');
        $filters = $vehicleFiltersService->getAllFiltersByRequest('car', $request);
        $specificFilter = $vehicleFiltersService->fetchFilterDataByVehicleType('car');
        
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
            'brands' => $specificFilter['brands'],
            'transmissions' => $specificFilter['transmissions'] ?? [],
            'nbSeatsOptions' => $specificFilter['nbSeatsOptions'] ?? [],
            'nbDoorsOptions' => $specificFilter['nbDoorsOptions'] ?? [],
            'years' => $specificFilter['years'] ?? [],
            'fuelTypes' => $specificFilter['fuelTypes'] ?? [],
            'filters' => $filters,
            'search' => $search
        ]);
    }
    #[Route('/nos-motos', name: 'app_motorcycle')]
    public function show_motorcycle(
        Request $request,
        VehicleRepository $vehicleRepository,
        FavoriteRepository $favoriteRepository,
        PaginatorInterface $paginator,
        VehicleFiltersService $vehicleFiltersService
        ): Response
    {
        $search = $request->query->get('search');
        $filters = $vehicleFiltersService->getAllFiltersByRequest('motorcycle', $request);
        $specificFilter = $vehicleFiltersService->fetchFilterDataByVehicleType('motorcycle');
        
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
            'brands' =>  $specificFilter['brands'],
            'years' => $specificFilter['years'] ?? [],
            'types' => $specificFilter['engineTypes'] ?? [],
            'filters' => $filters,
            'search' => $search
        ]);
    }

    #[Route('/nos-van', name: 'app_van')]
    public function show_vans(
        Request $request,
        VehicleRepository $vehicleRepository,
        FavoriteRepository $favoriteRepository,
        PaginatorInterface $paginator,
        VehicleFiltersService $vehicleFiltersService
        ): Response
    {
        $search = $request->query->get('search');
        $filters = $vehicleFiltersService->getAllFiltersByRequest('van', $request);
        $specificFilter = $vehicleFiltersService->fetchFilterDataByVehicleType('van');

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
            'brands' => $specificFilter['brands'],
            'nbSeatsOptions' => $specificFilter['nbSeatsOptions'] ?? [],
            'nbDoorsOptions' => $specificFilter['nbDoorsOptions'] ?? [],
            'years' => $specificFilter['years'] ?? [],
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
        if (!$vehicle) {
            throw $this->createNotFoundException('Vehicle not found');
        }

        $categories = $categoryRepository->findCategoriesByVehicle($vehicle);
        $options = $vehicleOptionRepository->findOptionsByVehicle($vehicle);
        $reviews = $reviewRepository->findBy(['vehicle' => $vehicle], ['createdAt' => 'DESC']);
        $averageRating = $reviewRepository->getAverageRating($vehicle);
        $type = $vehicleService->getVehicleType($vehicle);

        if(!$vehicle) {
           $flasher->addError( 'Ce véhicule n\'existe pas.');
            return $this->redirectToRoute('app_collections');
        }
        $similarVehicles = $vehicleRepository->findSimilar($vehicle);
        


        
        return $this->render('vehicle/_display_details.html.twig', [
            'vehicle' => $vehicle,
            'type' => $type,
            'categories'=> $categories,
            'options' => $options,
            'reviews' => array_values(array_filter($reviews, function($review) {
                return $review->getParent() === null;
            })),
            'averageRating' => $averageRating,
            'similar_vehicles' => $similarVehicles,
            'userId' => $this->getUser() ? $this->getUser()->getId() : null,
            'google_maps_api_key' => $this->getParameter('google_maps_api_key'),
        ]);
    }




    #[Route('/vehicules-disponible/{type}/{startDateTime}/{endDateTime}', name: 'app_available')]
public function showAvailableVehicles(
    string $type,
    string $startDateTime, 
    string $endDateTime,
    VehicleRepository $vehicleRepository,
    CarRepository $carRepository,
    ReservationRepository $reservationRepository,
    SessionInterface $session,
    FlasherInterface $flasher, 
    PaginatorInterface $paginator,
    Request $request,
    FavoriteRepository $favoriteRepository,
    VehicleService $vehicleService,
    VehicleFiltersService $vehicleFiltersService
): Response {

    $user = $this->getUser();
    
    $search = $request->query->get('search');
    $filters = $vehicleFiltersService->getAllFiltersByRequest($type, $request);
    $specificFilter = $vehicleFiltersService->fetchFilterDataByVehicleType($type);


    $startDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $startDateTime);
    $endDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $endDateTime);
    
    if (!$startDate || !$endDate) {
        $flasher->error('Le format des dates et heures est invalide.');
        return $this->redirectToRoute('app_home');
    }

    $allVehicles = $vehicleRepository->findAll();
    $overlappingReservations = $reservationRepository->findOverlappingReservations($startDate, $endDate);
    
    $reservedVehiclesIds = array_map(fn($reservation) => $reservation->getVehicle()->getId(), $overlappingReservations);
    $availableVehicles = array_filter($allVehicles, fn($vehicle) => !in_array($vehicle->getId(), $reservedVehiclesIds));
    
    $filteredVehicles = $vehicleService->findAvailableVehiclesByType($availableVehicles, $type);
    
    if (empty($filteredVehicles)) {
        $flasher->info('Aucun véhicule disponible pour les dates et heures sélectionnées.');
        return $this->redirectToRoute('app_home');
    }
    dump(vars: $filteredVehicles);



    $vehiclesBySearch = match ($type) {
     'car' => $search ? $vehicleRepository->findCarsBySearch($search) : $vehicleRepository->findCarsByFilters($filters),
     'motorcycle' => $search ? $vehicleRepository->findMotorcyclesBySearch($search) : $vehicleRepository->findMotorcyclesByFilters($filters),
     'van' => $search ? $vehicleRepository->findVansBySearch($search) : $vehicleRepository->findVansByFilters($filters),
     'all' => $search ? $vehicleRepository->findVehicleBySearch($search) : $vehicleRepository->findAllVehicleByFilters($filters)
     };
     
    dump($vehiclesBySearch);

    $vehiclePerPage = $paginator->paginate(
        $vehiclesBySearch,
        $request->query->getInt('page', 1),
        10
    );

    

    $isFavorite = array_fill_keys(
        array_map(fn($vehicle) => $vehicle->getId(), $vehiclePerPage->getItems()),
        false
    );
     

    if ($user) {
        $favorite = $favoriteRepository->findOneBy(['client' => $user]);
        if ($favorite) {
            $favoriteVehicles = $favorite->getVehicles();
            foreach ($vehiclePerPage->getItems() as $vehicle) {
                $isFavorite[$vehicle->getId()] = $favoriteVehicles->contains($vehicle);
            }
        }
    }




    switch ($type) {
        case 'car':
            return $this->render('vehicle/_display_car.html.twig', [
                'cars' => $vehiclePerPage,
                'type' => $type,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'search' => $search,
                'isFavorite' => $isFavorite,
                'filters' => $filters,
                'brands' => $specificFilter['brands'],
                'transmissions' => $specificFilter['transmissions'] ?? [],
                'nbSeatsOptions' => $specificFilter['nbSeatsOptions'] ?? [],
                'nbDoorsOptions' => $specificFilter['nbDoorsOptions'] ?? [],
                'years' => $specificFilter['years'] ?? [],
                'fuelTypes' => $specificFilter['fuelTypes'] ?? [],
            ]);
        case 'motorcycle':
            return $this->render('vehicle/_display_motorcycle.html.twig', [
                'motorcycles' => $vehiclePerPage,
                'type' => $type,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'isFavorite' => $isFavorite,
                'filters' => $filters,
                'search' => $search,
                'brands' =>  $specificFilter['brands'],
                'years' => $specificFilter['years'] ?? [],
                'types' => $specificFilter['engineTypes'] ?? [],
            ]);
        case 'van':
            return $this->render('vehicle/_display_van.html.twig', [
                'vans' => $vehiclePerPage,
                'type' => $type,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'isFavorite' => $isFavorite,
                'filters' => $filters,
                'search' => $search,
                'brands' => $specificFilter['brands'],
                'nbSeatsOptions' => $specificFilter['nbSeatsOptions'] ?? [],
                'nbDoorsOptions' => $specificFilter['nbDoorsOptions'] ?? [],
                'years' => $specificFilter['years'] ?? [],              
            ]);
        case 'all':
            $cars = [];
            $motorcycles = [];
            $vans = [];
    
            foreach ($vehiclePerPage->getItems() as $vehicle) {
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
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'type' => $type,
                'search' => $search,
                'filters' => $filters,
                'brands' => $specificFilter['brands'],
                'years' => $specificFilter['years'] ?? [],
                'fuelTypes' => $specificFilter['fuelTypes'] ?? [],
                'pagination' => $vehiclePerPage,
            ]);
        default:
            return $this->redirectToRoute('app');
    }
}

    
}
