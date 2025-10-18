<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/contacts', name:'app_admin_contacts_')]
final class ContactsController extends AbstractController{


    #[Route('/', name: 'index')]
    public function index(ContactRepository $repository): Response
    {
        $contact = $repository->findAll();

    return $this->render('admin/contact/index.html.twig', [
        'contact' => $contact
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id'=> '\d+'])]
    public function show(Request $request, int $id, ContactRepository $repository): Response
    {
        $contact = $repository->find($id);
        if ($contact->getId() !== $id) {
            return $this->redirectToRoute('app_admin_contacts_show', [ 'id' => $contact->getId()]);
        }
        return $this->render('contact/show.html.twig' , [
            'contact' => $contact
        ]);
    }

    #[Route('/add', name:'add')]
    public function add(Request $request, EntityManagerInterface $em)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        $form -> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $contact->setEmail($contact->getEmail());
            
            $em->persist($contact);
            $em->flush();

            $this->addFlash('success', 'Le contact a été créé');
            return $this->redirectToRoute('app_admin_contacts_index');
        }
        return $this->render('admin/contact/add.html.twig', [
            'addForm' => $form
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET','POST'], requirements: ['id'=>Requirement::DIGITS])]
    public function edit(Contact $contact, Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'L\'habitat a bien été modifiée');
            return $this->redirectToRoute('app_admin_contacts_index');
        }
        return $this->render('admin/contact/edit.html.twig', [
            'contact' => $contact,
            'addForm'=> $form
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id'=>Requirement::DIGITS])]
    public function remove(Contact $contact, EntityManagerInterface $em)
    {
        $em->remove($contact);
        $em->flush();
        $this->addFlash('success', 'Le message a bien été supprimée');
        return $this->redirectToRoute('app_admin_contacts_index');
    }
}
