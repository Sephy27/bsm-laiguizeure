<?php

namespace App\Controller;

use App\Repository\GalerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GalerieController extends AbstractController
{
    #[Route('/galerie', name: 'app_galerie')]
    public function index(GalerieRepository $repository, Request $request): Response
    {
        $galerie = $repository->findAll();
        /* $page = $request->query->get('page',1);
        $galerie = $repository->getAllPaginated($page, 2); */

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
}
