<?php

namespace App\Controller;

# Nom: Loic Leonetti #
# Date: 2024-02-24   #

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Produit;

use App\Classe\Panier;
use App\Classe\ProduitPanier;




class catalogueController extends AbstractController
{
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/catalogue', name: 'catalogue')]
    public function accueil(ManagerRegistry $doctrine, Request $request): Response
    {   
        $panier = $request->getSession()->get('panier');
        $clientConnecte = $request->getSession()->get('clientConnecte');

        if ($panier == null) {
            $panier = new Panier();
            $request->getSession()->set('panier',$panier);
        }

        if ($clientConnecte != null) {
            $this->addFlash('succes', "Bienvenu " . $clientConnecte->getNom());
        }


        $produits = $doctrine->getManager()->getRepository(Produit::class)->findAll();

        $categories = $doctrine->getManager()->getRepository(Categorie::class)->findAll();

        return $this->render('catalogue.html.twig', ['produits' => $produits, 'categories' => $categories,'panier'=>$panier,'clientConnecte'=>$clientConnecte]);
    }


    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/filtre', name: 'filtre')]
    public function filtre(ManagerRegistry $doctrine, Request $request): Response
    {
        $panier = $request->getSession()->get('panier');

        if ($panier == null) {
            $panier = new Panier();
            $request->getSession()->set('panier',$panier);
        }


        $produits = $doctrine->getManager()->getRepository(Produit::class)->findAll();
        $categories = $doctrine->getManager()->getRepository(Categorie::class)->findAll();


        // Filtre Texte
        $texteFiltre = $request->query->get('texteFiltre');
        $categorieId = $request->query->get('categorieFiltre');

        if (strlen($texteFiltre) != 0) {

            $produits = $this->filtrerTexte($produits, $texteFiltre);
        }

        // Filtre Categorie
        if ($categorieId != "Toutes") {
            $produits = $this->filtrerCategorie($produits, $categorieId);
        }

        return $this->render('catalogue.html.twig', ['produits' => $produits, 'categories' => $categories,'panier'=>$panier]);
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    private function filtrerTexte($produits, $texteFiltre)
    {
        $tabTMP = [];

        // Ajoute les produits dans le liste si le nom ou la description du produit contient la recherche
        foreach ($produits as $p) {

            if (strpos($p->getNom(), $texteFiltre) !== false) {
                $tabTMP[] = $p;
            }
        }

        return $tabTMP;
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    private function filtrerCategorie($produits, $categorieId)
    {

        $tabTMP = [];
        // Ajoute les produits dans le liste si le categorieId est le meme que le recherche
        foreach ($produits as $p) {
            if ($p->getCategorie()->getId() == $categorieId) {

                $tabTMP[] = $p;
            }
        }

        return $tabTMP;
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/produit_details/{id}', name: 'produit_details')]
    public function produit_details(ManagerRegistry $doctrine, $id): Response
    {

        $produit = $doctrine->getManager()->getRepository(Produit::class)->find($id);
        $categorie = $doctrine->getManager()->getRepository(Categorie::class)->find($produit->getCategorie()->getId());

        return $this->render("detailsProduit.html.twig", ["produit" => $produit, "categorie" => $categorie]);
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request): Response
    {

        $panier = $request->getSession()->get('panier');

        if ($panier == null) {
            $panier = new Panier();
            $request->getSession()->set('panier',$panier);
        }

        $clientConnecte = $request->getSession()->get('clientConnecte');

        return $this->render("contact.html.twig",['panier'=>$panier,'clientConnecte'=>$clientConnecte]);
    }
}
