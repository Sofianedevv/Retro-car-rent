<?php

namespace App\test\functional\controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\VehicleRepository;
use App\Repository\ReservationRepository;
use App\Entity\User;
use App\Entity\Reservation;
use App\Enum\StatusReservationEnum; 
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

class ReservationControllerTest extends WebTestCase
{
    private function createUser($email = 'test@example.com', $password = 'password')
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $testUser = $userRepository->findOneByEmail($email);
        if (!$testUser) {
            $testUser = new User();
            $testUser->setEmail($email);
            $testUser->setPassword(password_hash($password, PASSWORD_BCRYPT));
            $testUser->setRoles(['ROLE_USER']);
            $testUser->setFirstName('John');
            $testUser->setLastName('Doe');
            $testUser->setPhoneNumber('0102030405');
            $testUser->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($testUser);
            $entityManager->flush();
        }

        return $testUser;
    }

    public function testAddReservationRequiresAuthentication()
    {
        $client = static::createClient();
        $vehicleId = 1; 
        $client->request('GET', '/nouvelle_reservation/' . $vehicleId);

        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testAuthenticatedUserCanAccessReservationForm()
    {
        $client = static::createClient();
        $testUser = $this->createUser(); 
        $vehicleRepository = static::getContainer()->get(VehicleRepository::class);
        
        $vehicle = $vehicleRepository->find(1);

        $client->loginUser($testUser);
        $client->request('GET', '/nouvelle_reservation/' . $vehicle->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name=reservation]');
    }

    public function testReservationSubmission()
    {
        $client = static::createClient();
        $testUser = $this->createUser(); 
        $vehicleRepository = static::getContainer()->get(VehicleRepository::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $vehicle = $vehicleRepository->find(1); 

        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/nouvelle_reservation/' . $vehicle->getId());

        $form = $crawler->selectButton('Réserver')->form([
            'reservation[startDate]' => '2025-02-15',
            'reservation[startTime]' => '10:00',
            'reservation[endDate]'   => '2025-02-20',
            'reservation[endTime]'   => '10:00',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects();  
        $locationHeader = $client->getResponse()->headers->get('Location');
        $this->assertStringContainsString('checkout.stripe.com', $locationHeader);  

       
        $reservation = $entityManager->getRepository(Reservation::class)->findOneBy([
            'client' => $testUser,
            'vehicle' => $vehicle
        ]);
        $this->assertNotNull($reservation);

        $this->assertEquals(StatusReservationEnum::PENDING, $reservation->getStatus());
    }


}
