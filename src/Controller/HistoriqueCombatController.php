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


        return $this->render('historique_combat/statistiques.html.twig', [
            'adherant' => $adherant,
            'totalCombats' => $totalCombats,
            'victoires' => count($victoires),
            'defaites' => $defaites,
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
        $adherants = $categorie->getAdherants();

        $classement = [];
        foreach ($adherants as $adherant) {
            // On récupère tous les combats de l'adhérant
            $combats = array_merge(
                $adherant->getCombattant1()->toArray(),
                $adherant->getCombattant2()->toArray()
            );

            // Filtrer les combats gagnés
            $victoires = array_filter($combats, fn($combat) => $combat->Vainqueur($adherant));

            $classement[] = [
                'adherant' => $adherant,
                'victoires' => count($victoires),
            ];
        }

        // Tri par nombre de victoires
        usort($classement, fn($a, $b) => $b['victoires'] <=> $a['victoires']);

        return $this->render('historique_combat/classement_categorie.html.twig', [
            'categorie' => $categorie,
            'classement' => $classement,
        ]);
    }

}
