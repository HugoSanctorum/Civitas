<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use App\Entity\Permission;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $role;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Permission", mappedBy="Role")
     */
    private $Permissions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HistoriqueAction", mappedBy="Role")
     */
    private $HistoriqueActions;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Personne", mappedBy="Roles")
     */
    private $Personnes;

    public function __construct()
    {
        $this->Permissions = new ArrayCollection();
        $this->HistoriqueActions = new ArrayCollection();
        $this->Personnes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }


    /**
     * @return Collection|Permission[]
     */
    public function getPermissions(): Collection
    {
        return $this->Permissions;
    }

    public function addPermissions(Permission $permission): self
    {
        if (!$this->Permissions->contains($permission)) {
            $this->Permissions[] = $permission;
            $permission->addRole($this);
        }

        return $this;
    }

    public function removePermissions(Permission $permission): self
    {
        if ($this->Permissions->contains($permission)) {
            $this->Permissions->removeElement($permission);
            $permission->removeRole($this);
        }

        return $this;
    }


    /**
     * @return Collection|HistoriqueAction[]
     */
    public function getHistoriqueAction(): Collection
    {
        return $this->HistoriqueActions;
    }

    public function addHistoriqueAction(HistoriqueAction $historiqueAction): self
    {
        if (!$this->HistoriqueActions->contains($historiqueAction)) {
            $this->HistoriqueActions[] = $historiqueAction;
            $historiqueAction->setRole($this);
        }

        return $this;
    }

    public function removeHistoriqueAction(HistoriqueAction $historiqueAction): self
    {
        if ($this->HistoriqueActions->contains($historiqueAction)) {
            $this->HistoriqueActions->removeElement($historiqueAction);
            // set the owning side to null (unless already changed)
            if ($historiqueAction->getRole() === $this) {
                $historiqueAction->setRole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Personne[]
     */
    public function getPersonnes(): Collection
    {
        return $this->Personnes;
    }

    public function addPersonne(Personne $personne): self
    {
        if (!$this->Personnes->contains($personne)) {
            $this->Personnes[] = $personne;
            $personne->addRole($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->Personnes->contains($personne)) {
            $this->Personnes->removeElement($personne);
            $personne->removeRole($this);
        }

        return $this;
    }
    public function __toString()
    {
        return $this->getRole();
    }
    public function getLabel(): ?string
    {
            return $this->getRole();
    }

}
