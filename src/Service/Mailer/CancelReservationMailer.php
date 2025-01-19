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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CancelReservationMailer {
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;
    private Environment $twig;

    public function __construct(MailerInterface $mailer,EntityManagerInterface $entityManager, Environment $twig, UrlGeneratorInterface $router)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->router = $router;
    }

    public function sendCancelReservationEmail(string $userEmail, int $reservationId): void {

       $url = $this->router->generate('app_reservation_cancel', ['reservatioId' => $reservationId], UrlGeneratorInterface::ABSOLUTE_URL);


        $email = (new Email())
            ->from('noreply@retrocar.com')
            ->to($userEmail)
            ->subject('Annulation de réservation')
            ->html($this->twig->render('email/_cancel_reservation.html.twig', [
                'url' => $url
            ]));

            $this->mailer->send($email);

     
     


    }
}