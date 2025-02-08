<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Van extends Vehicle
{
    #[ORM\Column(type: 'float')]
    private ?float $cargoVolume = null;

    #[ORM\Column(type: 'integer')]
    private ?int $nbSeats = null;

    #[ORM\Column(type: 'integer')]
    private ?int $nbDoors = null;

    public function getCargoVolume(): ?float
    {
        return $this->cargoVolume;
    }

    public function setCargoVolume(float $cargoVolume): self
    {
        $this->cargoVolume = $cargoVolume;
        return $this;
    }

    public function getNbSeats(): ?int
    {
        return $this->nbSeats;
    }

    public function setNbSeats(int $nbSeats): self
    {
        $this->nbSeats = $nbSeats;
        return $this;
    }

    public function getNbDoors(): ?int
    {
        return $this->nbDoors;
    }

    public function setNbDoors(int $nbDoors): self
    {
        $this->nbDoors = $nbDoors;
        return $this;
    }
}
