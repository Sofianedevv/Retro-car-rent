<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car extends Vehicle
{
    #[ORM\Column]
    private ?int $nbSeats = null;

    #[ORM\Column]
    private ?int $nbDoors = null;

    #[ORM\Column]
    private ?int $trunkSize = null;

    #[ORM\Column(length: 20)]
    private ?string $transmission = null;

    public function getNbSeats(): ?int
    {
        return $this->nbSeats;
    }

    public function setNbSeats(int $nbSeats): static
    {
        $this->nbSeats = $nbSeats;

        return $this;
    }

    public function getNbDoors(): ?int
    {
        return $this->nbDoors;
    }

    public function setNbDoors(int $nbDoors): static
    {
        $this->nbDoors = $nbDoors;

        return $this;
    }

    public function getTrunkSize(): ?int
    {
        return $this->trunkSize;
    }

    public function setTrunkSize(int $trunkSize): static
    {
        $this->trunkSize = $trunkSize;

        return $this;
    }

    public function getTransmission(): ?string
    {
        return $this->transmission;
    }

    public function setTransmission(string $transmission): static
    {
        $this->transmission = $transmission;

        return $this;
    }
}
