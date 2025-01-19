<?php

namespace App\Service\Reservation;

class ReservationService {

    public function calculateDays(\DateTimeInterface $startDate, \DateTimeInterface $endDate): int
    {
        if($startDate > $endDate) {
            return 0;
        }

        $interval = $startDate->diff($endDate);
        return $interval->days + 1;
    }
    public function calculateOptionPrice(array $options): float
    {
        $totalOptionPrice = 0;

        foreach ($options as $option) {
            $totalOptionPrice += $option['option']->getPrice() * $option['count'];
        }

        return $totalOptionPrice;
    }

    

}