<?php

namespace App\Service\PDF;
use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;
use App\Entity\Invoice;
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
        $css = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/css/invoice.css');
        /** @var \Dompdf\Dompdf  $dompdf */
        $dompdf = $this->factory->create();
        $htmlWithCss = "<style>" . $css . "</style>" . $html;
        $dompdf->loadHtml($htmlWithCss);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->output();
    }

    public function generateInvoicePdf(Invoice $invoice): string
    {
        $reservation = $invoice->getReservation();
        $client = $reservation->getClient();
        $vehicle = $reservation->getVehicle();
        $reservationOptions = $reservation->getReservationVehicleOptions();
        $startDate = $reservation->getStartDate();
        $endDate = $reservation->getEndDate();
        $duration = $endDate->diff($startDate)->days;
        $vehiclePrice = $vehicle->getPrice();
        $totalPriceVehicle = $vehiclePrice * $duration;
    
        $totalPriceOptions = 0;
        foreach ($reservationOptions as $option) {
            $totalPriceOptions += $option->getPriceByOption();
        }

        $html = $this->twig->render('invoice/pdf.html.twig', [
            'invoice' => $invoice,
            'reservation' => $reservation,
            'client' => $client,
            'vehicle' => $vehicle,
            'reservationOptions' => $reservationOptions,
            'totalPriceVehicle' => $totalPriceVehicle,
            'totalPriceOptions' => $totalPriceOptions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'duration' => $duration,
        ]);

        return $this->generatePDF($html);
    }
    
}