<?php 

namespace App\Service\Vehicle;

use App\Entity\Vehicle;
use App\Entity\Car;
use App\Entity\Van;
use App\Entity\Motorcycle;
use DateTime;
class VehicleService {
    
    public function getVehicleType(Vehicle $vehicle): string
    {
        if ($vehicle instanceof Car) {
            return 'Car';
        } elseif ($vehicle instanceof Van) {
            return 'Van';
        } elseif ($vehicle instanceof Motorcycle) {
            return 'Motorcycle';
        }

        return 'Type de véhicule inconnu';
    }

}