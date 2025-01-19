<?php

namespace App\Service\Mailer;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CancelReservationMailer {
    private MailerInterface $mailer;
    private ReservationRepository $reservationRepository;
    private EntityManagerInterface $entityManager;


    public function __construct(MailerInterface $mailer,EntityManagerInterface $entityManager, Environment $twig, ReservationRepository $reservationRepository)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->reservationRepository = $reservationRepository;
    }

    public function sendCancelReservationEmail(string $userEmail, int $reservationId): void {

        $reservation = $this->reservationRepository->find($reservationId);
        $vehicle = $reservation->getVehicle();
        $reservationDetails = [
            'model' => $vehicle->getModel(),
            'brand' => $vehicle->getBrand(),
            'createdAt' => $reservation->getCreatedAt()->format('d/m/Y'),
            'startDate' => $reservation->getStartDate()->format('d/m/Y'),
            'endDate' => $reservation->getEndDate()->format('d/m/Y'),
        ];

        $email = (new Email())
            ->from('noreply@retrocar.com')
            ->to($userEmail)
            ->subject('Annulation de réservation')
            ->html($this->twig->render('email/_cancel_reservation.html.twig', [
                'reservationDetails' => $reservationDetails
            ]));

            $this->mailer->send($email);

     
     


    }
}