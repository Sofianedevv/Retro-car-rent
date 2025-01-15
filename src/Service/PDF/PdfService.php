<?php

namespace App\Service\PDF;
use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;
use Nucleos\DompdfBundle\Wrapper\DompdfInterface;
use App\Entity\Invoice;
use App\Entity\Reservation;
use App\Entity\User;
use Twig\Environment ;

class PdfService
{
    private $twig;
    private $factory;

    public function __construct(DompdfFactoryInterface $factory, Environment $twig)
    {
    $this->factory = $factory;
    $this->twig = $twig;
    }

    public function generatePDF(string $html): string
    {
        /** @var \Dompdf\Dompdf  $dompdf */
        $dompdf = $this->factory->create();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->output();
    }

    public function generateInvoicePdf(Invoice $invoice): string
    {
        $reservation = $invoice->getReservation();
        $client = $reservation->getClient();
        $vehicle = $reservation->getVehicle();

        $html = $this->twig->render('invoice/pdf.html.twig', [
            'invoice' => $invoice,
            'reservation' => $reservation,
            'client' => $client,
            'vehicle' => $vehicle,
        ]);

        return $this->generatePDF($html);
    }
    
}