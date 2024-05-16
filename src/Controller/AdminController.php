<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Classe\ConnexionClient;
use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Component\Form\Extension\Core\Type\{SubmitType};
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategorieType;
use App\Form\ProduitType;
use App\Entity\Commande;

class AdminController extends AbstractController
{
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/', name: 'adminConnexion')]
    public function adminConnexion(ManagerRegistry $doctrine, Request $request): Response
    {   
        $connexion = new ConnexionClient();

        $formBuilder = $this->createFormBuilder($connexion) 
        ->add('nom')
        ->add('mdp')
        ->add('valider', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            
            if ($connexion->getNom() == 'admin' && $connexion->getMdp() == 'admin') {
                $this->addFlash('succes', 'Vous êtes maintenant connecté en mode admin');

                $request->getSession()->set('admin',true);

                return $this->redirectToRoute('adminMenu');
            } else {
                $this->addFlash('erreur', 'Nom ou mot de passe incorrect');
            }

        }


        return $this->render('adminConnexion.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/adminMenu', name: 'adminMenu')]
    public function adminMenu(ManagerRegistry $doctrine, Request $request): Response
    {   
        $admin = $request->getSession()->get('admin');


        if ($admin == false || $admin == null) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }

        return $this->render('adminMenu.html.twig');
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/adminDeconnexion', name: 'adminDeconnexion')]
    public function adminDeconnexion(ManagerRegistry $doctrine, Request $request): Response
    {   
        $admin = $request->getSession()->get('admin');

        if (!$admin) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }

        $request->getSession()->set('admin',false);

        return $this->redirectToRoute('adminConnexion');
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/adminAjouterCategorie', name: 'adminAjouterCategorie')]
    public function adminAjouterCategorie(ManagerRegistry $doctrine, Request $request): Response
    {   
        $em = $doctrine->getManager();

        $admin = $request->getSession()->get('admin');

        if (!$admin) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }

        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $em = $doctrine->getManager();
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('succes', 'Catégorie '. $categorie->getDescription() .  ' ajoutée avec succès');
            return $this->redirectToRoute('adminMenu');
        }

        $categories = $em->getRepository(Categorie::class)->findAll();

        return $this->render('adminAjouterCategorie.html.twig',['form' => $form->createView(), 'categories' => $categories]);
    }

    #[Route('/adminAjouterProduit', name: 'adminAjouterProduit')]
    public function adminAjouterProduit(ManagerRegistry $doctrine, Request $request): Response
    {   
        $em = $doctrine->getManager();

        $admin = $request->getSession()->get('admin');

        if (!$admin) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }

        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $produit->setImage('ImageManquante');

            $em->persist($produit);
            $em->flush();

            $this->addFlash('succes', 'Produit '. $produit->getNom() .  ' ajouté avec succès');
            return $this->redirectToRoute('adminMenu');
        }

        $categories = $em->getRepository(Categorie::class)->findAll();

        return $this->render('adminAjouterProduit.html.twig',['form' => $form->createView(), 'categories' => $categories]);
    }

    #[Route('/adminRapportVentes', name: 'adminRapportVentes')]
    public function adminRapportVentes(ManagerRegistry $doctrine, Request $request): Response
    {   
        $em = $doctrine->getManager();

        $admin = $request->getSession()->get('admin');

        if (!$admin) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }

        $commandes = $em->getRepository(Commande::class)->findAll();
        
        array_reverse($commandes);

        return $this->render('adminRapportVente.html.twig',['commandes' => $commandes]);
    }

    
    
}
