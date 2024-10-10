<?php

namespace App\Controller;

use App\Entity\Combat;
use App\Entity\Groupe;
use App\Entity\Tournoi;
use Doctrine\ORM\EntityManagerInterface;
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
            $this->creerCombatsPourGroupe($groupeCombattants, $groupe, $tournoi);
        }

        $this->em->flush();
    }

    return $groupesParCategorie;
}

    
// Méthode pour créer les combats entre les membres d'un groupe
private function creerCombatsPourGroupe(array $groupe, Groupe $groupes, Tournoi $tournoi): void{
    // Créer les combats round-robin (chaque combattant affronte tous les autres)
    for ($i = 0; $i < count($groupe); $i++) {
        for ($j = $i + 1; $j < count($groupe); $j++) {
            $combat = new Combat();
            $combat->setCombattant1($groupe[$i]);
            $combat->setCombattant2($groupe[$j]);
            $combat->setTournoi($tournoi);
            $combat->setGroupe($groupes);

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
public function creerQuarts(Tournoi $tournoi): Response{

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



    foreach ($groupes as $groupe) {
        // Générer le classement pour chaque groupe
        $classement = $this->genererClassementPourGroupe($groupe);

        // Ajouter les 2 premiers du classement à la liste des qualifiés
        if (count($classement) >= 2) {
            $combattantsQualifies1[] = $classement[0]['combattant']; // Premier du groupe
            $combattantsQualifies2[] = $classement[1]['combattant']; // Deuxième du groupe
        }
    }
    /*
        // S'assurer qu'on a bien 8 combattants qualifiés
        if (count($combattantsQualifies) !== 8) {
            throw new \Exception("Le nombre de qualifiés est incorrect. Attendu : 8, Actuel : " . count($combattantsQualifies));
        }
    */
        // Mélanger les combattants pour les quarts de finale (les premiers rencontrent les deuxièmes)
    shuffle($combattantsQualifies1);
    shuffle($combattantsQualifies2);


    // Créer les combats pour les quarts de finale
    for ($i = 0; $i < 4; $i++) {
        $combat = new Combat();
        $combat->setCombattant1($combattantsQualifies1[$i]); // Premier combattant
        $combat->setCombattant2($combattantsQualifies2[$i]); // Deuxième combattant
        $combat->setTournoi($tournoi);
        $combat->setPhase('Quart_de_finale');
        // Sauvegarder le combat en base de données
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


        $this->em->persist($combat);
    }

    // Sauvegarder tous les combats
    $this->em->flush();

    // Rediriger vers la page des combats
    return $this->render('combat/quart_final.html.twig', [
        'combats' => $combat,
    ]);
}


#[Route('/tournoi/{id}/demi', name: 'creer_demi')]
public function creerDemiFinale(Tournoi $tournoi): Response {

 // Vérifier si une finale existe déjà pour ce tournoi
 $combatDemiFinaleExistant = $this->em->getRepository(Combat::class)->findOneBy([
    'tournoi' => $tournoi,
    'Phase' => 'Demi-finale'
]);

// Si une finale existe déjà, ne pas la régénérer
if ($combatDemiFinaleExistant) {
    return $this->render('combat/quart_final.html.twig', [
        'combats' => $combatDemiFinaleExistant,
    ]);
}


    // Récupérer les combats des quarts de finale
    $combatsQuarts = $this->em->getRepository(Combat::class)->findBy(['tournoi' => $tournoi, 'Phase' => 'Quart_de_finale']);
    
    // Vérifier qu'il y a bien 4 combats pour les quarts de finale
    if (count($combatsQuarts) !== 4) {
        throw new \Exception("Le nombre de combats pour les quarts de finale est incorrect. Attendu : 4, Actuel : " . count($combatsQuarts));
    }

    // Récupérer les gagnants des quarts de finale
    $gagnants = [];
    foreach ($combatsQuarts as $combat) {
        $resultat = $combat->getResultat();
        if ($resultat === 'combattant1') {
            $gagnants[] = $combat->getCombattant1();
        } elseif ($resultat === 'combattant2') {
            $gagnants[] = $combat->getCombattant2();
        }
    }

    // S'assurer qu'on a bien 4 gagnants
    if (count($gagnants) !== 4) {
        throw new \Exception("Le nombre de gagnants est incorrect. Attendu : 4, Actuel : " . count($gagnants));
    }

    // Mélanger les gagnants pour les demi-finales
    shuffle($gagnants);

    // Créer les combats pour les demi-finales
    for ($i = 0; $i < 2; $i++) { // 2 demi-finales
        $combat = new Combat();
        $combat->setCombattant1($gagnants[$i * 2]); // Premier combattant
        $combat->setCombattant2($gagnants[$i * 2 + 1]); // Deuxième combattant
        $combat->setTournoi($tournoi);
        $combat->setPhase('Demi-finale');

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

        // Sauvegarder le combat en base de données
        $this->em->persist($combat);
    }

    // Sauvegarder tous les combats
    $this->em->flush();

    // Rediriger vers la page des combats
    return $this->render('combat/quart_final.html.twig', [
        'combats' => $combat,
    ]);
}

#[Route('/tournoi/{id}/finale', name: 'creer_finale')]
public function creerFinale(Tournoi $tournoi): Response {

    // Vérifier si une finale existe déjà pour ce tournoi
    $combatFinaleExistant = $this->em->getRepository(Combat::class)->findOneBy([
        'tournoi' => $tournoi,
        'Phase' => 'Finale'
    ]);

    // Si une finale existe déjà, ne pas la régénérer
    if ($combatFinaleExistant) {
        return $this->render('combat/quart_final.html.twig', [
            'combats' => $combatFinaleExistant,
        ]);
    }

    // Récupérer les combats des demi-finales
    $combatsDemi = $this->em->getRepository(Combat::class)->findBy(['tournoi' => $tournoi, 'Phase' => 'Demi-finale']);
    
    // Vérifier qu'il y a bien 2 combats pour les demi-finales
    if (count($combatsDemi) !== 2) {
        throw new \Exception("Le nombre de combats pour les demi-finales est incorrect. Attendu : 2, Actuel : " . count($combatsDemi));
    }

    // Récupérer les gagnants des demi-finales
    $gagnants = [];
    foreach ($combatsDemi as $combat) {
        $resultat = $combat->getResultat();
        if ($resultat === 'combattant1') {
            $gagnants[] = $combat->getCombattant1();
        } elseif ($resultat === 'combattant2') {
            $gagnants[] = $combat->getCombattant2();
        }
    }

    // S'assurer qu'on a bien 2 gagnants
    if (count($gagnants) !== 2) {
        throw new \Exception("Le nombre de gagnants est incorrect. Attendu : 2, Actuel : " . count($gagnants));
    }

    // Créer le combat pour la finale
    $combat = new Combat();
    $combat->setCombattant1($gagnants[0]); // Premier finaliste
    $combat->setCombattant2($gagnants[1]); // Deuxième finaliste
    $combat->setTournoi($tournoi);
    $combat->setPhase('Finale');

    // Simuler le résultat du combat de la finale
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

    // Sauvegarder le combat en base de données
    $this->em->persist($combat);
    $this->em->flush();

    // Rediriger vers la page d'affichage de la finale
    return $this->render('combat/quart_final.html.twig', [
        'combats' => $combat,
    ]);
}




    /*
    #[Route('/tournoi/{id}/quart_finale', name: 'creer_phases_finales')]
    public function creerPhasesFinales(Tournoi $tournoi): Response
    {
        // Récupérer les classements de chaque groupe
        $groupes = $tournoi->getGroupes();
    $qualifies = [];

    // Récupérer les 2 premiers de chaque groupe
    foreach ($groupes as $groupe) {
        $classement = $this->genererClassementPourGroupe($groupe);
        $premier = $classement[0]['combattant'];
        $deuxieme = $classement[1]['combattant'];
        $qualifies[] = ['premier' => $premier, 'deuxieme' => $deuxieme];
    }

    // Créer les quarts de finale (un premier contre un deuxième d'un autre groupe)
    $quartsDeFinale = [];
    for ($i = 0; $i < 4; $i++) {
        $combat = new Combat();
        $combat->setCombattant1($qualifies[$i]['premier']);
        $combat->setCombattant2($qualifies[($i + 1) % 4]['deuxieme']);
        $combat->setTournoi($tournoi);
        $this->em->persist($combat);
        $quartsDeFinale[] = $combat;
    }

    $this->em->flush();

    // Passer à la phase demi-finale après les résultats des quarts
    $demiFinalistes = $this->creerDemiFinales($quartsDeFinale, $tournoi);

    // Passer à la finale après les résultats des demi-finales
    $this->creerFinale($demiFinalistes, $tournoi);

    return $this->redirectToRoute('afficher_combats', ['id' => $tournoi->getId()]);
 }

public function creerDemiFinales(array $quartsDeFinale, Tournoi $tournoi): array
{
    $demiFinalistes = [];

    foreach ($quartsDeFinale as $quart) {
        // Simuler le gagnant de chaque quart de finale
        $gagnant = $quart->getScoreCombattant1() > $quart->getScoreCombattant2() 
                    ? $quart->getCombattant1() 
                    : $quart->getCombattant2();
        $demiFinalistes[] = $gagnant;
    }

    // Créer les deux demi-finales
    for ($i = 0; $i < 2; $i++) {
        $combat = new Combat();
        $combat->setCombattant1($demiFinalistes[$i * 2]);
        $combat->setCombattant2($demiFinalistes[$i * 2 + 1]);
        $combat->setTournoi($tournoi);
        $this->em->persist($combat);
    }

    $this->em->flush();
    return $demiFinalistes;
}

public function creerFinale(array $demiFinalistes, Tournoi $tournoi): void
{
    // Simuler les gagnants des demi-finales
    $finalistes = [];
    for ($i = 0; $i < 2; $i++) {
        $combat = $this->em->getRepository(Combat::class)
                           ->findOneBy(['combattant1' => $demiFinalistes[$i], 'tournoi' => $tournoi]);
        
        $gagnant = $combat->getScoreCombattant1() > $combat->getScoreCombattant2() 
                    ? $combat->getCombattant1() 
                    : $combat->getCombattant2();
        $finalistes[] = $gagnant;
    }

    // Créer la finale
    $finale = new Combat();
    $finale->setCombattant1($finalistes[0]);
    $finale->setCombattant2($finalistes[1]);
    $finale->setTournoi($tournoi);
    $this->em->persist($finale);

    $this->em->flush();
}

#[Route('/tournoi/{id}/finales/afficher', name: 'afficher_phases_finales')]
public function afficherPhasesFinales(Tournoi $tournoi): Response
{
    // Récupérer les combats de quarts, demi-finales et finale
    $combats = $this->em->getRepository(Combat::class)->findBy(['tournoi' => $tournoi]);

    return $this->render('combat/finale.html.twig', [
        'tournoi' => $tournoi,
        'combats' => $combats,
    ]);
}
*/






/*
    // Dans votre contrôleur ou service pour gérer la phase de poules
public function creerGroupesParCategorie(Tournoi $tournoi)
{
    $groupes = [];
    
    // Pour chaque catégorie de poids inscrite au tournoi
    foreach ($tournoi->getPoids() as $categorie) {
        // Récupérer les combattants inscrits à cette catégorie
        $combattants = $categorie->getAdherants();

        // Mélanger aléatoirement les combattants pour créer des groupes équilibrés
        $combattantsArray = $combattants->toArray();
        shuffle($combattantsArray);

        // Diviser en groupes de 4 combattants
        $groupesParCategorie = array_chunk($combattantsArray, 4);

        $groupes[$categorie->getCategoriePoids()] = $groupesParCategorie;
    }

    return $groupes;
}

public function genererCombatsPhaseDePoule(Tournoi $tournoi)
{
    // Récupérer les groupes par catégorie
    $groupes = $this->creerGroupesParCategorie($tournoi);

    foreach ($groupes as $categoriePoids => $groupesParCategorie) {
        foreach ($groupesParCategorie as $groupe) {
            // Chaque groupe contient 4 combattants
            // Exemple de génération de combats round-robin pour chaque groupe
            for ($i = 0; $i < count($groupe); $i++) {
                for ($j = $i + 1; $j < count($groupe); $j++) {
                    // Créer un combat entre deux combattants
                    $combat = new Combat();
                    $combat->setCombattant1($groupe[$i]);
                    $combat->setCombattant2($groupe[$j]);
                    $combat->setTournoi($tournoi);

                    // Enregistrer le combat dans la base de données
                    $this->em->persist($combat);
                }
            }
        }
    }

    // Sauvegarder les combats générés
    $this->em->flush();
}

*/
 
    
    /*
    public function creerGroupes(array $combattants): array
    {
        $groupes = [];
        
        // Étape 1: Classer les combattants par catégorie de poids
        $combattantsParPoids = [];
        foreach ($combattants as $combattant) {
            $poids = $combattant->getPoids(); // Supposons que chaque combattant a une méthode getPoids()
            $combattantsParPoids[$poids][] = $combattant;
        }
    
        // Étape 2: Mélanger les combattants dans chaque catégorie
        foreach ($combattantsParPoids as $categorie => $combattantsDansCategorie) {
            shuffle($combattantsDansCategorie); // Mélange aléatoire
            $combattantsParPoids[$categorie] = $combattantsDansCategorie;
        }
    
        // Étape 3: Créer les groupes
        // Initialiser les groupes
        for ($i = 0; $i < 4; $i++) {
            $groupes[$i] = [];
        }
    
        // Répartir les combattants dans les groupes
        $indexGroupe = 0;
        foreach ($combattantsParPoids as $combattantsDansCategorie) {
            foreach ($combattantsDansCategorie as $combattant) {
                if (count($groupes[$indexGroupe]) < 4) {
                    $groupes[$indexGroupe][] = $combattant; // Ajouter le combattant au groupe actuel
                }
    
                // Passer au groupe suivant si le groupe actuel a 4 combattants
                if (count($groupes[$indexGroupe]) == 4) {
                    $indexGroupe++;
                    if ($indexGroupe >= 4) {
                        break; // Arrêter si nous avons déjà 4 groupes
                    }
                }
            }
            // Arrêter la boucle si tous les groupes sont remplis
            if ($indexGroupe >= 4) {
                break;
            }
        }
    
        return $groupes;
    }
    #[Route('/tournoi/groupe', name: 'tournoi_groupe')]
    public function afficherGroupes(array $combattants): Response
    {
        $groupes = $this->creerGroupes($combattants); // Appel de la méthode pour créer les groupes

        // Passer les groupes à la vue
        return $this->render('combat/groupe.html.twig', [
            'groupes' => $groupes,
        ]);
    }  
    public function gererCombats(array $groupes): void
{
    foreach ($groupes as $groupe) {
        for ($i = 0; $i < count($groupe); $i++) {
            for ($j = $i + 1; $j < count($groupe); $j++) {
                $this->lancerCombat($groupe[$i], $groupe[$j]);
            }
        }
    }
}
private function lancerCombat(Adherant $combattant1, Adherant $combattant2): void
{
    // Logique pour simuler le combat
    $score1 = rand(0, 10); // Gagne de 0 à 10
    $score2 = rand(0, 10); // Gagne de 0 à 10

    $combat = new Combat();
    $combat->setCombattant1($combattant1);
    $combat->setCombattant2($combattant2);
    $combat->setScoreCombattant1($score1);
    $combat->setScoreCombattant2($score2);
    
    // Déterminer le résultat
    if ($score1 > $score2) {
        $combat->setResultat('combattant1');
    } elseif ($score2 > $score1) {
        $combat->setResultat('combattant2');
    } else {
        $combat->setResultat('égalité');
    }

    
     $this->em->persist($combat);
    $this->em->flush();
}
*/
    
}
