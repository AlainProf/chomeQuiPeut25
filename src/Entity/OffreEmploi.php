<?php

namespace App\Entity;

use App\Repository\OffreEmploiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreEmploiRepository::class)]
class OffreEmploi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $salaireAnnuel = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\ManyToOne(inversedBy: 'offreEmplois', cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entreprise $entreprise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSalaireAnnuel(): ?int
    {
        return $this->salaireAnnuel;
    }

    public function setSalaireAnnuel(?int $salaireAnnuel): static
    {
        $this->salaireAnnuel = $salaireAnnuel;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }
}
