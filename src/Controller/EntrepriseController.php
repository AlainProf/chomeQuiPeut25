<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response,Request, Session\SessionInterface};
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Doctrine\Persistence\ManagerRegistry;
use App\Entity\{OffreEmploi, Application};
use App\Entity\Chomeur;
use App\Entity\Adresse;
use App\Entity\Entreprise;

use App\Form\EntrepriseType;
use App\Form\OffreEmploiType;

final class EntrepriseController extends AbstractController
{
    #[Route('/accueilEntreprise', name: 'accueilEntreprise')]
    public function accueilEntreprise(ManagerRegistry $doctrine, Request $req): Response
    {
       $idEntreprise = $req->query->get('entrepriseConnectee');
       if (empty($idEntreprise))
       {
           $idEntreprise = $req->getSession()->get('entrepriseConnectee')->getId();
       }

       $entrepriseConnectee =  $doctrine->getManager()
                ->getRepository(Entreprise::class)
                ->find($idEntreprise);
        
        if ($entrepriseConnectee)
        {
            $this->addFlash('succes', 'Bienvenue ' . $entrepriseConnectee->getNom());
            $req->getSession()->set('entrepriseConnectee', $entrepriseConnectee);
        }

        return $this->render('accueilEntreprise.html.twig', 
        [
            "entrepriseConnectee" => $entrepriseConnectee
        ]);
    }


   
   
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
    public function entrepInscription(ManagerRegistry $doctrine, Request $req ,     SessionInterface $sess): Response
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
                $sess->set('entrepriseConnectee', $entrepCandidat);

                return $this->redirectToRoute('accueilEntreprise');
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
        $IdEntrep = $req->getSession()->get('entrepriseConnectee')->getId();

        $entrep = $doctrine->getManager()->getRepository(Entreprise::class)->find($IdEntrep);

        $formOE = $this->createForm(OffreEmploiType::class, $oeCandidat);
   
        $formOE->handleRequest($req);
        if ($formOE->isSubmitted()   )
        {
            //2- Nous somme bien en mode soumission
            // on valide les info du post
            if ($formOE->isValid())
            {
                //3- Le fom soumis est valide on sauvegarde $entreCandidat en BD
                $entrep->addOffreEmploi($oeCandidat);
                
                $em  = $doctrine->getManager();
                $em->persist($oeCandidat);
                $em->flush();

                return $this->redirectToRoute('accueilEntreprise');
            }
            else
            {
                $this->addFlash('erreur', 'Info invalide');
            }
        }
        return $this->render('creerOffreEmploi.html.twig', ['formulaire' => $formOE, 'nomEntrep' => $entrep->getNom()] );
     
    }

    #[Route('/convoquer/{idApplic}', name:'convoquer')]
    public function convoquer(ManagerRegistry $doctrine, Request $req, $idApplic): Response
    {
        $app = $doctrine->getManager()->getRepository(Application::class)->find($idApplic);
        $app->setConvoque(true);
        $doctrine->getManager()->flush();
        return $this->redirectToRoute('accueilEntreprise');
    }

    #[Route('/annulerConv/{idApplic}', name:'annulerConv')]
    public function annulerConv(ManagerRegistry $doctrine, Request $req, $idApplic): Response
    {
        $app = $doctrine->getManager()->getRepository(Application::class)->find($idApplic);
        $app->setConvoque(false);
        $doctrine->getManager()->flush();
        return $this->redirectToRoute('accueilEntreprise');
    }


}
