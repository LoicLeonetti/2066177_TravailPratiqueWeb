<?php

namespace App\Controller;

use App\Classe\ConnexionClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Classe\Panier;

use Symfony\Component\Form\Extension\Core\Type\{SubmitType};

class ClientController extends AbstractController
{   
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
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
        
        if ($formClient->isSubmitted()) 
        {
            if($formClient->isValid()){
                
               $request->getSession()->set('client_candidat',$client);
                return $this->render('confirmationCreationClient.html.twig', 
                ['client' => $client, 'panier' => $panier]);
            }else{
                $this->addFlash('erreur', "Au moins une erreur dans les données fournies");
            }
        }

       

        
        return $this->render('creationClient.html.twig', [
            'formClient' => $formClient->createView(),
            'panier' => $panier,
            'clientConnecte' => $request->getSession()->get('clientConnecte'),
            'mode'=> 'creation'
        ]);
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#    
    #[Route('/confirmerCreation', name: 'confirmerCreation')]
    public function confirmerCreation(ManagerRegistry $doctrine, Request $request): Response
    {   
        $client = $request->getSession()->get('client_candidat');
        $request->getSession()->remove('client_candidat');

        $em = $doctrine->getManager();
        $em->persist($client);
        $em->flush();

        $request->getSession()->set('clientConnecte',$client);
        $this->addFlash('succes', "Client créé avec succès");
        
        return $this->redirectToRoute('catalogue');
    }

    #[Route('/annulerCreation', name: 'annulerCreation')]
    public function annulerCreation(ManagerRegistry $doctrine, Request $request): Response
    {   
   
        $request->getSession()->remove('client_candidat');
        
        return $this->redirectToRoute('catalogue');
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/connexion', name: 'connexion')]
    public function connexion(ManagerRegistry $doctrine, Request $request): Response
    {   
        $connexion = new ConnexionClient();

        $panier = $request->getSession()->get('panier');

        if ($panier == null) {
            $panier = new Panier();
            $request->getSession()->set('panier',$panier);
        }

        $formBuilder = $this->createFormBuilder($connexion) 
        ->add('nom')
        ->add('mdp')
        ->add('connexion', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()){
            
            if ($form->isValid()) {
                $em = $doctrine->getManager();

                $client = $em->getRepository(Client::class)->findOneBy(['nom' => $connexion->getNom(), 'motDePasse' => $connexion->getMdp()]);

                if ($client != null) {
                    $request->getSession()->set('clientConnecte',$client);
                    $this->addFlash('succes', "Connexion réussie");
                    return $this->redirectToRoute('catalogue');
                }else{
                    $this->addFlash('erreur', "Nom ou mot de passe incorrect");
                }
            }
        }

        return $this->render('connexion.html.twig', ['formConnexion' => $form->createView(),"panier"=>$panier,"clientConnecte" => null]);
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/deconnexion', name: 'deconnexion')]
    public function deconnexion(ManagerRegistry $doctrine, Request $request): Response
    {   
        $request->getSession()->set('clientConnecte',null);

        $this->addFlash('succes', "Déconnexion réussie");

        return $this->redirectToRoute('catalogue');
    }
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #--------------------------------------------------------------------------------#
    #[Route('/modification', name: 'modification')]
    public function modification(ManagerRegistry $doctrine, Request $request): Response
    {   
        $clientAModifier = $request->getSession()->get('clientConnecte');
        $em = $doctrine->getManager();


        $panier = $request->getSession()->get('panier');

        if ($panier == null) {
            $panier = new Panier();
            $request->getSession()->set('panier',$panier);
        }

        $clientAModifier = $em->getRepository(Client::class)->find($clientAModifier->getId());
        $clientInfo = new Client();

        $formInfo = $this->createForm(ClientType::class, $clientInfo);

        $formInfo->handleRequest($request);
        
        if ($formInfo->isSubmitted()) 
        {
            if($formInfo->isValid()){



                $clientAModifier->setPrenom($clientInfo->getPrenom());
                $clientAModifier->setNomFamille($clientInfo->getNomFamille());
                $clientAModifier->setGenre($clientInfo->getGenre());
                $clientAModifier->setAdresse($clientInfo->getAdresse());
                $clientAModifier->setVille($clientInfo->getVille());
                $clientAModifier->setCodePostal($clientInfo->getCodePostal());
                $clientAModifier->setAdresseCourriel($clientInfo->getAdresseCourriel());
                $clientAModifier->setMotDePasse($clientInfo->getMotDePasse());
                
                $em->flush();

                $request->getSession()->set('clientConnecte',$clientAModifier);
                
                $this->addFlash('succes', "Modification réussie");
                
                return $this->redirectToRoute('catalogue');


            }else{
                $this->addFlash('erreur', "Au moins une erreur dans les données fournies");
            }
        }

    

        return $this->render('creationClient.html.twig', [
            'formInfo' => $formInfo->createView(),
            'panier' => $panier,
            'clientConnecte' => $request->getSession()->get('clientConnecte'),
            'mode'=> 'modification'
        ]);
    }

    
}
