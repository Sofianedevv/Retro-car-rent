<?php

namespace App\Entity;

use App\Repository\MotorcycleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MotorcycleRepository::class)]
class Motorcycle extends Vehicle
{
    #[ORM\Column]
    private ?int $engineCapacity = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\ManyToOne(targetEntity: MotorcycleType::class, inversedBy: 'motorcycles')]
    #[ORM\JoinColumn(nullable: true)]
    private ?MotorcycleType $motorcycleType = null;

    public function getEngineCapacity(): ?int
    {
        return $this->engineCapacity;
    }

    public function setEngineCapacity(int $engineCapacity): static
    {
        $this->engineCapacity = $engineCapacity;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getMotorcycleType(): ?MotorcycleType
    {
        return $this->motorcycleType;
    }

    public function setMotorcycleType(?MotorcycleType $motorcycleType): self
    {
        $this->motorcycleType = $motorcycleType;
        return $this;
    }
}
