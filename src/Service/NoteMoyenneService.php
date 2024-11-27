<?php
namespace App\Service;

use App\Entity\Note;
use App\Entity\Combat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NoteMoyenneService extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function NoteMoyenneEtNombreVotants(Combat $combat): array
    {
        $notes = $this->em->getRepository(Note::class)->findBy(['combat' => $combat]);
        $totalVotants = count($notes);

        if ($totalVotants === 0) {
            return [
                'moyenne' => null,
                'totalVotants' => 0,
            ];
        }

        $somme = 0;
        foreach ($notes as $note) {
            $somme += $note->getNote();
        }

        $moyenne = $somme / $totalVotants;

        return [
            'moyenne' => $moyenne,
            'totalVotants' => $totalVotants,
        ];
    }


}