<?php

namespace App\Classe;

use App\Classe\ProduitPanier;


class Panier{
    // Tableau de ProduitPanier
    public $produits;


    public function test()
    {
        echo "test";
    }

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
        return $this->produits->count;
    }

    // Calcul du nombre d’items dans le panier
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function getNombreItem()
    {
        $totale = 0;

        foreach($this->produits as $p)
        {
            $totale += $p->quantiteCommandee;
        }

        return $totale;
    }

    // Suppression d’un produit du panier
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function supprimerProduit(ProduitPanier $produit)
    {

        dd($produit);

        $index = array_search($produit, $this->produits); 

        

        unset($this->produits[$index]);
    }

    // Calcul de la valeur totale du panier 
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    public function getValeurPanier()
    {
        $totale = 0;

        foreach($this->produits as $p)
        {
            $totale += $p->getQuantiteCommandee * $p->getPrix();
        }

        return $totale;
    }


}
?>


