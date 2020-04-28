<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HistoriqueActionRepository")
 */
class HistoriqueAction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne", inversedBy="HistoriqueActions")
     */
    private $Personne;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="HistoriqueAction")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Role;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Permission", inversedBy="HistoriqueActions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Permission;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getPersonne(): ?Personne
    {
        return $this->Personne;
    }

    public function setPersonne(?Personne $Personne): self
    {
        $this->Personne = $Personne;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->Role;
    }

    public function setRole(?Role $Role): self
    {
        $this->Role = $Role;

        return $this;
    }

    public function getPermission(): ?Permission
    {
        return $this->Permission;
    }

    public function setPermission(?Permission $Permission): self
    {
        $this->Permission = $Permission;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
