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
use Flasher\Prime\FlasherInterface;
use App\Entity\Notification;
use App\Entity\Reservation;
use Dompdf\Dompdf;
use Dompdf\Options;


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

    
    #[Route('/vehicle/car/new', name: 'vehicle_car_new')]
    public function newCar(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, FlasherInterface $flasher): Response
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            
            $users = $userRepository->findAll();
            foreach ($users as $user) {
                if (!in_array('ROLE_ADMIN', $user->getRoles())) {  
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
           $flasher->addSuccess( 'La voiture a été ajoutée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form,
            'vehicle_type' => 'car'
        ]);
    }

    #[Route('/vehicle/car/{id}/edit', name: 'vehicle_car_edit')]
    public function editCar(Car $car, Request $request, EntityManagerInterface $entityManager, FlasherInterface $flasher): Response
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

           $flasher->addSuccess( 'La voiture a été modifiée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/edit.html.twig', [
            'form' => $form,
            'vehicle' => $car,
            'vehicle_type' => 'car'
        ]);
    }

    #[Route('/vehicle/van/new', name: 'vehicle_van_new')]
    public function newVan(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, FlasherInterface $flasher): Response
    {
        $van = new Van();
        $form = $this->createForm(VanType::class, $van);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($van);
            
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
           $flasher->addSuccess( 'Le van a été ajouté avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form,
            'vehicle_type' => 'van'
        ]);
    }

    #[Route('/vehicle/van/{id}/edit', name: 'vehicle_van_edit')]
    public function editVan(Van $van, Request $request, EntityManagerInterface $entityManager, FlasherInterface $flasher): Response
    {
        $form = $this->createForm(VanType::class, $van);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

           $flasher->addSuccess( 'Le van a été modifié avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/edit.html.twig', [
            'form' => $form,
            'vehicle' => $van,
            'vehicle_type' => 'van'
        ]);
    }

    #[Route('/vehicle/motorcycle/new', name: 'vehicle_motorcycle_new')]
    public function newMotorcycle(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, FlasherInterface $flasher): Response
    {
        $motorcycle = new Motorcycle();
        $form = $this->createForm(MotorcycleType::class, $motorcycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($motorcycle);
            
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
           $flasher->addSuccess( 'La moto a été ajoutée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form,
            'vehicle_type' => 'motorcycle'
        ]);
    }

    #[Route('/vehicle/motorcycle/{id}/edit', name: 'vehicle_motorcycle_edit')]
    public function editMotorcycle(Motorcycle $motorcycle, Request $request, EntityManagerInterface $entityManager, FlasherInterface $flasher): Response
    {
        $form = $this->createForm(MotorcycleType::class, $motorcycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

           $flasher->addSuccess( 'La moto a été modifiée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/edit.html.twig', [
            'form' => $form,
            'vehicle' => $motorcycle,
            'vehicle_type' => 'motorcycle'
        ]);
    }


    #[Route('/vehicle/{id}/delete', name: 'vehicle_delete')]
    public function deleteVehicle(int $id, VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager, FlasherInterface $flasher): Response
    {
        $vehicle = $vehicleRepository->find($id);
        
        if (!$vehicle) {
            throw $this->createNotFoundException('Véhicule non trouvé');
        }

        $entityManager->remove($vehicle);
        $entityManager->flush();

       $flasher->addSuccess( 'Le véhicule a été supprimé avec succès.');
        return $this->redirectToRoute('admin_vehicles');
    }

    #[Route('/users', name: 'users')]
    public function users(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/users.html.twig');
    }

    #[Route('/reservations', name: 'reservations')]
    public function reservations(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findAll();
        
        // Ajout d'un dump pour vérifier les statuts
        foreach ($reservations as $reservation) {
            dump($reservation->getStatus());
        }
        
        return $this->render('admin/reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/reservation/{id}', name: 'reservation_show')]
    public function showReservation(Reservation $reservation): Response
    {
        return $this->render('admin/reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/reservation/{id}/edit', name: 'reservation_edit')]
    public function editReservation(Reservation $reservation): Response
    {
        return $this->render('admin/reservation/edit.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/reservation/{id}/invoice', name: 'reservation_invoice_download')]
    public function downloadInvoice(Reservation $reservation): Response
    {
        // Configure Dompdf selon vos préférences
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        // Instancier Dompdf
        $dompdf = new Dompdf($options);

        // Calculer les totaux
        $totalPriceOptions = $reservation->getReservationVehicleOptions()->reduce(
            function ($sum, $option) {
                return $sum + ($option->getVehicleOptions() ? $option->getVehicleOptions()->getPrice() * $option->getCount() : 0);
            }, 
            0
        );

        $totalPriceVehicle = $reservation->getTotalPrice() - $totalPriceOptions;

        // Calculer le nombre de jours
        $interval = $reservation->getStartDate()->diff($reservation->getEndDate());
        $days = $interval->days;

        // Générer le HTML de la facture en utilisant le template existant
        $html = $this->renderView('invoice/pdf.html.twig', [
            'invoice' => $reservation->getInvoice(),
            'client' => $reservation->getClient(),
            'vehicle' => $reservation->getVehicle(),
            'startDate' => $reservation->getStartDate(),
            'endDate' => $reservation->getEndDate(),
            'days' => $days,
            'reservationOptions' => $reservation->getReservationVehicleOptions(),
            'totalPriceVehicle' => $totalPriceVehicle,
            'totalPriceOptions' => $totalPriceOptions
        ]);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Configurer le format du papier
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le PDF
        $dompdf->render();

        // Générer un nom de fichier
        $filename = sprintf('facture-%s.pdf', $reservation->getId());

        // Retourner le PDF comme réponse
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    #[Route('/reservation/{id}/cancel', name: 'reservation_cancel')]
    public function cancelReservation(Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        // Mettre à jour le statut de la réservation à CANCELLED
        $reservation->setStatus(\App\Enum\StatusReservationEnum::CANCELLED);
        $entityManager->flush();

        $this->addFlash('success', 'La réservation a été annulée avec succès.');
        return $this->redirectToRoute('admin_reservations');
    }

    #[Route('/reservation/{id}/delete', name: 'reservation_delete')]
    public function deleteReservation(Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'La réservation a été supprimée avec succès.');
        return $this->redirectToRoute('admin_reservations');
    }
} 