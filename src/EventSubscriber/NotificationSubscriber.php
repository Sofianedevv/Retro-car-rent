<?php

namespace App\EventSubscriber;

use App\Repository\NotificationRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment;

class NotificationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private Security $security,
        private Environment $twig
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        $user = $this->security->getUser();
        
        if ($user) {
            $unreadCount = $this->notificationRepository->count([
                'client' => $user,
                'readStatus' => false
            ]);
            
            $this->twig->addGlobal('unread_notifications_count', $unreadCount);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
} 