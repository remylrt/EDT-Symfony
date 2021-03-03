<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AvisRepository::class)
 * @UniqueEntity(
 *  fields={"professeur","emailEtudiant"},
 *  errorPath="emailEtudiant",
 *  message="Cet étudiant a déjà noté ce professeur."
 * )
 */
class Avis {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner la note."
     * )
     * @Assert\Range(min=0, max=5)
     */
    private $note;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner le commentaire."
     * )
     */
    private $commentaire;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner l'email."
     * )
     * @Assert\Email(
     *      message = "Le format de l'email est invalide."
     * )
     */
    private $emailEtudiant;

    /**
     * @ORM\ManyToOne(targetEntity=Professeur::class, inversedBy="avis")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner le professeur."
     * )
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
