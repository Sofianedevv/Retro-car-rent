<?php
namespace App\test\unit\service;

use App\Service\Reservation\ReservationService;
use PHPUnit\Framework\TestCase;
use \DateTime;

class ReservationServiceTest extends TestCase
{
    private ReservationService $reservationService;

    protected function setUp(): void
    {
        $this->reservationService = new ReservationService();
    }

    public function testCalculateDaysWithValidDates()
    {
        $startDate = new \DateTime('2025-02-01');
        $endDate = new \DateTime('2025-02-03');

        $days = $this->reservationService->calculateDays($startDate, $endDate);

        $this->assertEquals(3, $days);  
    }

    public function testCalculateDaysWithSameDates()
    {
        $startDate = new \DateTime('2025-02-01');
        $endDate = new \DateTime('2025-02-01');

        $days = $this->reservationService->calculateDays($startDate, $endDate);

        $this->assertEquals(1, $days);  
    }

    public function testCalculateDaysWithStartDateAfterEndDate()
    {
        $startDate = new \DateTime('2025-02-03');
        $endDate = new \DateTime('2025-02-01');

        $days = $this->reservationService->calculateDays($startDate, $endDate);

        $this->assertEquals(0, $days); 
    }
}
