<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;

#[ORM\InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap(['car' => Car::class, 'motorcycle' => Motorcycle::class, 'van' => Van::class])]
#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    private ?string $fuelType = null;

    #[ORM\Column]
    private ?int $mileage = null;

    #[ORM\Column]
    private ?bool $availability = null;

    /**
     * @var Collection<int, Favorite>
     */
    #[ORM\ManyToMany(targetEntity: Favorite::class, mappedBy: 'vehicles')]
    private Collection $favorites;

    /**
     * @var Collection<int, VehicleOption>
     */
    #[ORM\ManyToMany(targetEntity: VehicleOption::class, inversedBy: 'vehicles')]
    private Collection $vehicleOptions;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'vehicle')]
    private Collection $reservations;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'vehicle')]
    private Collection $reviews;

    /**
     * @var Collection<int, VehicleImage>
     */
    #[ORM\OneToMany(targetEntity: VehicleImage::class, mappedBy: 'vehicle')]
    private Collection $vehicleImages;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'vehicles')]
    private Collection $categories;

    #[ORM\Column(length: 255)]
    private ?string $defaultImage = null;

    public function __construct()
    {
        $this->favorites = new ArrayCollection();
        $this->vehicleOptions = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->vehicleImages = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

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

    public function getFuelType(): ?string
    {
        return $this->fuelType;
    }

    public function setFuelType(string $fuelType): static
    {
        $this->fuelType = $fuelType;

        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(int $mileage): static
    {
        $this->mileage = $mileage;

        return $this;
    }

    public function isAvailability(): ?bool
    {
        return $this->availability;
    }

    public function setAvailability(bool $availability): static
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->addVehicle($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            $favorite->removeVehicle($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, VehicleOption>
     */
    public function getVehicleOptions(): Collection
    {
        return $this->vehicleOptions;
    }

    public function addVehicleOption(VehicleOption $vehicleOption): static
    {
        if (!$this->vehicleOptions->contains($vehicleOption)) {
            $this->vehicleOptions->add($vehicleOption);
        }

        return $this;
    }

    public function removeVehicleOption(VehicleOption $vehicleOption): static
    {
        $this->vehicleOptions->removeElement($vehicleOption);

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setVehicle($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getVehicle() === $this) {
                $reservation->setVehicle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setVehicle($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getVehicle() === $this) {
                $review->setVehicle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VehicleImage>
     */
    public function getVehicleImages(): Collection
    {
        return $this->vehicleImages;
    }

    public function addVehicleImage(VehicleImage $vehicleImage): static
    {
        if (!$this->vehicleImages->contains($vehicleImage)) {
            $this->vehicleImages->add($vehicleImage);
            $vehicleImage->setVehicle($this);
        }

        return $this;
    }

    public function removeVehicleImage(VehicleImage $vehicleImage): static
    {
        if ($this->vehicleImages->removeElement($vehicleImage)) {
            // set the owning side to null (unless already changed)
            if ($vehicleImage->getVehicle() === $this) {
                $vehicleImage->setVehicle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getDefaultImage(): ?string
    {
        return $this->defaultImage;
    }

    public function setDefaultImage(string $defaultImage): static
    {
        $this->defaultImage = $defaultImage;

        return $this;
    }
}
