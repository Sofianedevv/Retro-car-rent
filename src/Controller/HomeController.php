<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
        Request               $request,
        CarRepository         $carRepository,
        MotorcycleRepository  $motorcycleRepository,
        VanRepository         $vanRepository,
        VehicleRepository     $vehicleRepository,
        ReservationRepository $reservationRepository,
        SessionInterface      $session
    ): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();


            $typeSearch = $form->get('vehicle_type')->getData();
            $startDate = $form->get('start_date')->getData();
            $startTime = $form->get('start_time')->getData();
            $endDate = $form->get('end_date')->getData();
            $endTime = $form->get('end_time')->getData();

            $startDateTime = new DateTimeImmutable($startDate->format('Y-m-d') . ' ' . $startTime);
            $endDateTime = new DateTimeImmutable($endDate->format('Y-m-d') . ' ' . $endTime);


            return $this->redirectToRoute('app_available', [
                'type' => $typeSearch,
                'startDateTime' => $startDateTime->format('Y-m-d H:i'),
                'endDateTime' => $endDateTime->format('Y-m-d H:i'),


            ]);
        }
//        $bestRatedCars = $carRepository->findBestRated(2);
//        $bestRatedMotorcycles = $motorcycleRepository->findBestRated(1);
//        $bestRatedVans = $vanRepository->findBestRated(1);

//        $bestRatedVehicles = array_merge($bestRatedCars, $bestRatedMotorcycles, $bestRatedVans);
//        shuffle($bestRatedVehicles);

        return $this->render('home/home.html.twig', [
            'bestRatedVehicles' => '',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
        ]);
    }


}
