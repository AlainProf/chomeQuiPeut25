<?php
// ..\src\Classe\PhotoChomeur.php
namespace App\Classe;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoChomeur
{
   private $chomeurId;
   private $imageChomeur;
   
   public function getImageChomeur() : ?UploadedFile 
   { 
      return $this->imageChomeur; 
   }
   public function setImageChomeur (UploadedFile $fichier = null)
   {
      $this->imageChomeur = $fichier;  
   }
   public function getChomeurId()   { return $this->chomeurId; }
   public function setChomeurId($id) {$this->chomeurId= $id;}

   // C’est une fonction booléenne : true = succès false = échec. Le
   // param $codeErreur retournera un code d’erreur au contexte appelant cette
   // méthode
  public function televerse(&$codeErreur = 0)
  {
    $codeErreur = 0; 
    // on récupère l’extension du fichier à téléverser
    $type = $this->imageChomeur->getClientMimeType();
    // on accepte seulement les formats jpg, png et gif
    if ($type == 'image/gif' ||
        $type == 'image/png' ||            
        $type == 'image/jpeg' )
    {
       // on construit le nom du dossier de destination
       $nomDossier = __DIR__ . '/../../public/images';
       // on construit le nom du fichier de destination
       $nomFichier = "chomeur$this->chomeurId.png";
       
       // on fait move le fichier reçu dans son dossier/fichier final
       $this->imageChomeur->move($nomDossier, $nomFichier);
       return true;
    }
    else
    {
       // -3 : mauvaise extension de fichier
       $codeErreur = -3;
       return false;
    }
  } 

}   
