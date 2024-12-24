<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function home(): Response
    {
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('Auth/login.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/register', name: 'app_register')]
    public function register(): Response
    {
        return $this->render('Auth/register.html.twig', [
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
