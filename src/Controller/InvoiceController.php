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
use App\Repository\InvoiceRepository;
use App\Service\PDF\PdfService;
use Flasher\Prime\FlasherInterface;


class InvoiceController extends AbstractController
{
    #[Route('/vos-factures', name: 'app_invoice')]
    public function getInvoices(
        PdfService $pdf,
        ReservationRepository $reservationRepository,
        FlasherInterface $flasher
        ): Response
    {
        $user = $this->getUser();

        if (!$user) {
           $flasher->addInfo( 'Vous devez être connecté pour visualiser vos factures.');
            return $this->redirectToRoute('app_login');
        }
        
        $reservations = $reservationRepository->findBy(['client' => $user->getId()]);
        $invoices = [];
        foreach ($reservations as $reservation) {
            $invoice = $reservation->getInvoice();
            if (!$invoice || !$invoice->getReservation()) {
               $flasher->addError( 'Facture ou réservation manquante pour cette réservation.');
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

    #[Route('/factures/{reservationId}', name: 'app_invoice_download')]
    public function downloadInvoice(int $reservationId, InvoiceRepository $invoiceRepository, PdfService $pdfService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $invoice = $invoiceRepository->findOneBy([
            'reservation' => $reservationId,
        ]);

        if (!$invoice || $invoice->getReservation()->getClient()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Facture introuvable ou vous n\'êtes pas autorisé à accéder à cette facture.'], Response::HTTP_FORBIDDEN);
        }

        $pdfContent = $pdfService->generateInvoicePdf($invoice);

        return new Response($pdfContent, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="facture_' . $invoice->getId() . '.pdf"',
        ]);
    }
}
