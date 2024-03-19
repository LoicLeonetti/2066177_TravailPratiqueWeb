<?php

use App\Entity\Produit;

class ProduitPanier extends Produit
{
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

