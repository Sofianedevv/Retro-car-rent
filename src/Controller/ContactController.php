<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class ContactController extends AbstractController
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        TransportInterface $transport
    ): Response {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            $email = new Email();
            $email->from('test@retro-car-rent.com')
                  ->to('0575e765519fc7@inbox.mailtrap.io')
                  ->subject('Contact from ' . $contact->getName())
                  ->html($this->renderView('emails/contact.html.twig', [
                      'contact' => $contact
                  ]));

            $transport->send($email);
            
            $this->addFlash('success', 'Message envoyé avec succès !');
            
            return $this->render('contact/index.html.twig', [
                'form' => $this->createForm(ContactType::class, new Contact())->createView(),
                'sent' => true
            ]);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'sent' => false
        ]);
    }
} 