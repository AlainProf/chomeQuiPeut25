<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request, Session\SessionInterface};
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\OffreEmploi;
use App\Entity\Chomeur;
use App\Entity\Adresse;
use App\Entity\Entreprise;

//ini_set('date.timezone', 'america/new_york');


final class BaseController extends AbstractController
{
    #[Route('/', name: 'racine')]
    public function racine(ManagerRegistry $doctrine, SessionInterface $sess): Response
    {
        $sess->clear();
        
        $offresEmplois = $doctrine->getManager()
                                  ->getRepository(OffreEmploi::class)
                                  ->findAll();

        $chomeurs = $doctrine->getManager()->getRepository(Chomeur::class)->findAll();                                  
        $entreprises = $doctrine->getManager()->getRepository(Entreprise::class)->findAll();                                  
              
        return $this->render('racine.html.twig', 
        [
            'tabOE' => $offresEmplois,
            'chomeurs' =>$chomeurs,
            'entreprises' => $entreprises
        ]);
    }

    





    #[Route('/accueil', name: 'accueil')]
    public function accueil(ManagerRegistry $doctrine): Response
    {
        $offresEmplois = $doctrine->getManager()
                                  ->getRepository(OffreEmploi::class)
                                  ->findAll();

        $chomeurs = $doctrine->getManager()->getRepository(Chomeur::class)->findAll();                                  
        $entreprises = $doctrine->getManager()->getRepository(Entreprise::class)->findAll();                                  
              
         
        if (count($offresEmplois) == 0)
        {
            $this->addFlash('notice', 'Aucune offres d\'emploi présentement affichée sur le site');
        }

        return $this->render('accueil.html.twig', 
        [
            'tabOE' => $offresEmplois,
            'chomeurs' =>$chomeurs,
            'tabEntrep' => $entreprises
        ]);
    }

    #[Route('/accueilFiltre/{idEntrep}', name: 'accueil_filtre')]
    public function accueilFiltre(ManagerRegistry $doctrine, Request $req, $idEntrep): Response
    {
        //$valFiltre = $req->request->get('filtreEntrep');
        if ($idEntrep == '1000')
           return $this->redirectToRoute('accueil');

        $em = $doctrine->getManager();
        $chomeurs = $doctrine->getManager()->getRepository(Chomeur::class)->findAll();                                  
        $entrepFiltree = $em->getRepository(Entreprise::class)->find($idEntrep); 

        $entreprises = $em->getRepository(Entreprise::class)->findAll();

        $tabOE = $entrepFiltree->getOffreEmplois();
        if (count($tabOE) == 0)
        {
            $this->addFlash('notice', 'Aucune offres d\'emploi affichée par ' . $entrepFiltree->getNom());
        }
        else
        {
            $this->addFlash('success', ' ' . count($tabOE) . ' offres d\'emplois affichées par ' . $entrepFiltree->getNom());
        }
        return $this->render('accueil.html.twig', 
        [
            'tabOE' => $tabOE,
            'chomeurs' =>$chomeurs,
            'tabEntrep' => $entreprises,
            'entrepFiltree' => $entrepFiltree
        ]);
    }

   
    #[Route('/voirSession', name:'voirSession')]
    public function voirSession(Request $req): Response
    {
        $chomeur = $req->getSession()->get('chomeurConnecte');
        $entreprise = $req->getSession()->get('entrepriseConnectee');

        $info = "";
        if ($chomeur)
          $info = "Chomeur:" . $chomeur->getNom() . "(" . $chomeur->getId() . ")";
        if ($entreprise)
          $info .= "Entreprise:" . $entreprise->getNom() . "(" . $entreprise->getId() . ")";
        dd($info);
    }

    #[Route('/viderSession', name:'viderSession')]
    public function viderSession(Request $req): Response
    {
        $req->getSession()->clear();

        return $this->redirectToRoute('racine');
    }



}
