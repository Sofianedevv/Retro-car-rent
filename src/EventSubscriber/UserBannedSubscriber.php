<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserBannedSubscriber implements EventSubscriberInterface
{
    private $security;
    private $urlGenerator;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 1],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        // Vérifier si l'utilisateur est banni
        if ($user->isBanned()) {
            $currentRoute = $event->getRequest()->attributes->get('_route');
            
            // Permettre l'accès uniquement aux pages autorisées
            if (!in_array($currentRoute, ['app_login', 'app_logout', 'app_banned'])) {
                // Rediriger vers la page de bannissement
                $loginUrl = $this->urlGenerator->generate('app_banned');
                $event->setResponse(new RedirectResponse($loginUrl));
            }
        }
    }
} 