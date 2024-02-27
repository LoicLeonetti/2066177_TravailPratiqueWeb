<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Produit;

use function PHPSTORM_META\map;

class catalogueController extends AbstractController
{
    #[Route('/', name: 'catalogue')]
    public function accueil(ManagerRegistry $doctrine): Response
    {
        $produits = $doctrine -> getManager() ->getRepository(Produit::class)->findAll();

        $categories = $doctrine -> getManager()->getRepository(Categorie::class)->findAll();

        return $this->render('catalogue.html.twig',['produits' => $produits,'categories' => $categories]);
    }


    #[Route('/{id}', name: 'filtre_categorie')]
    public function filtre_categorie(ManagerRegistry $doctrine,$id): Response
    {
        
        $produits = $doctrine -> getManager() ->getRepository(Produit::class)->findBy(array("idCategorie"=>$id));

        $categories = $doctrine -> getManager()->getRepository(Categorie::class)->findAll();

        return $this->render('catalogue.html.twig',['produits' => $produits,'categories' => $categories]);
    }

    #[Route('/{recherche}', name: 'filtre_recherche')]
    public function filtre_recherche(ManagerRegistry $doctrine,$recherche): Response
    {

        //TODO
        
        $produits = $doctrine -> getManager() ->getRepository(Produit::class)->findBy(array('Nom'=> $recherche,"Description"=>$recherche));

        $categories = $doctrine -> getManager()->getRepository(Categorie::class)->findAll();

        return $this->render('catalogue.html.twig',['produits' => $produits,'categories' => $categories]);
    }

    #[Route('/produit_details/{id}', name: 'produit_details')]
    public function produit_details(ManagerRegistry $doctrine,$id): Response
    {

        $produit = $doctrine->getManager()->getRepository(Produit::class)->find($id);
        $categorie = $doctrine -> getManager()->getRepository(Categorie::class)->find($produit->getIdCategorie());

        return $this->render("detailsProduit.html.twig",["produit"=>$produit,"categorie"=>$categorie]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {

        return $this->render("contact.html.twig");
    }
}

