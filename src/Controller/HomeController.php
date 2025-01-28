<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Vehicle;
use App\Repository\CarRepository;
use App\Repository\MotorcycleRepository;
use App\Repository\VanRepository;
use App\Repository\VehicleRepository;
use App\Repository\ReservationRepository;
use App\Form\SearchType;
use DateTimeImmutable;
class HomeController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function app(
        Request $request,
        CarRepository $carRepository,
        MotorcycleRepository $motorcycleRepository,
        VanRepository $vanRepository,
        VehicleRepository $vehicleRepository,
        ReservationRepository $reservationRepository,
        SessionInterface $session
    ): Response {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $form->getData();

            $startDate = $form->get('start_date')->getData();
            $startTime = $form->get('start_time')->getData();
            $endDate = $form->get('end_date')->getData();
            $endTime = $form->get('end_time')->getData();

            $startDate = $startDate->format('Y-m-d');
            $startTime = $startTime->format('H:i:s');
            $endDate = $endDate->format('Y-m-d');
            $endTime = $endTime->format('H:i:s');

            $startDateTime = new DateTimeImmutable($startDate . ' ' . $startTime);
            $endDateTime = new DateTimeImmutable($endDate . ' ' . $endTime);
            
            $allVehicles = $vehicleRepository->findAll();
            $overlappingReservations = $reservationRepository->findOverlappingReservations($startDateTime, $endDateTime);
            $reservedVehiclesIds = array_map(fn($reservation) => $reservation->getVehicle()->getId(), $overlappingReservations);
            $availableVehicles = array_filter($allVehicles, fn($vehicle) => !in_array($vehicle->getId(), $reservedVehiclesIds));
            $data = array_map(function (Vehicle $vehicle) {
                return [
                    'id' => $vehicle->getId(),
                ];
            }, $availableVehicles);
            $vehicleIds = array_map(fn($item) => $item['id'], $data);
            $session->set('available_vehicles_ids', $vehicleIds);

            
     
            return $this->redirectToRoute('app_available', [
            'startDateTime' => $startDateTime->format('Y-m-d H:i:s'),
            'endDateTime' => $endDateTime->format('Y-m-d H:i:s'),

            
            ]);
        }
        $bestRatedCars = $carRepository->findBestRated(2);
        $bestRatedMotorcycles = $motorcycleRepository->findBestRated(1);
        $bestRatedVans = $vanRepository->findBestRated(1);

        $bestRatedVehicles = array_merge($bestRatedCars, $bestRatedMotorcycles, $bestRatedVans);
        shuffle($bestRatedVehicles);

        return $this->render('home/home.html.twig', [
            'bestRatedVehicles' => $bestRatedVehicles,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
        ]);
    }

    // #[Route('/home', name: 'app_home')]
    // public function index(
    //     CarRepository $carRepository,
    //     MotorcycleRepository $motorcycleRepository,
    //     VanRepository $vanRepository
    // ): Response {
    //     // Récupérer les véhicules les mieux notés
    //     $bestRatedCars = $carRepository->findBestRated(2);
    //     $bestRatedMotorcycles = $motorcycleRepository->findBestRated(1);
    //     $bestRatedVans = $vanRepository->findBestRated(1);

    //     // Mélanger les résultats pour avoir une variété
    //     $bestRatedVehicles = array_merge($bestRatedCars, $bestRatedMotorcycles, $bestRatedVans);
    //     shuffle($bestRatedVehicles);

    //     return $this->render('home/home.html.twig', [
    //         'bestRatedVehicles' => $bestRatedVehicles
    //     ]);
    // }
    // #[Route('/login', name: 'app_login')]
    // public function login(): Response
    // {
    //     return $this->render('Auth/login.html.twig', [
    //         'controller_name' => 'HomeController',
    //     ]);
    // }
    // #[Route('/register', name: 'app_register')]
    // public function register(): Response
    // {
    //     return $this->render('Auth/register.html.twig', [
    //         'controller_name' => 'HomeController',
    //     ]);
    // }
    #[Route('/shop', name: 'app_shop')]
        public function shop(): Response
    {
        return $this->render('products/shop.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/product', name: 'app_product')]
    public function product(): Response
    {
        return $this->render('products/single_product.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/not-found', name: 'app_not_found')]
    public function notFound(): Response
    {
        return $this->render('other/not_found.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/cart', name: 'app_cart')]
    public function cart(): Response
    {
        return $this->render('shop/cart.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(): Response
    {
        return $this->render('shop/checkout.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }




}
