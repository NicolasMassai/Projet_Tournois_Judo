<?php

namespace App\Entity;

use App\Repository\CombatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CombatRepository::class)]
class Combat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'combattant1')]
    private ?Adherant $combattant1 = null;

    #[ORM\ManyToOne(inversedBy: 'combattant2')]
    private ?Adherant $combattant2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $scoreCombattant1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $scoreCombattant2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resultat = null;

    #[ORM\ManyToOne(inversedBy: 'combats')]
    private ?Tournoi $tournoi = null;



    public function __construct()
    {
       }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCombattant1(): ?Adherant
    {
        return $this->combattant1;
    }

    public function setCombattant1(?Adherant $combattant1): static
    {
        $this->combattant1 = $combattant1;

        return $this;
    }

    public function getCombattant2(): ?Adherant
    {
        return $this->combattant2;
    }

    public function setCombattant2(?Adherant $combattant2): static
    {
        $this->combattant2 = $combattant2;

        return $this;
    }

    public function getScoreCombattant1(): ?int
    {
        return $this->scoreCombattant1;
    }

    public function setScoreCombattant1(?int $scoreCombattant1): static
    {
        $this->scoreCombattant1 = $scoreCombattant1;

        return $this;
    }

    public function getScoreCombattant2(): ?int
    {
        return $this->scoreCombattant2;
    }

    public function setScoreCombattant2(?int $scoreCombattant2): static
    {
        $this->scoreCombattant2 = $scoreCombattant2;

        return $this;
    }

    public function getResultat(): ?string
    {
        return $this->resultat;
    }

    public function setResultat(?string $resultat): static
    {
        $this->resultat = $resultat;

        return $this;
    }

    public function getTournoi(): ?Tournoi
    {
        return $this->tournoi;
    }

    public function setTournoi(?Tournoi $tournoi): static
    {
        $this->tournoi = $tournoi;

        return $this;
    }

    
}
