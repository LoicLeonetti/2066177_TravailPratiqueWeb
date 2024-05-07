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
use App\Classe\Panier;

# Nom: Loic Leonetti #
# Date: 2024-05-01   #

class CommandeController extends AbstractController
{

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/commande', name: 'commande')]
    public function commande(ManagerRegistry $doctrine, Request $request): Response
    {   

        $em = $doctrine->getManager();
        
        // On vérifie si le client est connecté sinon on le redirige vers la page de connexion
        $client = $request->getSession()->get('clientConnecte');

        if ($client == null) {
           $this->addFlash('erreur', "Vous devez vous connecter avant de passer la commande");

           return $this->redirectToRoute('connexion',['state'=>'1']);
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
        
        $request->getSession()->set('commande',$commande);
        
        return $this->render('confirmationCommande.html.twig',['panier'=>$panier,'clientConnecte'=>$client,'commande'=>$commande]);
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/payer_commande', name: 'payer_commande')]
    public function payer_commande(ManagerRegistry $doctrine, Request $request): Response
    {   
        $client = $request->getSession()->get('clientConnecte');
        if ($client == null) {
           $this->addFlash('erreur', "Vous devez vous connecter avant de passer la commande");
           return $this->redirectToRoute('connexion');
        }

        $panier = $request->getSession()->get('panier');
        if ($panier == null) {
            $this->addFlash('erreur', "Votre panier est vide");
            return $this->redirectToRoute('catalogue');
        }

        return $this->render('payerCommande.html.twig',['panier'=>$panier,'clientConnecte'=>$client]);
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/enregistrement_commande', name: 'enregistrement_commande')]
    public function enregistrement_commande(ManagerRegistry $doctrine, Request $request): Response
    {   

        $em = $doctrine->getManager();

        $client = $request->getSession()->get('clientConnecte');
        if ($client == null) {
           $this->addFlash('erreur', "Vous devez vous connecter avant de passer la commande");
           return $this->redirectToRoute('connexion');
        }

        $panier = $request->getSession()->get('panier');
        if ($panier == null) {
            $this->addFlash('erreur', "Votre panier est vide");
            return $this->redirectToRoute('catalogue');
        }

        $client = $em->getRepository(Client::class)->find($client->getId());
        $commande = new Commande();

        // Ajout Commande Details
        foreach($panier->produits as $p)
        {   
            $produit = $em->getRepository(Produit::class)->find($p->getProduitId());

            $commandeDetail = new CommandeDetail();
            $commandeDetail->setProduit($produit);
            $commandeDetail->setQuantite($p->getQuantiteCommandee());
            $commandeDetail->setQuantiteRupture($this->CalculeRupture($p->getQuantiteCommandee(), $produit->getQtteStock()));

            if ($commandeDetail->getQuantite() > $produit->getQtteStock()){
                $produit->setQtteStock(0);
                $this->addFlash('erreur', "Attention: rupture de stock pour ".$produit->getNom()."(manque ".$commandeDetail->getQuantiteRupture().") !");

            }else
            {
                $produit->setQtteStock($produit->getQtteStock() - $commandeDetail->getQuantite());
            }


            $commande->addCommandeDetail($commandeDetail);
        }

        // Ajout Client
        $commande->setClient($client);
        
        // Ajout Date Commande
        $commande->setDateCommande(new \DateTime());

        // Ajout la commande à la base de données
        $em->persist($commande);
        $em->flush();

        // On vide le panier
        $panier = new Panier();
        $request->getSession()->set('panier',$panier);




        //$this->redirectToRoute('facture_commande',['id'=>$commande->getId()]);
        return $this->render('factureCommande.html.twig',['panier'=>$panier,'clientConnecte'=>$client,'commande'=>$commande]);
        
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/historique', name: 'historique')]
    public function historique(ManagerRegistry $doctrine, Request $request): Response
    {   

        $em = $doctrine->getManager();

        $client = $request->getSession()->get('clientConnecte');
        if ($client == null) {
           $this->addFlash('erreur', "Vous devez vous connecter avant de passer la commande");
           return $this->redirectToRoute('connexion');
        }

        $panier = $request->getSession()->get('panier');
        if ($panier == null) {
            $this->addFlash('erreur', "Votre panier est vide");
            return $this->redirectToRoute('catalogue');
        }

        $commandes = $em->getRepository(Commande::class)->findBy(['client'=>$client]);

        // Le plus recent en premier
        $commandes = array_reverse($commandes);

        return $this->render('historiqueAchat.html.twig',['panier'=>$panier,'clientConnecte'=>$client,'commandes'=>$commandes]);
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/commande_annulation/{id}', name: 'commande_annulation')]
    public function commande_annulation(ManagerRegistry $doctrine, Request $request,$id): Response
    {   

        $em = $doctrine->getManager();

        $client = $request->getSession()->get('clientConnecte');
        if ($client == null) {
           $this->addFlash('erreur', "Vous devez vous connecter avant de passer la commande");
           return $this->redirectToRoute('connexion');
        }

        $panier = $request->getSession()->get('panier');
        if ($panier == null) {
            $this->addFlash('erreur', "Votre panier est vide");
            return $this->redirectToRoute('catalogue');
        }

        return $this->render("commandeAnnulation.html.twig",['panier'=>$panier,'clientConnecte'=>$client,'id'=>$id]);
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/annuler_commande/{id}', name: 'annuler_commande')]
    public function annuler_commande(ManagerRegistry $doctrine, Request $request,$id): Response
    {   

        $em = $doctrine->getManager();

        $client = $request->getSession()->get('clientConnecte');
        if ($client == null) {
           $this->addFlash('erreur', "Vous devez vous connecter avant de passer la commande");
           return $this->redirectToRoute('connexion');
        }

        $panier = $request->getSession()->get('panier');
        if ($panier == null) {
            $this->addFlash('erreur', "Votre panier est vide");
            return $this->redirectToRoute('catalogue');
        }

        $commande = $em->getRepository(Commande::class)->find($id);

        $em->remove($commande);
        $em->flush();

        return $this->redirectToRoute('historique');
    }

    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    private function CalculeRupture($quantiteCommandee, $quantiteStock)
    {
        $rupture = $quantiteCommandee - $quantiteStock;

        if ($rupture < 0) {
            return 0;
        }

        return $rupture;

        
    }  
}
