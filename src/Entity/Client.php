<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(fields:"nom", message:"existe déjà")] 
#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column()]
    #[Assert\Regex(
        pattern: '/^.{2,15}$/i',
        match: true,
        message: 'La taille ou le contenu non conforme'
    )]
    private ?string $nom = null;

    #[ORM\Column()]
    #[Assert\Regex(
        pattern: '/^.{2,15}$/i',
        match: true,
        message: 'La taille ou le contenu non conforme'
    )]
    private ?string $prenom = null;

    #[ORM\Column()]
    #[Assert\Regex(
        pattern: '/^.{2,15}$/i',
        match: true,
        message: 'La taille ou le contenu non conforme'
    )]
    private ?string $nomFamille = null;

    #[ORM\Column(length: 10)]
    #[Assert\Choice(
        choices: ['Homme', 'Femme', 'Neutre'],
        message: 'Choisissez un genre valide'
    )]
    private ?string $genre = null;

    #[ORM\Column()]
    #[Assert\Regex(
        pattern: '/^.{2,15}$/i',
        match: true,
        message: 'La taille ou le contenu non conforme'
    )]
    private ?string $adresse = null;

    #[ORM\Column()]
    #[Assert\Regex(
        pattern: '/^.{2,15}$/i',
        match: true,
        message: 'La taille ou le contenu non conforme'
    )]
    private ?string $ville = null;
    #
    #[ORM\Column()]
    #[Assert\Choice(
        choices: [
            'Ontario', 'Québec ', 'Nouvelle-Écosse', 'Nouveau-Brunswick', 'Manitoba', 'Colombie-Britannique',
            'Île-du-Prince-Édouard', 'Saskatchewan', 'Alberta', 'Terre-Neuve-et-Labrador', 'Territoires du Nord-Ouest', 'Yukon', 'Nunavut'
        ],
        message: 'Choisissez une province ou territoires valide'
    )]
    private ?string $province = null;

    #[ORM\Column()]
    #[Assert\Regex(
        pattern: '/^[A-CEGHJ-NPR-TV-Z][0-9][A-CEGHJ-NPR-TV-Z] ?[0-9][A-CEGHJ-NPR-TV-Z][0-9]$/i',
        match: true,
        message: 'Le format du code postal est on conforme'
    )]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(
        message: 'Le format de l\'adresse courriel est invalide'
    )]
    private ?string $adresseCourriel = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/^.{2,15}$/i',
        match: true,
        message: 'La taille ou le contenu non conforme'
    )]
    private ?string $motDePasse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNomFamille(): ?string
    {
        return $this->nomFamille;
    }

    public function setNomFamille(string $nomFamille): static
    {
        $this->nomFamille = $nomFamille;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

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

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(string $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getAdresseCourriel(): ?string
    {
        return $this->adresseCourriel;
    }

    public function setAdresseCourriel(string $adresseCourriel): static
    {
        $this->adresseCourriel = $adresseCourriel;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }
}
