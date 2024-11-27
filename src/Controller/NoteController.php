<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Combat;
use App\Entity\Tournoi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NoteController extends AbstractController
{


    private EntityManagerInterface $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/combat/{id}/note/{tournoiId}', name: 'note_combat', methods: ['POST'])]
    public function noterCombat(Combat $combat, Request $request, int $tournoiId): Response
    {
        $tournoi = $this->em->getRepository(Tournoi::class)->find($tournoiId);
        
        if (!$tournoi) {
            $this->addFlash('error', 'Tournoi non trouvé.');
            return $this->redirectToRoute('afficher_combats', ['id' => $tournoiId]);
        }
        
        $spectateur = $this->getUser();
        if (!$spectateur) {
            $this->addFlash('error', 'Veuillez vous connecter pour noter.');
            return $this->redirectToRoute('afficher_combats', ['id' => $tournoiId]);
        }

        // Vérifier si le résultat du combat est non nul
        if ($combat->getResultat() === null) {
            $this->addFlash('error', 'La note ne peut être renseignée que si le résultat du combat est non nul.');
            return $this->redirectToRoute('afficher_combats', ['id' => $tournoiId]);
        }

        $existingNote = $this->em->getRepository(Note::class)->findOneBy(['spectateur' => $spectateur, 'combat' => $combat]);
        if ($existingNote) {
            $this->addFlash('error', 'Vous avez déjà soumis une note pour ce combat.');
            return $this->redirectToRoute('afficher_combats', ['id' => $tournoiId]);
        }

        $note = (int)$request->request->get('note');
        if ($note < 1 || $note > 5) {
            $this->addFlash('error', 'La note doit être entre 1 et 5.');
            return $this->redirectToRoute('afficher_combats', ['id' => $tournoiId]);
        }

        $noteCombat = new Note();
        $noteCombat->setSpectateur($spectateur)
                   ->setCombat($combat)
                   ->setNote($note);

        $this->em->persist($noteCombat);
        $this->em->flush();

        // Afficher un message de succès
        $this->addFlash('success', 'Note soumise avec succès.');
        return $this->redirectToRoute('afficher_combats', ['id' => $tournoiId]);
    } 
    
}
