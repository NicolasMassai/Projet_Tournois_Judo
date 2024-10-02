<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Tournoi;
use App\Service\Service;
use App\Entity\Categorie;
use App\Form\TournoiType;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManager;
use App\Repository\ClubRepository;
use App\Repository\TournoiRepository;
use App\Repository\AdherantRepository;
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


    #[Route('/tournoi/{id}/inscription', name: 'inscrire_club_tournoi')]
    public function inscrireClub(Tournoi $tournoi, Request $request, EntityManagerInterface $em, AdherantRepository $adherantRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        // Récupérer le club du président
        $club = $user->getPresidentClub();

        // Récupérer les membres du club (Adherant)
        $adherants = $adherantRepository->findBy(['club' => $club]);

        // Créer le formulaire en passant les membres du club dans les options
        $form = $this->createForm(InscriptionType::class, $tournoi, [
            'club_adherants' => $adherants
        ]);
        dd($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les membres sélectionnés
            $selectedMembers = $form->get('combattant')->getData();

            foreach ($selectedMembers as $member) {
                // Ajouter chaque membre au tournoi
                $tournoi->addParticipant($member);
            }

            // Ajouter le club au tournoi
            $tournoi->addClub($club);

            // Sauvegarder les changements en base de données
            $em->persist($tournoi);
            $em->flush();

            // Rediriger vers la page de détails du tournoi
            return $this->redirectToRoute('tournoi_show', ['id' => $tournoi->getId()]);
        }

        return $this->render('tournoi/inscription_create.html.twig', [
            'form' => $form->createView(),
            'tournoi' => $tournoi,
        ]);
    }

}