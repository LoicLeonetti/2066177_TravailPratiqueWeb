<?php
namespace App\Classe;

class ConnexionClient
{
    private $nom;
    private $description;
    private $prix;
    private $qtte_stock;
    private $qtte_seuil_min;
    private $categorie;

    private $mdp;

    public function __construct()
    {
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setnom($n)
    {
       $this->nom = $n;
    }

    public function getMdp()
    {
        return $this->mdp;
    }

    public function setMdp($m)
    {
        $this->mdp = $m;
    }
}