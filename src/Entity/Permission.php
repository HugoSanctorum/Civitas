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

    public function __construct()
    {
        $this->Role = new ArrayCollection();
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
}
