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
use App\Classe\PhotoProduit;
use App\Form\PhotoProduitType;



class AdminController extends AbstractController
{
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/', name: 'adminConnexion')]
    public function adminConnexion(ManagerRegistry $doctrine, Request $request): Response
    {
        $connexion = new ConnexionClient();

        $produits = $doctrine->getManager()->getRepository(Produit::class)->findAll();

        foreach ($produits as $p) {
            $p->setImage($p->getId());
        }

        $em = $doctrine->getManager();

        $em->flush();




        $formBuilder = $this->createFormBuilder($connexion)
            ->add('nom')
            ->add('mdp')
            ->add('valider', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($connexion->getNom() == 'admin' && $connexion->getMdp() == 'admin') {
                $this->addFlash('succes', 'Vous êtes maintenant connecté en mode admin');

                $request->getSession()->set('admin', true);

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

        $request->getSession()->set('admin', false);

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

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('succes', 'Catégorie ' . $categorie->getDescription() .  ' ajoutée avec succès');
            return $this->redirectToRoute('adminMenu');
        }

        $categories = $em->getRepository(Categorie::class)->findAll();

        return $this->render('adminAjouterCategorie.html.twig', ['form' => $form->createView(), 'categories' => $categories]);
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
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

        if ($form->isSubmitted() && $form->isValid()) {

            $produit->setImage('ImageManquante');

            $em->persist($produit);
            $em->flush();

            $this->addFlash('succes', 'Produit ' . $produit->getNom() .  ' ajouté avec succès');
            return $this->redirectToRoute('adminMenu');
        }

        $categories = $em->getRepository(Categorie::class)->findAll();

        return $this->render('adminAjouterProduit.html.twig', ['form' => $form->createView(), 'categories' => $categories]);
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
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

        return $this->render('adminRapportVente.html.twig', ['commandes' => $commandes]);
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/adminProduits', name: 'adminProduits')]
    public function adminProduits(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();

        $admin = $request->getSession()->get('admin');

        if (!$admin) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }

        $produits = $em->getRepository(Produit::class)->findAll();

        array_reverse($produits);

        return $this->render('adminProduits.html.twig', ['produits' => $produits]);
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/adminProduitsCommander', name: 'adminProduitsCommander')]
    public function adminProduitsCommander(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();

        $admin = $request->getSession()->get('admin');

        if (!$admin) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }

        $produits = $em->getRepository(Produit::class)->findAll();

        $produitsACommander = [];

        foreach ($produits as $p) {
            if ($p->getQtteStock() < $p->getQtteSeuilMin()) {
                array_push($produitsACommander, $p);
            }
        }

        return $this->render('adminProduits.html.twig', ['produits' => $produitsACommander]);
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/adminModifierCategorie', name: 'adminModifierCategorie')]
    public function adminModifierCategorie(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();

        $admin = $request->getSession()->get('admin');

        if (!$admin) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }

        $categories = $em->getRepository(Categorie::class)->findAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $key => $value) {

                $categorie = $em->getRepository(Categorie::class)->find($key);

                if ($categorie) {
                    $categorie->setDescription($value);
                    $em->persist($categorie);
                }
            }
            $em->flush();
            $this->addFlash('succes', 'Catégories modifiées avec succès');
            $this->redirectToRoute('adminMenu');
        }

        return $this->render('adminModifierCategorie.html.twig', ['categories' => $categories]);
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/adminModifierProduits', name: 'adminModifierProduits')]
    public function adminModifierProduits(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();

        $admin = $request->getSession()->get('admin');

        if (!$admin) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }

        $produits = $em->getRepository(Produit::class)->findAll();

        return $this->render('adminModifierProduits.html.twig', ['produits' => $produits]);
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#    
    #[Route('/adminModifierProduit/{id}', name: 'adminModifierProduit')]
    public function adminModifierProduit(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $em = $doctrine->getManager();

        $admin = $request->getSession()->get('admin');

        if (!$admin) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }


        $produit = $em->getRepository(Produit::class)->find($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $produit->setNom($_POST['nom']);
            $produit->setDescription($_POST['description']);
            $produit->setPrix($_POST['prix']);
            $produit->setQtteStock($_POST['qtteStock']);
            $produit->setQtteSeuilMin($_POST['qtteSeuilMin']);

            $em->flush();
            $this->addFlash('succes', 'Produit modifié avec succès');

            return $this->redirectToRoute('adminMenu');
        }


        return $this->render('adminModifierProduit.html.twig', ['produit' => $produit]);
    }

    #[Route('/adminImageCouverture/{id}', name: 'adminImageCouverture')]
    public function adminImageCouverture(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $em = $doctrine->getManager();

        $admin = $request->getSession()->get('admin');

        if (!$admin) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }


        $produit = $em->getRepository(Produit::class)->find($id);

        $imageFolder = '/../../public/images/produits';

        $photoProduit = new PhotoProduit();
        $photoProduit->setProduitId($produit->getId());

        $form = $this->createForm(PhotoProduitType::class, $photoProduit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fichier = $photoProduit->getImageProduit();

            $fichier->move(
                __DIR__ . $imageFolder,
                $produit->getId() . '.png'
            );

            $produit->setImage($produit->getId());

            $em->flush();

            $this->addFlash('succes', 'Image de couverture modifiée avec succès');
            return $this->redirectToRoute('adminMenu');
        }



        return $this->render('adminImageCouverture.html.twig', ['produit' => $produit, 'form' => $form->createView()]);
    }

    #[Route('/adminImageDetails/{id}', name: 'adminImageDetails')]
    public function adminImageDetails(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $em = $doctrine->getManager();

        $admin = $request->getSession()->get('admin');

        if (!$admin) {
            $this->addFlash('erreur', 'Non au piratage!');
            return $this->redirectToRoute('catalogue');
        }


        $produit = $em->getRepository(Produit::class)->find($id);

        $imageFolder = '/../../public/images/modal';

        $photoProduit = new PhotoProduit();
        $photoProduit->setProduitId($produit->getId());

        $formPhoto = $this->createForm(PhotoProduitType::class, $photoProduit);

        $formPhoto->handleRequest($request);

        if ($formPhoto->isSubmitted() && $formPhoto->isValid()) {

            $fichier = $photoProduit->getImageProduit();

            $fichier->move(
                __DIR__ . $imageFolder,
                $produit->getId() . '.png'
            );

            $produit->setImage($produit->getId());

            $em->flush();

            $this->addFlash('succes', 'Image de couverture modifiée avec succès');
            return $this->redirectToRoute('adminMenu');
        }



        return $this->render('adminImageDetails.html.twig', ['produit' => $produit, 'form' => $formPhoto->createView()]);
    }
}
