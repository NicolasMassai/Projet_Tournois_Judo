<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Tournoi;
use App\Service\Service;
use App\Entity\Categorie;
use App\Form\TournoiType;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManager;
use App\Repository\TournoiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TournoiController extends AbstractController
{


    private EntityManagerInterface $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/tournoi', name: 'app_tournoi')]
    public function tournoi(TournoiRepository $tournoiRepository): Response
    {
        $tournoi = $tournoiRepository->findAll();


        return $this->render('tournoi/tournoi.html.twig', [
            'tournois' => $tournoi
        ]);
    }


    #[Route('/tournoi/create', name: 'app_tournoi_create')]
    public function create(Request $request): Response
    {
        $var = new Tournoi();
        $form = $this->createForm(TournoiType::class, $var);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
    
            $this->em->persist($var);
            $this->em->flush();
            return $this->redirectToRoute('app_tournoi');
        }

        return $this->render('tournoi/tournoi_create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/tournoi/update/{tournoi}', name: 'app_tournoi_update')]
    public function update(Service $myService, Tournoi $tournoi, Request $request): Response
    {

        $form = $myService->update(
            $request,
            new TournoiType,
            $tournoi,
            new ByteString('tournoi'),
            new ByteString('tournoi'),
            new ByteString('tournoi')
        );

        return $form;
    }

    #[Route('/tournoi/delete/{tournoi}', name: 'app_tournoi_delete')]
    public function delete(Tournoi $tournoi): Response

    {

        $this->em->remove($tournoi);
        $this->em->flush();

        return $this->redirectToRoute("app_tournoi");
    }


    #[Route('/tournoi/inscription', name: 'app_tournoi_inscription')]
    public function inscription(Service $myService, Request $request): Response
    {
        $club = new Club(); 
        $form = $this->createForm(InscriptionType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            
            $tournois = $form->get('tournois')->getData();

            foreach ($tournois as $tournoi) {
                // Associer le tournoi au club
                $club->addTournoi($tournoi);
            }
    
            $this->em->persist($club);
            $this->em->flush();
    
            return $this->redirectToRoute('app_tournoi');
        }
    
        return $this->render('tournoi/inscription_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}