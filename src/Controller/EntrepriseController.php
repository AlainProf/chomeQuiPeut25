<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Doctrine\Persistence\ManagerRegistry;
use App\Entity\OffreEmploi;
use App\Entity\Chomeur;
use App\Entity\Adresse;
use App\Entity\Entreprise;

use App\Form\EntrepriseType;
use App\Form\OffreEmploiType;

final class EntrepriseController extends AbstractController
{
    #[Route('/entrepriseEtSesOffres', name:'rte_entrep_et_ses_offres')]
    public function entrepriseEtSesOffres(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $tabEntrep = $em->getRepository(Entreprise::class)->findAll();


        return $this->render('entreprise.html.twig', [ 'tabEntrep'
            => $tabEntrep
        ]);
    }

    #[Route('/entrepInscription', name:'entrep_inscription')]
    public function entrepInscription(ManagerRegistry $doctrine, Request $req ): Response
    {
  
        $entrepCandidat = new Entreprise;

        $formExterne = $this->createForm(EntrepriseType::class, $entrepCandidat );
      
        // 1- Si le form vient d'être soumis handleRequest($req) remplira l'objet $entreCandidat
        $formExterne->handleRequest($req);
        if ($formExterne->isSubmitted()   )
        {
            //2- Nous somme bien en mode soumission
            // on valide les info du post
            if ($formExterne->isValid())
            {
                //3- Le fom soumis est valide on sauvegarde $entreCandidat en BD
                $em  = $doctrine->getManager();
                $em->persist($entrepCandidat);
                $em->flush();

                return $this->redirectToRoute('accueil');
            }
            else
            {
                $this->addFlash('erreur', 'Info invalide');
            }
        }
        return $this->render('entrepInscription.html.twig', ['formulaire' => $formExterne] );
    }


    
    #[Route('/appliquerFiltre', name:'rte_appliquer_filtre')]
    public function appliquerFiltre(ManagerRegistry $doctrine, Request $req): Response
    {
        //  Accès à un $_GET
        //$filEntrep = $req->query->get();

        // Accès à un $_POST
        $filtreEntrep = $req->request->get('filtreEntrep');

        //dd($filtreEntrep);

        $em = $doctrine->getManager();
        $entrep = $em->getRepository(Entreprise::class)->find($filtreEntrep);
        
        return $this->redirectToRoute('accueil_filtre', ["idEntrep" => $filtreEntrep] );


        //return new Response("<h1>Filtrer ourentrep :"  . $_POST['filtreEntrep'] . "</h1>");

        
    }

    
    #[Route('/creerOffreEmploi', name:'rte_creer_offre_emploi')]
    public function creerOffreEmploi(ManagerRegistry $doctrine, Request $req): Response
    {
        $oeCandidat = new OffreEmploi;
        $oeCandidat->setdatePublication(new \DateTime);

        $formOE = $this->createForm(OffreEmploiType::class, $oeCandidat);
   
        $formOE->handleRequest($req);
        if ($formOE->isSubmitted()   )
        {
            //2- Nous somme bien en mode soumission
            // on valide les info du post
            if ($formOE->isValid())
            {
                //3- Le fom soumis est valide on sauvegarde $entreCandidat en BD
                $em  = $doctrine->getManager();
                $em->persist($oeCandidat);
                $em->flush();

                return $this->redirectToRoute('accueil');
            }
            else
            {
                $this->addFlash('erreur', 'Info invalide');
            }
        }
        return $this->render('creerOffreEmploi.html.twig', ['formulaire' => $formOE] );
     
    }

}
