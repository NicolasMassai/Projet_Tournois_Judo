<?php

namespace App\Entity;

use App\Repository\AdherantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdherantRepository::class)]
class Adherant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\OneToOne(inversedBy: 'adherant', cascade: ['persist', 'remove'])]
    private ?combattant $combattant = null;

    /**
     * @var Collection<int, club>
     */
    #[ORM\OneToMany(targetEntity: club::class, mappedBy: 'adherant')]
    private Collection $club;

    public function __construct()
    {
        $this->club = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCombattant(): ?combattant
    {
        return $this->combattant;
    }

    public function setCombattant(?combattant $combattant): static
    {
        $this->combattant = $combattant;

        return $this;
    }

    /**
     * @return Collection<int, club>
     */
    public function getClub(): Collection
    {
        return $this->club;
    }

    public function addClub(club $club): static
    {
        if (!$this->club->contains($club)) {
            $this->club->add($club);
            $club->setAdherant($this);
        }

        return $this;
    }

    public function removeClub(club $club): static
    {
        if ($this->club->removeElement($club)) {
            // set the owning side to null (unless already changed)
            if ($club->getAdherant() === $this) {
                $club->setAdherant(null);
            }
        }

        return $this;
    }
}
