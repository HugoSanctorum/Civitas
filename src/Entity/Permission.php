<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PermissionRepository")
 */
class Permission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $permission;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", inversedBy="Permission")
     */
    private $Role;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HistoriqueAction", mappedBy="Permission")
     */
    private $HistoriqueActions;

    public function __construct()
    {
        $this->Role = new ArrayCollection();
        $this->HistoriqueActions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function setPermission(string $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getRole(): Collection
    {
        return $this->Role;
    }

    public function addRole(Role $role): self
    {
        if (!$this->Role->contains($role)) {
            $this->Role[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->Role->contains($role)) {
            $this->Role->removeElement($role);
        }

        return $this;
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
            $historiqueAction->setPermission($this);
        }

        return $this;
    }

    public function removeHistoriqueAction(HistoriqueAction $historiqueAction): self
    {
        if ($this->HistoriqueActions->contains($historiqueAction)) {
            $this->HistoriqueActions->removeElement($historiqueAction);
            // set the owning side to null (unless already changed)
            if ($historiqueAction->getPermission() === $this) {
                $historiqueAction->setPermission(null);
            }
        }

        return $this;
    }
}
