<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Vehicle;
use App\Entity\Favorite;
use App\Repository\VehicleRepository;
use App\Repository\FavoriteRepository;
class FavoriteController extends AbstractController
{
    #[Route('/favoris/add/{vehicleId}', name: 'app_favorite_add')]
    public function addFavoris(int $vehicleId, Request $request, EntityManagerInterface $entityManager, VehicleRepository $vehicleRepository, FavoriteRepository $favoriteRepository): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter un favori.');
            return $this->redirectToRoute('app_login');
        }
    
        $vehicle =$vehicleRepository->find($vehicleId);
    
        if (!$vehicle) {
            $this->addFlash('error', 'Le véhicule n\'existe pas.');
            return $this->redirectToRoute('app_home');
        }
    
        $favorite = $favoriteRepository->findOneBy(['client' => $user]);
    
        if (!$favorite) {
            $favorite = new Favorite();
            $favorite->setClient($user);
            $entityManager->persist($favorite);
            $entityManager->flush();
        }
    
        if ($favorite->getVehicles()->contains($vehicle)) {
            $this->addFlash('error', 'Ce véhicule est déjà dans vos favoris.');
            return $this->redirectToRoute('app_home');
        }
    
        $favorite->addVehicle($vehicle);
        $entityManager->flush();
        
        $this->addFlash('success', 'Le véhicule a été ajouté à vos favoris.');
        return $this->redirectToRoute('app_home');
    }
    

    #[Route('/show_favorites', name: 'app_favorite_show')]
    public function showFavoris(FavoriteRepository $favoriteRepository): Response
    {
        $user = $this->getUser();
    
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir vos favoris.');
            return $this->redirectToRoute('app_home');
        }
    
        $favorites = $favoriteRepository->findBy(['client' => $user]);
    
        return $this->render('favorite/show.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    #[Route('/delete_favorites/{vehicleId}', name: 'app_favorite_delete')]
    public function deleteFavoris(int $vehicleId, EntityManagerInterface $entityManager, VehicleRepository $vehicleRepository, FavoriteRepository $favoriteRepository): Response
    {
        $user = $this->getUser ();
    
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour supprimer un favori.');
            return $this->redirectToRoute('app_login');
        }
    
        $vehicle = $vehicleRepository->find($vehicleId);
        $favorite = $favoriteRepository->findOneBy(['client' => $user]);
    
        if ($favorite) {
            if ($favorite->getVehicles()->contains($vehicle)) {
                $favorite->removeVehicle($vehicle); 
                $entityManager->flush();
                $this->addFlash('success', 'Le véhicule a été retiré de vos favoris.');
            } else {
                $this->addFlash('error', 'Ce véhicule n\'est pas dans vos favoris.');
            }
        } else {
            $this->addFlash('error', 'Aucun favori trouvé.');
        }
    
        return $this->redirectToRoute('app_favorite_show');
    }
    
    

}


