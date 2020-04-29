<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Role;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonneRepository")
 */
class Personne implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;
    /**
     * @ORM\Column(type="datetime", length=255)
     */
    private $createdAt;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HistoriqueAction", mappedBy="Personne")
     */
    private $HistoriqueActions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Intervenir", mappedBy="Personne")
     */
    private $Intervenir;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commune", inversedBy="Personnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Commune;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CompteRendu", mappedBy="Personne")
     */
    private $CompteRendus;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", inversedBy="Personnes")
     */
    private $Roles;


    public function __construct()
    {
        $this->Commune = new ArrayCollection();
        $this->HistoriqueActions = new ArrayCollection();
        $this->Intervenir = new ArrayCollection();
        $this->CompteRendus = new ArrayCollection();
        $this->Roles = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }



    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }


    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }



    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return Collection|HistoriqueAction[]
     */
    public function getHistoriqueActions(): Collection
    {
        return $this->HistoriqueActions;
    }

    public function addHistoriqueAction(HistoriqueAction $historiqueAction): self
    {
        if (!$this->HistoriqueActions->contains($historiqueAction)) {
            $this->HistoriqueActions[] = $historiqueAction;
            $historiqueAction->setPersonne($this);
        }

        return $this;
    }

    public function removeHistoriqueAction(HistoriqueAction $historiqueAction): self
    {
        if ($this->HistoriqueActions->contains($historiqueAction)) {
            $this->HistoriqueActions->removeElement($historiqueAction);
            // set the owning side to null (unless already changed)
            if ($historiqueAction->getPersonne() === $this) {
                $historiqueAction->setPersonne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Intervenir[]
     */
    public function getIntervenir(): Collection
    {
        return $this->Intervenir;
    }

    public function addIntervenir(Intervenir $intervenir): self
    {
        if (!$this->Intervenir->contains($intervenir)) {
            $this->Intervenir[] = $intervenir;
            $intervenir->setPersonne($this);
        }

        return $this;
    }

    public function removeIntervenir(Intervenir $intervenir): self
    {
        if ($this->Intervenir->contains($intervenir)) {
            $this->Intervenir->removeElement($intervenir);
            // set the owning side to null (unless already changed)
            if ($intervenir->getPersonne() === $this) {
                $intervenir->setPersonne(null);
            }
        }

        return $this;
    }


    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCommune(): ?Commune
    {
        return $this->Commune;
    }

    public function setCommune(?Commune $Commune): self
    {
        $this->Commune = $Commune;

        return $this;
    }


    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return Collection|CompteRendu[]
     */
    public function getCompteRendus(): Collection
    {
        return $this->CompteRendus;
    }

    public function addCompteRendus(CompteRendu $compteRendus): self
    {
        if (!$this->CompteRendus->contains($compteRendus)) {
            $this->CompteRendus[] = $compteRendus;
            $compteRendus->setPersonne($this);
        }

        return $this;
    }

    public function removeCompteRendus(CompteRendu $compteRendus): self
    {
        if ($this->CompteRendus->contains($compteRendus)) {
            $this->CompteRendus->removeElement($compteRendus);
            // set the owning side to null (unless already changed)
            if ($compteRendus->getPersonne() === $this) {
                $compteRendus->setPersonne(null);
            }
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->Roles;
        $array = $roles->toArray();
        $roleToString = [];
        foreach($array as $role){
            array_push($roleToString,$role->getRole());
        }
        return (array_unique(array_merge(['ROLE_USER'],$roleToString)));
    }

    public function addRole(Role $role): self
    {
        if (!$this->Roles->contains($role)) {
            $this->Roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->Roles->contains($role)) {
            $this->Roles->removeElement($role);
        }

        return $this;
    }


    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }
}
