<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\OffreEmploi;
use App\Entity\Chomeur;
use App\Entity\Adresse;
use App\Entity\Entreprise;

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





    
    #[Route('/chomeurInscription', name:'chomeur_inscription')]
    public function chomeurInscription(ManagerRegistry $doctrine, Request $req ): Response
    {
        $chomeurCandidat = new Chomeur;

        $formBuilder = $this->createFormBuilder($chomeurCandidat)
        ->add('nom', TextType::class)
        ->add('courriel', TextType::class)
        ->add('tel', TextType::class)
        ->add('naissance', DateType::class);
        
        
        
        return $this->render('chomeurInscription.html.twig');
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
}
