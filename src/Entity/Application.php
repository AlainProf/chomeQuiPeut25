<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    private ?Chomeur $chomeur = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OffreEmploi $offreEmploi = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datePostulee = null;

    #[ORM\Column]
    private ?bool $convoque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChomeur(): ?Chomeur
    {
        return $this->chomeur;
    }

    public function setChomeur(?Chomeur $chomeur): static
    {
        $this->chomeur = $chomeur;

        return $this;
    }

    public function getOffreEmploi(): ?OffreEmploi
    {
        return $this->offreEmploi;
    }

    public function setOffreEmploi(?OffreEmploi $offreEmploi): static
    {
        $this->offreEmploi = $offreEmploi;

        return $this;
    }

    public function getDatePostulee(): ?\DateTimeInterface
    {
        return $this->datePostulee;
    }

    public function setDatePostulee(\DateTimeInterface $datePostulee): static
    {
        $this->datePostulee = $datePostulee;

        return $this;
    }

    public function isConvoque(): ?bool
    {
        return $this->convoque;
    }

    public function setConvoque(bool $convoque): static
    {
        $this->convoque = $convoque;

        return $this;
    }
}
