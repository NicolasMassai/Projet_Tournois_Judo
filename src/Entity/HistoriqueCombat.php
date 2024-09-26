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

    /**
     * @var Collection<int, Combattant>
     */
    #[ORM\OneToMany(targetEntity: Combattant::class, mappedBy: 'historiqueCombat')]
    private Collection $combattant;

    #[ORM\OneToOne(inversedBy: 'historiqueCombat', cascade: ['persist', 'remove'])]
    private ?tournoi $tournoi = null;

    public function __construct()
    {
        $this->combattant = new ArrayCollection();
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

    /**
     * @return Collection<int, Combattant>
     */
    public function getCombattant(): Collection
    {
        return $this->combattant;
    }

    public function addCombattant(Combattant $combattant): static
    {
        if (!$this->combattant->contains($combattant)) {
            $this->combattant->add($combattant);
            $combattant->setHistoriqueCombat($this);
        }

        return $this;
    }

    public function removeCombattant(Combattant $combattant): static
    {
        if ($this->combattant->removeElement($combattant)) {
            // set the owning side to null (unless already changed)
            if ($combattant->getHistoriqueCombat() === $this) {
                $combattant->setHistoriqueCombat(null);
            }
        }

        return $this;
    }

    public function getTournoi(): ?tournoi
    {
        return $this->tournoi;
    }

    public function setTournoi(?tournoi $tournoi): static
    {
        $this->tournoi = $tournoi;

        return $this;
    }
}
