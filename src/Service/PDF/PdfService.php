<?php

namespace App\Service\PDF;
use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;
use App\Entity\Invoice;
use App\Service\Reservation\ReservationService;
use Twig\Environment ;

class PdfService
{
    private $twig;
    private $factory;
    private ReservationService $reservationService;

    public function __construct(DompdfFactoryInterface $factory, Environment $twig, ReservationService $reservationService)
    {
    $this->factory = $factory;
    $this->twig = $twig;
    $this->reservationService = $reservationService;
    }

    public function generatePDF(string $html): string
    {
        $css = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/template-html/assets/css/styles.css');
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
        $days = $this->reservationService->calculateDays($startDate, $endDate);

        $vehiclePrice = $vehicle->getPrice();
        $totalPriceVehicle = $vehiclePrice * $days;
    
        $totalPriceOptions = 0;
        foreach ($reservationOptions as $option) {
            $totalPriceOptions += $option->getPriceByOption();
        }
        $css = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/template-html/assets/css/styles.css');

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
            'days' => $days,
        ]);
        $htmlWithCss = "<style>" . $css . "</style>" . $html;

        return $this->generatePDF($htmlWithCss);
    }
    
}