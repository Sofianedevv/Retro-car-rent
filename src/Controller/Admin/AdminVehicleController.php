<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use App\Entity\Van;
use App\Entity\Motorcycle;
use App\Entity\VehicleImage;
use App\Form\CarType;
use App\Form\VanType;
use App\Form\MotorcycleType;
use App\Repository\CarRepository;
use App\Repository\VanRepository;
use App\Repository\MotorcycleRepository;
use App\Security\Voter\VehicleVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/admin')]
class AdminVehicleController extends AbstractController
{
    #[IsGranted(VehicleVoter::MANAGE_ALL)]
    #[Route('/vehicles', name: 'admin_vehicles')]
    public function index(
        CarRepository $carRepository,
        VanRepository $vanRepository,
        MotorcycleRepository $motorcycleRepository
    ): Response {
        return $this->render('admin/vehicles/index.html.twig', [
            'cars' => $carRepository->findAll(),
            'vans' => $vanRepository->findAll(),
            'motorcycles' => $motorcycleRepository->findAll(),
        ]);
    }
    #[IsGranted(VehicleVoter::CREATE)]
    #[Route('/vehicle/car/new', name: 'admin_vehicle_car_new')]
    public function newCar(
        Request $request, 
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {

        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $imageFiles */
            $imageFiles = $form->get('imageFiles')->getData();

            foreach ($imageFiles as $imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('vehicle_images_directory'),
                        $newFilename
                    );

                    $vehicleImage = new VehicleImage();
                    $vehicleImage->setUrl($newFilename);
                    $car->addVehicleImage($vehicleImage);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $entityManager->persist($car);
            $entityManager->flush();

            $this->addFlash('success', 'La voiture a été ajoutée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form->createView(),
            'vehicle_type' => 'car'
        ]);
    }
    #[IsGranted(attribute: VehicleVoter::CREATE)]
    #[Route('/vehicle/van/new', name: 'admin_vehicle_van_new')]
    public function newVan(Request $request, EntityManagerInterface $entityManager): Response
    {
        $van = new Van();
        $form = $this->createForm(VanType::class, $van);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($van);
            $entityManager->flush();

            $this->addFlash('success', 'Le van a été ajouté avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form->createView(),
            'vehicle_type' => 'van'
        ]);
    }
    #[IsGranted(VehicleVoter::CREATE)]
    #[Route('/vehicle/motorcycle/new', name: 'admin_vehicle_motorcycle_new')]
    public function newMotorcycle(Request $request, EntityManagerInterface $entityManager): Response
    {
        $motorcycle = new Motorcycle();
        $form = $this->createForm(MotorcycleType::class, $motorcycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($motorcycle);
            $entityManager->flush();

            $this->addFlash('success', 'La moto a été ajoutée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/new.html.twig', [
            'form' => $form->createView(),
            'vehicle_type' => 'motorcycle'
        ]);
    }

    #[IsGranted(attribute: VehicleVoter::EDIT, subject: 'car')]

    #[Route('/vehicle/car/{id}/edit', name: 'admin_vehicle_car_edit')]
    public function editCar(
        Request $request, 
        Car $car, 
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $imageFiles */
            $imageFiles = $form->get('imageFiles')->getData();

            foreach ($imageFiles as $imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('vehicle_images_directory'),
                        $newFilename
                    );

                    $vehicleImage = new VehicleImage();
                    $vehicleImage->setUrl($newFilename);
                    $car->addVehicleImage($vehicleImage);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'La voiture a été modifiée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/edit.html.twig', [
            'form' => $form->createView(),
            'vehicle' => $car,
            'vehicle_type' => 'car'
        ]);
    }

    #[IsGranted(attribute: VehicleVoter::EDIT, subject: 'van')]
    #[Route('/vehicle/van/{id}/edit', name: 'admin_vehicle_van_edit')]
    public function editVan(
        Request $request, 
        Van $van, 
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(VanType::class, $van);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $imageFiles */
            $imageFiles = $form->get('imageFiles')->getData();

            foreach ($imageFiles as $imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('vehicle_images_directory'),
                        $newFilename
                    );

                    $vehicleImage = new VehicleImage();
                    $vehicleImage->setUrl($newFilename);
                    $van->addVehicleImage($vehicleImage);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Le van a été modifié avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/edit.html.twig', [
            'form' => $form->createView(),
            'vehicle' => $van,
            'vehicle_type' => 'van'
        ]);
    }

    #[IsGranted(attribute: VehicleVoter::EDIT, subject: 'motorcycle')]
    #[Route('/vehicle/motorcycle/{id}/edit', name: 'admin_vehicle_motorcycle_edit')]
    public function editMotorcycle(
        Request $request, 
        Motorcycle $motorcycle, 
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(MotorcycleType::class, $motorcycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $imageFiles */
            $imageFiles = $form->get('imageFiles')->getData();

            foreach ($imageFiles as $imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('vehicle_images_directory'),
                        $newFilename
                    );

                    $vehicleImage = new VehicleImage();
                    $vehicleImage->setUrl($newFilename);
                    $motorcycle->addVehicleImage($vehicleImage);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'La moto a été modifiée avec succès.');
            return $this->redirectToRoute('admin_vehicles');
        }

        return $this->render('admin/vehicles/edit.html.twig', [
            'form' => $form->createView(),
            'vehicle' => $motorcycle,
            'vehicle_type' => 'motorcycle'
        ]);
    }

    #[Route('/vehicle/image/{id}/delete', name: 'admin_vehicle_image_delete')]
    public function deleteImage(
        VehicleImage $image, 
        EntityManagerInterface $entityManager,
        Filesystem $filesystem
    ): Response {
        $vehicle = $image->getVehicle();
        $vehicleType = match (true) {
            $vehicle instanceof Car => 'car',
            $vehicle instanceof Van => 'van',
            $vehicle instanceof Motorcycle => 'motorcycle',
            default => throw new \RuntimeException('Type de véhicule inconnu'),
        };
        
        $imagePath = $this->getParameter('vehicle_images_directory').'/'.$image->getUrl();
        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }

        $entityManager->remove($image);
        $entityManager->flush();

        $this->addFlash('success', 'L\'image a été supprimée avec succès.');
        
        return $this->redirectToRoute("admin_vehicle_{$vehicleType}_edit", ['id' => $vehicle->getId()]);
    }

} 