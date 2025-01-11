<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use App\Entity\Van;
use App\Entity\User;
use App\Form\CarType;
use App\Form\VanType;
use App\Entity\Motorcycle;
use App\Form\MotorcycleType;
use App\Repository\UserRepository;
use App\Repository\VehicleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Notification;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(VehicleRepository $vehicleRepository, UserRepository $userRepository, CategoryRepository $categoryRepository, ReservationRepository $reservationRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'vehicles_count' => $vehicleRepository->count([]),
            'users_count' => $userRepository->count([]),
            'categories_count' => $categoryRepository->count([]),
            'bookings_count' => $reservationRepository->count([]),
            
        ]);
    }

    #[Route('/vehicles', name: 'vehicles')]
    public function vehicles(VehicleRepository $vehicleRepository): Response
    {
        return $this->render('admin/vehicles/index.html.twig', [
            'cars' => $vehicleRepository->findCars(),
            'vans' => $vehicleRepository->findVan(),
            'motorcycles' => $vehicleRepository->findMotorcycles(),
        ]);
    }

    // CRUD pour les voitures
    #[Route('/vehicle/car/new', name: 'vehicle_car_new')]
    public function newCar(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            
            // Notifier tous les utilisateurs non-admin du nouveau véhicule
            $users = $userRepository->findAll();
            foreach ($users as $user) {
                if (!in_array('ROLE_ADMIN', $user->getRoles())) {  // Ne pas notifier les admins
                    $notification = new Notification();
                    $notification->setMessage(sprintf(
                        'Une nouvelle voiture est disponible : %s %s',
                        $car->getBrand(),
                        $car->getModel()
                    ));
                    $notification->setCreatedAt(new \DateTimeImmutable());
                    $notification->setReadStatus(false);
                    $notification->setType(Notification::TYPE_NEW_VEHICLE);
                    $notification->setClient($user);
                    
                    $entityManager->persist($notification);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'La voiture a été ajoutée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form,
            'vehicle_type' => 'car'
        ]);
    }

    #[Route('/vehicle/car/{id}/edit', name: 'vehicle_car_edit')]
    public function editCar(Car $car, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La voiture a été modifiée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/edit.html.twig', [
            'form' => $form,
            'vehicle' => $car,
            'vehicle_type' => 'car'
        ]);
    }

    // CRUD pour les vans
    #[Route('/vehicle/van/new', name: 'vehicle_van_new')]
    public function newVan(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $van = new Van();
        $form = $this->createForm(VanType::class, $van);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($van);
            
            // Notifier tous les utilisateurs non-admin du nouveau véhicule
            $users = $userRepository->findAll();
            foreach ($users as $user) {
                if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                    $notification = new Notification();
                    $notification->setMessage(sprintf(
                        'Un nouveau van est disponible : %s %s',
                        $van->getBrand(),
                        $van->getModel()
                    ));
                    $notification->setCreatedAt(new \DateTimeImmutable());
                    $notification->setReadStatus(false);
                    $notification->setType(Notification::TYPE_NEW_VEHICLE);
                    $notification->setClient($user);
                    
                    $entityManager->persist($notification);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Le van a été ajouté avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form,
            'vehicle_type' => 'van'
        ]);
    }

    #[Route('/vehicle/van/{id}/edit', name: 'vehicle_van_edit')]
    public function editVan(Van $van, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VanType::class, $van);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le van a été modifié avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/edit.html.twig', [
            'form' => $form,
            'vehicle' => $van,
            'vehicle_type' => 'van'
        ]);
    }

    // CRUD pour les motos
    #[Route('/vehicle/motorcycle/new', name: 'vehicle_motorcycle_new')]
    public function newMotorcycle(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $motorcycle = new Motorcycle();
        $form = $this->createForm(MotorcycleType::class, $motorcycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($motorcycle);
            
            // Notifier tous les utilisateurs non-admin du nouveau véhicule
            $users = $userRepository->findAll();
            foreach ($users as $user) {
                if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                    $notification = new Notification();
                    $notification->setMessage(sprintf(
                        'Une nouvelle moto est disponible : %s %s',
                        $motorcycle->getBrand(),
                        $motorcycle->getModel()
                    ));
                    $notification->setCreatedAt(new \DateTimeImmutable());
                    $notification->setReadStatus(false);
                    $notification->setType(Notification::TYPE_NEW_VEHICLE);
                    $notification->setClient($user);
                    
                    $entityManager->persist($notification);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'La moto a été ajoutée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form,
            'vehicle_type' => 'motorcycle'
        ]);
    }

    #[Route('/vehicle/motorcycle/{id}/edit', name: 'vehicle_motorcycle_edit')]
    public function editMotorcycle(Motorcycle $motorcycle, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MotorcycleType::class, $motorcycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La moto a été modifiée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/edit.html.twig', [
            'form' => $form,
            'vehicle' => $motorcycle,
            'vehicle_type' => 'motorcycle'
        ]);
    }

    // Suppression commune pour tous les types de véhicules
    #[Route('/vehicle/{id}/delete', name: 'vehicle_delete')]
    public function deleteVehicle(int $id, VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager): Response
    {
        $vehicle = $vehicleRepository->find($id);
        
        if (!$vehicle) {
            throw $this->createNotFoundException('Véhicule non trouvé');
        }

        $entityManager->remove($vehicle);
        $entityManager->flush();

        $this->addFlash('success', 'Le véhicule a été supprimé avec succès.');
        return $this->redirectToRoute('admin_vehicles');
    }

    // Gestion des utilisateurs
    #[Route('/users', name: 'users')]
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
} 