<?php

namespace App\Controller;

// src/Controller/GalerieController.php

use App\Repository\GalerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalerieController extends AbstractController
{
    #[Route('/galerie', name: 'app_galerie')]
    public function index(GalerieRepository $galerieRepository): Response
    {
        $galerie = $galerieRepository->findBy(
            [],
            [
                'position' => 'ASC',
                'createdAt' => 'DESC',
            ]
        );

        return $this->render('galerie/index.html.twig', [
            'galerie' => $galerie,
        ]);
    }
}

