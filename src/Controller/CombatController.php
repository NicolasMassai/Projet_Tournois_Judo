<?php

namespace App\Controller;

use App\Entity\Combat;
use App\Entity\Groupe;
use App\Entity\Tournoi;
use App\Form\CombatType;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CombatController extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/tournoi/{id}/groupes', name: 'afficher_groupes')]
    public function afficherGroupes(Tournoi $tournoi): Response
    {
        $groupes = $this->creerGroupesParCategorie($tournoi);

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

    #[Route('/tournoi/{id}/classement', name: 'afficher_classement')]
public function afficherClassement(Tournoi $tournoi): Response
{
    $classements = [];

    // Parcourir tous les groupes du tournoi
    foreach ($tournoi->getGroupes() as $groupe) {
        // Générer le classement pour chaque groupe
        $classements[$groupe->getNom()] = $this->genererClassementPourGroupe($groupe);
    }

    return $this->render('combat/classement.html.twig', [
        'tournoi' => $tournoi,
        'classements' => $classements,
    ]);
}    
    public function creerGroupesParCategorie(Tournoi $tournoi): array
{
    // Vérifier si les groupes ont déjà été générés
    if ($tournoi->getGroupes()->count() > 0) {
        return []; // Si oui, on ne régénère pas les groupes
    }

    // Tableau pour stocker les groupes de toutes les catégories
    $groupesParCategorie = [];
    
    // Récupérer toutes les catégories de poids du tournoi
    $categories = $tournoi->getPoids();

    foreach ($categories as $categorie) {
        // Récupérer les combattants de la catégorie
        $combattants = $categorie->getAdherants();

        // Filtrer les combattants inscrits dans le tournoi
        $combattantsTournoi = $combattants->filter(function($combattant) use ($tournoi) {
            return $combattant->getTournois()->contains($tournoi);
        });

        // Si la catégorie a moins de 16 combattants, elle ne participe pas
        if (count($combattantsTournoi) < 16) {
            continue;
        }

        // Mélanger les combattants pour une sélection aléatoire
        $combattantsTournoi = $combattantsTournoi instanceof \Doctrine\Common\Collections\Collection
            ? $combattantsTournoi->toArray()
            : $combattantsTournoi;
        shuffle($combattantsTournoi);

        // Prendre les 16 premiers combattants
        $combattantsTournoi = array_slice($combattantsTournoi, 0, 16);

        // Créer 4 groupes de 4 combattants
        $groupes = array_chunk($combattantsTournoi, 4);

        // Enregistrer les groupes en base de données
        foreach ($groupes as $index => $groupeCombattants) {
            $groupe = new Groupe();
            $groupe->setNom("Groupe " . ($index + 1) . " - Catégorie " . $categorie->getCategoriePoids() . " kg");
            $groupe->setTournoi($tournoi);
            $groupe->setCategorie($categorie);

            foreach ($groupeCombattants as $combattant) {
                $groupe->addCombattant($combattant);
            }

            $this->em->persist($groupe);
            $tournoi->addGroupe($groupe);

            // Créer les combats pour ce groupe
            //$this->creerCombatsPourGroupe($groupeCombattants, $groupe, $tournoi,$categorie);
            $this->creerCombatsPourGroupeManuel($groupeCombattants, $groupe, $tournoi,$categorie);
        }

        $this->em->flush();
    }

    return $groupesParCategorie;
}

    
// Méthode pour créer les combats entre les membres d'un groupe
private function creerCombatsPourGroupe(array $groupe, Groupe $groupes, Tournoi $tournoi, Categorie $categorie): void{
    // Créer les combats round-robin (chaque combattant affronte tous les autres)
    for ($i = 0; $i < count($groupe); $i++) {
        for ($j = $i + 1; $j < count($groupe); $j++) {
            $combat = new Combat();
            $combat->setCombattant1($groupe[$i]);
            $combat->setCombattant2($groupe[$j]);
            $combat->setTournoi($tournoi);
            $combat->setGroupe($groupes);
            $combat->setPhase('Phase_de_Poule');
            $combat->setCategorie($categorie);

            // Simuler le résultat du combat
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

            // Enregistrer le combat (Doctrine ou autre)
            $this->em->persist($combat);
        }
    }

    // Sauvegarder tous les combats
    $this->em->flush();
}

private function creerCombatsPourGroupeManuel(array $groupe, Groupe $groupes, Tournoi $tournoi, Categorie $categorie): void{
    // Créer les combats round-robin (chaque combattant affronte tous les autres)
    for ($i = 0; $i < count($groupe); $i++) {
        for ($j = $i + 1; $j < count($groupe); $j++) {
            $combat = new Combat();
            $combat->setCombattant1($groupe[$i]);
            $combat->setCombattant2($groupe[$j]);
            $combat->setTournoi($tournoi);
            $combat->setGroupe($groupes);
            $combat->setPhase('Phase_de_Poule');
            $combat->setCategorie($categorie);

            // Pas de simulation de résultat ici, on enregistre juste le combat
            $this->em->persist($combat);
        }
    }

    // Sauvegarder tous les combats (sans résultats pour l'instant)
    $this->em->flush();
}


#[Route('/tournoi/{id}/combats/{idC}/edit', name: 'edit_combat')]
public function editCombat(int $id, int $idC, Request $request): Response
{

        $combat = $this->em->getRepository(Combat::class)->find($idC);

        if (!$combat) {
            throw $this->createNotFoundException('Combat non trouvé.');
        }  

        $tournoi = $combat->getTournoi();

        $form = $this->createForm(CombatType::class, $combat);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer la logique de mise à jour du gagnant en fonction des scores
            $scoreCombattant1 = $combat->getScoreCombattant1();
            $scoreCombattant2 = $combat->getScoreCombattant2();

            if ($scoreCombattant1 > $scoreCombattant2) {
                $combat->setResultat('combattant1');
            } elseif ($scoreCombattant2 > $scoreCombattant1) {
                $combat->setResultat('combattant2');
            } else {
                $combat->setResultat('egalite');
            }
            
            // Sauvegarder les résultats dans la base de données
            $this->em->flush();

            // Message flash ou redirection après sauvegarde
            $this->addFlash('success', 'Le résultat du combat a été mis à jour.');
                return $this->redirectToRoute('afficher_combats', [
                'id' => $tournoi->getId(),
                ]);
        }

        return $this->render('combat/edit.html.twig', [
            'combat' => $combat,
            'form' => $form->createView(),
        ]);
}


private function genererClassementPourGroupe(Groupe $groupe): array
{
    // Tableau pour stocker les points et scores de chaque combattant
    $classement = [];

    // Initialiser le tableau de classement avec les combattants du groupe
    foreach ($groupe->getCombattants() as $combattant) {
        $classement[$combattant->getId()] = [
            'combattant' => $combattant,
            'points' => 0,
            'combats_gagnes' => 0,
            'combats_perdus' => 0,
            'score_total' => 0, // Score total des combats
        ];
    }

    // Récupérer les combats du groupe
    $combats = $this->em->getRepository(Combat::class)->findBy(['groupe' => $groupe]);

    // Parcourir tous les combats pour mettre à jour les points et scores
    foreach ($combats as $combat) {
        $combattant1 = $combat->getCombattant1();
        $combattant2 = $combat->getCombattant2();

        // Mettre à jour le score total des deux combattants
        $classement[$combattant1->getId()]['score_total'] += $combat->getScoreCombattant1();
        $classement[$combattant2->getId()]['score_total'] += $combat->getScoreCombattant2();

        // Si combattant1 gagne
        if ($combat->getScoreCombattant1() > $combat->getScoreCombattant2()) {
            $classement[$combattant1->getId()]['points'] += 1;
            $classement[$combattant1->getId()]['combats_gagnes'] += 1;
            $classement[$combattant2->getId()]['combats_perdus'] += 1;
        }
        // Si combattant2 gagne
        elseif ($combat->getScoreCombattant2() > $combat->getScoreCombattant1()) {
            $classement[$combattant2->getId()]['points'] += 1;
            $classement[$combattant2->getId()]['combats_gagnes'] += 1;
            $classement[$combattant1->getId()]['combats_perdus'] += 1;
        }
    }

    // Classer les combattants d'abord par points (victoires), puis par score total en cas d'égalité
    usort($classement, function ($a, $b) {
        // Comparer par points (victoires)
        if ($b['points'] !== $a['points']) {
            return $b['points'] <=> $a['points'];
        }
        // Si les points sont égaux, comparer par score total
        return $b['score_total'] <=> $a['score_total'];
    });

    return $classement;
}

#[Route('/tournoi/{id}/quarts', name: 'creer_quarts')]
public function creerQuarts(Tournoi $tournoi): Response
{
    // Vérifier si des combats de quart de finale existent déjà pour ce tournoi
    $combatsQuartFinaleExistants = $this->em->getRepository(Combat::class)->findBy([
        'tournoi' => $tournoi,
        'Phase' => 'Quart_de_finale'
    ]);

    // Si des combats existent déjà, les afficher
    if ($combatsQuartFinaleExistants) {
        return $this->render('combat/quart_final.html.twig', [
            'combats' => $combatsQuartFinaleExistants,
        ]);
    }

    // Récupérer tous les groupes du tournoi
    $groupes = $tournoi->getGroupes();
    $combattantsParCategorie = [];

    // Parcourir les groupes pour générer le classement et les qualifiés par catégorie
    foreach ($groupes as $groupe) {
        $classement = $this->genererClassementPourGroupe($groupe);

        // Ajouter les qualifiés (les deux premiers) à la liste des qualifiés par catégorie
        if (count($classement) >= 2) {
            $categorie = $groupe->getCategorie();
            $combattantsParCategorie[$categorie->getId()][] = $classement[0]['combattant']; // Premier
            $combattantsParCategorie[$categorie->getId()][] = $classement[1]['combattant']; // Deuxième
        }
    }

    // Créer les combats pour chaque catégorie
    foreach ($combattantsParCategorie as $categorieId => $combattantsQualifies) {
        // Séparer les qualifiés en deux groupes : les premiers et les deuxièmes
        $combattantsQualifies1 = [];
        $combattantsQualifies2 = [];

        // Parcourir les qualifiés et les classer en fonction de leur rang (1er ou 2ème)
        for ($i = 0; $i < count($combattantsQualifies); $i++) {
            if ($i % 2 === 0) {
                // Index pair => premier
                $combattantsQualifies1[] = $combattantsQualifies[$i];
            } else {
                // Index impair => deuxième
                $combattantsQualifies2[] = $combattantsQualifies[$i];
            }
        }

        // Créer les combats de quart de finale
        for ($i = 0; $i < count($combattantsQualifies1); $i++) {
            $combat = new Combat();
            $combat->setCombattant1($combattantsQualifies1[$i]);
            $combat->setCombattant2($combattantsQualifies2[$i]);
            $combat->setTournoi($tournoi);
            $combat->setPhase('Quart_de_finale');
            $combat->setCategorie($combattantsQualifies1[$i]->getCategorie());

            // Ajouter le combat à la base de données sans générer de résultats
            $this->em->persist($combat);
        }
    }

    // Sauvegarder tous les combats
    $this->em->flush();

    // Rediriger vers la page des quarts de finale
    return $this->redirectToRoute('app_home'); // ou toute autre route que vous utilisez
}

#[Route('/tournoi/{id}/demi', name: 'creer_demi_finales')]
public function creerDemiFinales(Tournoi $tournoi): Response
{
    // Vérifier si des combats de demi-finale existent déjà pour ce tournoi
    $combatsDemiFinaleExistants = $this->em->getRepository(Combat::class)->findBy([
        'tournoi' => $tournoi,
        'Phase' => 'Demi_finale'
    ]);

    // Si des combats existent déjà, les afficher
    if ($combatsDemiFinaleExistants) {
        return $this->render('combat/quart_final.html.twig', [
            'combats' => $combatsDemiFinaleExistants,
        ]);
    }

    // Récupérer les gagnants des quarts de finale
    $gagnantsQuart = $this->em->getRepository(Combat::class)->findBy([
        'tournoi' => $tournoi,
        'Phase' => 'Quart_de_finale',
        'resultat' => ['combattant1', 'combattant2'] // On ne garde que les combats qui ont un gagnant
    ]);

    // Organiser les gagnants par catégorie
    $combattantsParCategorie = [];
    foreach ($gagnantsQuart as $combat) {
        if ($combat->getResultat() === 'combattant1') {
            $combattantsParCategorie[$combat->getCategorie()->getId()][] = $combat->getCombattant1();
        } elseif ($combat->getResultat() === 'combattant2') {
            $combattantsParCategorie[$combat->getCategorie()->getId()][] = $combat->getCombattant2();
        }
    }

    // Créer les combats de demi-finale pour chaque catégorie
    foreach ($combattantsParCategorie as $categorieId => $combattantsQualifies) {
        // Séparer les qualifiés en deux groupes : les premiers et les deuxièmes
        $combattantsQualifies1 = [];
        $combattantsQualifies2 = [];

        // Parcourir les qualifiés et les classer en fonction de leur rang (1er ou 2ème)
        for ($i = 0; $i < count($combattantsQualifies); $i++) {
            if ($i % 2 === 0) {
                // Index pair => premier
                $combattantsQualifies1[] = $combattantsQualifies[$i];
            } else {
                // Index impair => deuxième
                $combattantsQualifies2[] = $combattantsQualifies[$i];
            }
        }

        // Créer les combats de demi-finale
        for ($i = 0; $i < count($combattantsQualifies1); $i++) { // Deux combats de demi-finale
            $combat = new Combat();
            $combat->setCombattant1($combattantsQualifies1[$i]);
            $combat->setCombattant2($combattantsQualifies2[$i]);
            $combat->setTournoi($tournoi);
            $combat->setPhase('Demi_finale');
            $combat->setCategorie($combattantsQualifies1[$i]->getCategorie());

            // Ajouter le combat à la base de données sans générer de résultats
            $this->em->persist($combat);
        }
    }

    // Sauvegarder tous les combats
    $this->em->flush();

    // Rediriger vers la page des demi-finales
    return $this->redirectToRoute('app_home'); // ou toute autre route que vous utilisez
}

#[Route('/tournoi/{id}/finale', name: 'creer_finales')]
public function creerFinales(Tournoi $tournoi): Response
{
    // Vérifier si des combats de finale existent déjà pour ce tournoi
    $combatFinaleExistant = $this->em->getRepository(Combat::class)->findOneBy([
        'tournoi' => $tournoi,
        'Phase' => 'Finale'
    ]);

    // Si des combats existent déjà, les afficher
    if ($combatFinaleExistant) {
        return $this->render('combat/final.html.twig', [
            'combats' => $combatFinaleExistant,
        ]);
    }

    // Récupérer les gagnants des demi-finales
    $gagnantsDemiFinale = $this->em->getRepository(Combat::class)->findBy([
        'tournoi' => $tournoi,
        'Phase' => 'Demi_finale',
        'resultat' => ['combattant1', 'combattant2'] // On ne garde que les combats qui ont un gagnant
    ]);

    // Organiser les gagnants par catégorie
    $combattantsParCategorie = [];
    foreach ($gagnantsDemiFinale as $combat) {
        if ($combat->getResultat() === 'combattant1') {
            $combattantsParCategorie[$combat->getCategorie()->getId()][] = $combat->getCombattant1();
        } elseif ($combat->getResultat() === 'combattant2') {
            $combattantsParCategorie[$combat->getCategorie()->getId()][] = $combat->getCombattant2();
        }
    }

    // Créer les combats de finale pour chaque catégorie
    foreach ($combattantsParCategorie as $categorieId => $combattantsQualifies) {
        // On s'assure qu'il n'y a que deux qualifiés pour la finale
        if (count($combattantsQualifies) == 2) {
            $combat = new Combat();
            $combat->setCombattant1($combattantsQualifies[0]);
            $combat->setCombattant2($combattantsQualifies[1]);
            $combat->setTournoi($tournoi);
            $combat->setPhase('Finale');
            $combat->setCategorie($combattantsQualifies[0]->getCategorie());

            // Ajouter le combat à la base de données sans générer de résultats
            $this->em->persist($combat);
        }
    }

    // Sauvegarder tous les combats
    $this->em->flush();

    // Rediriger vers la page de la finale
    return $this->redirectToRoute('app_home'); // ou toute autre route que vous utilisez
}



/*
#[Route('/tournoi/{id}/quarts', name: 'creer_quarts')]
public function creerQuarts(Tournoi $tournoi): Response
{
    // Vérifier si une finale existe déjà pour ce tournoi
    $combatQuartFinaleExistant = $this->em->getRepository(Combat::class)->findOneBy([
        'tournoi' => $tournoi,
        'Phase' => 'Quart_de_finale'
    ]);

    // Si une finale existe déjà, ne pas la régénérer
    if ($combatQuartFinaleExistant) {
        return $this->render('combat/quart_final.html.twig', [
            'combats' => $combatQuartFinaleExistant,
        ]);
    }

    // Récupérer tous les groupes du tournoi
    $groupes = $tournoi->getGroupes();
    $combattantsParCategorie = [];

    // Parcourir les groupes pour générer le classement et les qualifiés par catégorie
    foreach ($groupes as $groupe) {
        $classement = $this->genererClassementPourGroupe($groupe);

        // Ajouter les qualifiés (les deux premiers) à la liste des qualifiés par catégorie
        if (count($classement) >= 2) {
            $categorie = $groupe->getCategorie();
            $combattantsParCategorie[$categorie->getId()][] = $classement[0]['combattant']; // Premier
            $combattantsParCategorie[$categorie->getId()][] = $classement[1]['combattant']; // Deuxième
        }
    }

    // Créer les combats pour chaque catégorie
    foreach ($combattantsParCategorie as $categorieId => $combattantsQualifies) {
       

        // Séparer les qualifiés en deux groupes : les premiers et les deuxièmes
        $combattantsQualifies1 = [];
        $combattantsQualifies2 = [];

        // Parcourir les qualifiés et les classer en fonction de leur rang (1er ou 2ème)
        for ($i = 0; $i < count($combattantsQualifies); $i++) {
            if ($i % 2 === 0) {
                // Index pair => premier
                $combattantsQualifies1[] = $combattantsQualifies[$i];
            } else {
                // Index impair => deuxième
                $combattantsQualifies2[] = $combattantsQualifies[$i];
            }
        }

        // Mélanger les combattants dans chaque groupe
        shuffle($combattantsQualifies1);
        shuffle($combattantsQualifies2);

        // Créer les combats de quart de finale
        for ($i = 0; $i < count($combattantsQualifies1); $i++) {
            $combat = new Combat();
            $combat->setCombattant1($combattantsQualifies1[$i]);
            $combat->setCombattant2($combattantsQualifies2[$i]);
            $combat->setTournoi($tournoi);
            $combat->setPhase('Quart_de_finale');
            $combat->setCategorie($combattantsQualifies1[$i]->getCategorie());

            // Générer un résultat aléatoire pour le combat
            $resultat = rand(0, 2); // 0: combattant1 gagne, 1: combattant2 gagne, 2: combat serré
            switch ($resultat) {
                case 0:
                    $combat->setScoreCombattant1(10);
                    $combat->setScoreCombattant2(0);
                    $combat->setResultat('combattant1');
                    break;
                case 1:
                    $combat->setScoreCombattant1(0);
                    $combat->setScoreCombattant2(10);
                    $combat->setResultat('combattant2');
                    break;
                case 2:
                    $scoreGagnant = 10;
                    $scorePerdant = 7;
                    $gagnant = rand(0, 1);
                    if ($gagnant === 0) {
                        $combat->setScoreCombattant1($scoreGagnant);
                        $combat->setScoreCombattant2($scorePerdant);
                        $combat->setResultat('combattant1');
                    } else {
                        $combat->setScoreCombattant1($scorePerdant);
                        $combat->setScoreCombattant2($scoreGagnant);
                        $combat->setResultat('combattant2');
                    }
                    break;
            }

            $this->em->persist($combat);
        }
    }

    // Sauvegarder tous les combats
    $this->em->flush();

    // Rediriger vers la page des quarts de finale
    return $this->render('combat/quart_final.html.twig', [
        'combats' => $combat,
    ]);
}
*/


/*
#[Route('/tournoi/{id}/demi', name: 'creer_demi_finales')]
public function creerDemiFinales(Tournoi $tournoi): Response
{
    // Vérifier si des demi-finales existent déjà pour ce tournoi
    $combatDemiFinaleExistant = $this->em->getRepository(Combat::class)->findOneBy([
        'tournoi' => $tournoi,
        'Phase' => 'Demi_finale'
    ]);

    // Si des demi-finales existent déjà, ne pas les régénérer
    if ($combatDemiFinaleExistant) {
        return $this->render('combat/quart_final.html.twig', [
            'combats' => $combatDemiFinaleExistant,
        ]);
    }

    // Récupérer les gagnants des quarts de finale
    $gagnantsQuart = $this->em->getRepository(Combat::class)->findBy([
        'tournoi' => $tournoi,
        'Phase' => 'Quart_de_finale',
        'resultat' => ['combattant1', 'combattant2'] // On ne garde que les combats qui ont un gagnant
    ]);

    // Organiser les gagnants par catégorie
    $combattantsParCategorie = [];
    foreach ($gagnantsQuart as $combat) {
        if ($combat->getResultat() === 'combattant1') {
            $combattantsParCategorie[$combat->getCategorie()->getId()][] = $combat->getCombattant1();
        } elseif ($combat->getResultat() === 'combattant2') {
            $combattantsParCategorie[$combat->getCategorie()->getId()][] = $combat->getCombattant2();
        }
    }

    // Créer les combats de demi-finale pour chaque catégorie
    foreach ($combattantsParCategorie as $categorieId => $combattantsQualifies) {
     
       
        // Séparer les qualifiés en deux groupes : les premiers et les deuxièmes
        $combattantsQualifies1 = [];
        $combattantsQualifies2 = [];

        // Parcourir les qualifiés et les classer en fonction de leur rang (1er ou 2ème)
        for ($i = 0; $i < count($combattantsQualifies); $i++) {
            if ($i % 2 === 0) {
                // Index pair => premier
                $combattantsQualifies1[] = $combattantsQualifies[$i];
            } else {
                // Index impair => deuxième
                $combattantsQualifies2[] = $combattantsQualifies[$i];
            }
        }

        // Mélanger les combattants dans chaque groupe
        shuffle($combattantsQualifies1);
        shuffle($combattantsQualifies2);

        // Créer les combats de demi-finale
        for ($i = 0; $i < count($combattantsQualifies1); $i++) { // Deux combats de demi-finale
            $combat = new Combat();
            $combat->setCombattant1($combattantsQualifies1[$i]);
            $combat->setCombattant2($combattantsQualifies2[$i]);
            $combat->setTournoi($tournoi);
            $combat->setPhase('Demi_finale');
            $combat->setCategorie($combattantsQualifies1[$i]->getCategorie());

            // Générer un résultat aléatoire pour le combat
            $resultat = rand(0, 2); // 0: combattant1 gagne, 1: combattant2 gagne, 2: combat serré
            switch ($resultat) {
                case 0:
                    $combat->setScoreCombattant1(10);
                    $combat->setScoreCombattant2(0);
                    $combat->setResultat('combattant1');
                    break;
                case 1:
                    $combat->setScoreCombattant1(0);
                    $combat->setScoreCombattant2(10);
                    $combat->setResultat('combattant2');
                    break;
                case 2:
                    $scoreGagnant = 10;
                    $scorePerdant = 7;
                    $gagnant = rand(0, 1);
                    if ($gagnant === 0) {
                        $combat->setScoreCombattant1($scoreGagnant);
                        $combat->setScoreCombattant2($scorePerdant);
                        $combat->setResultat('combattant1');
                    } else {
                        $combat->setScoreCombattant1($scorePerdant);
                        $combat->setScoreCombattant2($scoreGagnant);
                        $combat->setResultat('combattant2');
                    }
                    break;
            }

            $this->em->persist($combat);
        }
    }

    // Sauvegarder tous les combats
    $this->em->flush();

    // Rediriger vers la page des demi-finales
    return $this->render('combat/quart_final.html.twig', [
        'combats' => $combatDemiFinaleExistant,
    ]);
}*/

/*
#[Route('/tournoi/{id}/finale', name: 'creer_finales')]
public function creerFinales(Tournoi $tournoi): Response
{
    // Vérifier si des demi-finales existent déjà pour ce tournoi
    $combatDemiFinaleExistant = $this->em->getRepository(Combat::class)->findOneBy([
        'tournoi' => $tournoi,
        'Phase' => 'Finale'
    ]);

    // Si des demi-finales existent déjà, ne pas les régénérer
    if ($combatDemiFinaleExistant) {
        return $this->render('combat/final.html.twig', [
            'combats' => $combatDemiFinaleExistant,
        ]);
    }

    // Récupérer les gagnants des quarts de finale
    $gagnantsQuart = $this->em->getRepository(Combat::class)->findBy([
        'tournoi' => $tournoi,
        'Phase' => 'Demi_finale',
        'resultat' => ['combattant1', 'combattant2'] // On ne garde que les combats qui ont un gagnant
    ]);

    // Organiser les gagnants par catégorie
    $combattantsParCategorie = [];
    foreach ($gagnantsQuart as $combat) {
        if ($combat->getResultat() === 'combattant1') {
            $combattantsParCategorie[$combat->getCategorie()->getId()][] = $combat->getCombattant1();
        } elseif ($combat->getResultat() === 'combattant2') {
            $combattantsParCategorie[$combat->getCategorie()->getId()][] = $combat->getCombattant2();
        }
    }

    // Créer les combats de demi-finale pour chaque catégorie
    foreach ($combattantsParCategorie as $categorieId => $combattantsQualifies) {
     
       
        // Séparer les qualifiés en deux groupes : les premiers et les deuxièmes
        $combattantsQualifies1 = [];
        $combattantsQualifies2 = [];

        // Parcourir les qualifiés et les classer en fonction de leur rang (1er ou 2ème)
        for ($i = 0; $i < count($combattantsQualifies); $i++) {
            if ($i % 2 === 0) {
                // Index pair => premier
                $combattantsQualifies1[] = $combattantsQualifies[$i];
            } else {
                // Index impair => deuxième
                $combattantsQualifies2[] = $combattantsQualifies[$i];
            }
        }

        // Mélanger les combattants dans chaque groupe
        shuffle($combattantsQualifies1);
        shuffle($combattantsQualifies2);

        // Créer les combats de demi-finale
        for ($i = 0; $i < count($combattantsQualifies1); $i++) { // Deux combats de demi-finale
            $combat = new Combat();
            $combat->setCombattant1($combattantsQualifies1[$i]);
            $combat->setCombattant2($combattantsQualifies2[$i]);
            $combat->setTournoi($tournoi);
            $combat->setPhase('Finale');
            $combat->setCategorie($combattantsQualifies1[$i]->getCategorie());

            // Générer un résultat aléatoire pour le combat
            $resultat = rand(0, 2); // 0: combattant1 gagne, 1: combattant2 gagne, 2: combat serré
            switch ($resultat) {
                case 0:
                    $combat->setScoreCombattant1(10);
                    $combat->setScoreCombattant2(0);
                    $combat->setResultat('combattant1');
                    break;
                case 1:
                    $combat->setScoreCombattant1(0);
                    $combat->setScoreCombattant2(10);
                    $combat->setResultat('combattant2');
                    break;
                case 2:
                    $scoreGagnant = 10;
                    $scorePerdant = 7;
                    $gagnant = rand(0, 1);
                    if ($gagnant === 0) {
                        $combat->setScoreCombattant1($scoreGagnant);
                        $combat->setScoreCombattant2($scorePerdant);
                        $combat->setResultat('combattant1');
                    } else {
                        $combat->setScoreCombattant1($scorePerdant);
                        $combat->setScoreCombattant2($scoreGagnant);
                        $combat->setResultat('combattant2');
                    }
                    break;
            }

            $this->em->persist($combat);
        }
    }

    // Sauvegarder tous les combats
    $this->em->flush();

    $combats = $this->em->getRepository(Combat::class)->findBy(['tournoi' => $tournoi]);


    // Rediriger vers la page des demi-finales
    return $this->render('combat/final.html.twig', [
        'combats' => $combats,
    ]);
}*/


}
