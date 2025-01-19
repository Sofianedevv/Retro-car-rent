<?php

namespace App\Controller\Reservation;

use App\Entity\Reservation;
use App\Entity\Vehicle;
use App\Entity\Invoice;
use App\Entity\ReservationVehicleOption;
use App\Repository\ReservationRepository;
use App\Repository\VehicleRepository;
use App\Repository\VehicleOptionRepository;
use App\Enum\StatusReservationEnum;
use App\Service\Mailer\ConfirmReservationMailer;
use App\Service\Mailer\CancelReservationMailer;
use App\Service\Reservation\ReservationService;
use App\Service\Vehicle\VehicleService;
use App\Form\ReservationType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Ramsey\Uuid\Guid\Guid;





class ReservationController extends AbstractController
{

    private VehicleService $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {

        $this->vehicleService = $vehicleService;

    }

    #[Route('/nouvelle_reservation/{vehicleId}', name: 'app_add_reservation', methods: ['GET','POST'])]
    public function addReservation(
            ConfirmReservationMailer $confirmReservationMailer,
            int $vehicleId,
            Request $request,
            VehicleRepository $vehicleRepository,
            VehicleOptionRepository $vehicleOptionRepository,
            EntityManagerInterface $entityManager,
            ReservationService $reservationService,
        ): Response {
            $user = $this->getUser();
            $user = $this->getUser();
            if (!$user) {
                $this->addFlash('error', 'Vous devez être connecté pour effectuer une réservation.');
                return $this->redirectToRoute('app_login');
            }
            
            $vehicle = $vehicleRepository->find($vehicleId);
            if (!$vehicle) {
                $this->addFlash('error', 'Véhicule non trouvé.');
            }
    
            $options = [];
            $totalOptionPrice = 0;
    
            $form = $this->createForm(ReservationType::class, [
                'vehicle' => $vehicle,
            ]);
    
            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
    
                $selectedOptions = $request->get('options', []);
                foreach ($selectedOptions as $optionId => $count) {
                    $count = (int) $count;  
                    if ($count > 0) {
                        $option = $vehicleOptionRepository->find($optionId);
                        if ($option) {
                            $options[] = [
                                'option' => $option,
                                'count' => $count,
                            ];
                        }
                    }
                }
    
                if ($form->isSubmitted() && $form->isValid()) {
                    $startDate = $form->get('startDate')->getData();
                    $endDate = $form->get('endDate')->getData();
    
                    if ($startDate >= $endDate) {
                        $this->addFlash('error', 'La date de fin doit être postérieure à la date de début.');
                        return $this->redirectToRoute('app_reservation_summary', ['vehicleId' => $vehicleId]);
                    }
    
                    $days = $reservationService->calculateDays($startDate, $endDate);
                    $vehiclePrice = $vehicle->getPrice();
                    $totalOptionPrice = $reservationService->calculateOptionPrice($options);
                    $totalPrice = ($vehiclePrice * $days) + $totalOptionPrice;
    
                    $reservation = new Reservation();
                    $reservation->setClient($user);
                    $reservation->setVehicle($vehicle);
                    $reservation->setStartDate(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-d H:i:s')));
                    $reservation->setEndDate(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $endDate->format('Y-m-d H:i:s')));
                    $reservation->setTotalPrice($totalPrice);
                    $reservation->setStatus(StatusReservationEnum::CONFIRMED);
                    $reservation->setCreatedAt(new DateTimeImmutable());
    
                    $entityManager->persist($reservation);
                    foreach ($options as $optionData) {
                        $reservationVehicleOption = new ReservationVehicleOption();
                        $reservationVehicleOption->setReservation($reservation);
                        $reservationVehicleOption->setVehicleOptions($optionData['option']);
                        $reservationVehicleOption->setCount($optionData['count']);
                        $reservationVehicleOption->setPriceByOption($optionData['count'] * $optionData['option']->getPrice());
                        $entityManager->persist($reservationVehicleOption);
                    }

                    $invoice = new Invoice();
                    $invoice->setReservation($reservation);
                    $invoice->setCreatedAt(new DateTimeImmutable());
                    $invoiceNumber = 'INV-' . Guid::uuid4()->toString();  
                    $invoice->setInvoiceNumber($invoiceNumber);
                    $entityManager->persist($invoice);
                    
                    $entityManager->flush();
    
                    $this->addFlash('success', 'Votre réservation a été enregistrée avec succès.');
                    return $this->redirectToRoute('app_all_reservation', ['clientId' => $user->getId()]);
                }
            }
    
            return $this->render('reservation/_recap_reservation.html.twig', [
                'vehicle' => $vehicle,
                'options' => $options,
                'totalOptionPrice' => $totalOptionPrice,
                'form' => $form->createView(),
            ]);
        }
        
        #[Route('/vos-reservations/{clientId}', name: 'app_all_reservation', methods: ['GET'])]
        public function getReservation(int $clientId, ReservationRepository $reservationRepository): Response
        {
            $reservations = $reservationRepository->findBy(['client' => $clientId]);
        
            if(empty($reservations)){
                $this->addFlash('error', 'Vous n\'avez pas de réservation en cours.');
            }
            $reservationStatus = $reservationRepository->findBy(['status' => StatusReservationEnum::CONFIRMED]);
            $reservationsOptions = [];

            foreach ($reservations as $reservation) {
                $options = $reservation->getReservationVehicleOptions(); 
                $reservationsOptions[] = $options;
            }
        
            return $this->render('reservation/_get_reservations.html.twig', [
                'reservations' => $reservations,
                'reservationsOptions'=> $reservationsOptions,
                'reservationStatus' => $reservationStatus
            ]);
        }
         

    #[Route('/annuler_reservation/{reservationId}', name: 'app_reservation_cancel', methods: ['POST'])]
    public function cancelReservation(int $reservationId, EntityManagerInterface $entityManagerInterface, ReservationRepository $reservationRepository, CancelReservationMailer $cancelReservationMailer): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour annuler une réservation.');
            return $this->redirectToRoute('app_login');
        }
        $reservation = $reservationRepository->find($reservationId);

        if(!$reservation){
            $this->addFlash('error', 'Réservation non trouvée.');
            return $this->redirectToRoute('app_all_reservation', ['clientId' => $user->getId()]);
        }


        $reservationCreatedAt = $reservation->getCreatedAt();
        $intervalTime = $reservationCreatedAt->diff(new DateTimeImmutable());
        $limitTime = ($intervalTime->days * 24) + $intervalTime->h;

        if($limitTime >= 48){
            $this->addFlash('error', 'Vous ne pouvez pas annuler votre réservation après 48h.');
            return $this->redirectToRoute('app_all_reservation', ['clientId' => $user->getId()]);
        }
        $reservation->setStatus(StatusReservationEnum::CANCELLED);


        $cancelReservationMailer->sendCancelReservationEmail($this->getUser()->getEmail(), $reservationId);

        $this->addFlash('success', 'Votre réservation a bien été annulée.');
        return $this->redirectToRoute('app_all_reservation', ['clientId' => $user->getId()]);
    
    }
        #[Route('/dates/reservations/{vehicleId}', name: 'app_get_dates_reservations', methods: ['GET'])]
        public function getDatesReservations(int $vehicleId, ReservationRepository $reservationRepository): JsonResponse
        {
            
            $reservations = $reservationRepository->findBy(['vehicle' => $vehicleId]);
        
            $data = array_map(function ($reservation) {
                $startDate = $reservation->getStartDate();
                $endDate = $reservation->getEndDate();
        
                if ($startDate > $endDate) {
                    $temp = $startDate;
                    $startDate = $endDate;
                    $endDate = $temp;
                }
        
                return [
                    'startDate' => $startDate->format('Y-m-d H:i:s'),
                    'endDate' => $endDate->format('Y-m-d H:i:s'),
                ];
            }, $reservations);
        
            return new JsonResponse($data, Response::HTTP_OK);
        }
    
}






