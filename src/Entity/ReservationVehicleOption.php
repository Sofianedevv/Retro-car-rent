<?php

namespace App\Entity;

use App\Repository\ReservationVehicleOptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationVehicleOptionRepository::class)]
class ReservationVehicleOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $priceByOption = null;

    #[ORM\Column(nullable: true)]
    private ?int $count = null;

    #[ORM\ManyToOne(inversedBy: 'reservationVehicleOptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    #[ORM\ManyToOne(inversedBy: 'reservationVehicleOptions')]
    private ?VehicleOption $vehicleOptions = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceByOption(): ?string
    {
        return $this->priceByOption;
    }

    public function setPriceByOption(?string $priceByOption): static
    {
        $this->priceByOption = $priceByOption;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getVehicleOptions(): ?VehicleOption
    {
        return $this->vehicleOptions;
    }

    public function setVehicleOptions(?VehicleOption $vehicleOptions): static
    {
        $this->vehicleOptions = $vehicleOptions;

        return $this;
    }
}
