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
        $groupe = $this->createMock(Groupe::class);
        $combattant1 = $this->createMock(User::class);
        $combattant2 = $this->createMock(User::class);

        $combattant1->method('getId')->willReturn(1);
        $combattant2->method('getId')->willReturn(2);

        $collection = new ArrayCollection([$combattant1, $combattant2]);
        $groupe->method('getCombattants')->willReturn($collection);

        $this->combatRepository->method('findBy')->willReturn([]);

        $result = $this->classementService->genererClassementPourGroupe($groupe);

        $this->assertCount(2, $result);
        $this->assertEquals(0, $result[0]['points']);
        $this->assertEquals(0, $result[1]['points']);
        $this->assertEquals(0, $result[0]['score_total']);
        $this->assertEquals(0, $result[1]['score_total']);

        $this->assertGreaterThanOrEqual($result[1]['points'], $result[0]['points']);
    }

    public function testGenererClassementPourGroupeAvecCombats()
    {
        $groupe = $this->createMock(Groupe::class);
        $combattant1 = $this->createMock(Adherant::class);
        $combattant2 = $this->createMock(Adherant::class);

        $combattant1->method('getId')->willReturn(1);
        $combattant2->method('getId')->willReturn(2);

        $collection = new ArrayCollection([$combattant1, $combattant2]);
        $groupe->method('getCombattants')->willReturn($collection);

        $combat = $this->createMock(Combat::class);
        $combat->method('getCombattant1')->willReturn($combattant1);
        $combat->method('getCombattant2')->willReturn($combattant2);
        $combat->method('getScoreCombattant1')->willReturn(10);
        $combat->method('getScoreCombattant2')->willReturn(7);

        $this->combatRepository->method('findBy')->willReturn([$combat]);

        $result = $this->classementService->genererClassementPourGroupe($groupe);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['points']);
        $this->assertEquals(0, $result[1]['points']);
        $this->assertGreaterThanOrEqual($result[1]['score_total'], $result[0]['score_total']);
        $this->assertArrayHasKey('points', $result[0]);
        $this->assertArrayHasKey('score_total', $result[0]);
        $this->assertArrayHasKey('points', $result[1]);
        $this->assertArrayHasKey('score_total', $result[1]);
    }

    public function testGenererClassementPourGroupeAvecScoresEgaux()
    {
        $groupe = $this->createMock(Groupe::class);
        $combattant1 = $this->createMock(Adherant::class);
        $combattant2 = $this->createMock(Adherant::class);

        $combattant1->method('getId')->willReturn(1);
        $combattant2->method('getId')->willReturn(2);

        $collection = new ArrayCollection([$combattant1, $combattant2]);
        $groupe->method('getCombattants')->willReturn($collection);

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

        $this->combatRepository->method('findBy')->willReturn([$combat1, $combat2]);

        $result = $this->classementService->genererClassementPourGroupe($groupe);

        $this->assertGreaterThanOrEqual($result[0]['score_total'], $result[1]['score_total']);
        $this->assertEquals($result[0]['points'], $result[1]['points']);
        $this->assertNotEmpty($result[0]['score_total']);
        $this->assertNotEmpty($result[1]['score_total']);
    }
}
