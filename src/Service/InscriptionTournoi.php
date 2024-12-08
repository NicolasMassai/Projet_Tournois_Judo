<?php

namespace App\Service;


use App\Entity\Arbitre;
use App\Entity\Tournoi;
use App\Entity\Adherant;
use App\Entity\CategorieTournoi;
use App\Repository\AdherantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InscriptionTournoi extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function inscrireClub(Tournoi $tournoi, Request $request, AdherantRepository $adherantRepository ): Response {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer le club du président
        $club = $user->getPresidentClub();

            // Vérifier si l'inscription au tournoi est fermée
        if (!$tournoi->isInscriptionOuvertes()) { // Remplacez 'getInscriptionOuverte()' par la méthode correcte de votre entité
            $this->addFlash('error', 'Les inscriptions sont fermées pour ce tournoi.');
            return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
        }

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
            $this->em->persist($categorieTournoi);
            $this->em->flush();

            // Rediriger vers la page de détails du tournoi
            return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
        }

        return $this->render('tournoi/inscription_create.html.twig', [
            'form' => $form->createView(),
            'tournoi' => $tournoi,
        ]);
    }
}