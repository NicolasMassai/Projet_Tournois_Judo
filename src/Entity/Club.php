<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
class Club
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    private ?string $pays = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /**
     * @var Collection<int, Combattant>
     */
    #[ORM\OneToMany(targetEntity: Combattant::class, mappedBy: 'club')]
    private Collection $combattant;

    /**
     * @var Collection<int, Adherant>
     */
    #[ORM\OneToMany(targetEntity: Adherant::class, mappedBy: 'club')]
    private Collection $adherant;

    /**
     * @var Collection<int, tournoi>
     */
    #[ORM\OneToMany(targetEntity: Tournoi::class, mappedBy: 'club')]
    private Collection $tournoi;

    public function __construct()
    {
        $this->combattant = new ArrayCollection();
        $this->adherant = new ArrayCollection();
        $this->tournoi = new ArrayCollection();
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

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Combattant>
     */
    public function getCombattant(): Collection
    {
        return $this->combattant;
    }

    public function addCombattant(Combattant $combattant): static
    {
        if (!$this->combattant->contains($combattant)) {
            $this->combattant->add($combattant);
            $combattant->setClub($this);
        }

        return $this;
    }

    public function removeCombattant(Combattant $combattant): static
    {
        if ($this->combattant->removeElement($combattant)) {
            // set the owning side to null (unless already changed)
            if ($combattant->getClub() === $this) {
                $combattant->setClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Adherant>
     */
    public function getAdherant(): Collection
    {
        return $this->adherant;
    }

    public function addAdherant(Adherant $adherant): static
    {
        if (!$this->adherant->contains($adherant)) {
            $this->adherant->add($adherant);
            $adherant->setClub($this);
        }

        return $this;
    }

    public function removeAdherant(Adherant $adherant): static
    {
        if ($this->adherant->removeElement($adherant)) {
            // set the owning side to null (unless already changed)
            if ($adherant->getClub() === $this) {
                $adherant->setClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, tournoi>
     */
    public function getTournoi(): Collection
    {
        return $this->tournoi;
    }

    public function addTournoi(Tournoi $tournoi): static
    {
        if (!$this->tournoi->contains($tournoi)) {
            $this->tournoi->add($tournoi);
            $tournoi->setClub($this);
        }

        return $this;
    }

    public function removeTournoi(Tournoi $tournoi): static
    {
        if ($this->tournoi->removeElement($tournoi)) {
            // set the owning side to null (unless already changed)
            if ($tournoi->getClub() === $this) {
                $tournoi->setClub(null);
            }
        }

        return $this;
    }
}
