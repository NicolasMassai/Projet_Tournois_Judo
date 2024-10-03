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

    public function __construct()
    {
        $this->tournois = new ArrayCollection();
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
}
