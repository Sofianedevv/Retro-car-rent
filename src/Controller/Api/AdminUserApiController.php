<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/admin', name: 'api_admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserApiController extends AbstractController
{
    #[Route('/users', name: 'users_list', methods: ['GET'])]
    public function list(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Accès non autorisé'], Response::HTTP_FORBIDDEN);
        }

        try {
            $users = $userRepository->findAll();
            
            $jsonData = $serializer->serialize(
                ['users' => $users, 'total' => count($users)],
                'json',
                ['groups' => ['user:read']]
            );

            return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/users/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user, SerializerInterface $serializer): JsonResponse
    {
        try {
            $userData = $serializer->normalize($user, null, ['groups' => ['user:read']]);
            
            return new JsonResponse($userData);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Erreur lors du chargement de l\'utilisateur'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/users', name: 'user_create', methods: ['POST'])]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        if ($plainPassword = $user->getPlainPassword()) {
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
        }

        $user->setCreatedAt(new \DateTimeImmutable());
        
        $em->persist($user);
        $em->flush();

        return new JsonResponse(
            $serializer->serialize($user, 'json', ['groups' => ['user:read']]),
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    #[Route('/users/{id}', name: 'user_update', methods: ['PUT'])]
    public function update(
        Request $request,
        User $user,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        try {
            if (!$user) {
                throw new \Exception('Utilisateur non trouvé');
            }
            
            if ($user === $this->getUser()) {
                throw new \Exception('Vous ne pouvez pas supprimer votre propre compte');
            }

            foreach ($user->getReservations() as $reservation) {
                $em->remove($reservation);
            }
            foreach ($user->getReviews() as $review) {
                $em->remove($review);
            }
            foreach ($user->getFavorites() as $favorite) {
                $em->remove($favorite);
            }
            foreach ($user->getNotifications() as $notification) {
                $em->remove($notification);
            }

            $em->remove($user);
            $em->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
} 