<?php

namespace App\Entity;

use App\Repository\MotorcycleTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MotorcycleTypeRepository::class)]
class MotorcycleType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'motorcycleType', targetEntity: Motorcycle::class)]
    private Collection $motorcycles;

    public function __construct()
    {
        $this->motorcycles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection<int, Motorcycle>
     */
    public function getMotorcycles(): Collection
    {
        return $this->motorcycles;
    }

    public function addMotorcycle(Motorcycle $motorcycle): self
    {
        if (!$this->motorcycles->contains($motorcycle)) {
            $this->motorcycles->add($motorcycle);
            $motorcycle->setMotorcycleType($this);
        }
        return $this;
    }

    public function removeMotorcycle(Motorcycle $motorcycle): self
    {
        if ($this->motorcycles->removeElement($motorcycle)) {
            if ($motorcycle->getMotorcycleType() === $this) {
                $motorcycle->setMotorcycleType(null);
            }
        }
        return $this;
    }
} 