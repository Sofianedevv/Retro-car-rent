<?php 

namespace App\Service\Vehicle;

use App\Entity\Vehicle;
use App\Entity\Car;
use App\Entity\Van;
use App\Entity\Motorcycle;
use App\Repository\VehicleRepository;
use App\Repository\ReservationRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

class VehicleService {
    private VehicleRepository $vehicleRepository;
    private ReservationRepository $reservationRepository;

    public function __construct(VehicleRepository $vehicleRepository, ReservationRepository $reservationRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
        $this->reservationRepository = $reservationRepository;
    }

    public function getVehicleType(Vehicle $vehicle): string
    {
        return match (true) {
            $vehicle instanceof Car => 'Car',
            $vehicle instanceof Van => 'Van',
            $vehicle instanceof Motorcycle => 'Motorcycle',
            default => 'Type de véhicule inconnu',
        };
    }
   
 
    
}
