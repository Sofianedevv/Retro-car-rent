<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Notification;

#[Route('/review')]
class ReviewController extends AbstractController
{
    #[Route('/add/{vehicleId}', name: 'app_review_add', methods: ['POST'])]
    public function add(
        int $vehicleId,
        Request $request,
        VehicleRepository $vehicleRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Debug des données reçues
        $requestData = $request->request->all();
        dump($requestData);
        
        if (empty($requestData)) {
            $this->addFlash('error', 'Aucune donnée reçue');
            return $this->redirectToRoute('app_vehicle_show_details', ['vehicleId' => $vehicleId]);
        }

        $vehicle = $vehicleRepository->find($vehicleId);
        if (!$vehicle) {
            $this->addFlash('error', 'Véhicule non trouvé');
            return $this->redirectToRoute('app_collections');
        }

        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour laisser un avis');
            return $this->redirectToRoute('app_login');
        }

        $rating = $request->request->get('rating');
        $comment = $request->request->get('comment');

        if (!$rating) {
            $this->addFlash('error', 'Veuillez donner une note');
            return $this->redirectToRoute('app_vehicle_show_details', ['vehicleId' => $vehicleId]);
        }

        if (!$comment) {
            $this->addFlash('error', 'Veuillez écrire un commentaire');
            return $this->redirectToRoute('app_vehicle_show_details', ['vehicleId' => $vehicleId]);
        }

        try {
            $review = new Review();
            $review->setRating((int)$rating);
            $review->setComment($comment);
            $review->setVehicle($vehicle);
            $review->setPublisher($user);
            $review->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($review);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre avis a été publié avec succès');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_vehicle_show_details', ['vehicleId' => $vehicleId]);
    }

    #[Route('/edit/{id}', name: 'app_review_edit', methods: ['GET', 'POST'])]
    public function edit(
        Review $review,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser() !== $review->getPublisher()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cet avis');
        }

        if ($request->isMethod('POST')) {
            $rating = $request->request->get('rating');
            $comment = $request->request->get('comment');

            if (!$rating || !$comment) {
                $this->addFlash('error', 'La note et le commentaire sont obligatoires');
                return $this->redirectToRoute('app_vehicle_show_details', ['vehicleId' => $review->getVehicle()->getId()]);
            }

            $review->setRating($rating);
            $review->setComment($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis a été modifié avec succès');
            return $this->redirectToRoute('app_vehicle_show_details', ['vehicleId' => $review->getVehicle()->getId()]);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review
        ]);
    }

    #[Route('/delete/{id}', name: 'app_review_delete', methods: ['GET'])]
    public function delete(
        Review $review,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser() !== $review->getPublisher()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cet avis');
        }

        $vehicleId = $review->getVehicle()->getId();
        $entityManager->remove($review);
        $entityManager->flush();

        $this->addFlash('success', 'Votre avis a été supprimé avec succès');
        return $this->redirectToRoute('app_vehicle_show_details', ['vehicleId' => $vehicleId]);
    }

    #[Route('/reply/{id}', name: 'app_review_reply', methods: ['POST'])]
    public function reply(
        Review $parentReview,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour répondre');
        }

        $comment = $request->request->get('comment');
        if (!$comment) {
            $this->addFlash('error', 'Le commentaire ne peut pas être vide');
            return $this->redirectToRoute('app_vehicle_show_details', [
                'vehicleId' => $parentReview->getVehicle()->getId()
            ]);
        }

        $reply = new Review();
        $reply->setComment($comment);
        $reply->setRating($parentReview->getRating());
        $reply->setVehicle($parentReview->getVehicle());
        $reply->setPublisher($user);
        $reply->setCreatedAt(new \DateTimeImmutable());
        $reply->setParent($parentReview);

        $entityManager->persist($reply);
        
        // Créer une notification pour l'auteur du commentaire parent
        $notification = new Notification();
        $notification->setMessage(sprintf(
            '%s %s a répondu à votre commentaire sur %s %s',
            $user->getFirstName(),
            $user->getLastName(),
            $parentReview->getVehicle()->getBrand(),
            $parentReview->getVehicle()->getModel()
        ));
        $notification->setCreatedAt(new \DateTimeImmutable());
        $notification->setReadStatus(false);
        $notification->setClient($parentReview->getPublisher());
        
        $entityManager->persist($notification);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réponse a été publiée');
        return $this->redirectToRoute('app_vehicle_show_details', [
            'vehicleId' => $parentReview->getVehicle()->getId()
        ]);
    }

    #[Route('/edit-reply/{id}', name: 'app_review_edit_reply', methods: ['GET', 'POST'])]
    public function editReply(
        Review $review,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser() !== $review->getPublisher()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette réponse');
        }

        if (!$review->getParent()) {
            throw $this->createNotFoundException('Cette réponse n\'existe pas');
        }

        if ($request->isMethod('POST')) {
            $comment = $request->request->get('comment');

            if (!$comment) {
                $this->addFlash('error', 'Le commentaire ne peut pas être vide');
                return $this->redirectToRoute('app_vehicle_show_details', [
                    'vehicleId' => $review->getVehicle()->getId()
                ]);
            }

            $review->setComment($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réponse a été modifiée avec succès');
            return $this->redirectToRoute('app_vehicle_show_details', [
                'vehicleId' => $review->getVehicle()->getId()
            ]);
        }

        return $this->render('review/edit_reply.html.twig', [
            'review' => $review
        ]);
    }
} 