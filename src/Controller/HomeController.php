<?php

namespace App\Controller;

use App\Repository\CarRepository;
use App\Repository\MotorcycleRepository;
use App\Repository\VanRepository;
use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function home(VehicleRepository $vehicleRepository, CarRepository $carRepository, MotorcycleRepository $motorcycleRepository, VanRepository $vanRepository): Response
    {
        $limit = 3;
        $cars = $carRepository->findTopReviewedCars($limit);
        $vans = $vanRepository->findTopReviewedVans($limit);
        $motorcycles = $motorcycleRepository->findTopReviewedMotocycles($limit);


        return $this->render('home/home.html.twig', [
            'limit' => $limit,
            'cars' => $cars,
            'vans' => $vans,
            'motorcycles' => $motorcycles,
        ]);
    }
    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
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
