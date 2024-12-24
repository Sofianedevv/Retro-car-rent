<?php

namespace App\Entity;

use App\Repository\VanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VanRepository::class)]
class Van extends Vehicle
{
    #[ORM\Column]
    private ?int $cargoVolume = null;


    public function getCargoVolume(): ?int
    {
        return $this->cargoVolume;
    }

    public function setCargoVolume(int $cargoVolume): static
    {
        $this->cargoVolume = $cargoVolume;

        return $this;
    }
}
