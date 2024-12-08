<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Arbitre;
use App\Entity\Tournoi;
use App\Entity\Adherant;
use App\Service\Service;
use App\Entity\Categorie;
use App\Form\TournoiType;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManager;
use App\Entity\CategorieTournoi;
use App\Repository\ClubRepository;
use App\Repository\TournoiRepository;
use App\Repository\AdherantRepository;
use App\Repository\CategorieTournoiRepository;
use App\Service\AssignArbitre;
use App\Service\InscriptionTournoi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

    #[Route('/tournoiUser', name: 'app_tournoiUser')]
    public function tournoiUser(TournoiRepository $tournoiRepository, Security $security, CategorieTournoiRepository $categorieTournoiRepository): Response
    {
        $user = $security->getUser();

       // $tournois = $tournoiRepository->findByUserParticipation($user);

        $tournois = $categorieTournoiRepository->findByUserParticipation($user);

        return $this->render('tournoi/tournoiUser.html.twig', [
            'tournois' => $tournois,
        ]);
    }

    #[IsGranted("ROLE_PRESIDENT")]
    #[Route('/tournoi/create', name: 'app_tournoi_create')]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        $tournoi = new Tournoi();
        $tournoi->setPresident($user);

        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les catégories sélectionnées
            $categories = $form->get('poids')->getData();

            foreach ($categories as $categorie) {
                $categorieTournoi = new CategorieTournoi();
                $categorieTournoi->setTournoi($tournoi);
                $categorieTournoi->setCategorie($categorie);

                $this->em->persist($categorieTournoi);
            }

            $tournoi->setInscriptionOuvertes(true);
            $this->em->persist($tournoi);
            $this->em->flush();

            return $this->redirectToRoute('app_tournoi');
        }

        return $this->render('tournoi/tournoi_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[IsGranted("ROLE_PRESIDENT")]
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

    #[IsGranted("ROLE_PRESIDENT")]
    #[Route('/tournoi/delete/{tournoi}', name: 'app_tournoi_delete')]
    public function delete(Tournoi $tournoi): Response

    {

        $this->em->remove($tournoi);
        $this->em->flush();

        return $this->redirectToRoute("app_tournoi");

        
    }

    #[Route('/tournoi/{id}', name: 'app_tournoi_show')]
    public function tournoiID(int $id, TournoiRepository $tournoiRepository): Response
    {
        // Récupérer le tournoi
        $tournoi = $tournoiRepository->find($id);
    
        if (!$tournoi) {
            throw $this->createNotFoundException('Tournoi non trouvé');
        }
    
        // Récupérer les catégories du tournoi
        $categoriesTournoi = $tournoi->getCategorieTournois();
    
        // Récupérer les combattants associés à ces catégories
        $combattants = [];
        foreach ($categoriesTournoi as $categorieTournoi) {
            foreach ($categorieTournoi->getCombattants() as $combattant) {
                $combattants[] = $combattant;
            }
        }
    
        // Passer toutes les informations à la vue
        return $this->render('tournoi/id.html.twig', [
            'tournoi' => $tournoi,
            'id' => $id,
            'clubs' => $tournoi->getClubs(),
            'combattants' => $combattants, // Passer les combattants
            'categoriesTournoi' => $categoriesTournoi, // Passer les catégories-tournois
        ]);
    }

    #[IsGranted("ROLE_PRESIDENT")]
    #[Route('/tournoi/{id}/fermer', name: 'app_tournoi_close')]
    public function fermerInscriptions(Tournoi $tournoi, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || $user !== $tournoi->getPresident()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à fermer les inscription pour ce tournoi.');
            return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
        }

        $tournoi->setInscriptionOuvertes(false);
        $this->em->flush();

        return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
    }

 

    #[IsGranted("ROLE_PRESIDENT")]
    #[Route('/tournoi/{id}/inscription', name: 'inscrire_club_tournoi')]
    public function inscrireClub(Tournoi $tournoi, Request $request, AdherantRepository $adherantRepository, InscriptionTournoi $inscription): Response {
       
        return $inscription->inscrireClub($tournoi, $request, $adherantRepository);

    }
    
    #[Route('/tournoi/{id}/arbitres', name: 'assign_arbitres')]
    public function assignArbitres(Tournoi $tournoi, Request $request, Security $security,AssignArbitre $arbitre): Response
    {
        return $arbitre->assignArbitres($tournoi, $request, $security);
    }
    
}