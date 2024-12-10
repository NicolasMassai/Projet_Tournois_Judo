<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MentionLegaleController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function index(): Response
    {
        return $this->render('mention_legale/index.html.twig');
    }
}
