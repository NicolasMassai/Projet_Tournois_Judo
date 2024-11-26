<?php

namespace App\Controller;

use App\Entity\Combat;
use App\Entity\Groupe;
use App\Entity\Arbitre;
use App\Entity\Tournoi;
use App\Form\CombatType;
use App\Entity\Categorie;
use App\Service\CombatService;
use App\Entity\CategorieTournoi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

class CombatController extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/tournoi/{id}/groupes', name: 'afficher_groupes')]
    public function afficherGroupes(Tournoi $tournoi, CombatService $service): Response
    {
        $groupes = $service->creerGroupesParCategorie($tournoi);

        // Récupérer les groupes du tournoi
        $groupes = $tournoi->getGroupes();
        
        $resultats = [];
    
        // Parcourir chaque groupe
        foreach ($groupes as $groupe) {
            // Récupérer les combattants du groupe
            $combattants = $groupe->getCombattants();
    
            // Préparer une liste des noms des combattants
            $nomsCombattants = [];
            foreach ($combattants as $combattant) {
                $nomsCombattants[] = $combattant->getNom() . ' ' . $combattant->getPrenom();
            }
    
            // Stocker le nom du groupe et les noms des combattants
            $resultats[] = [
                'groupe' => $groupe->getNom(),
                'combattants' => $nomsCombattants,
            ];
        }
    
        // Rendre la vue avec les données des groupes et combattants
        return $this->render('combat/groupes.html.twig', [
            'resultats' => $resultats,
        ]);
    }
     
    #[Route('/tournoi/{id}/combats', name: 'afficher_combats')]
    public function afficherCombats(Tournoi $tournoi): Response
    {
        // Récupérer tous les combats du tournoi
        $combats = $this->em->getRepository(Combat::class)->findBy(['tournoi' => $tournoi]);

        return $this->render('combat/combats.html.twig', [
            'tournoi' => $tournoi,
            'combats' => $combats,
        ]);
    }


    #[IsGranted("ROLE_ADHERANT")]
    #[Route('/tournoi/{id}/combatUser', name: 'afficher_combat_User')]
    public function afficherCombatsUser(Tournoi $tournoi): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer les combats du tournoi où l'utilisateur est impliqué
        // Supposons que les combattants soient liés à un utilisateur
        $combats = $this->em->getRepository(Combat::class)->findByUserAndTournoi($user, $tournoi);

        return $this->render('combat/combatUser.html.twig', [
            'tournoi' => $tournoi,
            'combats' => $combats,
        ]);
    }


    #[Route('/tournoi/{id}/classement', name: 'afficher_classement')]
    public function afficherClassement(Tournoi $tournoi, CombatService $service): Response
    {
        $classements = [];

        // Parcourir tous les groupes du tournoi
        foreach ($tournoi->getGroupes() as $groupe) {
            // Générer le classement pour chaque groupe
            $classements[$groupe->getNom()] = $service->genererClassementPourGroupe($groupe);
        }

        return $this->render('combat/classement.html.twig', [
            'tournoi' => $tournoi,
            'classements' => $classements,
        ]);
    }


    #[Route('/tournoi/{id}/combats/auto', name: 'edit_combat_auto')]
    public function editCombatAuto(Tournoi $tournoi): Response
    {
        
        // Récupérer le tournoi
        //$tournoi = $this->em->getRepository(Tournoi::class)->find($id);

        if (!$tournoi) {
            throw $this->createNotFoundException('Tournoi non trouvé.');
        }

        // Récupérer tous les combats associés à ce tournoi
        $combats = $this->em->getRepository(Combat::class)->findBy([
            'tournoi' => $tournoi
        ]);

        foreach ($combats as $combat) {
            if ($combat->getResultat()) {
                continue; // Passer au prochain combat
            }

            // Simuler un résultat aléatoire pour chaque combat
            $resultat = rand(0, 2); // 0: combattant1 gagne 10-0, 1: combattant2 gagne 10-0, 2: égalité 10-7 ou 7-10

            switch ($resultat) {
                case 0: // combattant1 gagne 10 à 0
                    $combat->setScoreCombattant1(10);
                    $combat->setScoreCombattant2(0);
                    $combat->setResultat('combattant1'); // combattant1 gagne
                    break;

                case 1: // combattant2 gagne 10 à 0
                    $combat->setScoreCombattant1(0);
                    $combat->setScoreCombattant2(10);
                    $combat->setResultat('combattant2'); // combattant2 gagne
                    break;

                case 2: // Simuler un score de 10-7 ou 7-10
                    $scoreGagnant = 10;
                    $scorePerdant = 7;
                    $gagnant = rand(0, 1); // 0 pour combattant1, 1 pour combattant2

                    if ($gagnant === 0) {
                        $combat->setScoreCombattant1($scoreGagnant);
                        $combat->setScoreCombattant2($scorePerdant);
                        $combat->setResultat('combattant1'); // combattant1 gagne
                    } else {
                        $combat->setScoreCombattant1($scorePerdant);
                        $combat->setScoreCombattant2($scoreGagnant);
                        $combat->setResultat('combattant2'); // combattant2 gagne
                    }
                    break;
            }

            // Persister le résultat dans la base de données
            $this->em->persist($combat);
        }

        // Sauvegarder tous les résultats simulés dans la base de données
        $this->em->flush();

        // Ajouter un message flash ou rediriger après la simulation
        $this->addFlash('success', 'Les combats ont été simulés avec succès.');

        return $this->redirectToRoute('afficher_combats', [
            'id' => $tournoi->getId(),
        ]);
    }
    
    #[Route('/tournoi/{id}/combats/{idC}/edit', name: 'edit_combat')]
    public function editCombat(int $id, int $idC, Request $request): Response
    {
        $combat = $this->em->getRepository(Combat::class)->find($idC);

        if (!$combat) {
            throw $this->createNotFoundException('Combat non trouvé.');
        }

        $categorieTournoi = $combat->getCategorieTournoi(); // Suppose que Combat a une relation avec CategorieTournoi
        if (!$categorieTournoi) {
            throw $this->createNotFoundException('Catégorie du combat introuvable.');
        }

        $user = $this->getUser(); // Récupérer l'utilisateur connecté

        // Vérifier si l'utilisateur est un arbitre assigné à cette catégorie
        if (!$user instanceof Arbitre || !$categorieTournoi->getArbitres()->contains($user)) {
            throw $this->createAccessDeniedException('Accès refusé. Vous n\'êtes pas arbitre pour cette catégorie.');
        }

        $form = $this->createForm(CombatType::class, $combat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer la logique de mise à jour des résultats
            $scoreCombattant1 = $combat->getScoreCombattant1();
            $scoreCombattant2 = $combat->getScoreCombattant2();

            if ($scoreCombattant1 > $scoreCombattant2) {
                $combat->setResultat('combattant1');
            } elseif ($scoreCombattant2 > $scoreCombattant1) {
                $combat->setResultat('combattant2');
            } else {
                $combat->setResultat('egalite');
            }

            $this->em->flush();

            $this->addFlash('success', 'Le résultat du combat a été mis à jour.');
            return $this->redirectToRoute('afficher_combats', ['id' => $id]);
        }

        return $this->render('combat/edit.html.twig', [
            'combat' => $combat,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/tournoi/{id}/quarts', name: 'creer_quarts')]
    public function creerQuarts(Tournoi $tournoi, CombatService $service, Security $security): Response
    {
        $user = $security->getUser();

          if (!$user || $user !== $tournoi->getPresident()) {
              return $this->redirectToRoute('app_home');
          }

        return $service->creerCombats($tournoi, 'Quart_de_finale');
    }

    #[Route('/tournoi/{id}/demi', name: 'creer_demi_finales')]
    public function creerDemiFinales(Tournoi $tournoi, CombatService $service, Security $security): Response
    {
        $user = $security->getUser();

          if (!$user || $user !== $tournoi->getPresident()) {
              return $this->redirectToRoute('app_home');
          }

        return $service->creerCombats($tournoi, 'Demi_finale', 'Quart_de_finale');
    }

    #[Route('/tournoi/{id}/finale', name: 'creer_finales')]
    public function creerFinales(Tournoi $tournoi, CombatService $service, Security $security): Response
    {
          $user = $security->getUser();

          if (!$user || $user !== $tournoi->getPresident()) {
              return $this->redirectToRoute('app_home');
          }
        return $service->creerCombats($tournoi, 'Finale', 'Demi_finale');
    }


}
