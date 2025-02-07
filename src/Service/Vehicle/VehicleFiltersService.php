<?php 

namespace App\Service\Vehicle;

use App\Entity\Vehicle;
use App\Entity\Car;
use App\Entity\Van;
use App\Entity\Motorcycle;
use App\Repository\VehicleRepository;
use App\Repository\CarRepository;
use App\Repository\VanRepository;
use App\Repository\MotorcycleRepository;

use DateTime;
use Symfony\Component\HttpFoundation\Request;

class VehicleFiltersService {
    private VehicleRepository $vehicleRepository;
    private CarRepository $carRepository;
    private VanRepository $vanRepository;

    private MotorcycleRepository $motorcycleRepository;

    public function __construct(VehicleRepository $vehicleRepository, CarRepository $carRepository, VanRepository $vanRepository, MotorcycleRepository $motorcycleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
        $this->carRepository = $carRepository;
        $this->vanRepository = $vanRepository;
        $this->motorcycleRepository = $motorcycleRepository;
        
    }

  
    public function getAllFiltersByRequest(string $type, Request $request): array {
        $filters = [
            'brand' =>  $request->query->all('brand') ?? [],
            'minPrice' => $request->query->get('minPrice') ?? null,
            'maxPrice' => $request->query->get('maxPrice') ?? null,
            'minYear' => $request->query->get('minYear') ?? null,
            'maxYear' => $request->query->get('maxYear') ?? null,
            'minMileage' => $request->query->get('minMileage') ?? null,
            'maxMileage' => $request->query->get('maxMileage') ?? null,
        ];
    
        return match ($type) {
            'car' => array_merge($filters, [

                'transmission' => $request->query->all('transmission') ?? [],
                'fuelType' => $request->query->all('fuelType') ?? [],
                'nbSeats' => $request->query->all('nbSeats') ?? [],
                'nbDoors' => $request->query->all('nbDoors') ?? [],
                'years' => $request->query->all('years') ?? [],
            ]),
            'motorcycle' => array_merge($filters, [

                'type' => $request->query->all('type') ?? [],
                'minEngineCapacity' => $request->query->get('minEngineCapacity') ?? null,
                'maxEngineCapacity' => $request->query->get('maxEngineCapacity') ?? null,
                'years' => $request->query->all('years') ?? [],

            ]),
            'van' => array_merge($filters, [

                'minCargoVolume' => $request->query->get('minCargoVolume')  ?? null,
                'maxCargoVolume' => $request->query->get('maxCargoVolume') ?? null,
                'nbSeats' => $request->query->all('nbSeats') ?? [],
                'nbDoors' => $request->query->all('nbDoors') ?? [],
                'years' => $request->query->all('years') ?? [],

            ]),
            'all' => array_merge($filters, [

                'fuelType' => $request->query->all('fuelType') ?? [],
                'years' => $request->query->all('years') ?? [],
            ]),
            default => $filters 
        };
    }
    

    public function fetchFilterDataByVehicleType(string $type): array {

        switch ($type) {
            case 'car':
                return [
                    'brands' => $this->vehicleRepository->findAllCarBrands(),
                    'transmissions' => $this->carRepository->findAllTransmissions(),
                    'fuelTypes' => $this->vehicleRepository->findAllFuelTypes(),
                    'nbSeatsOptions' => $this->carRepository->findNbSeats(),
                    'nbDoorsOptions' => $this->carRepository->findNbDoors(),
                    'years' => $this->vehicleRepository->findYears()
                ];
            case 'motorcycle':
                return [
                    'brands' => $this->vehicleRepository->findAllMotorcycleBrands(),
                    'engineTypes' => $this->motorcycleRepository->findAllEngineTypes(),
                    'years' => $this->vehicleRepository->findYears()
                ];
            case 'van':
                return [
                    'brands' => $this->vehicleRepository->findAllVanBrands(),
                    'nbSeatsOptions' => $this->vanRepository->findNbSeats(),
                    'nbDoorsOptions' => $this->vanRepository->findNbDoors(),
                    'years' => $this->vehicleRepository->findYears()
                ];
            case 'all':
                return [
                    'brands' => $this->vehicleRepository->findAllVehicleBrands(),
                    'years' => $this->vehicleRepository->findYears(),
                    'fuelTypes' => $this->vehicleRepository->findAllFuelTypes()
                ];
            default:
                return [];
        }
    }
    

    



    



    
}
