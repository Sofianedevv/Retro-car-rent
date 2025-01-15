<?php

namespace App\Controller\Reservation;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Service\Mailer\ConfirmReservationMailer;
use App\Service\Mailer\CancelReservationMailer;
use App\Serializer\Normalizer\ReservationNormalizer;
use App\Serializer\Denormalizer\ReservationDenormalizer;
use App\Service\Vehicle\VehicleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Enum\StatusReservationEnum;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

class ReservationController extends AbstractController
{
    private ReservationNormalizer $normalizer;
    private ReservationDenormalizer $denormalizer;
    private VehicleService $vehicleService;

    public function __construct(ReservationNormalizer $normalizer, VehicleService $vehicleService, ReservationDenormalizer $denormalizer)
    {
        $this->normalizer = $normalizer;    
        $this->vehicleService = $vehicleService;
        $this->denormalizer = $denormalizer;
    }

    #[Route('/reservation', name: 'app_reservation', methods: ['GET','POST'])]
    public function addReservation(Request $request, ConfirmReservationMailer $confirmReservationMailer,EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $this->getUser()->getId();
        try {
            $context = ['userId' => $userId];
            $reservation = $this->denormalizer->denormalize($data, Reservation::class, 'json', $context);          

            $confirmReservationMailer->sendConfirmationEmail($reservation);

            $entityManager->persist($reservation);
            $entityManager->flush();
            
            return new JsonResponse([
                'message' => 'Votre réservation a été enregistrée avec succès',

            ], Response::HTTP_CREATED);
            
        }
        catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Une erreur est survenue lors de la création de la réservation',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/reservation/annuler/{reservationId}', name: 'app_reservation_cancel', methods: ['DELETE'])]
    public function cancelReservation(int $reservationId, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): JsonResponse
    {
        $reservation = $reservationRepository->find($reservationId);

        if (!$reservation) {
            return new JsonResponse([             
                'message' => 'La réservation n\'existe pas'
            ], Response::HTTP_NOT_FOUND);

        }

        $reservationCreatedAt = $reservation->getCreatedAt();
        $intervalTime = $reservationCreatedAt->diff(new DateTimeImmutable());
        $totalHours = ($intervalTime->days * 24) + $intervalTime->h;

        if($totalHours >= 48) {
            return new JsonResponse([
                'message' => 'Vous ne pouvez pas annuler une réservation après 48 heures suivant votre réservation'
            ], Response::HTTP_BAD_REQUEST);

        }

        $entityManager->remove($reservation);
        $entityManager->flush();
        return new JsonResponse([
            'message' => 'La réservation a été supprimée avec succès'
        ], Response::HTTP_OK);

    }


    #[Route('/reservations/{vehicleId}', name: 'api_reservations', methods: ['GET'])]
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

    #[Route('/vos-reservations/{clientId}', name: 'api_reservations_client', methods: ['GET'])]
    public function getReservations(int $clientId, ReservationRepository $reservationRepository): JsonResponse
    {
        $reservations = $reservationRepository->findBy(['client' => $clientId]);

        if(empty($reservations)){
            return new JsonResponse(['message' => 'Aucune réservation trouvée pour ce client'], Response::HTTP_NOT_FOUND);
        }

        $data = [];

        foreach ($reservations as $reservation) {
            $normalizeData = $this->normalizer->normalize($reservation, 'json', ['groups' => 'reservations:read']);
            $data[] = $normalizeData;
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/annulation/{reservationId}', name: 'app_reservation_cancel_mail', methods: ['POST'])]
    public function sendEmailToCancelReservation(int $reservationId, ReservationRepository $reservationRepository, CancelReservationMailer $cancelReservationMailer): JsonResponse
    {
        $reservation = $reservationRepository->find($reservationId);

        if (!$reservation) {
            return new JsonResponse(['message' => 'Réservation non trouvée'], Response::HTTP_NOT_FOUND);
        }
         $cancelReservationMailer->sendCancelReservationEmail($reservation->getClient()->getEmail(), $reservationId);

        return new JsonResponse(['message' => 'Email d\'annulation de réservation envoyé'], Response::HTTP_OK);
        
    }



 
}
