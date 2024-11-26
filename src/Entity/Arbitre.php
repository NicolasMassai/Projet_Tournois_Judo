<?php

namespace App\Entity;

use App\Repository\ArbitreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArbitreRepository::class)]
class Arbitre extends User
{

    #[ORM\Column]
    private ?bool $disponibilite = null;

    /**
     * @var Collection<int, CategorieTournoi>
     */
    #[ORM\ManyToMany(targetEntity: CategorieTournoi::class, mappedBy: 'arbitres')]
    private Collection $categorieTournois;

    public function __construct()
    {
        parent::__construct();
        $this->categorieTournois = new ArrayCollection();
    }


    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    /**
     * @return Collection<int, CategorieTournoi>
     */
    public function getCategorieTournois(): Collection
    {
        return $this->categorieTournois;
    }

    public function addCategorieTournoi(CategorieTournoi $categorieTournoi): static
    {
        if (!$this->categorieTournois->contains($categorieTournoi)) {
            $this->categorieTournois->add($categorieTournoi);
            $categorieTournoi->addArbitre($this);
        }

        return $this;
    }

    public function removeCategorieTournoi(CategorieTournoi $categorieTournoi): static
    {
        if ($this->categorieTournois->removeElement($categorieTournoi)) {
            $categorieTournoi->removeArbitre($this);
        }

        return $this;
    }
  
}
