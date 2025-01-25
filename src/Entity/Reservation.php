<?php

namespace App\Entity;

use App\Enum\StatusReservationEnum;
use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;

    #[ORM\Column]


    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column]
  
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
 

    private ?string $totalPrice = null;

    #[ORM\Column(enumType: StatusReservationEnum::class)]
    #[Groups(['reservation:read'])]

    private ?StatusReservationEnum $status = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]


    private ?User $client = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]


    private ?Vehicle $vehicle = null;

    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]

    private ?Payment $payment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Invoice $invoice = null;

    /**
     * @var Collection<int, ReservationVehicleOption>
     */
    #[ORM\OneToMany(targetEntity: ReservationVehicleOption::class, mappedBy: 'reservation')]
    private Collection $reservationVehicleOptions;

    #[ORM\Column(type: 'uuid', unique: true, nullable: true)]
    private ?UuidInterface $reference = null;

    public function __construct()
    {
        $this->reservationVehicleOptions = new ArrayCollection();
        // Commentez temporairement cette ligne
        // $this->reference = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getStatus(): ?StatusReservationEnum
    {
        return $this->status;
    }

    public function setStatus(StatusReservationEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): static
    {
        // set the owning side of the relation if necessary
        if ($payment->getReservation() !== $this) {
            $payment->setReservation($this);
        }

        $this->payment = $payment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): static
    {
        if ($invoice->getReservation() !== $this) {
            $invoice->setReservation($this);
        }

        $this->invoice = $invoice;

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
            $reservationVehicleOption->setReservation($this);
        }

        return $this;
    }

    public function removeReservationVehicleOption(ReservationVehicleOption $reservationVehicleOption): static
    {
        if ($this->reservationVehicleOptions->removeElement($reservationVehicleOption)) {
            // set the owning side to null (unless already changed)
            if ($reservationVehicleOption->getReservation() === $this) {
                $reservationVehicleOption->setReservation(null);
            }
        }

        return $this;
    }

    public function getReference(): ?UuidInterface
    {
        return $this->reference;
    }

    public function setReference(UuidInterface $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

}
