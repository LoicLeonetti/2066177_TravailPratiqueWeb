<?php

use App\Entity\Produit;

class Panier
{
    // Tableau de ProduitPanier
    public $produits;

    // Ajouter un produit au panier
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
    
    // Vérification de la présence d’un produit dans son tableau
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


