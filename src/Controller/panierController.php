<?php

namespace App\Controller;


# Nom: Loic Leonetti #
# Date: 2024-02-24   #

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use ProduitPanier;
use Panier;


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

        dd($panier);

        return $this->render('panier.html.twig',['panier'=>$panier]);
    }

}