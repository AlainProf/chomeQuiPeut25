<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoChomeur
{
   private $chomeurId;
   private $imageChomeur;

   public function getChomeurId()
   {
      return $this->chomeurId;
   }
   
   public function setChomeurId($id)
   {
       $this->chomeurId = $id ;
   }
 

  public function getImageChomeur() : ?UploadedFile
  {
     return $this->imageChomeur;
  }

  public function setImageChomeur(UploadedFile $fichier)
  {
    $this->imageChomeur = $fichier;
  }

  public function televerse(&$codeErr)
  {
     $type = $this->imageChomeur->getClientMimeType();

     if ($type == 'image/png')
     {
        $nomDossier = __DIR__ . '/../../public/images';
        $nomFichier =  "chomeur$this->chomeurId.png";

        $this->imageChomeur->move($nomDossier, $nomFichier);
        return true;
     }
     else
     {
        $codeErr = -1;
        return false;
     }
  }
}

