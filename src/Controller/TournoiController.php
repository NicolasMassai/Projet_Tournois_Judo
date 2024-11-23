<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Tournoi;
use App\Entity\Adherant;
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
    public function tournoiUser(TournoiRepository $tournoiRepository, Security $security): Response
    {
        $user = $security->getUser();

        $tournois = $tournoiRepository->findByUserParticipation($user);

        return $this->render('tournoi/tournoiUser.html.twig', [
            'tournois' => $tournois,
        ]);
    }

    #[Route('/tournoi/create', name: 'app_tournoi_create')]
    public function create(Request $request): Response
    {        
        $user = $this->getUser();
        $var = new Tournoi();
        $var->setPresident($user);
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

    #[Route('/tournoi/{id}', name: 'app_tournoi_show')]
    public function tournoiID(int $id, TournoiRepository $tournoiRepository): Response
    {

        $tournoi = $tournoiRepository->find($id);

        return $this->render('tournoi/id.html.twig', [
            'tournoi' => $tournoi,
            'id' => $id,
            'clubs' => $tournoi->getClubs(),
            'combattants' => $tournoi->getCombattant(),
            'categories' => $tournoi->getPoids(),
        ]);
    }


    #[IsGranted("ROLE_PRESIDENT")]
    #[Route('/tournoi/{id}/inscription', name: 'inscrire_club_tournoi')]
    public function inscrireClub(Tournoi $tournoi, Request $request, 
        EntityManagerInterface $em, AdherantRepository $adherantRepository): Response {
    
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        // Récupérer le club du président
        $club = $user->getPresidentClub();
   
    
        // Récupérer les adhérents du club
        $adherants = $adherantRepository->findBy(['club' => $club]);
        // Créer le formulaire en passant les adhérents du club
        $form = $this->createFormBuilder()
            ->add('combattant', EntityType::class, [
                'class' => Adherant::class,
                'choices' => $adherants,  // Seuls les adhérents du club
                'choice_label' => function (Adherant $adherant) {
                    $categorie = $adherant->getCategorie();
                    return sprintf('%s (%s)', $adherant->getNom(), $categorie ? $categorie->getCategoriePoids() : 'Pas de catégorie');
                },                'multiple' => true,
                'expanded' => true,
            ])
            ->add('Inscrire', SubmitType::class)
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les combattants sélectionnés
            $selectedCombattants = $form->get('combattant')->getData();
    
            foreach ($selectedCombattants as $combattant) {
                // Ajouter chaque combattant au tournoi
                $tournoi->addCombattant($combattant);
            }
    
            // Ajouter le club au tournoi
            $tournoi->addClub($club);
    
            // Sauvegarder les changements en base de données
            $em->persist($tournoi);
            $em->flush();
    
            // Rediriger vers la page de détails du tournoi
            return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);        
        }
    
        return $this->render('tournoi/inscription_create.html.twig', [
            'form' => $form->createView(),
            'tournoi' => $tournoi,
        ]);
    }
    
    #[IsGranted("ROLE_PRESIDENT")]
    #[Route('/tournoi/{id}/poids', name: 'ajouter_categorie_poids_tournoi')]
    public function ajouterCategoriePoids(Tournoi $tournoi, Request $request, 
        EntityManagerInterface $em, AdherantRepository $adherantRepository): Response {


        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        // Récupérer le club du président
        $club = $user->getPresidentClub();

        // Récupérer les adhérents du club déjà inscrits au tournoi
        
        $tournoiID = $tournoi->getId();

        // Récupérer les adhérents déjà inscrits au tournoi
        $adherantsInscrits = $adherantRepository->findAdherantsInscritsDansTournoi($tournoiID, $club->getId());

        // Créer le formulaire pour associer les adhérents à une catégorie de poids
        $form = $this->createFormBuilder()
            ->add('adherants', EntityType::class, [
                'class' => Adherant::class,
                'choices' => $adherantsInscrits, // Seuls les adhérents inscrits au tournoi
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('poids', EntityType::class, [
                'class' => Categorie::class,
                'choices' => $tournoi->getPoids(), // Seules les catégories de poids disponibles pour ce tournoi
                'choice_label' => 'categorie_poids',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('Assigner', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les adhérents sélectionnés
            $selectedAdherants = $form->get('adherants')->getData();
            // Récupérer la catégorie de poids sélectionnée
            $categoriePoids = $form->get('poids')->getData();

            // Assigner la catégorie de poids à chaque adhérent
            foreach ($selectedAdherants as $adherant) {
                $adherant->addCategorie($categoriePoids);
                $em->persist($adherant);
            }

            // Sauvegarder les modifications
            $em->flush();

            // Rediriger vers la page de détails du tournoi
            return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]); 
        }

        return $this->render('tournoi/categorie_poids.html.twig', [
            'form' => $form->createView(),
            'tournoi' => $tournoi,
        ]);
    }



}