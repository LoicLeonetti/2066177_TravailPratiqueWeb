<?php

use App\Entity\Produit;

class ProduitPanier extends Produit
{
    public function __construct(Produit $var = null) {
        if ($var != null) {
            $this->setNom($var->getNom());
            $this->setDescription($var->getDescription());
            $this->setPrix($var->getPrix());
            $this->setImage($var->getImage());
            $this->setIdCategorie($var->getIdCategorie());
        }
    }

    private int $quantiteCommandee = 0;
   
    public function getQuantiteCommandee() : ?int
    {
        return $this->quantiteCommandee;
    }

    public function setQuantiteCommandee(int $quantite)
    {
        $this->quantiteCommandee = $quantite;
    }
}

?> 

