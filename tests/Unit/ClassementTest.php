<?php
namespace Unit;

use App\Entity\Adherant;
use App\Entity\User;
use App\Entity\Combat;
use App\Entity\Groupe;
use App\Service\CombatService;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;

class ClassementTest extends TestCase
{
    private $em;
    private $combatRepository;
    private $classementService;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->combatRepository = $this->createMock(EntityRepository::class);
        $this->em->method('getRepository')->willReturn($this->combatRepository);
        $this->classementService = new CombatService($this->em);
    }

    public function testGenererClassementPourGroupeSansCombats()
    {
        // Création des mocks pour un groupe et ses combattants
        $groupe = $this->createMock(Groupe::class);
        $combattant1 = $this->createMock(User::class);
        $combattant2 = $this->createMock(User::class);

        // Simulation des IDs des combattants
        $combattant1->method('getId')->willReturn(1);
        $combattant2->method('getId')->willReturn(2);

        // Simulation de la liste des combattants dans le groupe
        $collection = new ArrayCollection([$combattant1, $combattant2]);
        $groupe->method('getCombattants')->willReturn($collection);

        // Aucun combat trouvé dans le repository
        $this->combatRepository->method('findBy')->willReturn([]);

        // Appel de la méthode de génération de classement
        $result = $this->classementService->genererClassementPourGroupe($groupe);

        // Vérifications sur le résultat
        $this->assertCount(2, $result); // Deux combattants dans le classement
        $this->assertEquals(0, $result[0]['points']); // Pas de points attribués
        $this->assertEquals(0, $result[1]['points']);
        $this->assertEquals(0, $result[0]['score_total']); // Pas de score
        $this->assertEquals(0, $result[1]['score_total']);

        // Ordre des résultats
        $this->assertGreaterThanOrEqual($result[1]['points'], $result[0]['points']);
    }

    public function testGenererClassementPourGroupeAvecCombats()
    {
        // Création des mocks pour un groupe et ses combattants
        $groupe = $this->createMock(Groupe::class);
        $combattant1 = $this->createMock(Adherant::class);
        $combattant2 = $this->createMock(Adherant::class);

        // Simulation des IDs des combattants
        $combattant1->method('getId')->willReturn(1);
        $combattant2->method('getId')->willReturn(2);

        // Simulation de la liste des combattants dans le groupe
        $collection = new ArrayCollection([$combattant1, $combattant2]);
        $groupe->method('getCombattants')->willReturn($collection);

        // Simulation d'un combat entre les deux combattants
        $combat = $this->createMock(Combat::class);
        $combat->method('getCombattant1')->willReturn($combattant1);
        $combat->method('getCombattant2')->willReturn($combattant2);
        $combat->method('getScoreCombattant1')->willReturn(10);
        $combat->method('getScoreCombattant2')->willReturn(7);

        // Retourne un combat depuis le repository
        $this->combatRepository->method('findBy')->willReturn([$combat]);

        // Appel de la méthode de génération de classement
        $result = $this->classementService->genererClassementPourGroupe($groupe);

        // Vérifications sur le résultat
        $this->assertCount(2, $result); // Deux combattants dans le classement
        $this->assertEquals(1, $result[0]['points']); // Combattant1 a gagné
        $this->assertEquals(0, $result[1]['points']); // Combattant2 a perdu
        $this->assertGreaterThanOrEqual($result[1]['score_total'], 
            $result[0]['score_total']);
    }

    public function testGenererClassementPourGroupeAvecScoresEgaux()
    {
        // Création des mocks pour un groupe et ses combattants
        $groupe = $this->createMock(Groupe::class);
        $combattant1 = $this->createMock(Adherant::class);
        $combattant2 = $this->createMock(Adherant::class);

        // Simulation des IDs des combattants
        $combattant1->method('getId')->willReturn(1);
        $combattant2->method('getId')->willReturn(2);

        // Simulation de la liste des combattants dans le groupe
        $collection = new ArrayCollection([$combattant1, $combattant2]);
        $groupe->method('getCombattants')->willReturn($collection);

        // Simulation de deux combats avec des scores égaux
        $combat1 = $this->createMock(Combat::class);
        $combat1->method('getCombattant1')->willReturn($combattant1);
        $combat1->method('getCombattant2')->willReturn($combattant2);
        $combat1->method('getScoreCombattant1')->willReturn(7);
        $combat1->method('getScoreCombattant2')->willReturn(7);

        $combat2 = $this->createMock(Combat::class);
        $combat2->method('getCombattant1')->willReturn($combattant1);
        $combat2->method('getCombattant2')->willReturn($combattant2);
        $combat2->method('getScoreCombattant1')->willReturn(10);
        $combat2->method('getScoreCombattant2')->willReturn(10);

        // Retourne deux combats depuis le repository
        $this->combatRepository->method('findBy')
            ->willReturn([$combat1, $combat2]);

        // Appel de la méthode de génération de classement
        $result = $this->classementService
            ->genererClassementPourGroupe($groupe);

        // Vérifications sur le résultat
        $this->assertGreaterThanOrEqual($result[0]['score_total'], 
            $result[1]['score_total']);
        $this->assertEquals($result[0]['points'], $result[1]['points']);
    }
}
