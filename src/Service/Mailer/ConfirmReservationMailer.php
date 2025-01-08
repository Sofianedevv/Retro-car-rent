<?php

namespace App\Service\Mailer;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Vehicle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class ConfirmReservationMailer {
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;
    private Environment $twig;

    public function __construct(MailerInterface $mailer,EntityManagerInterface $entityManager, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(Reservation $reservation): JsonResponse {

        $startDate = $reservation->getStartDate()->format('d/m/Y');
        $endDate = $reservation->getEndDate()->format('d/m/Y');
        $totalPrice = $reservation->getTotalPrice();
        $client = $reservation->getClient();
        $vehicle = $reservation->getVehicle();

        $email = (new Email())
            ->from('noreply@retrocar.com')
            ->to($client->getEmail())
            ->subject('Confirmation de réservation')
            ->html($this->twig->render('email/_email_location.html.twig', [
                'client' => $client,
                'vehicle' => $vehicle,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalPrice' => $totalPrice
            ]));

        try {
            $this->mailer->send($email);
            return new JsonResponse([
                'message' => 'Email envoyé avec succès'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Une erreur est survenue lors de l\'envoi de l\'email',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);

        }
     
     


    }
}