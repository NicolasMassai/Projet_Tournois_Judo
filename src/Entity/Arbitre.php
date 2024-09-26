<?php

namespace App\Entity;

use App\Repository\ArbitreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArbitreRepository::class)]
class Arbitre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $qualification = null;

    #[ORM\Column]
    private ?bool $disponibilite = null;

    #[ORM\OneToOne(mappedBy: 'arbitre', cascade: ['persist', 'remove'])]
    private ?Combat $combat = null;

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

    public function getQualification(): ?string
    {
        return $this->qualification;
    }

    public function setQualification(string $qualification): static
    {
        $this->qualification = $qualification;

        return $this;
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


    public function getCombat(): ?Combat
    {
        return $this->combat;
    }

    public function setCombat(?Combat $combat): static
    {
        // unset the owning side of the relation if necessary
        if ($combat === null && $this->combat !== null) {
            $this->combat->setArbitre(null);
        }

        // set the owning side of the relation if necessary
        if ($combat !== null && $combat->getArbitre() !== $this) {
            $combat->setArbitre($this);
        }

        $this->combat = $combat;

        return $this;
    }
}
