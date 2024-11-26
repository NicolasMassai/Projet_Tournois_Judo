<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TournoiRepository;

#[ORM\Entity(repositoryClass: TournoiRepository::class)]
class Tournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    /**
     * @var Collection<int, Club>
     */
    #[ORM\ManyToMany(targetEntity: Club::class, inversedBy: 'tournois')]
    private Collection $clubs;

    /**
     * @var Collection<int, Combat>
     */
    #[ORM\OneToMany(targetEntity: Combat::class, mappedBy: 'tournoi')]
    private Collection $combats;

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\OneToMany(targetEntity: Groupe::class, mappedBy: 'tournoi')]
    private Collection $groupes;

    #[ORM\ManyToOne(inversedBy: 'tournoi_president')]
    private ?User $president = null;

    /**
     * @var Collection<int, CategorieTournoi>
     */
    #[ORM\OneToMany(targetEntity: CategorieTournoi::class, mappedBy: 'tournoi')]
    private Collection $categorieTournois;

    public function __construct()
    {
        $this->clubs = new ArrayCollection();
        $this->combats = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->categorieTournois = new ArrayCollection();
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * @return Collection<int, Club>
     */
    public function getClubs(): Collection
    {
        return $this->clubs;
    }

    public function addClub(Club $club): static
    {
        if (!$this->clubs->contains($club)) {
            $this->clubs->add($club);
        }

        return $this;
    }

    public function removeClub(Club $club): static
    {
        $this->clubs->removeElement($club);

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
            $combat->setTournoi($this);
        }

        return $this;
    }

    public function removeCombat(Combat $combat): static
    {
        if ($this->combats->removeElement($combat)) {
            // set the owning side to null (unless already changed)
            if ($combat->getTournoi() === $this) {
                $combat->setTournoi(null);
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
            $groupe->setTournoi($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getTournoi() === $this) {
                $groupe->setTournoi(null);
            }
        }

        return $this;
    }

    public function getPresident(): ?User
    {
        return $this->president;
    }

    public function setPresident(?User $president): static
    {
        $this->president = $president;

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
            $categorieTournoi->setTournoi($this);
        }

        return $this;
    }

    public function removeCategorieTournoi(CategorieTournoi $categorieTournoi): static
    {
        if ($this->categorieTournois->removeElement($categorieTournoi)) {
            // set the owning side to null (unless already changed)
            if ($categorieTournoi->getTournoi() === $this) {
                $categorieTournoi->setTournoi(null);
            }
        }

        return $this;
    }

}
