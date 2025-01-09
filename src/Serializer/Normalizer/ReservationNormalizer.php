<?php
namespace App\Serializer\Normalizer;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Repository\UserRepository;
use App\Repository\VehicleRepository;
use App\Enum\StatusReservationEnum;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class ReservationNormalizer implements NormalizerInterface
{
    private NormalizerInterface $normalizer;
    private EntityManagerInterface $entityManager;

    private UserRepository $userRepository;
    private VehicleRepository $vehicleRepository;

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        NormalizerInterface $normalizer,
                
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        VehicleRepository $vehicleRepository
    ) {
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        if (!$object instanceof Reservation) {
            throw new \InvalidArgumentException('L\'objet doit être une instance de Reservation');
        }

        $data = [
            'id' => $object->getId(),
            'client' => $object->getClient()->getId(),
            'vehicle' => [
                'id'=> $object->getId(),
                'brand' => $object->getVehicle()->getBrand(),
                'model' => $object->getVehicle()->getModel(),
            ],
            'startDate' => $object->getStartDate()->format('d/m/Y'),
            'endDate' => $object->getEndDate()->format('d/m/Y'),
            'totalPrice' => $object->getTotalPrice()
        ];

        return $data;
    }

    
    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Reservation;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Reservation::class => true];
    }
}
