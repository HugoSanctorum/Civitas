<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HistoriqueStatutInterventionRepository")
 */
class HistoriqueStatutIntervention
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StatutIntervention", inversedBy="HistoriqueStatutIntervention")
     * @ORM\JoinColumn(nullable=false)
     */
    private $StatutIntervention;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Intervenir", inversedBy="HistoriqueStatutInterventions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Intervenir;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatutIntervention(): ?StatutIntervention
    {
        return $this->StatutIntervention;
    }

    public function setStatutIntervention(?StatutIntervention $StatutIntervention): self
    {
        $this->StatutIntervention = $StatutIntervention;

        return $this;
    }

    public function getIntervenir(): ?Intervenir
    {
        return $this->Intervenir;
    }

    public function setIntervenir(?Intervenir $Intervenir): self
    {
        $this->Intervenir = $Intervenir;

        return $this;
    }
}
