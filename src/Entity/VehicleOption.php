<?php

namespace App\Entity;

use App\Repository\VehicleOptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleOptionRepository::class)]
class VehicleOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Vehicle>
     */
    #[ORM\ManyToMany(targetEntity: Vehicle::class, mappedBy: 'vehicleOptions')]
    private Collection $vehicles;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $price = null;

    /**
     * @var Collection<int, ReservationVehicleOption>
     */
    #[ORM\OneToMany(targetEntity: ReservationVehicleOption::class, mappedBy: 'vehicleOptions')]
    private Collection $reservationVehicleOptions;

    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
        $this->reservationVehicleOptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Vehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicle $vehicle): static
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
            $vehicle->addVehicleOption($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): static
    {
        if ($this->vehicles->removeElement($vehicle)) {
            $vehicle->removeVehicleOption($this);
        }

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, ReservationVehicleOption>
     */
    public function getReservationVehicleOptions(): Collection
    {
        return $this->reservationVehicleOptions;
    }

    public function addReservationVehicleOption(ReservationVehicleOption $reservationVehicleOption): static
    {
        if (!$this->reservationVehicleOptions->contains($reservationVehicleOption)) {
            $this->reservationVehicleOptions->add($reservationVehicleOption);
            $reservationVehicleOption->setVehicleOptions($this);
        }

        return $this;
    }

    public function removeReservationVehicleOption(ReservationVehicleOption $reservationVehicleOption): static
    {
        if ($this->reservationVehicleOptions->removeElement($reservationVehicleOption)) {
            // set the owning side to null (unless already changed)
            if ($reservationVehicleOption->getVehicleOptions() === $this) {
                $reservationVehicleOption->setVehicleOptions(null);
            }
        }

        return $this;
    }
}
