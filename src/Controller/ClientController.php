<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Classe\Panier;

class ClientController extends AbstractController
{
    #[Route('/creationClient', name: 'creationClient')]
    public function creerClient(ManagerRegistry $doctrine, Request $request): Response
    {

        $panier = $request->getSession()->get('panier');

        if ($panier == null) {
            $panier = new Panier();
            $request->getSession()->set('panier',$panier);
        }

       

        $client = new Client();
        $formClient = $this->createForm(ClientType::class, $client);

        
        $formClient->handleRequest($request);
        if ($formClient->isSubmitted() && $formClient->isValid()) {
            //Create Client
        }

       

        
        return $this->render('creationClient.html.twig', [
            'formClient' => $formClient->createView(),
            'panier' => $panier
        ]);
    }
}
