<?php

namespace App\Entity;

use App\Repository\HistoriqueCombatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueCombatRepository::class)]
class HistoriqueCombat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $resultat = null;

    #[ORM\Column]
    private ?int $points = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_combat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResultat(): ?string
    {
        return $this->resultat;
    }

    public function setResultat(string $resultat): static
    {
        $this->resultat = $resultat;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getDateCombat(): ?\DateTimeInterface
    {
        return $this->date_combat;
    }

    public function setDateCombat(\DateTimeInterface $date_combat): static
    {
        $this->date_combat = $date_combat;

        return $this;
    }
}
