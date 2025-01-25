<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;

use App\Entity\Vehicle;
use App\Entity\Favorite;
use App\Repository\VehicleRepository;
use App\Repository\FavoriteRepository;
use App\Entity\Notification;

class FavoriteController extends AbstractController
{
    #[Route('/ajouter-favoris/{vehicleId}', name: 'app_favorite_add')]
    public function addFavoris(int $vehicleId, Request $request, EntityManagerInterface $entityManager, VehicleRepository $vehicleRepository, FavoriteRepository $favoriteRepository, FlasherInterface $flasher): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            $flasher->addInfo('Vous devez être connecté pour ajouter un favori.');
            
            return $this->redirectToRoute('app_login');
        }
    
        $vehicle = $vehicleRepository->find($vehicleId);
    
        if (!$vehicle) {
         $flasher->addError('Le véhicule n\'existe pas.');
            return $this->redirectToRoute('app');
        }
    
        $favorite = $favoriteRepository->findOneBy(['client' => $user]);
    
        if (!$favorite) {
            $favorite = new Favorite();
            $favorite->setClient($user);
            $entityManager->persist($favorite);
        }
    
        if ($favorite->getVehicles()->contains($vehicle)) {
            $favorite->removeVehicle($vehicle);
            $message = sprintf('Vous avez retiré %s %s de vos favoris', $vehicle->getBrand(), $vehicle->getModel());

            $notification = new Notification();
            $notification->setMessage($message);
            $notification->setCreatedAt(new \DateTimeImmutable());
            $notification->setReadStatus(false);
            $notification->setType(Notification::TYPE_NEW_FAVORITE);
            $notification->setClient($user);
            
            $entityManager->persist($notification);
        } else {
            $favorite->addVehicle($vehicle);
            $message = sprintf('Vous avez ajouté %s %s à vos favoris', $vehicle->getBrand(), $vehicle->getModel());
            

            $notification = new Notification();
            $notification->setMessage($message);
            $notification->setCreatedAt(new \DateTimeImmutable());
            $notification->setReadStatus(false);
            $notification->setType(Notification::TYPE_NEW_FAVORITE);
            $notification->setClient($user);
            
            $entityManager->persist($notification);
        }
    
        $entityManager->flush();
        $flasher->addSuccess($message);
        
        return $this->redirectToRoute(route: 'app');
    }
    

    #[Route('/vos-favoris', name: 'app_favorite_show')]
    public function showFavoris(FavoriteRepository $favoriteRepository, FlasherInterface $flasher): Response
    {
        $user = $this->getUser();
    
        if (!$user) {
         $flasher->addInfo('Vous devez être connecté pour voir vos favoris.');
            return $this->redirectToRoute('app_home');
        }
    
        $favorites = $favoriteRepository->findBy(['client' => $user]);

        $nbFavorites = 0;
        foreach ($favorites as $favorite) {
            $nbFavorites += count($favorite->getVehicles()); 
        }
        
        return $this->render('favorite/show.html.twig', [
            'favorites' => $favorites,
            'nbFavorites' => $nbFavorites
        ]);
    }

    #[Route('/supprimer-favoris/{vehicleId}', name: 'app_favorite_delete')]
    public function deleteFavoris(int $vehicleId, EntityManagerInterface $entityManager, VehicleRepository $vehicleRepository, FavoriteRepository $favoriteRepository, FlasherInterface $flasher): Response
    {
        $user = $this->getUser();

        if (!$user) {
         $flasher->addInfo('Vous devez être connecté pour supprimer un favori.');
            return $this->redirectToRoute('app_login');
        }

        $vehicle = $vehicleRepository->find($vehicleId);
        $favorite = $favoriteRepository->findOneBy(['client' => $user]);

        if ($favorite) {
            if ($favorite->getVehicles()->contains($vehicle)) {
                $favorite->removeVehicle($vehicle);
                
                $message = sprintf('Vous avez retiré %s %s de vos favoris', $vehicle->getBrand(), $vehicle->getModel());
            
                $notification = new Notification();
                $notification->setMessage($message);
                $notification->setCreatedAt(new \DateTimeImmutable());
                $notification->setReadStatus(false);
                $notification->setType(Notification::TYPE_NEW_FAVORITE);
                $notification->setClient($user);
                
                $entityManager->persist($notification);
                $entityManager->flush();
                
                $flasher->addSuccess($message);
            } else {
             $flasher->addError('Ce véhicule n\'est pas dans vos favoris.');
            }
        } else {
         $flasher->addInfo('Aucun favori trouvé.');
        }

        return $this->redirectToRoute('app_favorite_show');
    }
    
    

}


