<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CarRepository;
use App\Repository\MotorcycleRepository;
use App\Repository\VanRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function app(
        CarRepository $carRepository,
        MotorcycleRepository $motorcycleRepository,
        VanRepository $vanRepository
    ): Response {
        // Récupérer les véhicules les mieux notés
        $bestRatedCars = $carRepository->findBestRated(2);
        $bestRatedMotorcycles = $motorcycleRepository->findBestRated(1);
        $bestRatedVans = $vanRepository->findBestRated(1);

        // Mélanger les résultats pour avoir une variété
        $bestRatedVehicles = array_merge($bestRatedCars, $bestRatedMotorcycles, $bestRatedVans);
        shuffle($bestRatedVehicles);

        return $this->render('home/home.html.twig', [
            'bestRatedVehicles' => $bestRatedVehicles
        ]);
    }
    #[Route('/about', name: 'app_about')]
    public function login(): Response
    {
        return $this->render('other/about.html.twig', [
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
