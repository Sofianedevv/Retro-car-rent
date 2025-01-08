<?php

namespace App\Controller\Reservation;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Repository\VehicleRepository;
use App\repository\OptionRepository;
use App\Service\Mailer\ConfirmReservationMailer;
use App\Serializer\Normalizer\ReservationNormalizer;
use App\Service\Vehicle\VehicleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Enum\StatusReservationEnum;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;


class ReservationController extends AbstractController
{
    private ReservationNormalizer $normalizer;
    private VehicleService $vehicleService;

    public function __construct(ReservationNormalizer $normalizer, VehicleService $vehicleService)
    {
        $this->normalizer = $normalizer;    
        $this->vehicleService = $vehicleService;
    }

    #[Route('/reservation', name: 'app_reservation', methods: ['GET','POST'])]
    public function addReservation(Request $request, ConfirmReservationMailer $confirmReservationMailer,EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $this->getUser()->getId();
        dump($data);
        try {
            $reservation = $this->normalizer->denormalize($data, Reservation::class, 'json', [], $userId);          
            dump($reservation);

            $confirmReservationMailer->sendConfirmationEmail($reservation->getClient(), $reservation->getVehicle(), $reservation);

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

    // #[Route('/reservation/annuler/{id}', name: 'app_reservation_delete', methods: ['DELETE'])]
    // public function cancelReservation(Reservation $reservation,EntityManagerInterface $entityManager): JsonResponse
    // {
    //     $reservationCreatedAt = $reservation->getCreatedAt();
    //     $interval = $reservationCreatedAt->diff(new DateTimeImmutable());


    //     $entityManager->remove($reservation);
    //     $entityManager->flush();
    //     return new JsonResponse([
    //         'message' => 'La réservation a été supprimée avec succès'
    //     ], Response::HTTP_OK);

    // }

    #[Route('/api/reservations/{vehicleId}', name: 'api_reservations', methods: ['GET'])]
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
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d'),
            ];
        }, $reservations);
    
        return new JsonResponse($data, Response::HTTP_OK);
    }


 
}
