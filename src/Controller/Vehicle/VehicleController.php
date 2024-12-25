<?php

namespace App\Controller\Vehicle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VehicleController extends AbstractController
{
    #[Route('/nos-vehicules', name: 'vehicle_collections')]
    public function collection(): Response
    {
        return $this->render('vehicle/collections.html.twig');
    }
}
