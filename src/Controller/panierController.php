<?php

namespace App\Controller;


# Nom: Loic Leonetti #
# Date: 2024-03-20   #

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

use App\Classe\Panier;
use App\Classe\ProduitPanier;





use Symfony\Component\Routing\Attribute\Route;

class panierController extends AbstractController
{
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/panier', name: 'panier')]
    public function panier(Request $request)
    {
        $panier = $request->getSession()->get('panier');

        if ($panier == null) {
            $panier = new Panier();
            $request->getSession()->set('panier',$panier);
        }

        return $this->render('panier.html.twig',['panier'=>$panier]);
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/ajouterProduit/{id}', name: 'ajouterProduit')]
    public function ajouterProduit(ManagerRegistry $doctrine,Request $request,$id)
    {
        $panier = $request->getSession()->get('panier');

        $produitPanier = new ProduitPanier($doctrine->getManager()->getRepository(Produit::class)->find($id));
        
        // Si panier est null on crée un nouveau panier
        if(!$panier){
            $panier = new Panier();
        }

        // On ajoute le produit au panier et on le stocke en session
        $panier->ajouterProduit($produitPanier);
        $request->getSession()->set('panier',$panier);

        return $this->redirectToRoute('catalogue');
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/supprimerProduit/{idProduit}', name: 'supprimerProduit')]
    public function supprimerProduit(ManagerRegistry $doctrine,Request $request,$idProduit)
    {
        // When clicked on the delete button, the product is removed from the cart
        $panier = $request->getSession()->get('panier');

        if ($panier) { 

            // Id is always null because ProduitPanier does not store the id
            $produitPanier = new ProduitPanier($doctrine->getManager()->getRepository(Produit::class)->find($idProduit));
            $panier->supprimerProduit($produitPanier); 
        }
        
        //dd($panier);
        $panier = $request->getSession()->get('panier');

        return $this->redirectToRoute('panier');
    }

}