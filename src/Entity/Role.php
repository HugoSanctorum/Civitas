<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Permission;

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
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Personne", inversedBy="Role")
     */
    private $Personne;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Permission", mappedBy="Role")
     */
    private $Permissions;

    public function __construct()
    {
        $this->Personne = new ArrayCollection();
        $this->Permissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Personne[]
     */
    public function getPersonne(): Collection
    {
        return $this->Personne;
    }

    public function addPersonne(Personne $personne): self
    {
        if (!$this->Personne->contains($personne)) {
            $this->Personne[] = $personne;
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->Personne->contains($personne)) {
            $this->Personne->removeElement($personne);
        }

        return $this;
    }

    /**
     * @return Collection|Permission[]
     */
    public function getPermissions(): Collection
    {
        return $this->Permission;
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
}
