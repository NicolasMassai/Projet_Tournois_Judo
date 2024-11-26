<?php
namespace App\Service;

use App\Entity\Combat;
use App\Entity\Groupe;
use App\Entity\Tournoi;
use App\Entity\CategorieTournoi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CombatService extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function creerCombats(Tournoi $tournoi, string $phase, string $phasePrecedente = null): Response
    {
        // Vérifier si des combats existent déjà pour cette phase
        $combatsExistants = $this->em->getRepository(Combat::class)->findBy([
            'tournoi' => $tournoi,
            'Phase' => $phase
        ]);

        // Si des combats existent déjà, les afficher
        if ($combatsExistants) {
            return $this->render('combat/quart_final.html.twig', [
                'combats' => $combatsExistants,
            ]);
        }

        // Si une phase précédente est spécifiée, récupérer les gagnants de cette phase
        if ($phasePrecedente) {
            $combatsPrecedents = $this->em->getRepository(Combat::class)->findBy([
                'tournoi' => $tournoi,
                'Phase' => $phasePrecedente,
                'resultat' => ['combattant1', 'combattant2']
            ]);

            // Organiser les gagnants par catégorie_tournoi
            $combattantsParCategorieTournoi = [];
            foreach ($combatsPrecedents as $combat) {
                $categorieTournoi = $combat->getCategorieTournoi();
                if ($combat->getResultat() === 'combattant1') {
                    $combattantsParCategorieTournoi[$categorieTournoi->getId()][] = $combat->getCombattant1();
                } elseif ($combat->getResultat() === 'combattant2') {
                    $combattantsParCategorieTournoi[$categorieTournoi->getId()][] = $combat->getCombattant2();
                }
            }
        } else {
            // Récupérer tous les groupes du tournoi
            $groupes = $tournoi->getGroupes();
            $combattantsParCategorieTournoi = [];

            // Parcourir les groupes pour générer le classement et les qualifiés par catégorie_tournoi
            foreach ($groupes as $groupe) {
                $classement = $this->genererClassementPourGroupe($groupe);

                // Ajouter les qualifiés (les deux premiers) à la liste des qualifiés par catégorie_tournoi
                if (count($classement) >= 2) {
                    $categorieTournoi = $groupe->getCategorieTournoi();
                    $combattantsParCategorieTournoi[$categorieTournoi->getId()][] = $classement[0]['combattant']; // Premier
                    $combattantsParCategorieTournoi[$categorieTournoi->getId()][] = $classement[1]['combattant']; // Deuxième
                }
            }
        }

        // Créer les combats pour chaque catégorie_tournoi
        foreach ($combattantsParCategorieTournoi as $categorieTournoiId => $combattantsQualifies) {
            $categorieTournoi = $this->em->getRepository(CategorieTournoi::class)->find($categorieTournoiId);

            // Séparer les qualifiés en deux groupes : les premiers et les deuxièmes
            $combattantsQualifies1 = [];
            $combattantsQualifies2 = [];

            for ($i = 0; $i < count($combattantsQualifies); $i++) {
                if ($i % 2 === 0) {
                    $combattantsQualifies1[] = $combattantsQualifies[$i];
                } else {
                    $combattantsQualifies2[] = $combattantsQualifies[$i];
                }
            }

            shuffle($combattantsQualifies1);
            shuffle($combattantsQualifies2);

            // Créer les combats
            for ($i = 0; $i < count($combattantsQualifies1); $i++) {
                $combat = new Combat();
                $combat->setCombattant1($combattantsQualifies1[$i]);
                $combat->setCombattant2($combattantsQualifies2[$i]);
                $combat->setTournoi($tournoi);
                $combat->setPhase($phase);
                $combat->setCategorieTournoi($categorieTournoi);
                $this->em->persist($combat);
            }
        }

        // Sauvegarder tous les combats
        $this->em->flush();

        return $this->redirectToRoute('afficher_combats', ['id' => $tournoi->getId()]);
    }
   

    public function genererClassementPourGroupe(Groupe $groupe): array
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

    public function creerGroupesParCategorie(Tournoi $tournoi): array
    {
        // Vérifier si les groupes ont déjà été générés
        if ($tournoi->getGroupes()->count() > 0) {
            return []; // Si oui, on ne régénère pas les groupes
        }

        // Tableau pour stocker les groupes de toutes les catégories
        $groupesParCategorie = [];

        // Récupérer les relations CategorieTournoi du tournoi
        $categoriesTournoi = $tournoi->getCategorieTournois();


        foreach ($categoriesTournoi as $categorieTournoi) {
            // Récupérer les combattants de la catégorie-tournoi
            $combattants = $categorieTournoi->getCombattants();
            // Si la catégorie a moins de 16 combattants, elle ne participe pas
            if (count($combattants) < 16) {
                continue;
            }


            // Mélanger les combattants pour une sélection aléatoire
            $combattants = $combattants instanceof \Doctrine\Common\Collections\Collection
                ? $combattants->toArray()
                : $combattants;
            shuffle($combattants);

            // Prendre les 16 premiers combattants
            $combattants = array_slice($combattants, 0, 16);


            // Créer 4 groupes de 4 combattants
            $groupes = array_chunk($combattants, 4);

            // Enregistrer les groupes en base de données
            foreach ($groupes as $index => $groupeCombattants) {
                $groupe = new Groupe();
                $groupe->setNom("Groupe " . ($index + 1) . " - Catégorie " . $categorieTournoi->getCategorie()->getCategoriePoids() . " kg");
                $groupe->setTournoi($tournoi);
                $groupe->setCategorieTournoi($categorieTournoi);

                foreach ($groupeCombattants as $combattant) {
                    $groupe->addCombattant($combattant);
                }

                $this->em->persist($groupe);
                $tournoi->addGroupe($groupe);

                // Créer les combats pour ce groupe
                $this->creerCombatsPourGroupeManuel($groupeCombattants, $groupe, $tournoi, $categorieTournoi);
            }

            $this->em->flush();
        }

        return $groupesParCategorie;
    }


    private function creerCombatsPourGroupeManuel(array $groupe, Groupe $groupes, Tournoi $tournoi, CategorieTournoi $categorieTournoi): void
    {
        for ($i = 0; $i < count($groupe); $i++) {
            for ($j = $i + 1; $j < count($groupe); $j++) {
                $combat = new Combat();
                $combat->setCombattant1($groupe[$i]);
                $combat->setCombattant2($groupe[$j]);
                $combat->setTournoi($tournoi);
                $combat->setGroupe($groupes);
                $combat->setPhase('Phase_de_Poule');
                $combat->setCategorieTournoi($categorieTournoi);

                $this->em->persist($combat);
            }
        }

        // Sauvegarder tous les combats (sans résultats pour l'instant)
        $this->em->flush();
    }
}