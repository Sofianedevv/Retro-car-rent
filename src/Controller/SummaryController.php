<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Vehicle;
use App\Entity\Favorite;
use App\Repository\VehicleRepository;
use App\Repository\FavoriteRepository;
class SummaryController extends AbstractController
{
    #[Route('/recapitulatif', name: 'app_reservation_summary')]
    public function reservation_summary(Request $request, VehicleRepository $vehicleRepository): Response
    {
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');
        $totalPrice = $request->query->get('totalPrice');
        $vehicleId = (int) $request->query->get('vehicleId');

        $vehicle = $vehicleRepository->find($vehicleId);
        if(!$vehicle){
            $this->addFlash('error', 'Vehicle introuvable');
            return $this->redirectToRoute('app_collections');
        }
        return $this->render('reservation/_recap_reservation.html.twig', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalPrice' => $totalPrice,
            'vehicle' => $vehicle
    
        ]);

        

    }
    

    
    
    

}


