<?php

namespace App\Entity;

use JsonSerializable;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=SalleRepository::class)
 * @UniqueEntity(
 *      fields={"numero"},
 *      message="Cette salle existe déjà."
 * )
 */
class Salle implements JsonSerializable {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner le numéro."
     * )
     */
    private $numero;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="salle")
     */
    private $cours;

    public function __construct() {
        $this->cours = new ArrayCollection();
    }

    public function __toString() {
        if ($this->numero) {
            return $this->numero;
        }

        return '';
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'salle' => $this->numero,
            'cours' => $this->cours->toArray(),
        ];
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getNumero(): ?string {
        return $this->numero;
    }

    public function setNumero(?string $numero): self {
        $this->numero = $numero;

        return $this;
    }

    /**
     * @return Collection|Cours[]
     */
    public function getCours(): Collection {
        return $this->cours;
    }

    public function addCour(Cours $cour): self {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
            $cour->setSalle($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getSalle() === $this) {
                $cour->setSalle(null);
            }
        }

        return $this;
    }
}
