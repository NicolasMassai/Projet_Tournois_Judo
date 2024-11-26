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
     * @var Collection<int, Adherant>
     */
    #[ORM\OneToMany(targetEntity: Adherant::class, mappedBy: 'club', cascade:["remove"])]
    private Collection $adherant;

    /**
     * @var Collection<int, Tournoi>
     */
    #[ORM\ManyToMany(targetEntity: Tournoi::class, mappedBy: 'clubs')]
    private Collection $tournois;

    #[ORM\OneToOne(inversedBy: 'president_club', cascade: ['persist', 'remove'])]
    private ?User $president = null;


    public function __construct()
    {
        $this->adherant = new ArrayCollection();
        $this->tournois = new ArrayCollection();
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

    
    public function __tostring(){
        return $this->nom;
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
            $tournoi->addClub($this);
        }

        return $this;
    }

    public function removeTournoi(Tournoi $tournoi): static
    {
        if ($this->tournois->removeElement($tournoi)) {
            $tournoi->removeClub($this);
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

}
