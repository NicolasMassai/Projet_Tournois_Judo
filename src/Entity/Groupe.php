<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'groupes')]
    private ?Tournoi $tournoi = null;

    /**
     * @var Collection<int, Adherant>
     */
    #[ORM\ManyToMany(targetEntity: Adherant::class, inversedBy: 'groupes')]
    private Collection $combattants;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Combat>
     */
    #[ORM\OneToMany(targetEntity: Combat::class, mappedBy: 'groupe')]
    private Collection $combats;

    #[ORM\ManyToOne(inversedBy: 'groupes')]
    private ?CategorieTournoi $CategorieTournoi = null;

    public function __construct()
    {
        $this->combattants = new ArrayCollection();
        $this->combats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Adherant>
     */
    public function getCombattants(): Collection
    {
        return $this->combattants;
    }

    public function addCombattant(Adherant $combattant): static
    {
        if (!$this->combattants->contains($combattant)) {
            $this->combattants->add($combattant);
        }

        return $this;
    }

    public function removeCombattant(Adherant $combattant): static
    {
        $this->combattants->removeElement($combattant);

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Combat>
     */
    public function getCombats(): Collection
    {
        return $this->combats;
    }

    public function addCombat(Combat $combat): static
    {
        if (!$this->combats->contains($combat)) {
            $this->combats->add($combat);
            $combat->setGroupe($this);
        }

        return $this;
    }

    public function removeCombat(Combat $combat): static
    {
        if ($this->combats->removeElement($combat)) {
            // set the owning side to null (unless already changed)
            if ($combat->getGroupe() === $this) {
                $combat->setGroupe(null);
            }
        }

        return $this;
    }

    public function getCategorieTournoi(): ?CategorieTournoi
    {
        return $this->CategorieTournoi;
    }

    public function setCategorieTournoi(?CategorieTournoi $CategorieTournoi): static
    {
        $this->CategorieTournoi = $CategorieTournoi;

        return $this;
    }
}
