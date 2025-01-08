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

class ReservationNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private NormalizerInterface $normalizer;
    private DenormalizerInterface $denormalizer;
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

        $data = $this->normalizer->normalize($object, $format, array_merge($context, ['groups' => ['reservation:read']]));


        return $data;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [], int $userId = null): mixed
    {
        if ($type !== Reservation::class) {
            return new JsonResponse(
                ['message' => 'Type de classe invalide pour la dénormalisation'],
                Response::HTTP_BAD_REQUEST
            );      
        }
    
        if (!isset($data['vehicleId'], $data['startDate'], $data['endDate'], $data['totalPrice'])) {
            return new JsonResponse(
                ['message' => 'Champs requis manquants'],
                Response::HTTP_BAD_REQUEST
            );        }
    
        $client = $this->userRepository->find($userId);
        $vehicle = $this->vehicleRepository->find($data['vehicleId']);
    
        if (!$client) {
            return new JsonResponse(
                ['message' => 'Client introuvable'],
                Response::HTTP_NOT_FOUND
            );  
        }
    
        if (!$vehicle) {
            return new JsonResponse(
                ['message' => 'Vehicule introuvable'],
                Response::HTTP_NOT_FOUND
            ); 
        }
    
        $reservation = new Reservation();
        $reservation->setClient($client)
            ->setVehicle($vehicle)
            ->setStartDate(new DateTimeImmutable($data['startDate']))
            ->setEndDate(new DateTimeImmutable($data['endDate']))
            ->setTotalPrice($data['totalPrice'])
            ->setStatus(StatusReservationEnum::CONFIRMED)
            ->setCreatedAt(new DateTimeImmutable());

    
        return $reservation;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Reservation;
    }
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Reservation::class;
    }
    public function getSupportedTypes(?string $format): array
    {
        return [Reservation::class => true];
    }
}