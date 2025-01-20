<?php
namespace App\Tests\Unitaires;

use App\Controller\Api\AdminUserApiController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class AdminUserApiControllerTest extends TestCase
{
    public function testCreateUser(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'phoneNumber' => '1234567890',
            'roles' => ['ROLE_USER'],
            'plainPassword' => 'password123'
        ];

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail($userData['email']);
        $user->setFirstName($userData['firstName']);
        $user->setLastName($userData['lastName']);
        $user->setPhoneNumber($userData['phoneNumber']);
        $user->setRoles($userData['roles']);
        $user->setPlainPassword($userData['plainPassword']);
        $user->setCreatedAt(new \DateTimeImmutable());

        $constraintViolationList = new ConstraintViolationList();
        $validator->method('validate')->willReturn($constraintViolationList);

        $passwordHasher->method('hashPassword')->willReturn('hashed_password');

        $entityManager->expects($this->once())
                       ->method('persist')
                       ->with($user);
        $entityManager->expects($this->once())
                       ->method('flush');

        $serializer->method('deserialize')
                   ->willReturn($user);

        $controller = new AdminUserApiController();

        $request = Request::create('/api/admin/users', 'POST', [], [], [], [], json_encode($userData));
        $response = $controller->create($request, $serializer, $entityManager, $validator, $passwordHasher);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }
}
