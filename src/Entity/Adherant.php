<?php

namespace App\Entity;

use App\Repository\AdherantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdherantRepository::class)]
class Adherant extends User
{

    #[ORM\ManyToOne(inversedBy: 'adherant')]
    private ?Club $club = null;

    /**
     * @var Collection<int, Tournoi>
     */
    #[ORM\ManyToMany(targetEntity: Tournoi::class, mappedBy: 'combattant')]
    private Collection $tournois;

    /**
     * @var Collection<int, Combat>
     */
    #[ORM\OneToMany(targetEntity: Combat::class, mappedBy: 'combattant1')]
    private Collection $combattant1;

    /**
     * @var Collection<int, Combat>
     */
    #[ORM\OneToMany(targetEntity: Combat::class, mappedBy: 'combattant2')]
    private Collection $combattant2;

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\ManyToMany(targetEntity: Groupe::class, mappedBy: 'combattants')]
    private Collection $groupes;

    #[ORM\ManyToOne(inversedBy: 'adherants')]
    private ?Categorie $categorie = null;

    public function __construct()
    {
        $this->tournois = new ArrayCollection();
        $this->combattant1 = new ArrayCollection();
        $this->combattant2 = new ArrayCollection();
        $this->groupes = new ArrayCollection();
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): static
    {
        $this->club = $club;

        return $this;
    }

    /**
     * @return Collection<int, Tournoi>
     */
    public function getTournois(): Collection
    {
        return $this->tournois;
    }

    public function addTournoi(Tournoi $tournoi): static
    {
        if (!$this->tournois->contains($tournoi)) {
            $this->tournois->add($tournoi);
            $tournoi->addCombattant($this);
        }

        return $this;
    }

    public function removeTournoi(Tournoi $tournoi): static
    {
        if ($this->tournois->removeElement($tournoi)) {
            $tournoi->removeCombattant($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, Combat>
     */
    public function getCombattant1(): Collection
    {
        return $this->combattant1;
    }

    public function addCombattant1(Combat $combattant1): static
    {
        if (!$this->combattant1->contains($combattant1)) {
            $this->combattant1->add($combattant1);
            $combattant1->setCombattant1($this);
        }

        return $this;
    }

    public function removeCombattant1(Combat $combattant1): static
    {
        if ($this->combattant1->removeElement($combattant1)) {
            // set the owning side to null (unless already changed)
            if ($combattant1->getCombattant1() === $this) {
                $combattant1->setCombattant1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Combat>
     */
    public function getCombattant2(): Collection
    {
        return $this->combattant2;
    }

    public function addCombattant2(Combat $combattant2): static
    {
        if (!$this->combattant2->contains($combattant2)) {
            $this->combattant2->add($combattant2);
            $combattant2->setCombattant2($this);
        }

        return $this;
    }

    public function removeCombattant2(Combat $combattant2): static
    {
        if ($this->combattant2->removeElement($combattant2)) {
            // set the owning side to null (unless already changed)
            if ($combattant2->getCombattant2() === $this) {
                $combattant2->setCombattant2(null);
            }
        }

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
            $groupe->addCombattant($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            $groupe->removeCombattant($this);
        }

        return $this;
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

    public function __toString(): string
    {
        return $this->categorie ? (string) $this->categorie : 'Aucune catégorie';
    }
    
}
