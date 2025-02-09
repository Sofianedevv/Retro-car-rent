<?php

namespace App\Controller\Reservation;

use App\Entity\Reservation;
use App\Entity\Vehicle;
use App\Entity\Invoice;
use App\Entity\ReservationVehicleOption;
use App\Entity\Payment;
use App\Repository\ReservationRepository;
use App\Repository\VehicleRepository;
use App\Repository\VehicleOptionRepository;
use App\Enum\StatusReservationEnum;
use App\Enum\PaymentStatusEnum;
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
use Symfony\Component\HttpFoundation\RedirectResponse;

use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Ramsey\Uuid\Guid\Guid;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;







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
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour effectuer une réservation.');
            return $this->redirectToRoute('app_login');
        }

        $vehicle = $vehicleRepository->find($vehicleId);
        if (!$vehicle) {
            $this->addFlash('error', 'Véhicule non trouvé.');
            return $this->redirectToRoute('app_vehicle_show_details', ['vehicleId' => $vehicleId]);
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
                $startTime = $form->get('startTime')->getData();
                $endDate = $form->get('endDate')->getData();
                $endTime = $form->get('endTime')->getData();

                $startDateTime = new DateTimeImmutable($startDate->format('Y-m-d') . ' ' . $startTime);
                $endDateTime = new DateTimeImmutable($endDate->format('Y-m-d') . ' ' . $endTime);

                if ($startDateTime >= $endDateTime) {
                    $this->addFlash('error', 'La date de fin doit être postérieure à la date de début.');
                    return $this->redirectToRoute('app_reservation_summary', ['vehicleId' => $vehicleId]);
                }

                $days = $reservationService->calculateDays($startDateTime, $endDateTime);
                $vehiclePrice = $vehicle->getPrice();
                $totalOptionPrice = $reservationService->calculateOptionPrice($options);
                $totalPrice = ($vehiclePrice * $days) + $totalOptionPrice;

       
                $reservation = new Reservation();
                $reservation->setClient($user);
                $reservation->setVehicle($vehicle);
                $reservation->setStartDate($startDateTime);
                $reservation->setEndDate($endDateTime);
                $reservation->setTotalPrice($totalPrice);
                $reservation->setStatus(StatusReservationEnum::PENDING);
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

                $entityManager->flush();
                
                Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

                $checkoutSession = Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [
                        [
                            'price_data' => [ 
                                'currency' => 'eur',
                                'product_data' => [
                                    'name' => 'Réservation du véhicule - ' . $vehicle->getBrand() . ' ' . $vehicle->getModel(),
                                    'description' => 'Du ' . $startDateTime->format('Y-m-d') . ' au ' . $endDateTime->format('Y-m-d')
                                ],
                                'unit_amount' => (int) ($totalPrice * 100),
                            ],
                            'quantity' => 1
                        ],
                    ],
                    'mode' => 'payment',
                    'success_url' => $this->generateUrl('app_payment_success', ['reservationId' => $reservation->getId()], 0),
                    'cancel_url' => $this->generateUrl('app_payment_cancel', ['reservationId' => $reservation->getId()], 0),
                    'client_reference_id' => (string) $user->getId()
                ]);
                return new RedirectResponse($checkoutSession->url);

            }
        }

        return $this->render('reservation/_add_reservation.html.twig', [
            'vehicle' => $vehicle,
            'options' => $options,
            'totalOptionPrice' => $totalOptionPrice,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/payment/success/{reservationId}', name: 'app_payment_success')]
    public function paymentSuccess(int $reservationId, EntityManagerInterface $entityManager, ConfirmReservationMailer $confirmReservationMailer, ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();
        $reservation = $reservationRepository->find($reservationId);
        if($reservation) {
            $reservation->setStatus(StatusReservationEnum::CONFIRMED);
            $entityManager->persist($reservation);
            $entityManager->flush();
            $payment = new Payment();
            $payment->setReservation($reservation);
            $payment->setAmount($reservation->getTotalPrice());
            $payment->setPaymentDate(new DateTimeImmutable());
            $payment->setPaymentMethod('CB');
            $payment->setPaymentStatus(PaymentStatusEnum::PAID);

            $entityManager->persist($payment);

            $invoice = new Invoice();
            $invoice->setReservation($reservation);
            $invoice->setCreatedAt(new DateTimeImmutable());
            $invoiceNumber = 'INV-' . Guid::uuid4()->toString();
            $invoice->setInvoiceNumber($invoiceNumber);
            $entityManager->persist($invoice);


            $entityManager->flush();

            $confirmReservationMailer->sendConfirmationEmail($reservation);
        }
        $this->addFlash('success', 'Paiement réussi ! Votre réservation a été confirmée.');
            
        return $this->redirectToRoute('app_all_reservation', ['clientId' => $user->getId()]);
    }

    #[Route('/payment/cancel/{reservationId}', name: 'app_payment_cancel')]
    public function paymentCancel(int $reservationId, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $reservation = $reservationRepository->find($reservationId);
        if ($reservation) {
            $reservation->setStatus(StatusReservationEnum::CANCELLED);
            $entityManager->flush();
        }
        $this->addFlash('error', 'Le paiement a été annulé. Veuillez réessayer.');
        return $this->redirectToRoute('app_add_reservation', ['vehicleId' => $reservation->getVehicle()->getId()]);
    }

    #[Route('/vos-reservations/{clientId}', name: 'app_all_reservation', methods: ['GET'])]
    public function getReservation(int $clientId, ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findBy([
            'client' => $clientId,
            'status' => StatusReservationEnum::CONFIRMED  
        ]);
        
        if (empty($reservations)) {
            $this->addFlash('error', 'Vous n\'avez pas de réservation confirmée en cours.');
        }
        
        $reservationsOptions = [];
        
        foreach ($reservations as $reservation) {
            $options = $reservation->getReservationVehicleOptions(); 
            $reservationsOptions[] = $options;
        }
        
        return $this->render('reservation/_get_reservations.html.twig', [
            'reservations' => $reservations,
            'reservationsOptions' => $reservationsOptions,
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
        $entityManagerInterface->flush();


        $cancelReservationMailer->sendCancelReservationEmail($user->getEmail(), $reservationId);
        $this->addFlash('success', 'Votre réservation a bien été annulée.');
        return $this->redirectToRoute('app_all_reservation', ['clientId' => $user->getId()]);
    
    }
        #[Route('/dates/reservations/{vehicleId}', name: 'app_get_dates_reservations', methods: ['GET'])]
        public function getDatesReservations(int $vehicleId, ReservationRepository $reservationRepository): JsonResponse
        {
            
            $reservations = $reservationRepository->findBy([
                'vehicle' => $vehicleId,
                'status' => StatusReservationEnum::CONFIRMED   
            ]);
        
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

        


        #[Route('/webhook', name: 'stripe_webhook', methods: ['POST'])]
        public function stripeWebhook(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
        {
            $payload = $request->getContent();
            $sigHeader = $request->headers->get('Stripe-Signature');
            $endpointSecret = $_ENV['STRIPE_ENDPOINT_SECRET'];
        
            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } catch (\UnexpectedValueException $e) {
                return new Response('Invalid payload', Response::HTTP_BAD_REQUEST);
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                return new Response('Invalid signature', Response::HTTP_BAD_REQUEST);
            }
        
            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;
                $reservationId = (int) $session->client_reference_id;
        
                $reservation = $reservationRepository->find($reservationId);
                if ($reservation) {
                    $reservation->setStatus(StatusReservationEnum::CONFIRMED);
                    $reservation->persist($reservation);
                    $entityManager->flush();
                }
            }
        
            return new Response('Webhook processed', Response::HTTP_OK);
        }
        
        
}






