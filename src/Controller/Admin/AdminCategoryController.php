<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Flasher\Prime\FlasherInterface;
use Knp\Component\Pager\PaginatorInterface;

class AdminCategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'admin_categories')]
    public function index(CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $categoryRepository->findAll();
        $categoriesData = [];
        
        foreach ($categories as $category) {
            $vehicleTypes = [
                'cars' => 0,
                'vans' => 0,
                'motorcycles' => 0
            ];
            
            foreach ($category->getVehicles() as $vehicle) {
                if ($vehicle instanceof Car) {
                    $vehicleTypes['cars']++;
                } elseif ($vehicle instanceof Van) {
                    $vehicleTypes['vans']++;
                } elseif ($vehicle instanceof Motorcycle) {
                    $vehicleTypes['motorcycles']++;
                }
            }
            
            $categoriesData[] = [
                'category' => $category,
                'vehicleTypes' => $vehicleTypes
            ];
        }
    
        return $this->render('admin/categories/index.html.twig', [
            'categoriesData' => $categoriesData,
        ]);
    }
    
    #[Route('/admin/category/new', name: 'admin_category_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, FlasherInterface $flasher): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
    
            $flasher->addSuccess('Catégorie créée avec succès.');
            return $this->redirectToRoute('admin_categories');
        }
    
        return $this->render('admin/categories/new.html.twig', [
            'form' => $form->createView(),
        ]);
    } 

    #[Route('/admin/category/{id}/edit', name: 'admin_category_edit')]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager, FlasherInterface $flasher): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $flasher->addSucces('Catégorie modifiée avec succès.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route('/admin/category/{id}/delete', name: 'admin_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager, FlasherInterface $flasher): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            $flasher->addSuccess('Catégorie supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_categories');
    }
} 