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

    #[ORM\Column(length: 255)]
    private ?string $resultat = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    /**
     * @var Collection<int, tournoi>
     */
    #[ORM\OneToMany(targetEntity: Tournoi::class, mappedBy: 'combat')]
    private Collection $tournoi;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'combats')]
    private Collection $combattant;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'combattant')]
    private Collection $combats;

    #[ORM\OneToOne(inversedBy: 'combat', cascade: ['persist', 'remove'])]
    private ?arbitre $arbitre = null;

    public function __construct()
    {
        $this->tournoi = new ArrayCollection();
        $this->combattant = new ArrayCollection();
        $this->combats = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, tournoi>
     */
    public function getTournoi(): Collection
    {
        return $this->tournoi;
    }

    public function addTournoi(tournoi $tournoi): static
    {
        if (!$this->tournoi->contains($tournoi)) {
            $this->tournoi->add($tournoi);
            $tournoi->setCombat($this);
        }

        return $this;
    }

    public function removeTournoi(tournoi $tournoi): static
    {
        if ($this->tournoi->removeElement($tournoi)) {
            // set the owning side to null (unless already changed)
            if ($tournoi->getCombat() === $this) {
                $tournoi->setCombat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCombattant(): Collection
    {
        return $this->combattant;
    }

    public function addCombattant(self $combattant): static
    {
        if (!$this->combattant->contains($combattant)) {
            $this->combattant->add($combattant);
        }

        return $this;
    }

    public function removeCombattant(self $combattant): static
    {
        $this->combattant->removeElement($combattant);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCombats(): Collection
    {
        return $this->combats;
    }

    public function addCombat(self $combat): static
    {
        if (!$this->combats->contains($combat)) {
            $this->combats->add($combat);
            $combat->addCombattant($this);
        }

        return $this;
    }

    public function removeCombat(self $combat): static
    {
        if ($this->combats->removeElement($combat)) {
            $combat->removeCombattant($this);
        }

        return $this;
    }

    public function getArbitre(): ?arbitre
    {
        return $this->arbitre;
    }

    public function setArbitre(?arbitre $arbitre): static
    {
        $this->arbitre = $arbitre;

        return $this;
    }

}
