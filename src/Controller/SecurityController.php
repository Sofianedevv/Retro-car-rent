<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Flasher\Prime\FlasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;


class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, FlasherInterface $flasher): Response
    {
        if ($this->getUser()) {
            $flasher->addInfo('Vous êtes déjà connecté');
            if ($this->getUser()->isBanned()) {
                return $this->redirectToRoute('app_banned');
            }
            return $this->redirectToRoute('app');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $errorMessage = null;
        if ($error) {
            if ($error instanceof BadCredentialsException) {
                $errorMessage = 'Les identifiants fournis sont incorrects.';
            } else {
                $errorMessage = $error->getMessage();
            }
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $errorMessage,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FlasherInterface $flasher): Response
    {
        if ($this->getUser()) {
            $flasher->addInfo('Vous êtes déjà connecté');
            return $this->redirectToRoute('app');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setCreatedAt(new \DateTimeImmutable());
            
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            $flasher->addSuccess('Votre compte a été créé avec succès');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/banned', name: 'app_banned')]
    public function banned(): Response
    {
        $user = $this->getUser();
        if (!$user || !$user->isBanned()) {
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('security/banned.html.twig');
    }
}
