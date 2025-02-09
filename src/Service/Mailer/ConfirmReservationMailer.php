<?php

namespace App\Service\Mailer;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class ConfirmReservationMailer
{
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(Reservation $reservation): void
    {

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

        $this->mailer->send($email);


    }
}