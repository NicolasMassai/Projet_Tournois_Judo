<?php

namespace Unit;

use App\Entity\Combat;
use App\Entity\Adherant;
use App\Entity\Tournoi;
use App\Entity\Categorie;
use App\Entity\Groupe;
use App\Entity\HistoriqueCombat;
use PHPUnit\Framework\TestCase;

class CombatTest extends TestCase
{
    private Combat $combat;

    protected function setUp(): void
    {
        $this->combat = new Combat();
    }


    public function testGetSetScoreCombattant1(): void
    {
        $this->combat->setScoreCombattant1(10);
        $this->assertEquals(10, $this->combat->getScoreCombattant1());
    }

    public function testGetSetScoreCombattant2(): void
    {
        $this->combat->setScoreCombattant2(7);
        $this->assertEquals(7, $this->combat->getScoreCombattant2());
    }

    public function testGetSetResultat(): void
    {
        $this->combat->setResultat('combattant1');
        $this->assertEquals('combattant1', $this->combat->getResultat());
    }


    public function testGetSetPhase(): void
    {
        $this->combat->setPhase('Finale');
        $this->assertEquals('Finale', $this->combat->getPhase());
    }


    public function testVainqueurCombattant1(): void
    {
        $combattant1 = new Adherant;
        $this->combat->setCombattant1($combattant1);
        $this->combat->setResultat('combattant1');
        
        $this->assertTrue($this->combat->Vainqueur($combattant1));
    }

    public function testVainqueurCombattant2(): void
    {
        $combattant2 = new Adherant;
        $this->combat->setCombattant2($combattant2);
        $this->combat->setResultat('combattant2');
        
        $this->assertTrue($this->combat->Vainqueur($combattant2));
    }

    public function testNonVainqueur(): void
    {
        $combattant1 = new Adherant;
        $combattant2 = new Adherant;
        $this->combat->setCombattant1($combattant1);
        $this->combat->setCombattant2($combattant2);
        $this->combat->setResultat('combattant1');
        
        $this->assertFalse($this->combat->Vainqueur($combattant2));
    }
}
