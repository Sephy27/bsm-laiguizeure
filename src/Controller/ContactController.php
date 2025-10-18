<?php

namespace App\Controller;



use App\Entity\Contact;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;


final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this ->createForm(ContactFormType::class, $contact);
        $form ->handleRequest($request);
        if ($form ->isSubmitted() && $form ->isValid()) {
            
            $em->persist($contact);
            $em->flush();
            
            $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to('noreply@bsm-laiguizeure.fr')
            ->subject($contact->getSubject())
            ->htmlTemplate('emails/contact.html.twig')
            
            ->context([
                'contact' => $contact
            ]);

            $mailer->send($email);

            $this->addFlash('success', 'Message envoyé avec succès');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('contact/index.html.twig', [
            'contact' => $form
        ]);
    }

    
}
