<?php

namespace App\Classe;

use App\Classe\ProduitPanier;


class Panier{
    // Tableau de ProduitPanier
    public $produits = [];

    const FRAIS_LIVRAISON = 15;
    const TPS = 0.05;
    const TVQ = 0.09975;
    const DECIMAL = 2;

    // Ajouter un produit au panier
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function ajouterProduit(ProduitPanier $produitPanier)
    {  
        if ($this->produitExiste($produitPanier) != null) {
            //Le produits est déjà dans le panier
            $produitDansPanier = $this->produitExiste($produitPanier);
            $produitDansPanier->setQuantiteCommandee($produitDansPanier->getQuantiteCommandee() + 1);
        }else{
            //Le produit n'est pas dans le panier
            $produitPanier->setQuantiteCommandee(1);
            $this->produits[] = $produitPanier;
        }

    }

    // Vérification de la présence d’un produit dans son tableau si il existe on retourne le produit sinon on retourne null
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    private function produitExiste(ProduitPanier $produit)
    {
        // Si le tableau de produits est vide, cela veut dire que le produit n'est pas deja dans le panier
        if ($this->produits == null) {
            return null;
        }

        foreach($this->produits as $p)
        {
            if($p->getNom() == $produit->getNom())
            {
                return $p;
            }
        }
        return null;
    }

    // Calcul du nombre de produits distincts dans le panier
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function getNombreProduits()
    {
        return count($this->produits);
    }

    // Calcul du nombre d’items dans le panier
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function getNombreItem()
    {

        if ($this->produits == null) {
            return 0;
        }

        $totale = 0;

        foreach($this->produits as $p)
        {
            $totale += $p->getQuantiteCommandee();
        }

        return $totale;
    }

    // Suppression d’un produit du panier
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function supprimerProduit(ProduitPanier $produit)
    {

        foreach($this->produits as $key => $p)
        {
            if($p->getNom() == $produit->getNom())
            {
                unset($this->produits[$key]);
            }
        }
    }

    public function getFraisLivraison()
    {
        return number_format(Panier::FRAIS_LIVRAISON,Panier::DECIMAL);
    }

    // Calcul de la valeur totale du panier 
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function getValeurPanier()
    {
        $total = 0;

        

        foreach($this->produits as $p)
        {
            $total += $p->getQuantiteCommandee() * $p->getPrix();
        }

        // dd(is_numeric($total));

        return $total;
    }

    // Calcul du Total avant taxes
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function getTotalAvantTaxes()
    {

        return $this->getValeurPanier() + Panier::FRAIS_LIVRAISON;
    }

    // Calcul de la TPS
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function getTPS()
    {
        return $this->getTotalAvantTaxes() * Panier::TPS;
    }

    // Calcul de la TVQ
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function getTVQ()
    {
        return $this->getTotalAvantTaxes() * Panier::TVQ;
    }

    // Calcul du Total de l'achat
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function getTotalAchat()
    {
        return $this->getTotalAvantTaxes() + $this->getTPS() + $this->getTVQ();
    }

}



