<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
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
    private $URL;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Probleme", inversedBy="Images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Probleme;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getURL(): ?string
    {
        return $this->URL;
    }

    public function setURL(string $URL): self
    {
        $this->URL = $URL;

        return $this;
    }

    public function getProbleme(): ?Probleme
    {
        return $this->Probleme;
    }

    public function setProbleme(?Probleme $Probleme): self
    {
        $this->Probleme = $Probleme;

        return $this;
    }

    public function __toString()
    {
        return $this->getURL();
    }

}
