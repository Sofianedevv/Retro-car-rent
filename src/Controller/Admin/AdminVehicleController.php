<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use App\Entity\Van;
use App\Entity\Motorcycle;
use App\Form\CarType;
use App\Form\VanType;
use App\Form\MotorcycleType;
use App\Repository\CarRepository;
use App\Repository\VanRepository;
use App\Repository\MotorcycleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminVehicleController extends AbstractController
{
    #[Route('/vehicles', name: 'admin_vehicles')]
    public function index(
        CarRepository $carRepository,
        VanRepository $vanRepository,
        MotorcycleRepository $motorcycleRepository
    ): Response {
        return $this->render('admin/vehicles/index.html.twig', [
            'cars' => $carRepository->findAll(),
            'vans' => $vanRepository->findAll(),
            'motorcycles' => $motorcycleRepository->findAll(),
        ]);
    }

    #[Route('/vehicle/car/new', name: 'admin_vehicle_car_new')]
    public function newCar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            $entityManager->flush();

            $this->addFlash('success', 'La voiture a été ajoutée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form->createView(),
            'vehicle_type' => 'car'
        ]);
    }

    #[Route('/vehicle/van/new', name: 'admin_vehicle_van_new')]
    public function newVan(Request $request, EntityManagerInterface $entityManager): Response
    {
        $van = new Van();
        $form = $this->createForm(VanType::class, $van);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($van);
            $entityManager->flush();

            $this->addFlash('success', 'Le van a été ajouté avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form->createView(),
            'vehicle_type' => 'van'
        ]);
    }

    #[Route('/vehicle/motorcycle/new', name: 'admin_vehicle_motorcycle_new')]
    public function newMotorcycle(Request $request, EntityManagerInterface $entityManager): Response
    {
        $motorcycle = new Motorcycle();
        $form = $this->createForm(MotorcycleType::class, $motorcycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($motorcycle);
            $entityManager->flush();

            $this->addFlash('success', 'La moto a été ajoutée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form->createView(),
            'vehicle_type' => 'motorcycle'
        ]);
    }

    // Routes pour l'édition
    #[Route('/vehicle/car/{id}/edit', name: 'admin_vehicle_car_edit')]
    public function editCar(Request $request, Car $car, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La voiture a été modifiée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/edit.html.twig', [
            'form' => $form->createView(),
            'vehicle' => $car,
            'vehicle_type' => 'car'
        ]);
    }

    // Ajoutez les méthodes similaires pour l'édition des vans et des motos
} 