<?php

namespace App\Controller;

use App\Entity\Club;
use App\Form\ClubType;
use App\Repository\ClubRepository;
use App\Service\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ClubController extends AbstractController
{


    private EntityManagerInterface $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/club', name: 'app_club')]
    public function club(ClubRepository $clubRepository): Response
    {
        $club = $clubRepository->findAll();


        return $this->render('club/club.html.twig', [
            'clubs' => $club
        ]);
    }


    #[Route('/club/create', name: 'app_produit_create')]
    public function create(Service $myService, Request $request): Response
    {
        $form = $myService->create(
            $request,
            new Club,
            new ClubType,
            new ByteString('club'),
            new ByteString('club'),
            new ByteString('club')
        );

        return $form;
    }

    #[Route('/club/update/{club}', name: 'app_produit_update')]
    public function update(Service $myService, Club $club, Request $request): Response
    {

        $form = $myService->update(
            $request,
            new ClubType,
            $club,
            new ByteString('club'),
            new ByteString('club'),
            new ByteString('club')
        );

        return $form;

    }

    #[Route('/club/delete/{club}', name: 'app_produit_delete')]
    public function delete(Club $club): Response

    {
               
        $this->em->remove($club);
        $this->em->flush();

        return $this->redirectToRoute("app_club");
        }
}  

