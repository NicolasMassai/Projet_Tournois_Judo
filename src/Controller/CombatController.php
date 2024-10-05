<?php

namespace App\Controller;

use App\Entity\Combat;
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
        // Récupérer les groupes pour ce tournoi
        $groupes = $this->creerGroupesParCategorie($tournoi);

        // Rendre le template Twig et passer les groupes
        return $this->render('combat/groupes.html.twig', [
            'tournoi' => $tournoi,
            'groupes' => $groupes,
        ]);
    }

    #[Route('/tournoi/{id}/combats', name: 'afficher_combats')]
    public function afficherCombats(Tournoi $tournoi): Response
    {
        // Récupérer les combats pour ce tournoi
        $combats = $this->genererCombatsPhaseDePoule($tournoi);

        // Rendre le template Twig et passer les combats
        return $this->render('combat/combats.html.twig', [
            'tournoi' => $tournoi,
            'combats' => $combats,
        ]);
    }

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
