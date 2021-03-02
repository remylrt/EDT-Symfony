<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AvisRepository::class)
 */
class Avis
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $note;

    /**
     * @ORM\Column(type="text")
     */
    private $commentaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emailEtudiant;

    /**
     * @ORM\ManyToOne(targetEntity=Professeur::class, inversedBy="avis")
     * @ORM\JoinColumn(nullable=false)
     */
    private $professeur;

    public function getId(): ?int {
        return $this->id;
    }

    public function getNote(): ?int {
        return $this->note;
    }

    public function setNote(int $note): self {
        $this->note = $note;

        return $this;
    }

    public function getCommentaire(): ?string {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getEmailEtudiant(): ?string {
        return $this->emailEtudiant;
    }

    public function setEmailEtudiant(string $emailEtudiant): self {
        $this->emailEtudiant = $emailEtudiant;

        return $this;
    }

    public function getProfesseur(): ?Professeur {
        return $this->professeur;
    }

    public function setProfesseur(?Professeur $professeur): self {
        $this->professeur = $professeur;

        return $this;
    }
}
