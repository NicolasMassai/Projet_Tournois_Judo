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
            $this->creerCombatsPourGroupe($groupeCombattants, $tournoi);
        }

        $this->em->flush();
    }

    return $groupesParCategorie;
}

    
// Méthode pour créer les combats entre les membres d'un groupe
private function creerCombatsPourGroupe(array $groupe, Tournoi $tournoi): void
{
    // Créer les combats round-robin (chaque combattant affronte tous les autres)
    for ($i = 0; $i < count($groupe); $i++) {
        for ($j = $i + 1; $j < count($groupe); $j++) {
            $combat = new Combat();
            $combat->setCombattant1($groupe[$i]);
            $combat->setCombattant2($groupe[$j]);
            $combat->setTournoi($tournoi);

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
