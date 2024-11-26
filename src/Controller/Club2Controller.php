<?php

namespace App\Controller;

use App\Entity\Club;
use App\Form\ClubType;
use App\Service\Service;
use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Bundle\SecurityBundle\Security;
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

    #[Route('/clubUser', name: 'app_club_User')]
    public function clubUser(Security $security): Response
    {
        /** @var User $user */
        $user = $security->getUser();
        // Récupérer le club associé à l'utilisateur
        $club = $user->getClub();
        
        // Vérifier s'il existe un club
        if ($club) {
            // Récupérer les adhérents du club
            $adherants = $club->getAdherant();
    
            // Créer un tableau avec les données du club
            $clubData = [
                'club' => $club,
                'count' => $adherants->count(),
                'adherants' => $adherants
            ];
            
            return $this->render('club/clubUser.html.twig', [
                'club' => $clubData,
            ]);
        }
    
        return $this->redirectToRoute('app_home');
    }

        #[Route('/club/create', name: 'app_club_create')]
    public function create(Service $myService, Request $request): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer un club.');
        }

        return $myService->createClubWithPresident($request, $user);
    }



    
/*
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
    }*/

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
        
        // Casser la relation entre le club et son président
        if ($club->getPresident()) {
            $club->getPresident()->setPresidentClub(null);
        }
    
        // Supprimer les relations avec les adhérents
        foreach ($club->getAdherant() as $adherant) {
            $adherant->setClub(null);
        }
    
        // Supprimer le club
        $this->em->remove($club);
        $this->em->flush();
    
        return $this->redirectToRoute("app_club");
    }
    
}  

