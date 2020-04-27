<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(type="text", length=255)
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Commune")
     */
    private $Commune;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="Personne")
     */
    private $Role;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HistoriqueAction", mappedBy="Personne")
     */
    private $HistoriqueActions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Intervenir", mappedBy="Personne")
     */
    private $Intervenir;

    public function __construct()
    {
        $this->HistoriqueActions = new ArrayCollection();
        $this->Intervenir = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[] The user roles
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
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
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
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
}
