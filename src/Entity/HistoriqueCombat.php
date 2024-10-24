<?php

namespace App\Entity;

use App\Repository\HistoriqueCombatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToOne(inversedBy: 'historiqueCombats')]
    private ?Adherant $combattant = null;

    #[ORM\Column(nullable: true)]
    private ?int $victoire = null;

    #[ORM\Column(nullable: true)]
    private ?int $defaite = null;

    /**
     * @var Collection<int, Combat>
     */
    #[ORM\OneToMany(targetEntity: Combat::class, mappedBy: 'historiqueCombat')]
    private Collection $combat;


    public function __construct()
    {
        $this->combat = new ArrayCollection();
    }

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

    public function getCombattant(): ?Adherant
    {
        return $this->combattant;
    }

    public function setCombattant(?Adherant $combattant): static
    {
        $this->combattant = $combattant;

        return $this;
    }

    public function getVictoire(): ?int
    {
        return $this->victoire;
    }

    public function setVictoire(?int $victoire): static
    {
        $this->victoire = $victoire;

        return $this;
    }

    public function getDefaite(): ?int
    {
        return $this->defaite;
    }

    public function setDefaite(?int $defaite): static
    {
        $this->defaite = $defaite;

        return $this;
    }

    /**
     * @return Collection<int, Combat>
     */
    public function getCombat(): Collection
    {
        return $this->combat;
    }

    public function addCombat(Combat $combat): static
    {
        if (!$this->combat->contains($combat)) {
            $this->combat->add($combat);
            $combat->setHistoriqueCombat($this);
        }

        return $this;
    }

    public function removeCombat(Combat $combat): static
    {
        if ($this->combat->removeElement($combat)) {
            // set the owning side to null (unless already changed)
            if ($combat->getHistoriqueCombat() === $this) {
                $combat->setHistoriqueCombat(null);
            }
        }

        return $this;
    }

}
