<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request, Session\SessionInterface};
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\OffreEmploi;
use App\Entity\Chomeur;
use App\Entity\Adresse;
use App\Entity\{Entreprise, Application};

use App\Form\ChomeurType;
use App\Form\PhotoChomeurType;
use App\Classe\PhotoChomeur;


final class ChomeurController extends AbstractController
{
    #[Route('/chomeurDetail/{idChomeur}')]
    public function chomeurDetail($idChomeur, ManagerRegistry $doctrine ): Response
    {
        //return new Response("id du chomeur: $idChomeur");

        $chomeur = $doctrine->getManager()->getRepository(Chomeur::class)->find($idChomeur);
        return $this->render('chomeurDetails.html.twig', [
            'chomeur' => $chomeur
        ]);
    }

    #[Route('/accueilChomeur', name: 'accueilChomeur')]
    public function accueilChomeur(ManagerRegistry $doctrine, Request $req, SessionInterface $sess): Response
    {
        $idChomeur = $req->query->get('chomeurConnecte');

        if (empty($idChomeur))
        {
            $idChomeur = $sess->get('chomeurConnecte')->getId();
        }

        $chomeurConnecte =  $doctrine->getManager()
                 ->getRepository(Chomeur::class)
                 ->find($idChomeur);

        if ($chomeurConnecte)
        {
            $this->addFlash('succes', 'Bienvenue ' . $chomeurConnecte->getNom());
            $req->getSession()->set('chomeurConnecte', $chomeurConnecte);


            $nomFichierImage =  __DIR__ . '/../../public/images/chomeur' . $chomeurConnecte->getId() . '.png';

            //Si l’image du chomeur n’existe pas on met l’image par défaut
            if (file_Exists($nomFichierImage))
               $image = 'images/chomeur' . $chomeurConnecte->getId() . '.png';
            else
               $image = 'images/pasImage.png';

            $photoChomeur = new PhotoChomeur;
            $photoChomeur->setChomeurId($chomeurConnecte->getId());
            // On créé le formulaire avec le PhotoChomeurType et on lui passe une
            // instance de la classe PhotoChomeur
            $formPhotoChomeur = $this->createForm(PhotoChomeurType::Class, $photoChomeur);
            $formPhotoChomeur->handleRequest($req);
            if ($formPhotoChomeur->isSubmitted())		
            {
              if ($formPhotoChomeur->isValid())
              {
                 $codeErreur = 0;
                 if ($photoChomeur->televerse($codeErreur))
                 {
                    $this->addFlash('notice', 'image du chômeur téléversée avec 
                                               succès!');
                 }
                 else
                    $this->addFlash('erreur', "Erreur ($codeErreur) lors du 
                                               téléversement de l'image");
              }
              else
              {
                     $this->addFlash('erreur', "Formulaire invalide ");
              }
           }
        }


  
        $offresEmplois = $doctrine->getManager()
                                  ->getRepository(OffreEmploi::class)
                                  ->findAll();
        $entreprises = $doctrine->getManager()->getRepository(Entreprise::class)->findAll();                                  

        $offresEmploisNA = $this->filtrerOEDejaAppliquees($offresEmplois, $chomeurConnecte);

        if (count($offresEmplois) == 0)
        {
            $this->addFlash('notice', 'Aucune offres d\'emploi présentement affichée sur le site');
        }

        return $this->render('accueilChomeur.html.twig', 
        [
            'tabOE' => $offresEmploisNA,
            'chomeur' => $chomeurConnecte,
            'tabEntrep' => $entreprises,
            'image' => $image,
            'formPhotoChomeur' => $formPhotoChomeur->createView() 
        ]);
    }


    
    #[Route('/chomeurInscription', name:'chomeur_inscription')]
    public function chomeurInscription(ManagerRegistry $doctrine, Request $req ): Response
    {
        $chomeurCandidat = new Chomeur;
        $chomeurCandidat->setDateInscription(new \DateTime);
        $chomeurCandidat->setNaissance(new \DateTime);

        $adresse = new Adresse;
        $adresse->setNumCivique("1234");
        $adresse->setRue("sans issue");
        $adresse->setVille("pandemonium");
        $adresse->setcodePostal("G1Q1Q9");
        

        $chomeurCandidat->setAdresse($adresse);

        $formChomeur = $this->createForm(ChomeurType::class, $chomeurCandidat);
   
        $formChomeur->handleRequest($req);
        if ($formChomeur->isSubmitted()   )
        {
            //2- Nous somme bien en mode soumission
            // on valide les info du post
            if ($formChomeur->isValid())
            {
                //3- Le fom soumis est valide on sauvegarde $entreCandidat en BD
                $em  = $doctrine->getManager();
                $em->persist($chomeurCandidat);
                $em->flush();

                $sess = $req->getSession();
                $sess->set('chomeurConnecte', $chomeurCandidat );

                return $this->redirectToRoute('accueilChomeur');
            }
            else
            {
                $this->addFlash('erreur', 'Info invalide');
            }
        }
        return $this->render('chomeurInscription.html.twig', ['formulaire' => $formChomeur] );
    }
       



    
    #[Route('/rechercheTexte', name:'rte_recherche_textuelle')]
    public function rechercheTexte(ManagerRegistry $doctrine, Request $req ): Response
    {
        $texteRecherche = $req->request->get('filtreTexte');

        //dd($texteRecherche);

        $tousChomeurs = $doctrine->getManager()->getRepository(Chomeur::class)->findAll();

        $tabChomeurs = array();
        foreach($tousChomeurs as $unChomeur)
        {
            if (str_contains(strtoupper($unChomeur->getNom()), strtoupper($texteRecherche) ))
            {
                $tabChomeurs[] = $unChomeur;
            }
        }

        $offresEmplois = $doctrine->getManager()
                                  ->getRepository(OffreEmploi::class)
                                  ->findAll();

        $entreprises = $doctrine->getManager()->getRepository(Entreprise::class)->findAll();                                  
              
        if (count($offresEmplois) == 0)
        {
            $this->addFlash('notice', 'Aucune offres d\'emploi présentement affichée sur le site');
        }

        return $this->render('accueil.html.twig', 
        [
            'tabOE' => $offresEmplois,
            'chomeurs' =>$tabChomeurs,
            'tabEntrep' => $entreprises
        ]);
    }


    
    #[Route('/postuler/{oeid}', name:'postuler')]
    public function postuler(ManagerRegistry $doctrine, Request $req, $oeid ): Response
    {
        $idChomeur = $req->getSession()->get('chomeurConnecte')->getId();

        $chomeurPostulant = $doctrine->getManager()->getRepository(Chomeur::class)->find($idChomeur);
        $oePostulee = $doctrine->getManager()->getRepository(OffreEmploi::class)->find($oeid);

        $applic = new Application;
        $applic->setOffreEmploi($oePostulee);
        $applic->setChomeur($chomeurPostulant);
        $applic->setDatePostulee(new \DateTime);
        $applic->setConvoque(false);

        $chomeurPostulant->addApplication($applic);
        $doctrine->getManager()->flush();

        $this->addFlash('succes', 'Vous avez postulé correctement sur ' . $oePostulee->getTitre());
        return $this->redirectToroute('accueilChomeur');
    }

    
    #[Route('/annuler/{appid}', name:'annuler')]
    public function annuler(ManagerRegistry $doctrine, Request $req, $appid ): Response
    {
        $idChomeur = $req->getSession()->get('chomeurConnecte')->getId();

        $chomeurAnnulant = $doctrine->getManager()->getRepository(Chomeur::class)->find($idChomeur);
        $appAnnulee = $doctrine->getManager()->getRepository(Application::class)->find($appid);

        $chomeurAnnulant->removeApplication($appAnnulee);
        $doctrine->getManager()->remove($appAnnulee);
        $doctrine->getManager()->flush();

        $this->addFlash('notice', 'Vous avez correctement annulé votre postulation sur ' . $appAnnulee->getOffreEmploi()->getTitre());
        return $this->redirectToroute('accueilChomeur');
    }

    function filtrerOEDejaAppliquees($toutesOE,  $chomeur)
    {
       $tabOEFiltrees = [];
       foreach($toutesOE as $uneOE)   
       {
           $appliquee = false;
           foreach($chomeur->getApplications() as $uneApplic)
           {
               if ($uneOE->getId() === $uneApplic->getOffreEmploi()->getId())
               {
                  $appliquee = true;
               }
           }
           if (!$appliquee)
           {
            $tabOEFiltrees[] = $uneOE;
           }
       }
       return $tabOEFiltrees;
    }

    
    #[Route('/reqAvancee', name:'reqAvancee')]
    public function reqAvancee(ManagerRegistry $doctrine, Request $req): Response
    {
        $entreprise = $doctrine->getManager()->getRepository(Entreprise::class)->findOneBy(['nom'=>'pepsi']);

        $em = $doctrine->getmanager();

        $query = $em->createQuery(
            'select oe
            from App\Entity\OffreEmploi oe
            where oe.salaireAnnuel < 120000 and oe.entreprise = :entrep')
            ->setParameter('entrep', $entreprise); 
        $offres = $query->getResult();


        return $this->render('resultat.html.twig', ['offres' => $offres]);
    }

    #[Route('/validerUnicite', name:'reqAvancee')]
    public function validerUnicite(ManagerRegistry $doctrine, Request $request)
    {
        $reponse = "unique";
        if ($request->isXmlHttpRequest())
        {
           $nomUtilisateur = $request->query->get('nomChomeur');
           //die ("--$nomUtilisateur--");
          $em = $doctrine->getManager();
           $client = $em->getRepository(Chomeur::class)
        ->findOneBy(['nom'=>$nomUtilisateur]);
        if ($client)
           $reponse="doublon";
          return new Response($reponse);
        }
        die('Injection URL');
    }
    

}
