<?php
namespace App\Service;

use App\Entity\Combat;
use App\Entity\Groupe;
use App\Entity\Tournoi;
use App\Entity\CategorieTournoi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GroupeService extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function creerGroupesParCategorie(Tournoi $tournoi, Security $security): array
    {
        // Vérifier si les groupes ont déjà été générés
        if ($tournoi->getGroupes()->count() > 0) {
            return []; // Si oui, on ne régénère pas les groupes
        }
        // Vérifier que l'utilisateur connecté est le président du tournoi
        $user = $security->getUser();
        if (!$user || $user !== $tournoi->getPresident()) {
            throw new AccessDeniedHttpException(
                'Vous n\'êtes pas autorisé à créer des groupes pour ce tournoi.');
        }
        // Vérifier si l'inscription au tournoi est ouvert
        if ($tournoi->isInscriptionOuvertes()) { 
            $this->addFlash('error', 'Les inscriptions sont encore ouverte pour ce tournoi.');
            return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
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
                $groupe->setNom("Groupe " . ($index + 1) . " - Catégorie " . 
                    $categorieTournoi->getCategorie()->getCategoriePoids() . " kg");
                $groupe->setTournoi($tournoi);
                $groupe->setCategorieTournoi($categorieTournoi);
                foreach ($groupeCombattants as $combattant) {
                    $groupe->addCombattant($combattant);
                }
                $this->em->persist($groupe);
                $tournoi->addGroupe($groupe);
                $this->creerCombatsPourGroupeManuel(
                    $groupeCombattants, $groupe, $tournoi, $categorieTournoi
                );
    
                // Ajouter le groupe au tableau de retour
                $groupesParCategorie[] = $groupe;
            }
            $this->em->flush();
        }
        return $groupesParCategorie;
    }
    


    private function creerCombatsPourGroupeManuel(array $groupe, Groupe $groupes, 
        Tournoi $tournoi, CategorieTournoi $categorieTournoi): void
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