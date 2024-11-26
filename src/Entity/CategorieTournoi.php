<?php

namespace App\Entity;

use App\Repository\CategorieTournoiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieTournoiRepository::class)]
class CategorieTournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'categorieTournois')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'categorieTournois')]
    private ?Tournoi $tournoi = null;

    /**
     * @var Collection<int, Arbitre>
     */
    #[ORM\ManyToMany(targetEntity: Arbitre::class, inversedBy: 'categorieTournois')]
    private Collection $arbitres;

    /**
     * @var Collection<int, Adherant>
     */
    #[ORM\ManyToMany(targetEntity: Adherant::class, inversedBy: 'categorieTournois')]
    private Collection $combattants;

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\OneToMany(targetEntity: Groupe::class, mappedBy: 'CategorieTournoi')]
    private Collection $groupes;

    /**
     * @var Collection<int, Combat>
     */
    #[ORM\OneToMany(targetEntity: Combat::class, mappedBy: 'categorieTournoi')]
    private Collection $combats;

    public function __construct()
    {
        $this->arbitres = new ArrayCollection();
        $this->combattants = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->combats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

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

    /**
     * @return Collection<int, Arbitre>
     */
    public function getArbitres(): Collection
    {
        return $this->arbitres;
    }

    public function addArbitre(Arbitre $arbitre): static
    {
        if (!$this->arbitres->contains($arbitre)) {
            $this->arbitres->add($arbitre);
        }

        return $this;
    }

    public function removeArbitre(Arbitre $arbitre): static
    {
        $this->arbitres->removeElement($arbitre);

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

    /**
     * @return Collection<int, Groupe>
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): static
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes->add($groupe);
            $groupe->setCategorieTournoi($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getCategorieTournoi() === $this) {
                $groupe->setCategorieTournoi(null);
            }
        }

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
            $combat->setCategorieTournoi($this);
        }

        return $this;
    }

    public function removeCombat(Combat $combat): static
    {
        if ($this->combats->removeElement($combat)) {
            // set the owning side to null (unless already changed)
            if ($combat->getCategorieTournoi() === $this) {
                $combat->setCategorieTournoi(null);
            }
        }

        return $this;
    }
}
