<?php

namespace App\Entity;

use App\Repository\AdherantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdherantRepository::class)]
class Adherant extends User
{
    #[ORM\OneToOne(inversedBy: 'adherant', cascade: ['persist', 'remove'])]
    private ?Combattant $combattant = null;

    #[ORM\ManyToOne(inversedBy: 'adherant')]
    private ?Club $club = null;

    /**
     * @var Collection<int, Tournoi>
     */
    #[ORM\ManyToMany(targetEntity: Tournoi::class, mappedBy: 'combattant')]
    private Collection $tournois;

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'adherants')]
    private Collection $poids;

    public function __construct()
    {
        $this->tournois = new ArrayCollection();
        $this->poids = new ArrayCollection();
    }

    public function getCombattant(): ?Combattant
    {
        return $this->combattant;
    }

    public function setCombattant(?Combattant $combattant): static
    {
        $this->combattant = $combattant;

        return $this;
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
     * @return Collection<int, Categorie>
     */
    public function getPoids(): Collection
    {
        return $this->poids;
    }

    public function addPoid(Categorie $poid): static
    {
        if (!$this->poids->contains($poid)) {
            $this->poids->add($poid);
        }

        return $this;
    }

    public function removePoid(Categorie $poid): static
    {
        $this->poids->removeElement($poid);

        return $this;
    }
}
