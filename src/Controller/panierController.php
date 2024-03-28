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
use App\Form\QuantiteProduitType;





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
    
        return $this->render('panier.html.twig',['panier'=>$panier]);
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/updateQuantite', name: 'updateQuantite')]
    public function updateQuantite(Request $request)
    {
        $panier = $request->getSession()->get('panier');
        

        $soumettre = $request->query->get('soumettre');

        //dd(is_numeric($panier->getValeurPanier()));

        if(isset($soumettre)){

            foreach($panier->produits as $p){

                
                $quantite = $request->query->get($p->getProduitId());

                $quantite = intval($quantite);

                $p->setQuantiteCommandee($quantite);

                if ($p->getQuantiteCommandee() == 0){
                    
                    $panier->supprimerProduit($p);
                }

            }

            $request->getSession()->set('panier',$panier);

        }
   
        

        return $this->redirectToRoute('panier');
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
    #[Route('/supprimerProduit/{nomProduit}', name: 'supprimerProduit')]
    public function supprimerProduit(ManagerRegistry $doctrine,Request $request,$nomProduit)
    {
        // When clicked on the delete button, the product is removed from the cart
        $panier = $request->getSession()->get('panier');

        

        if ($panier) { 
            $produitPanier = new ProduitPanier($doctrine->getManager()->getRepository(Produit::class)->findOneBy(["Nom" => $nomProduit]));


            $panier->supprimerProduit($produitPanier); 
        }
        
        $panier = $request->getSession()->set('panier',$panier);

        return $this->redirectToRoute('panier');
    }

}