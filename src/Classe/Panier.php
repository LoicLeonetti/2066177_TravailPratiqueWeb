<?php

class Panier
{
    // Tableau de ProduitPanier
    public $produits;
    
    // Vérification de la présence d’un produit dans son tableau
    public function test()
    {
        return 1;
    }

    // Calcul du nombre de produits distincts dans le panier
    public function getNombreProduits()
    {
        return $this->produits->count;
    }

    // Calcul du nombre d’items dans le panier
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

    public function removeProduit(ProduitPanier $produit)
    {
        $this->produits->delete($produit);
    }

    // Calcul de la valeur totale du panier 

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


