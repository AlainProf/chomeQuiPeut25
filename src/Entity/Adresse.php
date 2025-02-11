<?php

namespace App\Entity;

use App\Repository\AdresseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
#[ORM\Table(name:"adresses")]
class Adresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name:"numCivique", length: 10)]
    private ?string $numCivique = null;

    #[ORM\Column(length: 30)]
    private ?string $rue = null;

    #[ORM\Column(length: 30)]
    private ?string $ville = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCivique(): ?string
    {
        return $this->numCivique;
    }

    public function setNumCivique(string $numCivique): static
    {
        $this->numCivique = $numCivique;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): static
    {
        $this->rue = $rue;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }
}
