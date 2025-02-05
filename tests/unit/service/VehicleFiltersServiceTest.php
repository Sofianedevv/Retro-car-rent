<?php

namespace App\test\unit\service;

use App\Service\Vehicle\VehicleFiltersService;
use App\Repository\VehicleRepository;
use App\Repository\CarRepository;
use App\Repository\VanRepository;
use App\Repository\MotorcycleRepository;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class VehicleFiltersServiceTest extends TestCase
{
    private VehicleFiltersService $vehicleFiltersService;
    private $vehicleRepositoryMock;
    private $carRepositoryMock;
    private $vanRepositoryMock;
    private $motorcycleRepositoryMock;

    protected function setUp(): void
    {
        $this->vehicleRepositoryMock = $this->createMock(VehicleRepository::class);
        $this->carRepositoryMock = $this->createMock(CarRepository::class);
        $this->vanRepositoryMock = $this->createMock(VanRepository::class);
        $this->motorcycleRepositoryMock = $this->createMock(MotorcycleRepository::class);

        $this->vehicleFiltersService = new VehicleFiltersService(
            $this->vehicleRepositoryMock,
            $this->carRepositoryMock,
            $this->vanRepositoryMock,
            $this->motorcycleRepositoryMock
        );
    }

    public function testGetAllFiltersByRequestForCarType()
    {
        $request = new Request([
            'brand' => ['BMW', 'Audi'],
            'minPrice' => 10000,
            'maxPrice' => 50000,
            'minYear' => 2010,
            'maxYear' => 2020,
            'minMileage' => 5000,
            'maxMileage' => 100000,
            'transmission' => ['manual'],
            'fuelType' => ['essence'],
            'nbSeats' => ['4'],
            'nbDoors' => ['4'],
            'years' => ['2015'],
        ]);

        $filters = $this->vehicleFiltersService->getAllFiltersByRequest('car', $request);

        $this->assertEquals([
            'brand' => ['BMW', 'Audi'],
            'minPrice' => 10000,
            'maxPrice' => 50000,
            'minYear' => 2010,
            'maxYear' => 2020,
            'minMileage' => 5000,
            'maxMileage' => 100000,
            'transmission' => ['manual'],
            'fuelType' => ['essence'],
            'nbSeats' => ['4'],
            'nbDoors' => ['4'],
            'years' => ['2015'],
        ], $filters);
    }

    public function testGetAllFiltersByRequestForMotorcycleType()
    {
        $request = new Request([
            'brand' => ['Harley Davidson'],
            'minPrice' => 1000,
            'maxPrice' => 20000,
            'minYear' => 2015,
            'maxYear' => 2022,
            'minMileage' => 1000,
            'maxMileage' => 50000,
            'type' => ['Cruiser'],
            'minEngineCapacity' => 500,
            'maxEngineCapacity' => 1500,
            'years' => ['2020'],
        ]);

        $filters = $this->vehicleFiltersService->getAllFiltersByRequest('motorcycle', $request);

        $this->assertEquals([
            'brand' => ['Harley Davidson'],
            'minPrice' => 1000,
            'maxPrice' => 20000,
            'minYear' => 2015,
            'maxYear' => 2022,
            'minMileage' => 1000,
            'maxMileage' => 50000,
            'type' => ['Cruiser'],
            'minEngineCapacity' => 500,
            'maxEngineCapacity' => 1500,
            'years' => ['2020'],
        ], $filters);
    }

    public function testFetchFilterDataByVehicleTypeForCar()
{
    $this->vehicleRepositoryMock->method('findAllCarBrands')->willReturn(['BMW', 'Audi']);
    $this->carRepositoryMock->method('findAllTransmissions')->willReturn(['manual', 'automatic']);
    $this->vehicleRepositoryMock->method('findAllFuelTypes')->willReturn(['essence', 'diesel']);
    $this->carRepositoryMock->method('findNbSeats')->willReturn([2, 4, 5]);
    $this->carRepositoryMock->method('findNbDoors')->willReturn([2, 4]);
    $this->vehicleRepositoryMock->method('findYears')->willReturn([2015, 2016, 2017]);

    $filterData = $this->vehicleFiltersService->fetchFilterDataByVehicleType('car');

    $this->assertEquals([
        'brands' => ['BMW', 'Audi'],
        'transmissions' => ['manual', 'automatic'],
        'fuelTypes' => ['essence', 'diesel'],
        'nbSeatsOptions' => [2, 4, 5],
        'nbDoorsOptions' => [2, 4],
        'years' => [2015, 2016, 2017],
    ], $filterData);
}

public function testFetchFilterDataByVehicleTypeForMotorcycle()
{
    $this->vehicleRepositoryMock->method('findAllMotorcycleBrands')->willReturn(['Harley Davidson', 'Yamaha']);
    $this->motorcycleRepositoryMock->method('findAllEngineTypes')->willReturn(['V-Twin', 'Inline']);
    $this->vehicleRepositoryMock->method('findYears')->willReturn([2018, 2019, 2020]);

    $filterData = $this->vehicleFiltersService->fetchFilterDataByVehicleType('motorcycle');

    $this->assertEquals([
        'brands' => ['Harley Davidson', 'Yamaha'],
        'engineTypes' => ['V-Twin', 'Inline'],
        'years' => [2018, 2019, 2020],
    ], $filterData);
}

public function testFetchFilterDataByVehicleTypeForVan()
{
    $this->vehicleRepositoryMock->method('findAllVanBrands')->willReturn(['Ford', 'Mercedes']);
    $this->vanRepositoryMock->method('findNbSeats')->willReturn([2, 5, 7]);
    $this->vanRepositoryMock->method('findNbDoors')->willReturn([2, 4]);
    $this->vehicleRepositoryMock->method('findYears')->willReturn([2015, 2016, 2017]);

    $filterData = $this->vehicleFiltersService->fetchFilterDataByVehicleType('van');

    $this->assertEquals([
        'brands' => ['Ford', 'Mercedes'],
        'nbSeatsOptions' => [2, 5, 7],
        'nbDoorsOptions' => [2, 4],
        'years' => [2015, 2016, 2017],
    ], $filterData);
}

}
