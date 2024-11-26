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

            $this->em->persist($tournoi);
            $this->em->flush();

            return $this->redirectToRoute('app_tournoi');
        }

        return $this->render('tournoi/tournoi_create.html.twig', [
            'form' => $form->createView(),
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
    #[Route('/tournoi/{id}/inscription', name: 'inscrire_club_tournoi')]
    public function inscrireClub(Tournoi $tournoi, Request $request, EntityManagerInterface $em, 
        AdherantRepository $adherantRepository ): Response {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer le club du président
        $club = $user->getPresidentClub();

        // Récupérer les adhérents du club
        $adherants = $adherantRepository->findBy(['club' => $club]);

        // Récupérer les catégories du tournoi
        $categoriesTournoi = $tournoi->getCategorieTournois();

        // Créer le formulaire pour sélectionner les combattants par catégorie
        $form = $this->createFormBuilder()
            ->add('categorieTournoi', EntityType::class, [
                'class' => CategorieTournoi::class,
                'choices' => $categoriesTournoi, // Limité aux catégories de ce tournoi
                'choice_label' => function (CategorieTournoi $categorieTournoi) {
                    return sprintf(
                        '%s kg',
                        $categorieTournoi->getCategorie()->getCategoriePoids()
                    );
                },
                'placeholder' => 'Sélectionnez une catégorie',
            ])
            ->add('combattant', EntityType::class, [
                'class' => Adherant::class,
                'choices' => $adherants, // Seuls les adhérents du club
            'choice_label' => function (Adherant $adherant) {
            $categorie = $adherant->getCategorie();
            $categorieText = $categorie 
                ? sprintf(' (%s kg)', $categorie->getCategoriePoids()) 
                : ' (Pas de catégorie)';

            return sprintf('%s %s%s', $adherant->getNom(), $adherant->getPrenom(), $categorieText);
        },
            'multiple' => true,
            'expanded' => true,
        ])
        ->add('Inscrire', SubmitType::class)
        ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $categorieTournoi = $form->get('categorieTournoi')->getData();
            $selectedCombattants = $form->get('combattant')->getData();

            // Ajouter les combattants sélectionnés à la catégorie du tournoi
            foreach ($selectedCombattants as $combattant) {
                $categorieTournoi->addCombattant($combattant);
            }

            // Ajouter le club au tournoi s'il n'est pas déjà présent
            if (!$tournoi->getClubs()->contains($club)) {
                $tournoi->addClub($club);
            }

            // Sauvegarder les changements en base de données
            $em->persist($categorieTournoi);
            $em->flush();

            // Rediriger vers la page de détails du tournoi
            return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
        }

        return $this->render('tournoi/inscription_create.html.twig', [
            'form' => $form->createView(),
            'tournoi' => $tournoi,
        ]);
    }



    #[Route('/tournoi/{id}/arbitres', name: 'assign_arbitres')]
    public function assignArbitres(Tournoi $tournoi, Request $request, Security $security): Response
    {

        // Vérifier que l'utilisateur connecté est le président du tournoi
        $user = $security->getUser();

        if (!$user || $user !== $tournoi->getPresident()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à assigner des arbitres pour ce tournoi.');
            return $this->redirectToRoute('app_home');
        }

        $arbitres = $this->em->getRepository(Arbitre::class)->findAll(); // Récupérer tous les arbitres
    
        $form = $this->createFormBuilder()
            ->add('categorieTournoi', EntityType::class, [
                'class' => CategorieTournoi::class,
                'choices' => $this->em->getRepository(CategorieTournoi::class)
                    ->findBy(['tournoi' => $tournoi]),
                'choice_label' => function (CategorieTournoi $categorieTournoi) {
                    return $categorieTournoi->getCategorie()->getCategoriePoids();
                },
            ])
            ->add('arbitres', EntityType::class, [
                'class' => Arbitre::class,
                'choices' => $arbitres,
                'choice_label' => function (Arbitre $arbitre) {
                    return $arbitre->getNom() . ' ' . $arbitre->getPrenom();
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('Assigner', SubmitType::class)
            ->getForm();
    
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $categorieTournoi = $form->get('categorieTournoi')->getData();
                $selectedArbitres = $form->get('arbitres')->getData();
        
                foreach ($selectedArbitres as $arbitre) {
                    $categorieTournoi->addArbitre($arbitre);
                }
        
                $this->em->persist($categorieTournoi);
                $this->em->flush();
        
                return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
            }
    
        return $this->render('tournoi/arbitres.html.twig', [
            'form' => $form->createView(),
            'tournoi' => $tournoi,
        ]);
    }
    




}