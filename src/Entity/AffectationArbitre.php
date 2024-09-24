<?php

namespace App\Entity;

use App\Repository\AffectationArbitreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AffectationArbitreRepository::class)]
class AffectationArbitre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_affectation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAffectation(): ?\DateTimeInterface
    {
        return $this->date_affectation;
    }

    public function setDateAffectation(\DateTimeInterface $date_affectation): static
    {
        $this->date_affectation = $date_affectation;

        return $this;
    }
}
