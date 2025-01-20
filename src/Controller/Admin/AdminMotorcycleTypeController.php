<?php

namespace App\Controller\Admin;

use App\Entity\MotorcycleType;
use App\Form\MotorcycleTypeFormType;
use App\Repository\MotorcycleTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/motorcycle-types')]
class AdminMotorcycleTypeController extends AbstractController
{
    #[Route('/', name: 'admin_motorcycle_types')]
    public function index(MotorcycleTypeRepository $motorcycleTypeRepository): Response
    {
        return $this->render('admin/motorcycle_types/index.html.twig', [
            'types' => $motorcycleTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_motorcycle_type_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $motorcycleType = new MotorcycleType();
        $form = $this->createForm(MotorcycleTypeFormType::class, $motorcycleType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($motorcycleType);
            $entityManager->flush();

            $this->addFlash('success', 'Type de moto créé avec succès.');
            return $this->redirectToRoute('admin_motorcycle_types');
        }

        return $this->render('admin/motorcycle_types/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_motorcycle_type_edit')]
    public function edit(Request $request, MotorcycleType $motorcycleType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MotorcycleTypeFormType::class, $motorcycleType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Type de moto modifié avec succès.');
            return $this->redirectToRoute('admin_motorcycle_types');
        }

        return $this->render('admin/motorcycle_types/edit.html.twig', [
            'form' => $form->createView(),
            'motorcycleType' => $motorcycleType
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_motorcycle_type_delete', methods: ['POST'])]
    public function delete(Request $request, MotorcycleType $motorcycleType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$motorcycleType->getId(), $request->request->get('_token'))) {
            $entityManager->remove($motorcycleType);
            $entityManager->flush();
            $this->addFlash('success', 'Type de moto supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_motorcycle_types');
    }
} 