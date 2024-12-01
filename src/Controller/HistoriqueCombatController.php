<?php

namespace App\Controller;

use App\Entity\Adherant;
use App\Entity\Categorie;
use App\Repository\CombatRepository;
use App\Repository\AdherantRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HistoriqueCombatController extends AbstractController
{
    #[Route('/statistiques', name: 'statistiques_combattant')]
    public function statistiquesCombattant(Security $security, CombatRepository $combatRepository): Response
    {
        $adherant = $security->getUser();

        if (!$adherant instanceof Adherant) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos statistiques.');
        }
        
        $combats = $combatRepository->findByCombattant($adherant);

        $totalCombats = count($combats);
        $victoires = array_filter($combats, fn($combat) => $combat->Vainqueur($adherant));
        $defaites = $totalCombats - count($victoires);

        $finales = array_filter($combats, fn($combat) => $combat->getPhase() === 'Finale');

        $victoiresFinale = array_filter($finales, fn($combat) => $combat->Vainqueur($adherant));


        return $this->render('historique_combat/statistiques.html.twig', [
            'adherant' => $adherant,
            'totalCombats' => $totalCombats,
            'victoires' => count($victoires),
            'defaites' => $defaites,
            'victoiresFinale' => count($victoiresFinale),
        ]);
    }

    #[Route('/classement/categorie/', name: 'classement')]
    public function classementCategorieALL(CategorieRepository $categorieRepository): Response
    {
        $categorie = $categorieRepository->findAll();


        return $this->render('historique_combat/classement.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/classement/categorie/{id}', name: 'classement_categorie')]
    public function classementCategorie(Categorie $categorie): Response
    {
        $adherants = $categorie->getAdherant();

        $classement = [];
        foreach ($adherants as $adherant) {
            // On récupère tous les combats de l'adhérant
            $combats = array_merge(
                $adherant->getCombattant1()->toArray(),
                $adherant->getCombattant2()->toArray()
            );

            // Filtrer les combats gagnés
            $victoires = array_filter($combats, fn($combat) => $combat->Vainqueur($adherant));

            $victoiresFinale = array_filter($victoires, fn($combat) => $combat->getPhase() === 'Finale');

            $classement[] = [
                'adherant' => $adherant,
                'victoires' => count($victoires),
                'victoires_finale' => count($victoiresFinale),
            ];
        }

        // Tri par nombre de victoires
       // usort($classement, fn($a, $b) => $b['victoires'] <=> $a['victoires']);
        usort($classement, function ($a, $b) {
            // Priorité 1 : Comparer sur les victoires en finale
            if ($a['victoires_finale'] > 0 && $b['victoires_finale'] === 0) {
                return -1; // $a est prioritaire
            }
            if ($b['victoires_finale'] > 0 && $a['victoires_finale'] === 0) {
                return 1; // $b est prioritaire
            }
            
            // Priorité 2 : Comparer sur le nombre total de victoires
            if ($b['victoires'] !== $a['victoires']) {
                return $b['victoires'] <=> $a['victoires'];
            }
        
            // Priorité 3 : Aucun critère supplémentaire, on considère une égalité
            return 0;
        });
    

        return $this->render('historique_combat/classement_categorie.html.twig', [
            'categorie' => $categorie,
            'classement' => $classement,
        ]);
    }

}
