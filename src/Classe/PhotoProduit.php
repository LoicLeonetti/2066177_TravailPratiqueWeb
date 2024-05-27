<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoProduit
{
    private $produitId;
    private $imageProduit;

    public function getImageProduit(): ?UploadedFile
    {
        return $this->imageProduit;
    }

    public function setImageProduit(UploadedFile $fichier = null)
    {
        $this->imageProduit = $fichier;
    }

    public function getProduitId()
    {
        return $this->produitId;
    }

    public function setProduitId($id)
    {
        $this->produitId = $id;
    }
}
