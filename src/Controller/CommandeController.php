<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Commande;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\CommandeDetail;
use App\Classe\ProduitPanier;
use App\Entity\Produit;
use App\Entity\Client;


class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'commande')]
    public function commande(ManagerRegistry $doctrine, Request $request): Response
    {   

        $em = $doctrine->getManager();
        
        // On vérifie si le client est connecté sinon on le redirige vers la page de connexion
        $client = $request->getSession()->get('clientConnecte');
        if ($client == null) {
           $this->addFlash('erreur', "Vous devez vous connecter avant de passer la commande");
           return $this->redirectToRoute('connexion');
        }

        $client = $em->getRepository(Client::class)->find($client->getId());

        // Le bouton pour payer ne devrait pas être visible si le panier est vide, mais on vérifie quand même
        $panier = $request->getSession()->get('panier');
        if ($panier == null) {
            $this->addFlash('erreur', "Votre panier est vide");
            return $this->redirectToRoute('catalogue');
        }

        $commande = new Commande();


        // Ajout Commande Details
        foreach($panier->produits as $p)
        {   
            $produit = $em->getRepository(Produit::class)->find($p->getProduitId());

            $commandeDetail = new CommandeDetail();
            $commandeDetail->setProduit($produit);
            $commandeDetail->setQuantite($p->getQuantiteCommandee());
            $commandeDetail->setQuantiteRupture($this->CalculeRupture($p->getQuantiteCommandee(), $produit->getQtteStock()));
            $commande->addCommandeDetail($commandeDetail);
        }

        // Ajout Client
        $commande->setClient($client);
        
        // Ajout Date Commande
        $commande->setDateCommande(new \DateTime());

        
        // Enregistrement de la commande en BD
        //$em->persist($commande);
        //$em->flush();

        

        //$this->addFlash('success', "Votre commande a été enregistrée avec succès");

        return $this->render('confirmationCommande.html.twig',['panier'=>$panier,'clientConnecte'=>$client,'commande'=>$commande]);
    }

    #[Route('/confirmation_commande', name: 'confirmation_commande')]
    public function confirmation_commande(ManagerRegistry $doctrine, Request $request, Commande $commande): Response
    {   

        $em = $doctrine->getManager();
        
        // On vérifie si le client est connecté sinon on le redirige vers la page de connexion
        $client = $request->getSession()->get('clientConnecte');
        if ($client == null) {
           $this->addFlash('erreur', "Vous devez vous connecter avant de passer la commande");
           return $this->redirectToRoute('connexion');
        }

        $client = $em->getRepository(Client::class)->find($client->getId());

        // Le bouton pour payer ne devrait pas être visible si le panier est vide, mais on vérifie quand même
        $panier = $request->getSession()->get('panier');
        if ($panier == null) {
            $this->addFlash('erreur', "Votre panier est vide");
            return $this->redirectToRoute('catalogue');
        }

        $commande = new Commande();


        // Ajout Commande Details
        foreach($panier->produits as $p)
        {   
            $produit = $em->getRepository(Produit::class)->find($p->getProduitId());

            $commandeDetail = new CommandeDetail();
            $commandeDetail->setProduit($produit);
            $commandeDetail->setQuantite($p->getQuantiteCommandee());
            $commandeDetail->setQuantiteRupture($this->CalculeRupture($p->getQuantiteCommandee(), $produit->getQtteStock()));
            $commande->addCommandeDetail($commandeDetail);
        }

        //Ajout Client
        $commande->setClient($client);
        
        //Ajout Date Commande
        $commande->setDateCommande(new \DateTime());

        
        // Enregistrement de la commande en BD
        $em->persist($commande);
        $em->flush();

        dd($commande);

        $this->addFlash('success', "Votre commande a été enregistrée avec succès");

        return $this->redirectToRoute('catalogue');
    }

    private function CalculeRupture($quantiteCommandee, $quantiteStock)
    {
        $rupture = $quantiteCommandee - $quantiteStock;

        if ($rupture < 0) {
            return 0;
        }

        return $rupture;

        
    }  
}
