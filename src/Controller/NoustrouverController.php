<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NoustrouverController extends AbstractController
{
    #[Route('/noustrouver', name: 'app_noustrouver')]
    public function index(): Response
    {
        return $this->render('noustrouver/index.html.twig', [
            'controller_name' => 'NoustrouverController',
        ]);
    }
}
