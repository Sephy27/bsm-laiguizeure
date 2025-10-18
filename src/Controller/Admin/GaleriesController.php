<?php

namespace App\Controller\Admin;

use App\Entity\Galerie;
use App\Form\AddGalerieFormType;
use App\Repository\GalerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/galeries', name: 'app_admin_galeries_')]
final class GaleriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(GalerieRepository $repository): Response
    {
        $galerie = $repository->findAll();

        return $this->render('galerie/index.html.twig', [
            'galerie' => $galerie
        ]);
    }

     #[Route('/{id}', name: 'show', requirements: ['id'=> '\d+'])]
    public function show(Request $request, int $id, GalerieRepository $repository): Response
    {
        $galerie = $repository->find($id);
        if ($galerie->getId() !== $id) {
            return $this->redirectToRoute('app_admin_galeries_show', [ 'id' => $galerie->getId()]);
        }
        return $this->render('galerie/show.html.twig' , [
            'galerie' => $galerie
        ]);
    }

    #[Route('/add', name:'add')]
    public function add(Request $request, EntityManagerInterface $em)
    {
        $galerie = new Galerie();
        $form = $this->createForm(AddGalerieFormType::class, $galerie);
        $form -> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $galerie->setName($galerie->getName());
            
            $em->persist($galerie);
            $em->flush();

            $this->addFlash('success', 'L\'image a été créé');
            return $this->redirectToRoute('app_admin_galeries_index');
        }
        return $this->render('admin/galerie/add.html.twig', [
            'addForm' => $form
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET','POST'], requirements: ['id'=>Requirement::DIGITS])]
    public function edit(Galerie $galerie, Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(AddGalerieFormType::class, $galerie);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'L\'image a bien été modifiée');
            return $this->redirectToRoute('app_admin_galeries_index');
        }
        return $this->render('admin/galerie/edit.html.twig', [
            'galerie' => $galerie,
            'addForm'=> $form
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id'=>Requirement::DIGITS])]
    public function remove(Galerie $contact, EntityManagerInterface $em)
    {
        $em->remove($contact);
        $em->flush();
        $this->addFlash('success', 'L\'image a bien été supprimée');
        return $this->redirectToRoute('app_admin_galeries_index');
    }

}
