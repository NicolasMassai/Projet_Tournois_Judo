<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PhaseFinaleController extends AbstractController
{
    #[Route('/phase/finale', name: 'app_phase_finale')]
    public function index(): Response
    {
        return $this->render('phase_finale/index.html.twig', [
            'controller_name' => 'PhaseFinaleController',
        ]);
    }
}
