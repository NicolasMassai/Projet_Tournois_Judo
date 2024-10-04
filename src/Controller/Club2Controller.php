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


class Club2Controller extends AbstractController
{


    private EntityManagerInterface $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/club', name: 'app_club')]
    public function club(ClubRepository $clubRepository): Response
    {
        $clubs = $clubRepository->findAll();

        $clubData = [];

        foreach ($clubs as $club) {
            $adherants = $club->getAdherant(); 
            $clubData[] = [
                'club' => $club,
                'count' => $adherants->count(),
                'adherants' => $adherants
            ];
        }

        return $this->render('club/club.html.twig', [
            'clubs' => $clubData,
        ]);
    }

    #[Route('/clubs/JSON', name: 'app_clubs2')]
    public function clubJSON(ClubRepository $clubRepository): Response
    {
        $Club = $clubRepository->findAll();


        $clubs = [];
        foreach ($Club as $club) {
            $clubs[] = [
                'id' => $club->getId(),
                'nom' => $club->getNom(),
                'ville' => $club->getVille()
            ];
        }
        return $this->json($clubs, 200);
    }

    #[Route('/clubs', name: 'app_clubs')]
    public function montre(): Response
    {

        return $this->render('club2/index.html.twig');
    }


    #[Route('/club/create', name: 'app_club_create')]
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

    #[Route('/club/update/{club}', name: 'app_club_update')]
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

    #[Route('/club/delete/{club}', name: 'app_club_delete')]
    public function delete(Club $club): Response

    {
               
        $this->em->remove($club);
        $this->em->flush();

        return $this->redirectToRoute("app_club");
        }
}  

