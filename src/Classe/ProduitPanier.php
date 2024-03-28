<?php

namespace App\Classe;

use App\Entity\Produit;

# Nom: Loic Leonetti #
# Date: 2024-03-20   #

class ProduitPanier extends Produit
{
    public function __construct(Produit $var = null) {
        if ($var != null) {
            
            $this->setNom($var->getNom());
            $this->setDescription($var->getDescription());
            $this->setPrix($var->getPrix());
            $this->setImage($var->getImage());
            $this->setIdCategorie($var->getIdCategorie());
            $this->produitId = $var->getId();
        }
    }

    private int $quantiteCommandee = 0;
    private int $produitId = 0;
   
    public function getQuantiteCommandee() : ?int
    {
        return $this->quantiteCommandee;
    }

    public function setQuantiteCommandee(int $quantite)
    {
        $this->quantiteCommandee = $quantite;
    }

    public function getProduitId() : ?int
    {
        return $this->produitId;
    }

    public function setProduitId(int $id)
    {
        $this->produitId = $id;
    }




}



