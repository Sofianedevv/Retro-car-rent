<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Reservation;
use App\Entity\Invoice;
use App\Repository\ReservationRepository;
use App\Service\PDF\PdfService;

class InvoiceController extends AbstractController
{
    #[Route('/vos-factures/{clientId}', name: 'app_invoice')]
    public function getInvoices(int $clientId,PdfService $pdf, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour visualiser vos factures.');
            return $this->redirectToRoute('app_login');
        }
        if (!$user || $user->getId() !== $clientId) {
            $this->addFlash('error', 'Vous devez être connecté pour visualiser vos factures ou vous ne pouvez voir que vos propres factures.');
            return $this->redirectToRoute('app_login');
        }
        $reservations = $reservationRepository->findBy(['client' => $clientId]);
        $invoices = [];
        foreach ($reservations as $reservation) {
            $invoice = $reservation->getInvoice();
            if (!$invoice || !$invoice->getReservation()) {
                $this->addFlash('error', 'Facture ou réservation manquante pour cette réservation.');
                continue; 
            }
            
            $pdfFile = $pdf->generateInvoicePdf($invoice);
            $invoices[] = [
                'invoice' => $invoice,
                'pdf' => base64_encode($pdfFile),
            ];

        }
       


        return $this->render('invoice/index.html.twig', [
           'invoices' => $invoices,
        ]);
    }
}
