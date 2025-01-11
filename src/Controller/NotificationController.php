<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notifications')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'app_notifications')]
    public function index(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $notifications = $notificationRepository->findBy(
            ['client' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications
        ]);
    }

    #[Route('/mark-as-read/{id}', name: 'app_notification_mark_as_read')]
    public function markAsRead(
        int $id,
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $notification = $notificationRepository->find($id);
        
        if (!$notification || $notification->getClient() !== $this->getUser()) {
            throw $this->createNotFoundException('Notification non trouvée');
        }

        $notification->setReadStatus(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_notifications');
    }

    #[Route('/mark-all-as-read', name: 'app_notifications_mark_all_as_read')]
    public function markAllAsRead(
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $notifications = $notificationRepository->findBy([
            'client' => $user,
            'readStatus' => false
        ]);

        foreach ($notifications as $notification) {
            $notification->setReadStatus(true);
        }

        $entityManager->flush();
        
        return $this->redirectToRoute('app_notifications');
    }

    #[Route('/delete/{id}', name: 'app_notification_delete')]
    public function delete(
        int $id,
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $notification = $notificationRepository->find($id);
        
        if (!$notification || $notification->getClient() !== $this->getUser()) {
            throw $this->createNotFoundException('Notification non trouvée');
        }

        $entityManager->remove($notification);
        $entityManager->flush();

        $this->addFlash('success', 'Notification supprimée avec succès');
        return $this->redirectToRoute('app_notifications');
    }
} 