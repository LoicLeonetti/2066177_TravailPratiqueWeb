<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    /**
     * @var Collection<int, commandeDetail>
     */
    #[ORM\OneToMany(targetEntity: CommandeDetail::class, mappedBy: 'commande',cascade: ['persist','remove'])]
    private Collection $commandeDetails;

    public function __construct()
    {
        $this->commandeDetails = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, commandeDetail>
     */
    public function getCommandeDetails(): Collection
    {
        return $this->commandeDetails;
    }

    public function addCommandeDetail(commandeDetail $commandeDetail): static
    {
        if (!$this->commandeDetails->contains($commandeDetail)) {
            $this->commandeDetails->add($commandeDetail);
            $commandeDetail->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeDetail(commandeDetail $commandeDetail): static
    {
        if ($this->commandeDetails->removeElement($commandeDetail)) {
            // set the owning side to null (unless already changed)
            if ($commandeDetail->getCommande() === $this) {
                $commandeDetail->setCommande(null);
            }
        }

        return $this;
    }

    public function peutAnnuler(): bool
    {
        if($this->getDateCommande() > new \DateTime('now - 48 hours')) {
            return true;
        }

        return false;
    }

    public function tempRestantPourAnnuler(): string
    {   
        $datelimite = date_modify($this->dateCommande, '+48 hours');
        $date = new \DateTime();
        $interval = $datelimite->diff($date);

        return $interval->format('%d jour %h heures');
    }   

    public function CalculeTotal(): float
    {
        $total = 0;
        foreach ($this->commandeDetails as $cd) {
            $total += $cd->getProduit()->getPrix() * $cd->getQuantite();
        }
        return $total;
    }


}
