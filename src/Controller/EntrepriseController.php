<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\OffreEmploi;
use App\Entity\Chomeur;
use App\Entity\Adresse;
use App\Entity\Entreprise;

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


    
    #[Route('/appliquerFiltre', name:'rte_appliquer_filtre')]
    public function appliquerFiltre(ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();
        $tabEntrep = $em->getRepository(Entreprise::class)->findAll();
     

        return new Response("<h1>Filtrer ourentrep :"  . $_GET['filtreEntrep'] . "</h1>");

        
    }

}
