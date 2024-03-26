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
    #[Route('/', name: 'catalogue')]
    public function accueil(ManagerRegistry $doctrine, Request $request): Response
    {

        $produits = $doctrine->getManager()->getRepository(Produit::class)->findAll();

        $categories = $doctrine->getManager()->getRepository(Categorie::class)->findAll();

        return $this->render('catalogue.html.twig', ['produits' => $produits, 'categories' => $categories]);
    }


    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/filtre', name: 'filtre')]
    public function filtre(ManagerRegistry $doctrine, Request $request): Response
    {

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

        return $this->render('catalogue.html.twig', ['produits' => $produits, 'categories' => $categories]);
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
            if ($p->getIdCategorie() == $categorieId) {

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
        $categorie = $doctrine->getManager()->getRepository(Categorie::class)->find($produit->getIdCategorie());

        return $this->render("detailsProduit.html.twig", ["produit" => $produit, "categorie" => $categorie]);
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {

        return $this->render("contact.html.twig");
    }
}
