<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "discr", type: "string")]
#[ORM\DiscriminatorMap(["user" => "User", "adherant" => "Adherant", "arbitre" => "Arbitre"])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToOne(mappedBy: 'president', cascade: ['persist', 'remove'])]
    private ?Club $president_club = null;

    /**
     * @var Collection<int, Tournoi>
     */
    #[ORM\OneToMany(targetEntity: Tournoi::class, mappedBy: 'president')]
    private Collection $tournoi_president;

    public function __construct()
    {
        $this->tournoi_president = new ArrayCollection();
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->email;
    }
    

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPresidentClub(): ?Club
    {
        return $this->president_club;
    }

    public function setPresidentClub(?Club $president_club): static
    {
        // unset the owning side of the relation if necessary
        if ($president_club === null && $this->president_club !== null) {
            $this->president_club->setPresident(null);
        }

        // set the owning side of the relation if necessary
        if ($president_club !== null && $president_club->getPresident() !== $this) {
            $president_club->setPresident($this);
        }

        $this->president_club = $president_club;

        return $this;
    }

    public function isPresident(): bool
    {
        return in_array('ROLE_PRESIDENT', $this->roles);
    }

    /**
     * @return Collection<int, Tournoi>
     */
    public function getTournoiPresident(): Collection
    {
        return $this->tournoi_president;
    }

    public function addTournoiPresident(Tournoi $tournoiPresident): static
    {
        if (!$this->tournoi_president->contains($tournoiPresident)) {
            $this->tournoi_president->add($tournoiPresident);
            $tournoiPresident->setPresident($this);
        }

        return $this;
    }

    public function removeTournoiPresident(Tournoi $tournoiPresident): static
    {
        if ($this->tournoi_president->removeElement($tournoiPresident)) {
            // set the owning side to null (unless already changed)
            if ($tournoiPresident->getPresident() === $this) {
                $tournoiPresident->setPresident(null);
            }
        }

        return $this;
    }
    
}
